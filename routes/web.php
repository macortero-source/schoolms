<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware(['role:admin'])->group(function () {
    
    // Student Management (Admin only for create/edit/delete)
    Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('students', [StudentController::class, 'store'])->name('students.store');
    Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])
        ->name('students.toggle-status');
    Route::get('students-export', [StudentController::class, 'export'])
        ->name('students.export');

    // Teacher Management
    Route::resource('teachers', TeacherController::class);
    Route::post('teachers/{teacher}/assign-subjects', [TeacherController::class, 'assignSubjects'])
        ->name('teachers.assign-subjects');

    // Class Management
    Route::resource('classes', ClassController::class);
    Route::get('classes/{class}/students', [ClassController::class, 'students'])
        ->name('classes.students');
    Route::post('classes/{class}/toggle-status', [ClassController::class, 'toggleStatus'])
        ->name('classes.toggle-status');

    // Subject Management
    Route::resource('subjects', SubjectController::class);
    Route::post('subjects/{subject}/toggle-status', [SubjectController::class, 'toggleStatus'])
        ->name('subjects.toggle-status');
});

    // Admin & Teacher Routes
    Route::middleware(['role:admin,teacher'])->group(function () {
    
    // Students (View only for teachers)
    Route::get('students', [StudentController::class, 'index'])->name('students.index');
    Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
    
    // Attendance Management
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');

    // Exam Management
    Route::resource('exams', ExamController::class);
    Route::post('exams/{exam}/publish', [ExamController::class, 'publish'])->name('exams.publish');
    Route::post('exams/{exam}/unpublish', [ExamController::class, 'unpublish'])->name('exams.unpublish');

    // Grade Management
    Route::get('exams/{exam}/grades/create', [GradeController::class, 'create'])->name('grades.create');
    Route::post('exams/{exam}/grades', [GradeController::class, 'storeBulk'])->name('grades.store-bulk');
    Route::get('grades/{grade}/edit', [GradeController::class, 'edit'])->name('grades.edit');
    Route::put('grades/{grade}', [GradeController::class, 'update'])->name('grades.update');
    Route::delete('grades/{grade}', [GradeController::class, 'destroy'])->name('grades.destroy');

    // Announcement Management
    Route::resource('announcements', AnnouncementController::class);
    Route::post('announcements/{announcement}/toggle-status', [AnnouncementController::class, 'toggleStatus'])
        ->name('announcements.toggle-status');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/form', [ReportController::class, 'showForm'])->name('reports.form');
    Route::post('reports/student-report-card', [ReportController::class, 'studentReportCard'])
        ->name('reports.student-report-card');
    Route::post('reports/class-performance', [ReportController::class, 'classPerformance'])
        ->name('reports.class-performance');
    Route::post('reports/attendance-summary', [ReportController::class, 'attendanceSummary'])
        ->name('reports.attendance-summary');
    Route::post('reports/exam-analysis', [ReportController::class, 'examAnalysis'])
        ->name('reports.exam-analysis');
    Route::post('reports/subject-performance', [ReportController::class, 'subjectPerformance'])
        ->name('reports.subject-performance');
});

    // Student Routes
Route::middleware([ 'role:student'])->group(function () {
    // View own grades
    Route::get('my-grades', [GradeController::class, 'studentReport'])
        ->name('grades.studentReport');

    // View own attendance
    Route::get('my-attendance', [AttendanceController::class, 'student'])
        ->name('student.attendance');
});


    // All Authenticated Users
    Route::get('announcements-public', [AnnouncementController::class, 'public'])->name('announcements.public');
    Route::get('attendance/student/{student}', [AttendanceController::class, 'student'])
        ->name('attendance.student');

    // Reports (Admin & Teacher)
    Route::middleware(['role:admin,teacher'])->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/form', [ReportController::class, 'showForm'])->name('reports.form');
        Route::post('reports/student-report-card', [ReportController::class, 'studentReportCard'])
            ->name('reports.student-report-card');
        Route::post('reports/class-performance', [ReportController::class, 'classPerformance'])
            ->name('reports.class-performance');
        Route::post('reports/attendance-summary', [ReportController::class, 'attendanceSummary'])
            ->name('reports.attendance-summary');
        Route::post('reports/exam-analysis', [ReportController::class, 'examAnalysis'])
            ->name('reports.exam-analysis');
        Route::post('reports/subject-performance', [ReportController::class, 'subjectPerformance'])
            ->name('reports.subject-performance');
    });
});

// Auth Routes (provided by Breeze)
require __DIR__.'/auth.php';