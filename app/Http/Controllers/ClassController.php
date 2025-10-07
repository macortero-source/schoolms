<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\User;
use App\Models\Student;
use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of classes.
     */
    public function index(Request $request)
    {
        $query = ClassRoom::with(['classTeacher', 'students']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('grade_level', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%");
        }

        // Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $classes = $query->latest()->paginate(15);

        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new class.
     */
    public function create()
    {
        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('classes.create', compact('teachers'));
    }

    /**
     * Store a newly created class in storage.
     */
    public function store(StoreClassRequest $request)
    {
        try {
            ClassRoom::create($request->validated());

            return redirect()->route('classes.index')
                ->with('success', 'Class created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create class: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified class.
     */
    public function show(ClassRoom $class)
    {
        $class->load(['classTeacher', 'students.user', 'teachers', 'exams']);

        // Statistics
        $stats = [
            'total_students' => $class->total_students,
            'available_seats' => $class->available_seats,
            'average_attendance' => $class->getAverageAttendancePercentage(),
            'average_gpa' => $class->getAverageGPA(),
        ];

        // Students with low attendance
        $lowAttendanceStudents = $class->getStudentsWithLowAttendance();

        // Top performers
        $topPerformers = $class->getTopPerformers(5);

        // Subjects taught in this class
        $subjects = $class->getSubjects();

        return view('classes.show', compact(
            'class',
            'stats',
            'lowAttendanceStudents',
            'topPerformers',
            'subjects'
        ));
    }

    /**
     * Show the form for editing the specified class.
     */
    public function edit(ClassRoom $class)
    {
        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('classes.edit', compact('class', 'teachers'));
    }

    /**
     * Update the specified class in storage.
     */
    public function update(UpdateClassRequest $request, ClassRoom $class)
    {
        try {
            $class->update($request->validated());

            return redirect()->route('classes.show', $class)
                ->with('success', 'Class updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update class: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified class from storage.
     */
    public function destroy(ClassRoom $class)
    {
        try {
            // Check if class has students
            if ($class->students()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete class with enrolled students. Please reassign students first.');
            }

            $class->delete();

            return redirect()->route('classes.index')
                ->with('success', 'Class deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete class: ' . $e->getMessage());
        }
    }

    /**
     * Show students in a class
     */
    public function students(ClassRoom $class)
    {
        $students = $class->students()
            ->with('user')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('classes.students', compact('class', 'students'));
    }

    /**
     * Toggle class active status
     */
    public function toggleStatus(ClassRoom $class)
    {
        $class->update(['is_active' => !$class->is_active]);

        $status = $class->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Class {$status} successfully!");
    }
}