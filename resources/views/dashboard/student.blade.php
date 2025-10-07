@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="mb-4">
    <h1 class="page-title">Student Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Profile Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <x-user-avatar 
                            :user="optional($student)->user ?? auth()->user()" 
                            :size="150" 
                            class="rounded-circle mb-3" 
                        />
                    </div>
                    <div class="col-md-10">
                        <h3 class="mb-2">{{ optional($student->user)->name ?? 'N/A' }}</h3>
                        <div class="row">
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Student Number:</strong></p>
                                <p class="text-muted">{{ optional($student)->student_number ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Class:</strong></p>
                                <p class="text-muted">{{ optional($class)->full_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Academic Year:</strong></p>
                                <p class="text-muted">{{ optional($student)->academic_year ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Class Rank:</strong></p>
                                <p class="text-muted">{{ $stats['class_rank'] ?? 0 }} / {{ optional($class)->total_students ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-calendar-check fa-2x mb-3"></i>
            <h3>{{ number_format($stats['attendance_percentage'], 1) }}%</h3>
            <p>Attendance Rate</p>
            <a href="{{ route('student.attendance', $student) }}" class="btn btn-light btn-sm mt-2">View Details</a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-graduation-cap fa-2x mb-3"></i>
            <h3>{{ number_format($stats['current_gpa'], 2) }}</h3>
            <p>Current GPA</p>
            <a href="{{ route('grades.studentReport') }}" class="btn btn-light btn-sm mt-2">View Grades</a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-check-circle fa-2x mb-3"></i>
            <h3>{{ $stats['total_present'] }}</h3>
            <p>Days Present (This Month)</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <i class="fas fa-times-circle fa-2x mb-3"></i>
            <h3>{{ $stats['total_absent'] }}</h3>
            <p>Days Absent (This Month)</p>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Grades -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-star me-2"></i>Recent Grades</span>
                <a href="{{ route('grades.studentReport', $student) }}" class="btn btn-light btn-sm mt-2">View All</a>
            </div>
            <div class="card-body">
                @forelse($recentGrades as $grade)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $grade->exam->subject->name }}</h6>
                            <small class="text-muted">{{ $grade->exam->name }}</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0">
                                <span class="badge bg-{{ getGradeBadge($grade->grade) }}">
                                    {{ $grade->grade }}
                                </span>
                            </h5>
                            <small class="text-muted">{{ $grade->marks_obtained }}/{{ $grade->exam->total_marks }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No grades yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Upcoming Exams -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-alt me-2"></i>
                Upcoming Exams
            </div>
            <div class="card-body">
                @forelse($upcomingExams as $exam)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $exam->subject->name }}</h6>
                            <small class="text-muted">{{ $exam->name }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-info d-block mb-1">
                                {{ $exam->exam_date->format('M d, Y') }}
                            </span>
                            <small class="text-muted">{{ $exam->start_time ? $exam->start_time->format('h:i A') : '' }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No upcoming exams</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Announcements -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bullhorn me-2"></i>Recent Announcements</span>
                <a href="{{ route('announcements.public') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($announcements as $announcement)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">{{ $announcement->title }}</h6>
                            <span class="badge bg-{{ $announcement->priority_badge_class }}">
                                {{ ucfirst($announcement->priority) }}
                            </span>
                        </div>
                        <p class="text-muted mb-2">{{ Str::limit($announcement->content, 150) }}</p>
                        <small class="text-muted">
                            <i class="far fa-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                        </small>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No announcements</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection