<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers.
     */
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'subjects']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('employee_number', 'like', "%{$search}%");
        }

        // Filter by specialization
        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $teachers = $query->latest()->paginate(15);

        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $classes = ClassRoom::active()->orderBy('name')->get();

        return view('teachers.create', compact('subjects', 'classes'));
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(StoreTeacherRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create user account
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => true,
            ];

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('teachers', 'public');
                $userData['profile_photo'] = $path;
            }

            $user = User::create($userData);

            // Create teacher profile
            $teacherData = $request->except([
                'name', 'email', 'password', 'password_confirmation', 
                'phone', 'address', 'profile_photo'
            ]);
            $teacherData['user_id'] = $user->id;

            // Auto-generate employee number if not provided
            if (!$request->filled('employee_number')) {
                $teacherData['employee_number'] = Teacher::generateEmployeeNumber();
            }

            Teacher::create($teacherData);

            DB::commit();

            return redirect()->route('teachers.index')
                ->with('success', 'Teacher registered successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to register teacher: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'subjects', 'assignedClasses']);

        // Get teaching assignments
        $assignments = $teacher->getCurrentAssignments();

        // Statistics
        $stats = [
            'total_students' => $teacher->getTotalStudentsCount(),
            'total_subjects' => $teacher->subjects->count(),
            'total_classes' => $teacher->classes->unique('id')->count(),
            'years_of_service' => $teacher->years_of_service,
            'total_exams_created' => $teacher->getTotalExamsCreated(),
            'total_grades_entered' => $teacher->getTotalGradesEntered(),
        ];

        return view('teachers.show', compact('teacher', 'assignments', 'stats'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        $subjects = Subject::active()->orderBy('name')->get();
        $classes = ClassRoom::active()->orderBy('name')->get();

        return view('teachers.edit', compact('teacher', 'subjects', 'classes'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        try {
            DB::beginTransaction();

            // Update user account
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => $request->boolean('is_active', true),
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($teacher->user->profile_photo) {
                    Storage::disk('public')->delete($teacher->user->profile_photo);
                }

                $path = $request->file('profile_photo')->store('teachers', 'public');
                $userData['profile_photo'] = $path;
            }

            $teacher->user->update($userData);

            // Update teacher profile
            $teacherData = $request->except([
                'name', 'email', 'password', 'password_confirmation', 
                'phone', 'address', 'profile_photo'
            ]);

            $teacher->update($teacherData);

            DB::commit();

            return redirect()->route('teachers.show', $teacher)
                ->with('success', 'Teacher updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update teacher: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(Teacher $teacher)
    {
        try {
            DB::beginTransaction();

            // Delete profile photo if exists
            if ($teacher->user->profile_photo) {
                Storage::disk('public')->delete($teacher->user->profile_photo);
            }

            // Soft delete the user (cascades to teacher)
            $teacher->user->delete();

            DB::commit();

            return redirect()->route('teachers.index')
                ->with('success', 'Teacher deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to delete teacher: ' . $e->getMessage());
        }
    }

    /**
     * Assign subjects to teacher
     */
    public function assignSubjects(Request $request, Teacher $teacher)
    {
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.subject_id' => 'required|exists:subjects,id',
            'assignments.*.class_id' => 'required|exists:classes,id',
        ]);

        $academicYear = date('Y') . '-' . (date('Y') + 1);

        // Detach existing assignments for current academic year
        $teacher->subjects()->wherePivot('academic_year', $academicYear)->detach();

        // Attach new assignments
        foreach ($request->assignments as $assignment) {
            $teacher->subjects()->attach($assignment['subject_id'], [
                'class_id' => $assignment['class_id'],
                'academic_year' => $academicYear,
            ]);
        }

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Subject assignments updated successfully!');
    }
}