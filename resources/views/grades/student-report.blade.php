@extends('layouts.app')

@section('title', 'Student Grade Report')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Grade Report</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Grades</li>
                </ol>
            </nav>
        </div>
        <div>
            <form action="{{ route('reports.student-report-card') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <input type="hidden" name="academic_year" value="{{ $academicYear }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Download Report Card (PDF)
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Student Profile Summary -->
<!-- Student Profile Summary -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <x-user-avatar :user="optional($student)->user" :size="100" class="rounded-circle" />
            </div>
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Student Name:</strong>
                        <p class="mb-0">{{ optional(optional($student)->user)->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Student Number:</strong>
                        <p class="mb-0">{{ optional($student)->student_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Class:</strong>
                        <p class="mb-0">{{ optional(optional($student)->class)->full_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Academic Year:</strong>
                        <p class="mb-0">{{ $academicYear }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary mb-2">{{ $stats['total_subjects'] }}</h3>
                <p class="text-muted mb-0">Total Subjects</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
               <h3 class="text-success mb-2">{{ number_format($stats['gpa'] ?? $stats['current_gpa'] ?? 0, 2) }}</h3>
                <p class="text-muted mb-0">Current GPA</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info mb-2">{{ $stats['class_rank'] }}</h3>
                <p class="text-muted mb-0">Class Rank</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning mb-2">{{ number_format($stats['attendance_percentage'], 1) }}%</h3>
                <p class="text-muted mb-0">Attendance</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Options -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('grades.studentReport', $student) }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="academic_year" class="form-label">Academic Year</label>
                    <select name="academic_year" id="academic_year" class="form-select">
                        @for($year = date('Y') - 2; $year <= date('Y') + 1; $year++)
                            @php
                                $yearRange = $year . '-' . ($year + 1);
                            @endphp
                            <option value="{{ $yearRange }}" {{ $academicYear == $yearRange ? 'selected' : '' }}>
                                {{ $yearRange }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Grades by Subject -->
@if($grades->isNotEmpty())
    @foreach($grades as $subjectName => $subjectGrades)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-book me-2"></i>
                    <strong>{{ $subjectName }}</strong>
                </div>
                <div>
                    @php
                        $avgMarks = $subjectGrades->avg('marks_obtained');
                        $avgPercentage = $subjectGrades->avg('percentage');
                    @endphp
                    <span class="badge bg-info">Average: {{ number_format($avgMarks, 1) }} ({{ number_format($avgPercentage, 1) }}%)</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th class="text-center">Total Marks</th>
                                <th class="text-center">Marks Obtained</th>
                                <th class="text-center">Percentage</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjectGrades as $grade)
                                <tr>
                                    <td>{{ $grade->exam->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $grade->exam->exam_type_badge_class }}">
                                            {{ ucfirst($grade->exam->exam_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $grade->exam->exam_date->format('M d, Y') }}</td>
                                    <td class="text-center">{{ $grade->exam->total_marks }}</td>
                                    <td class="text-center"><strong>{{ $grade->marks_obtained }}</strong></td>
                                    <td class="text-center">{{ number_format($grade->percentage, 1) }}%</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ getGradeBadge($grade->grade) }}">
                                            {{ $grade->grade }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ getStatusBadge($grade->status) }}">
                                            {{ ucfirst($grade->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end"><strong>Subject Average:</strong></td>
                                <td class="text-center"><strong>{{ number_format($avgMarks, 1) }}</strong></td>
                                <td class="text-center"><strong>{{ number_format($avgPercentage, 1) }}%</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($grade->remarks)
                    <div class="mt-3">
                        <strong>Remarks:</strong> {{ $grade->remarks }}
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
            <h5>No Grades Available</h5>
            <p class="text-muted">No grades have been entered for the selected academic year.</p>
        </div>
    </div>
@endif

<!-- Overall Performance Summary -->
@if($grades->isNotEmpty())
<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-line me-2"></i>Overall Performance Summary
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center mb-3">
                <h4 class="text-primary">{{ $stats['total_exams'] }}</h4>
                <p class="text-muted mb-0">Total Exams</p>
            </div>
            <div class="col-md-3 text-center mb-3">
                <h4 class="text-success">{{ number_format($stats['current_gpa'], 2) }}</h4>
                <p class="text-muted mb-0">GPA (4.0 Scale)</p>
            </div>
            <div class="col-md-3 text-center mb-3">
                <h4 class="text-info">{{ number_format($stats['average_marks'], 1) }}</h4>
                <p class="text-muted mb-0">Average Marks</p>
            </div>
            <div class="col-md-3 text-center mb-3">
                <h4 class="text-{{ $stats['failed_exams'] > 0 ? 'danger' : 'success' }}">
                    {{ $stats['failed_exams'] }}
                </h4>
                <p class="text-muted mb-0">Failed Exams</p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-4">
            <label class="form-label">Overall Performance</label>
            <div class="progress" style="height: 30px;">
                @php
                    $overallPercentage = ($stats['average_marks'] / 100) * 100;
                    $progressColor = $overallPercentage >= 75 ? 'success' : ($overallPercentage >= 60 ? 'warning' : 'danger');
                @endphp
                <div class="progress-bar bg-{{ $progressColor }}" 
                     role="progressbar" 
                     style="width: {{ min($overallPercentage, 100) }}%"
                     aria-valuenow="{{ $overallPercentage }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <strong>{{ number_format($overallPercentage, 1) }}%</strong>
                </div>
            </div>
        </div>

        <!-- Performance Indicator -->
        <div class="mt-4 text-center">
            @php
                $gpa = $stats['current_gpa'];
            @endphp
            @if($gpa >= 3.5)
                <div class="alert alert-success">
                    <i class="fas fa-trophy fa-2x mb-2"></i>
                    <h5 class="mb-0">Excellent Performance! Keep up the great work!</h5>
                </div>
            @elseif($gpa >= 3.0)
                <div class="alert alert-info">
                    <i class="fas fa-thumbs-up fa-2x mb-2"></i>
                    <h5 class="mb-0">Good Performance! You're doing well.</h5>
                </div>
            @elseif($gpa >= 2.5)
                <div class="alert alert-warning">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <h5 class="mb-0">Average Performance. There's room for improvement.</h5>
                </div>
            @else
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h5 class="mb-0">Needs Improvement. Please consult your teachers.</h5>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

@endsection  <!-- closes @section('content') -->

@push('styles')
<style>
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 2px solid #e9ecef;
    }

    .progress {
        border-radius: 10px;
    }

    .progress-bar {
        font-size: 1rem;
        line-height: 30px;
    }
</style>
@endpush
