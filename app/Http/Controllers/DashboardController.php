<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Attendance;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user role
     */
    public function index()
    {
        $user = auth()->user();

        return match($user->role) {
            'admin' => $this->adminDashboard(),
            'teacher' => $this->teacherDashboard(),
            'student' => $this->studentDashboard(),
            'parent' => $this->parentDashboard(),
            default => abort(403),
        };
    }

    /**
     * Admin Dashboard
     */
    private function adminDashboard()
    {
        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Teacher::where('is_active', true)->count(),
            'total_classes' => ClassRoom::where('is_active', true)->count(),
            'total_subjects' => Subject::where('is_active', true)->count(),
        ];

        // Today's attendance summary
        $todayAttendance = Attendance::whereDate('date', Carbon::today())
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent announcements
        $announcements = Announcement::published()
            ->latest()
            ->take(5)
            ->get();

        // Upcoming exams
        $upcomingExams = Exam::upcoming()
            ->with(['subject', 'class'])
            ->orderBy('exam_date')
            ->take(5)
            ->get();

        // Students with low attendance (optimized in DB)
        $lowAttendanceStudents = Student::where('is_active', true)
            ->whereHas('attendances', function($query) {
                $query->selectRaw('student_id, (SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) / COUNT(*)) as attendance_rate')
                      ->groupBy('student_id')
                      ->havingRaw('attendance_rate < 0.75'); // Threshold
            })
            ->take(10)
            ->get();

        // Recent registrations
        $recentStudents = Student::with(['user', 'class'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'todayAttendance',
            'announcements',
            'upcomingExams',
            'lowAttendanceStudents',
            'recentStudents'
        ));
    }

    /**
     * Teacher Dashboard
     */
    private function teacherDashboard()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }

        $classes = $teacher->getTeachingClasses();
        $subjects = $teacher->getTeachingSubjects();

        $totalStudents = Student::whereIn('class_id', $classes->pluck('id'))
            ->where('is_active', true)
            ->count();

        $upcomingExams = Exam::where('created_by', auth()->id())
            ->upcoming()
            ->with(['subject', 'class'])
            ->orderBy('exam_date')
            ->take(5)
            ->get();

        $todaySchedule = $teacher->getCurrentAssignments();
        $announcements = Announcement::published()
            ->forAudience('teacher')
            ->latest()
            ->take(5)
            ->get();

        // Pending grades (fixed)
        $pendingGradesCount = Exam::where('created_by', auth()->id())
            ->where('exam_date', '<=', Carbon::now())
            ->get()
            ->sum(fn($exam) => $exam->getPendingGradesCount());

        $assignedClasses = $teacher->assignedClasses;

        $stats = [
            'total_classes' => $classes->count(),
            'total_students' => $totalStudents,
            'total_subjects' => $subjects->count(),
            'pending_grades' => $pendingGradesCount,
        ];

        return view('dashboard.teacher', compact(
            'stats',
            'classes',
            'subjects',
            'upcomingExams',
            'todaySchedule',
            'announcements',
            'assignedClasses'
        ));
    }

    /**
     * Student Dashboard
     */
    private function studentDashboard()
    {
        $student = auth()->user()->student;

        if (!$student) {
            abort(403, 'Student profile not found.');
        }

        $class = $student->class;

        $attendancePercentage = $student->getCurrentMonthAttendance();
        $totalPresent = $student->getTotalPresentDays(Carbon::now()->startOfMonth(), Carbon::now());
        $totalAbsent = $student->getTotalAbsentDays(Carbon::now()->startOfMonth(), Carbon::now());
        $currentGPA = $student->getCurrentSemesterGPA();
        $recentGrades = $student->grades()
            ->with(['exam.subject'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingExams = Exam::where('class_id', $student->class_id)
            ->upcoming()
            ->with('subject')
            ->orderBy('exam_date')
            ->take(5)
            ->get();

        $announcements = Announcement::published()
            ->forAudience('student')
            ->latest()
            ->take(5)
            ->get();

        $classRank = $student->getClassRank();

        $stats = [
            'attendance_percentage' => $attendancePercentage,
            'current_gpa' => $currentGPA,
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
            'class_rank' => $classRank,
        ];

        return view('dashboard.student', compact(
            'stats',
            'class',
            'recentGrades',
            'upcomingExams',
            'announcements',
            'student'
        ));
    }

    /**
     * Parent Dashboard
     */
    private function parentDashboard()
    {
        $announcements = Announcement::published()
            ->forAudience('parent')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.parent', compact('announcements'));
    }
}
