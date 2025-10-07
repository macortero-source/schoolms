<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects.
     */
    public function index(Request $request)
    {
        $query = Subject::with('teachers');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $subjects = $query->latest()->paginate(15);

        return view('subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        return view('subjects.create');
    }

    /**
     * Store a newly created subject in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
        try {
            Subject::create($request->validated());

            return redirect()->route('subjects.index')
                ->with('success', 'Subject created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create subject: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $subject->load('teachers', 'exams');

        $academicYear = date('Y') . '-' . (date('Y') + 1);

        // Statistics
        $stats = [
            'total_teachers' => $subject->teachers->unique('id')->count(),
            'total_classes' => $subject->getClasses($academicYear)->count(),
            'total_students' => $subject->getTotalStudentsCount($academicYear),
            'average_marks' => $subject->getAverageMarks($academicYear),
            'pass_percentage' => $subject->getPassPercentage($academicYear),
        ];

        // Teachers teaching this subject
        $teachers = $subject->getTeachingTeachers($academicYear);

        // Classes where this subject is taught
        $classes = $subject->getClasses($academicYear);

        return view('subjects.show', compact('subject', 'stats', 'teachers', 'classes'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        try {
            $subject->update($request->validated());

            return redirect()->route('subjects.show', $subject)
                ->with('success', 'Subject updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update subject: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Subject $subject)
    {
        try {
            // Check if subject has exams
            if ($subject->exams()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete subject with existing exams.');
            }

            $subject->delete();

            return redirect()->route('subjects.index')
                ->with('success', 'Subject deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }
    }

    /**
     * Toggle subject active status
     */
    public function toggleStatus(Subject $subject)
    {
        $subject->update(['is_active' => !$subject->is_active]);

        $status = $subject->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Subject {$status} successfully!");
    }
}