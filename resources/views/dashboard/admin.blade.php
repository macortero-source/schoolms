@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-4">
    <h1 class="page-title">Admin Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-user-graduate fa-2x mb-3"></i>
            <h3>{{ $stats['total_students'] }}</h3>
            <p>Total Students</p>
            <a href="{{ route('students.index') }}" class="btn btn-light btn-sm mt-2">View All</a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-chalkboard-teacher fa-2x mb-3"></i>
            <h3>{{ $stats['total_teachers'] }}</h3>
            <p>Total Teachers</p>
            <a href="{{ route('teachers.index') }}" class="btn btn-light btn-sm mt-2">View All</a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-school fa-2x mb-3"></i>
            <h3>{{ $stats['total_classes'] }}</h3>
            <p>Total Classes</p>
            <a href="{{ route('classes.index') }}" class="btn btn-light btn-sm mt-2">View All</a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <i class="fas fa-book fa-2x mb-3"></i>
            <h3>{{ $stats['total_subjects'] }}</h3>
            <p>Total Subjects</p>
            <a href="{{ route('subjects.index') }}" class="btn btn-light btn-sm mt-2">View All</a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Attendance -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-check me-2"></i>
                Today's Attendance Summary
            </div>
            <div class="card-body">
                @if(count($todayAttendance) > 0)
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-success">{{ $todayAttendance['present'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Present</p>
                        </div>
                        <div class="col-4">
                            <h4 class="text-danger">{{ $todayAttendance['absent'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Absent</p>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning">{{ $todayAttendance['late'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Late</p>
                        </div>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">No attendance marked today</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Exams -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-alt me-2"></i>Upcoming Exams</span>
                <a href="{{ route('exams.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($upcomingExams as $exam)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $exam->name }}</h6>
                            <small class="text-muted">
                                {{ $exam->subject->name }} - {{ $exam->class->full_name }}
                            </small>
                        </div>
                        <span class="badge bg-info">{{ $exam->exam_date->format('M d') }}</span>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No upcoming exams</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Low Attendance Students -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Students with Low Attendance
            </div>
            <div class="card-body">
                @forelse($lowAttendanceStudents as $student)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">{{ $student->user->name }}</h6>
                            <small class="text-muted">{{ $student->class->full_name ?? 'N/A' }}</small>
                        </div>
                        <span class="badge bg-danger">{{ number_format($student->getCurrentMonthAttendance(), 1) }}%</span>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No students with low attendance</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Announcements -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bullhorn me-2"></i>Recent Announcements</span>
                <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($announcements as $announcement)
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="mb-1">{{ $announcement->title }}</h6>
                        <p class="text-muted small mb-1">{{ Str::limit($announcement->content, 80) }}</p>
                        <small class="text-muted">
                            <i class="far fa-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                        </small>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No recent announcements</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Students -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-plus me-2"></i>Recently Registered Students</span>
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStudents as $student)
                                <tr>
                                    <td><strong>{{ $student->student_number }}</strong></td>
                                    <td>{{ $student->user->name }}</td>
                                    <td>{{ $student->class->full_name ?? 'N/A' }}</td>
                                    <td>{{ $student->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $student->is_active ? 'success' : 'danger' }}">
                                            {{ $student->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No recent registrations</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection