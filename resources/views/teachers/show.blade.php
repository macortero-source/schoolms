@extends('layouts.app')

@section('title', 'Teacher Details')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Teacher Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
                    <li class="breadcrumb-item active">{{ $teacher->user->name }}</li>
                </ol>
            </nav>
        </div>
        @if(auth()->user()->isAdmin())
            <div>
                <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Profile Card -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ $teacher->user->profile_photo ? asset('storage/' . $teacher->user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($teacher->user->name) . '&size=200' }}" 
                     alt="{{ $teacher->user->name }}"
                     class="rounded-circle mb-3"
                     style="width: 150px; height: 150px; object-fit: cover;">
                
                <h4 class="mb-2">{{ $teacher->user->name }}</h4>
                <p class="text-muted mb-3">{{ $teacher->employee_number }}</p>
                
                <span class="badge bg-{{ $teacher->is_active ? 'success' : 'danger' }} mb-3">
                    {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                </span>

                <hr>

                <div class="text-start">
                    <p class="mb-2"><strong>Email:</strong> {{ $teacher->user->email }}</p>
                    <p class="mb-2"><strong>Phone:</strong> {{ $teacher->user->phone ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Gender:</strong> {{ ucfirst($teacher->gender) }}</p>
                    <p class="mb-2"><strong>Date of Birth:</strong> {{ $teacher->date_of_birth->format('M d, Y') }}</p>
                    <p class="mb-2"><strong>Age:</strong> {{ $teacher->age }} years</p>
                    <p class="mb-2"><strong>Blood Group:</strong> {{ $teacher->blood_group ?? 'N/A' }}</p>
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
                    <small class="text-muted">Years of Service</small>
                    <h4 class="mb-0">{{ $stats['years_of_service'] }} years</h4>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Total Students</small>
                    <h4 class="mb-0">{{ $stats['total_students'] }}</h4>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Subjects Teaching</small>
                    <h4 class="mb-0">{{ $stats['total_subjects'] }}</h4>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Classes Teaching</small>
                    <h4 class="mb-0">{{ $stats['total_classes'] }}</h4>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Exams Created</small>
                    <h4 class="mb-0">{{ $stats['total_exams_created'] }}</h4>
                </div>

                <div>
                    <small class="text-muted">Grades Entered</small>
                    <h4 class="mb-0">{{ $stats['total_grades_entered'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Professional Information -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-briefcase me-2"></i>Professional Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Employee Number:</strong>
                        <p class="mb-0">{{ $teacher->employee_number }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Qualification:</strong>
                        <p class="mb-0">{{ $teacher->qualification }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Specialization:</strong>
                        <p class="mb-0">{{ $teacher->specialization ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Employment Type:</strong>
                        <p class="mb-0">{{ ucfirst($teacher->employment_type) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Joining Date:</strong>
                        <p class="mb-0">{{ $teacher->joining_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Salary:</strong>
                        <p class="mb-0">{{ $teacher->salary ? '₦' . number_format($teacher->salary, 2) : 'N/A' }}</p>
                    </div>
                    @if($teacher->experience)
                    <div class="col-md-12 mb-3">
                        <strong>Experience:</strong>
                        <p class="mb-0">{{ $teacher->experience }}</p>
                    </div>
                    @endif
                    @if($teacher->certifications)
                    <div class="col-md-12 mb-3">
                        <strong>Certifications:</strong>
                        <p class="mb-0">{{ $teacher->certifications }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Teaching Assignments -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-chalkboard me-2"></i>Current Teaching Assignments
            </div>
            <div class="card-body">
                @if($assignments->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Academic Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                    <tr>
                                        <td>{{ $assignment['subject']->name }}</td>
                                        <td>{{ $assignment['class']->full_name }}</td>
                                        <td>{{ $assignment['subject']->pivot->academic_year }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">No current assignments</p>
                @endif
            </div>
        </div>

        <!-- Emergency Contact -->
        @if($teacher->emergency_contact_name)
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-phone-alt me-2"></i>Emergency Contact
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Name:</strong>
                        <p class="mb-0">{{ $teacher->emergency_contact_name }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Phone:</strong>
                        <p class="mb-0">{{ $teacher->emergency_contact_phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Relation:</strong>
                        <p class="mb-0">{{ $teacher->emergency_contact_relation ?? 'N/A' }}</p>
                    </div>
                </div></div>
        </div>
        @endif
    </div>
</div>
@endsection