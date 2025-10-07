@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Student Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">{{ $student->user->name }}</li>
                </ol>
            </nav>
        </div>
        @if(auth()->user()->isAdmin())
            <div>
                <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <form action="{{ route('students.toggle-status', $student) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-{{ $student->is_active ? 'danger' : 'success' }}">
                        <i class="fas fa-{{ $student->is_active ? 'ban' : 'check' }} me-2"></i>
                        {{ $student->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<!-- Profile Card -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ $student->user->profile_photo ? asset('storage/' . $student->user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name) . '&size=200' }}" 
                     alt="{{ $student->user->name }}"
                     class="rounded-circle mb-3"
                     style="width: 150px; height: 150px; object-fit: cover;">
                
                <h4 class="mb-2">{{ $student->user->name }}</h4>
                <p class="text-muted mb-3">{{ $student->student_number }}</p>
                
                <span class="badge bg-{{ $student->is_active ? 'success' : 'danger' }} mb-3">
                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                </span>

                <hr>

                <div class="text-start">
                    <p class="mb-2"><strong>Email:</strong> {{ $student->user->email }}</p>
                    <p class="mb-2"><strong>Phone:</strong> {{ $student->user->phone ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Gender:</strong> {{ ucfirst($student->gender) }}</p>
                    <p class="mb-2"><strong>Date of Birth:</strong> {{ $student->date_of_birth->format('M d, Y') }}</p>
                    <p class="mb-2"><strong>Age:</strong> {{ $student->age }} years</p>
                    <p class="mb-2"><strong>Blood Group:</strong> {{ $student->blood_group ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Statistics
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Attendance Rate</small>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="progress flex-grow-1 me-2" style="height: 20px;">
                            <div class="progress-bar bg-{{ $stats['attendance_percentage'] >= 75 ? 'success' : 'danger' }}" 
                                 style="width: {{ $stats['attendance_percentage'] }}%">
                                {{ number_format($stats['attendance_percentage'], 1) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Current GPA</small>
                    <h4 class="mb-0">{{ number_format($stats['current_gpa'], 2) }}</h4>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Class Rank</small>
                    <h4 class="mb-0">{{ $stats['class_rank'] }}</h4>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Total Exams</small>
                    <h4 class="mb-0">{{ $stats['total_exams'] }}</h4>
                </div>

                <div>
                    <small class="text-muted">Failed Exams</small>
                    <h4 class="mb-0 text-danger">{{ $stats['failed_exams'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Academic Information -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-graduation-cap me-2"></i>Academic Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Admission Number:</strong>
                        <p class="mb-0">{{ $student->admission_number }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Admission Date:</strong>
                        <p class="mb-0">{{ $student->admission_date->format('M d, Y') }}</p>
</div>
<div class="col-md-6 mb-3">
<strong>Academic Year:</strong>
<p class="mb-0">{{ $student->academic_year }}</p>
</div>
<div class="col-md-6 mb-3">
<strong>Current Class:</strong>
<p class="mb-0">
@if($student->class)
<a href="{{ route('classes.show', $student->class) }}">
{{ $student->class->full_name }}
</a>
@else
N/A
@endif
</p>
</div>
<div class="col-md-6 mb-3">
<strong>Previous School:</strong>
<p class="mb-0">{{ $student->previous_school ?? 'N/A' }}</p>
</div>
<div class="col-md-6 mb-3">
<strong>Class Teacher:</strong>
<p class="mb-0">{{ $student->class?->classTeacher?->name ?? 'N/A' }}</p>
</div>
</div>
</div>
</div>
    <!-- Parent/Guardian Information -->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-users me-2"></i>Parent/Guardian Information
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Parent Name:</strong>
                    <p class="mb-0">{{ $student->parent_name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Parent Phone:</strong>
                    <p class="mb-0">{{ $student->parent_phone }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Parent Email:</strong>
                    <p class="mb-0">{{ $student->parent_email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Parent Occupation:</strong>
                    <p class="mb-0">{{ $student->parent_occupation ?? 'N/A' }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>Parent Address:</strong>
                    <p class="mb-0">{{ $student->parent_address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency Contact -->
    @if($student->emergency_contact_name)
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-phone-alt me-2"></i>Emergency Contact
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Name:</strong>
                    <p class="mb-0">{{ $student->emergency_contact_name }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Phone:</strong>
                    <p class="mb-0">{{ $student->emergency_contact_phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Relation:</strong>
                    <p class="mb-0">{{ $student->emergency_contact_relation ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Medical Information -->
    @if($student->medical_conditions || $student->allergies)
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-heartbeat me-2"></i>Medical Information
        </div>
        <div class="card-body">
            <div class="row">
                @if($student->medical_conditions)
                <div class="col-md-6 mb-3">
                    <strong>Medical Conditions:</strong>
                    <p class="mb-0">{{ $student->medical_conditions }}</p>
                </div>
                @endif
                @if($student->allergies)
                <div class="col-md-6 mb-3">
                    <strong>Allergies:</strong>
                    <p class="mb-0">{{ $student->allergies }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Attendance -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-calendar-check me-2"></i>Recent Attendance</span>
            <a href="{{ route('attendance.student', $student) }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Marked By</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendance as $attendance)
                            <tr>
                                <td>{{ $attendance->date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ getStatusBadge($attendance->status) }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>{{ $attendance->markedByTeacher->name }}</td>
                                <td>{{ $attendance->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No attendance records</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Grades -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-star me-2"></i>Recent Grades</span>
            <a href="{{ route('grades.studentReport', $student) }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Exam</th>
                            <th>Marks</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentGrades as $grade)
                            <tr>
                                <td>{{ $grade->exam->subject->name }}</td>
                                <td>{{ $grade->exam->name }}</td>
                                <td>{{ $grade->marks_obtained }}/{{ $grade->exam->total_marks }}</td>
                                <td>
                                    <span class="badge bg-{{ getGradeBadge($grade->grade) }}">
                                        {{ $grade->grade }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ getStatusBadge($grade->status) }}">
                                        {{ ucfirst($grade->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No grades yet</td>
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
