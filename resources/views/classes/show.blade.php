@extends('layouts.app')

@section('title', 'Class Details')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ $class->full_name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Classes</a></li>
                    <li class="breadcrumb-item active">{{ $class->full_name }}</li>
                </ol>
            </nav>
        </div>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('classes.edit', $class) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary">{{ $stats['total_students'] }}</h3>
                <p class="text-muted mb-0">Total Students</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success">{{ $stats['available_seats'] }}</h3>
                <p class="text-muted mb-0">Available Seats</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-info">{{ number_format($stats['average_attendance'], 1) }}%</h3>
                <p class="text-muted mb-0">Avg Attendance</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-warning">{{ number_format($stats['average_gpa'], 2) }}</h3>
                <p class="text-muted mb-0">Average GPA</p>
            </div>
        </div>
    </div>
</div>

<!-- Class Information -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>Class Information
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Class Name:</th>
                        <td>{{ $class->name }}</td>
                    </tr>
                    <tr>
                        <th>Grade Level:</th>
                        <td>{{ $class->grade_level }}</td>
                    </tr>
                    <tr>
                        <th>Section:</th>
                        <td>{{ $class->section ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Room Number:</th>
                        <td>{{ $class->room_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Capacity:</th>
                        <td>{{ $class->capacity }}</td>
                    </tr>
                    <tr>
                        <th>Class Teacher:</th>
                        <td>{{ $class->class_teacher_name }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge bg-{{ $class->is_active ? 'success' : 'secondary' }}">
                                {{ $class->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-book me-2"></i>Subjects Taught
            </div>
            <div class="card-body">
                @if($subjects->isNotEmpty())
                    <ul class="list-unstyled mb-0">
                        @foreach($subjects as $subject)
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>{{ $subject->name }} <small class="text-muted">({{ $subject->code }})</small>
</li>
@endforeach
</ul>
@else
<p class="text-muted mb-0">No subjects assigned yet</p>
@endif
</div>
</div>
</div>
</div>
<!-- Top Performers -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-trophy me-2"></i>Top Performers
            </div>
            <div class="card-body">
                @if($topPerformers->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Student</th>
                                    <th>GPA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers as $index => $student)
                                    <tr>
                                        <td>
                                            @if($index == 0)
                                                <i class="fas fa-medal text-warning"></i>
                                            @elseif($index == 1)
                                                <i class="fas fa-medal text-secondary"></i>
                                            @elseif($index == 2)
                                                <i class="fas fa-medal" style="color: #CD7F32;"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>{{ $student->user->name }}</td>
                                        <td><strong>{{ number_format($student->getCurrentSemesterGPA(), 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">No performance data available</p>
                @endif
            </div>
        </div>
    </div>
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-exclamation-triangle me-2"></i>Low Attendance Alert
        </div>
        <div class="card-body">
            @if($lowAttendanceStudents->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Attendance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowAttendanceStudents as $student)
                                <tr>
                                    <td>{{ $student->user->name }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ number_format($student->getCurrentMonthAttendance(), 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center mb-0">
                    <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                    All students have good attendance!
                </p>
            @endif
        </div>
    </div>
</div>
</div>
<!-- Students List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users me-2"></i>Class Students</span>
        <a href="{{ route('classes.students', $class) }}" class="btn btn-sm btn-primary">View All Students</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student #</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Attendance %</th>
                        <th>GPA</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($class->students()->where('is_active', true)->take(10)->get() as $student)
                        <tr>
                            <td>{{ $student->student_number }}</td>
                            <td>{{ $student->user->name }}</td>
                            <td>{{ $student->user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $student->getCurrentMonthAttendance() >= 75 ? 'success' : 'danger' }}">
                                    {{ number_format($student->getCurrentMonthAttendance(), 1) }}%
                                </span>
                            </td>
                            <td>{{ number_format($student->getCurrentSemesterGPA(), 2) }}</td>
                            <td>
                                <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No students enrolled</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection