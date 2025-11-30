<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show report selection page
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Generate student report card
     */
    public function studentReportCard(Request $request)
{
    // Validate input
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'academic_year' => 'required|string',
        'semester' => 'nullable|string',
    ]);

    // Find student
    $student = Student::with(['user', 'class'])->findOrFail($request->student_id);
    $academicYear = $request->academic_year;
    $semester = $request->semester;

    // Split academic year into start and end years
    if (!str_contains($academicYear, '-')) {
        return back()->withErrors(['academic_year' => 'Invalid academic year format.']);
    }

    list($startYear, $endYear) = explode('-', $academicYear);

    $startDate = Carbon::createFromDate($startYear, 1, 1);
    $endDate = Carbon::createFromDate($endYear, 12, 31);

    // Get grades for student in the academic year (and semester if provided)
    $grades = $student->grades()
        ->with(['exam.subject'])
        ->whereHas('exam', function ($query) use ($academicYear, $semester) {
            $query->where('academic_year', $academicYear);
            if ($semester) {
                $query->where('semester', $semester);
            }
        })
        ->get()
        ->groupBy('exam.subject.name');

    // Calculate statistics
    $stats = [
        'total_subjects' => $grades->count(),
        'gpa' => $student->calculateGPA($academicYear, $semester),
        'attendance_percentage' => $student->getAttendancePercentage($startDate, $endDate),
        'class_rank' => $student->getClassRank(),
        'total_students' => $student->class->total_students ?? 0,
    ];

    // Generate PDF
    $pdf = PDF::loadView('reports.student-report-card', compact(
        'student',
        'grades',
        'stats',
        'academicYear',
        'semester'
    ));

    $filename = 'report_card_' . $student->student_number . '_' . str_replace('-', '_', $academicYear) . '.pdf';

    return $pdf->download($filename);
}


    /**
     * Generate class performance report
     */
    public function classPerformance(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year' => 'required|string',
        ]);

        $class = ClassRoom::with(['students.user', 'classTeacher'])->findOrFail($request->class_id);
        $academicYear = $request->academic_year;

        // Get all students with their GPAs
        $students = $class->students()
            ->where('is_active', true)
            ->get()
            ->map(function($student) use ($academicYear) {
                return [
                    'student' => $student,
                    'gpa' => $student->calculateGPA($academicYear),
                    'attendance' => $student->getAttendancePercentage(),
                    'total_exams' => $student->getTotalExamsCount(),
                ];
            })
            ->sortByDesc('gpa');

        // Class statistics
        $stats = [
            'total_students' => $class->total_students,
            'average_gpa' => $class->getAverageGPA($academicYear),
            'average_attendance' => $class->getAverageAttendancePercentage(),
            'subjects' => $class->getSubjects()->count(),
        ];

        // Generate PDF
        $pdf = PDF::loadView('reports.class-performance', compact(
            'class',
            'students',
            'stats',
            'academicYear'
        ));

        $filename = 'class_performance_' . str_replace(' ', '_', $class->name) . '_' . $academicYear . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate attendance summary report
     */
    public function attendanceSummary(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $class = $request->class_id ? ClassRoom::findOrFail($request->class_id) : null;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($class) {
            // Class-specific report
            $students = $class->students()
                ->where('is_active', true)
                ->get()
                ->map(function($student) use ($startDate, $endDate) {
                    return [
                        'student' => $student,
                        'present' => $student->getTotalPresentDays($startDate, $endDate),
                        'absent' => $student->getTotalAbsentDays($startDate, $endDate),
                        'percentage' => $student->getAttendancePercentage($startDate, $endDate),
                    ];
                })
                ->sortByDesc('percentage');

            $title = 'Attendance Report - ' . $class->full_name;
        } else {
            // School-wide report
            $students = Student::where('is_active', true)
                ->with(['user', 'class'])
                ->get()
                ->map(function($student) use ($startDate, $endDate) {
                    return [
                        'student' => $student,
                        'present' => $student->getTotalPresentDays($startDate, $endDate),
                        'absent' => $student->getTotalAbsentDays($startDate, $endDate),
                        'percentage' => $student->getAttendancePercentage($startDate, $endDate),
                    ];
                })
                ->sortByDesc('percentage');

            $title = 'School-Wide Attendance Report';
        }

        // Generate PDF
        $pdf = PDF::loadView('reports.attendance-summary', compact(
            'students',
            'class',
            'startDate',
            'endDate',
            'title'
        ));

        $filename = 'attendance_report_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate exam analysis report
     */
    public function examAnalysis(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $exam = Exam::with(['subject', 'class', 'grades.student.user'])->findOrFail($request->exam_id);

        // Grade distribution
        $gradeDistribution = $exam->getGradeDistribution();

        // Statistics
        $stats = [
            'total_students' => $exam->getTotalStudentsCount(),
            'graded_students' => $exam->getGradedStudentsCount(),
            'average_marks' => $exam->getAverageMarks(),
            'highest_marks' => $exam->getHighestMarks(),
            'lowest_marks' => $exam->getLowestMarks(),
            'pass_count' => $exam->getPassCount(),
            'fail_count' => $exam->getFailCount(),
            'pass_percentage' => $exam->getPassPercentage(),
        ];

        // Top performers
        $topPerformers = $exam->getTopPerformers(10);

        // All grades sorted
        $allGrades = $exam->grades()
            ->with('student.user')
            ->orderBy('marks_obtained', 'desc')
            ->get();

        // Generate PDF
        $pdf = PDF::loadView('reports.exam-analysis', compact(
            'exam',
            'stats',
            'gradeDistribution',
            'topPerformers',
            'allGrades'
        ));

        $filename = 'exam_analysis_' . str_replace(' ', '_', $exam->name) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate subject performance report
     */
    public function subjectPerformance(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'academic_year' => 'required|string',
        ]);

        $subject = Subject::findOrFail($request->subject_id);
        $academicYear = $request->academic_year;

        // Get all exams for this subject in the academic year
        $exams = Exam::where('subject_id', $subject->id)
            ->where('academic_year', $academicYear)
            ->with(['class', 'grades'])
            ->get();

        // Statistics
        $stats = [
            'total_exams' => $exams->count(),
            'total_students' => $subject->getTotalStudentsCount($academicYear),
            'average_marks' => $subject->getAverageMarks($academicYear),
            'pass_percentage' => $subject->getPassPercentage($academicYear),
            'total_classes' => $subject->getClasses($academicYear)->count(),
        ];

        // Exam-wise performance
        $examPerformance = $exams->map(function($exam) {
            return [
                'exam' => $exam,
                'average' => $exam->getAverageMarks(),
                'pass_percentage' => $exam->getPassPercentage(),
                'highest' => $exam->getHighestMarks(),
                'lowest' => $exam->getLowestMarks(),
            ];
        });

        // Generate PDF
        $pdf = PDF::loadView('reports.subject-performance', compact(
            'subject',
            'stats',
            'examPerformance',
            'academicYear'
        ));

        $filename = 'subject_performance_' . str_replace(' ', '_', $subject->name) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show report generation forms
     */
    public function showForm(Request $request)
    {
        $type = $request->type;

        $students = Student::with('user')->where('is_active', true)->orderBy('created_at')->get();
        $classes = ClassRoom::active()->orderBy('name')->get();
        $subjects = Subject::active()->orderBy('name')->get();
        $exams = Exam::with(['subject', 'class'])->latest('exam_date')->get();

        $currentYear = date('Y');
        $academicYears = [
            ($currentYear - 2) . '-' . ($currentYear - 1),
            ($currentYear - 1) . '-' . $currentYear,
            $currentYear . '-' . ($currentYear + 1),
        ];

        return view('reports.form', compact(
            'type',
            'students',
            'classes',
            'subjects',
            'exams',
            'academicYears'
        ));
    }
}