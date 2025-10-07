<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of exams.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['subject', 'class', 'creator']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by exam type
        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->upcoming();
            } elseif ($request->status === 'past') {
                $query->past();
            } elseif ($request->status === 'today') {
                $query->today();
            }
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $exams = $query->latest('exam_date')->paginate(15);
        $classes = ClassRoom::active()->orderBy('name')->get();
        $subjects = Subject::active()->orderBy('name')->get();

        return view('exams.index', compact('exams', 'classes', 'subjects'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $classes = ClassRoom::active()->orderBy('name')->get();
        $academicYear = date('Y') . '-' . (date('Y') + 1);

        return view('exams.create', compact('subjects', 'classes', 'academicYear'));
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(StoreExamRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            $data['is_published'] = false;

            Exam::create($data);

            return redirect()->route('exams.index')
                ->with('success', 'Exam created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create exam: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        $exam->load(['subject', 'class', 'creator', 'grades.student.user']);

        // Statistics
        $stats = [
            'total_students' => $exam->getTotalStudentsCount(),
            'graded_students' => $exam->getGradedStudentsCount(),
            'pending_grades' => $exam->getPendingGradesCount(),
            'average_marks' => $exam->getAverageMarks(),
            'highest_marks' => $exam->getHighestMarks(),
            'lowest_marks' => $exam->getLowestMarks(),
            'pass_count' => $exam->getPassCount(),
            'fail_count' => $exam->getFailCount(),
            'pass_percentage' => $exam->getPassPercentage(),
        ];

        // Grade distribution
        $gradeDistribution = $exam->getGradeDistribution();

        // Top performers
        $topPerformers = $exam->getTopPerformers(10);

        return view('exams.show', compact('exam', 'stats', 'gradeDistribution', 'topPerformers'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $classes = ClassRoom::active()->orderBy('name')->get();

        return view('exams.edit', compact('exam', 'subjects', 'classes'));
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        try {
            $exam->update($request->validated());

            return redirect()->route('exams.show', $exam)
                ->with('success', 'Exam updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update exam: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(Exam $exam)
    {
        try {
            // Check if exam has grades
            if ($exam->grades()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete exam with existing grades.');
            }

            $exam->delete();

            return redirect()->route('exams.index')
                ->with('success', 'Exam deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
    }

    /**
     * Publish exam results
     */
    public function publish(Exam $exam)
    {
        if (!$exam->isFullyGraded()) {
            return redirect()->back()
                ->with('error', 'Cannot publish exam results. Not all students have been graded.');
        }

        $exam->update(['is_published' => true]);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exam results published successfully!');
    }

    /**
     * Unpublish exam results
     */
    public function unpublish(Exam $exam)
    {
        $exam->update(['is_published' => false]);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exam results unpublished successfully!');
    }
}