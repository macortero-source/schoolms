@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="mb-4">
    <h1 class="page-title">Teacher Dashboard</h1>
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
            <i class="fas fa-school fa-2x mb-3"></i>
            <h3>{{ $stats['total_classes'] }}</h3>
            <p>My Classes</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-user-graduate fa-2x mb-3"></i>
            <h3>{{ $stats['total_students'] }}</h3>
            <p>Total Students</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-book fa-2x mb-3"></i>
            <h3>{{ $stats['total_subjects'] }}</h3>
            <p>My Subjects</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <i class="fas fa-tasks fa-2x mb-3"></i>
            <h3>{{ $stats['pending_grades'] }}</h3>
            <p>Pending Grades</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- My Classes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-school me-2"></i>
                My Classes
            </div>
            <div class="card-body">
                @forelse($classes as $class)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $class->full_name }}</h6>
                            <small class="text-muted">{{ $class->students->count() }} Students</small>
                        </div>
                        <a href="{{ route('classes.show', $class) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No classes assigned</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- My Subjects -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-book me-2"></i>
                My Subjects
            </div>
            <div class="card-body">
                @forelse($subjects as $subject)
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="mb-1">{{ $subject->name }}</h6>
                        <small class="text-muted">{{ $subject->code }} - {{ ucfirst($subject->type) }}</small>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No subjects assigned</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Upcoming Exams -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-alt me-2"></i>My Upcoming Exams</span>
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
                        <div class="text-end">
                            <span class="badge bg-info d-block mb-1">{{ $exam->exam_date->format('M d') }}</span>
                            <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No upcoming exams</p>
                @endforelse</div>
        </div>
    </div>
<!-- Recent Announcements -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-bullhorn me-2"></i>Announcements</span>
            <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-primary">View All</a>
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
<!-- Class Teacher Assignments -->
@if($assignedClasses->isNotEmpty())
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-tie me-2"></i>
                Classes Where I'm Class Teacher
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Total Students</th>
                                <th>Room</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignedClasses as $class)
                                <tr>
                                    <td><strong>{{ $class->full_name }}</strong></td>
                                    <td>{{ $class->total_students }} / {{ $class->capacity }}</td>
                                    <td>{{ $class->room_number }}</td>
                                    <td>
                                        <a href="{{ route('classes.show', $class) }}" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
