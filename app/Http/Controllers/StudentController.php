<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'class']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('student_number', 'like', "%{$search}%")
              ->orWhere('admission_number', 'like', "%{$search}%");
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $students = $query->latest()->paginate(15);
        $classes = ClassRoom::active()->orderBy('name')->get();

        return view('students.index', compact('students', 'classes'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $classes = ClassRoom::active()->orderBy('name')->get();
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);

        return view('students.create', compact('classes', 'academicYear'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create user account
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => true,
            ];

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('students', 'public');
                $userData['profile_photo'] = $path;
            }

            $user = User::create($userData);

            // Create student profile
            $studentData = $request->except(['name', 'email', 'password', 'password_confirmation', 'phone', 'address', 'profile_photo']);
            $studentData['user_id'] = $user->id;

            // Auto-generate student number if not provided
            if (!$request->filled('student_number')) {
                $studentData['student_number'] = Student::generateStudentNumber();
            }

            Student::create($studentData);

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student registered successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to register student: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load(['user', 'class.classTeacher', 'attendance', 'grades.exam.subject']);

        // Calculate statistics
        $stats = [
            'attendance_percentage' => $student->getCurrentMonthAttendance(),
            'current_gpa' => $student->getCurrentSemesterGPA(),
            'total_exams' => $student->getTotalExamsCount(),
            'failed_exams' => $student->getFailedExamsCount(),
            'class_rank' => $student->getClassRank(),
        ];

        // Recent attendance
        $recentAttendance = $student->attendance()
            ->with('markedByTeacher')
            ->latest('date')
            ->take(10)
            ->get();

        // Recent grades
        $recentGrades = $student->grades()
            ->with(['exam.subject', 'enteredByTeacher'])
            ->latest()
            ->take(10)
            ->get();

        return view('students.show', compact('student', 'stats', 'recentAttendance', 'recentGrades'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $student->load('user');
        $classes = ClassRoom::active()->orderBy('name')->get();

        return view('students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
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
                if ($student->user->profile_photo) {
                    Storage::disk('public')->delete($student->user->profile_photo);
                }

                $path = $request->file('profile_photo')->store('students', 'public');
                $userData['profile_photo'] = $path;
            }

            $student->user->update($userData);

            // Update student profile
            $studentData = $request->except([
                'name', 'email', 'password', 'password_confirmation', 
                'phone', 'address', 'profile_photo'
            ]);

            $student->update($studentData);

            DB::commit();

            return redirect()->route('students.show', $student)
                ->with('success', 'Student updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();

            // Delete profile photo if exists
            if ($student->user->profile_photo) {
                Storage::disk('public')->delete($student->user->profile_photo);
            }

            // Soft delete the user (cascades to student)
            $student->user->delete();

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    /**
     * Toggle student active status
     */
    public function toggleStatus(Student $student)
    {
        $student->update(['is_active' => !$student->is_active]);
        $student->user->update(['is_active' => !$student->user->is_active]);

        $status = $student->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Student {$status} successfully!");
    }

    /**
     * Export students to CSV
     */
    public function export(Request $request)
    {
        $query = Student::with(['user', 'class']);

        // Apply same filters as index
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->get();

        $filename = 'students_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Student Number', 'Name', 'Email', 'Class', 'Gender', 
                'Date of Birth', 'Parent Name', 'Parent Phone', 'Status'
            ]);

            // Data
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->student_number,
                    $student->user->name,
                    $student->user->email,
                    $student->class?->full_name ?? 'N/A',
                    ucfirst($student->gender),
                    $student->date_of_birth->format('Y-m-d'),
                    $student->parent_name,
                    $student->parent_phone,
                    $student->is_active ? 'Active' : 'Inactive',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}