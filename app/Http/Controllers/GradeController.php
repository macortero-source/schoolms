<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Exam;
use App\Models\Student;
use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    /**
     * Show grade entry form for an exam
     */
    public function create(Exam $exam)
    {
        $exam->load(['subject', 'class']);
        
        // Get students in the class
        $students = Student::where('class_id', $exam->class_id)
            ->where('is_active', true)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        // Get existing grades
        $grades = $exam->grades()->get()->keyBy('student_id');

        return view('grades.create', compact('exam', 'students', 'grades'));
    }

    /**
     * Store multiple grades
     */
    public function storeBulk(Request $request, Exam $exam)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.marks_obtained' => 'required|numeric|min:0|max:' . $exam->total_marks,
            'grades.*.remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->grades as $gradeData) {
                Grade::updateOrCreate(
                    [
                        'student_id' => $gradeData['student_id'],
                        'exam_id' => $exam->id,
                    ],
                    [
                        'marks_obtained' => $gradeData['marks_obtained'],
                        'remarks' => $gradeData['remarks'] ?? null,
                        'entered_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('exams.show', $exam)
                ->with('success', 'Grades entered successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to enter grades: ' . $e->getMessage());
        }
    }

    /**
     * Show grade edit form
     */
    public function edit(Grade $grade)
    {
        $grade->load(['student.user', 'exam.subject']);

        return view('grades.edit', compact('grade'));
    }

    /**
     * Update a grade
     */
    public function update(UpdateGradeRequest $request, Grade $grade)
    {
        try {
            $grade->update([
                'marks_obtained' => $request->marks_obtained,
                'remarks' => $request->remarks,
                'entered_by' => auth()->id(),
            ]);

            return redirect()->route('exams.show', $grade->exam_id)
                ->with('success', 'Grade updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update grade: ' . $e->getMessage());
        }
    }

    /**
     * Delete a grade
     */
    public function destroy(Grade $grade)
    {
        try {
            $examId = $grade->exam_id;
            $grade->delete();

            return redirect()->route('exams.show', $examId)
                ->with('success', 'Grade deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete grade: ' . $e->getMessage());
        }
    }

    /**
     * Show student's grade report
     */
    public function studentReport(Request $request)
{
    $user = auth()->user();

    if (!$user->student) {
        return redirect()->route('dashboard')
            ->with('error', 'Student profile not found. Please contact administrator.');
    }

    $student = $user->student;
    $academicYear = $request->academic_year ?? date('Y') . '-' . (date('Y') + 1);

    $grades = $student->grades()
        ->with(['exam.subject', 'exam.class'])
        ->whereHas('exam', function($query) use ($academicYear) {
            $query->where('academic_year', $academicYear);
        })
        ->get()
        ->groupBy('exam.subject.name');

    $stats = [
        'total_exams' => $student->getTotalExamsCount(),
        'current_gpa' => $student->calculateGPA($academicYear),
        'average_marks' => $student->getAverageMarks(),
        'failed_exams' => $student->getFailedExamsCount(),
        'total_subjects' => $grades->count(),
        'attendance_percentage' => $student->getCurrentMonthAttendance(),
        'class_rank' => $student->getClassRank(),
        'total_students' => $student->class ? $student->class->total_students : 0,
    ];

    return view('grades.student-report', compact('student', 'grades', 'stats', 'academicYear'));
}

}