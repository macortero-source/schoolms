@extends('layouts.app')

@section('title', 'Student Grade Report')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title fw-bold">Grade Report</h1>
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
<div class="card mb-4">
    <div class="card-body py-4">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <x-user-avatar :user="optional($student)->user" :size="100" class="rounded-circle border border-3 border-primary shadow-sm mb-2" />
                <span class="badge bg-success mt-2">Active</span>
            </div>
            <div class="col-md-10">
                <div class="row g-3">
                    <div class="col-md-3">
                        <small class="text-muted text-uppercase fw-bold">Name</small>
                        <p class="mb-0 fw-semibold">{{ optional($student->user)->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted text-uppercase fw-bold">Student #</small>
                        <span class="badge bg-secondary">{{ $student->student_number ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted text-uppercase fw-bold">Class</small>
                        <span class="badge bg-info text-dark">{{ optional($student->class)->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted text-uppercase fw-bold">Academic Year</small>
                        <span class="badge bg-primary px-3 py-2">{{ $academicYear }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    @php
        $statsCards = [
            ['value' => $stats['total_subjects'], 'label' => 'Total Subjects', 'color' => 'primary', 'icon' => 'fa-book'],
            ['value' => number_format($stats['current_gpa'], 2), 'label' => 'Current GPA', 'color' => 'success', 'icon' => 'fa-graduation-cap'],
            ['value' => $stats['class_rank'], 'label' => 'Class Rank', 'color' => 'info', 'icon' => 'fa-trophy'],
            ['value' => number_format($stats['attendance_percentage'], 1) . '%', 'label' => 'Attendance', 'color' => 'warning', 'icon' => 'fa-calendar-check'],
        ];
    @endphp
    @foreach($statsCards as $card)
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <i class="fas {{ $card['icon'] }} fa-2x mb-3"></i>
                <h3>{{ $card['value'] }}</h3>
                <p>{{ $card['label'] }}</p>
            </div>
        </div>
    @endforeach
</div>

<!-- Grades by Subject -->
@if($grades->isNotEmpty())
    @foreach($grades as $subjectName => $subjectGrades)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-book me-2"></i>{{ $subjectName }}</strong>
                @php
                    $avgMarks = $subjectGrades->avg('marks_obtained');
                    $avgPercentage = $subjectGrades->avg('percentage');
                @endphp
                <span class="badge bg-info">Average: {{ number_format($avgMarks, 1) }} ({{ number_format($avgPercentage, 1) }}%)</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th class="text-center">Total Marks</th>
                                <th class="text-center">Obtained</th>
                                <th class="text-center">%</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjectGrades as $grade)
                                <tr>
                                    <td>{{ $grade->exam->name }}</td>
                                    <td><span class="badge bg-{{ $grade->exam->exam_type_badge_class }}">{{ ucfirst($grade->exam->exam_type) }}</span></td>
                                    <td>{{ $grade->exam->exam_date->format('M d, Y') }}</td>
                                    <td class="text-center">{{ $grade->exam->total_marks }}</td>
                                    <td class="text-center fw-bold">{{ $grade->marks_obtained }}</td>
                                    <td class="text-center">{{ number_format($grade->percentage, 1) }}%</td>
                                    <td class="text-center"><span class="badge bg-{{ getGradeBadge($grade->grade) }}">{{ $grade->grade }}</span></td>
                                    <td class="text-center"><span class="badge bg-{{ getStatusBadge($grade->status) }}">{{ ucfirst($grade->status) }}</span></td>
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
            </div>
        </div>
    @endforeach
@else
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
            <h5>No Grades Available</h5>
            <p class="text-muted">No grades have been entered for the selected academic year.</p>
        </div>
    </div>
@endif

<!-- Overall Performance Summary -->
@if($grades->isNotEmpty())
<div class="card shadow-sm">
    <div class="card-header">
        <i class="fas fa-chart-line me-2"></i>Overall Performance Summary
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3"><h4 class="text-primary">{{ $stats['total_exams'] }}</h4><p class="text-muted">Total Exams</p></div>
            <div class="col-md-3"><h4 class="text-success">{{ number_format($stats['current_gpa'], 2) }}</h4><p class="text-muted">GPA</p></div>
            <div class="col-md-3"><h4 class="text-info">{{ number_format($stats['average_marks'], 1) }}</h4><p class="text-muted">Average Marks</p></div>
            <div class="col-md-3">
                <h4 class="text-{{ $stats['failed_exams'] > 0 ? 'danger' : 'success' }}">{{ $stats['failed_exams'] }}</h4>
                <p class="text-muted mb-0">Failed Exams</p>
            </div>
        </div>

                <!-- Progress Bar -->
        <div class="mt-4">
            <label class="form-label">Overall Performance</label>
            @php
                $overallPercentage = ($stats['average_marks'] / 100) * 100;
                $progressColor = $overallPercentage >= 75 ? 'success' : ($overallPercentage >= 60 ? 'warning' : 'danger');
            @endphp
            <div class="progress" style="height: 30px;">
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
            @php $gpa = $stats['current_gpa']; @endphp
            @if($gpa >= 3.5)
                <div class="alert alert-success">
                    <i class="fas fa-trophy fa-2x mb-2"></i>
                    <h5>Excellent Performance! Keep it up!</h5>
                </div>
            @elseif($gpa >= 3.0)
                <div class="alert alert-info">
                    <i class="fas fa-thumbs-up fa-2x mb-2"></i>
                    <h5>Good Performance! You're doing well.</h5>
                </div>
            @elseif($gpa >= 2.5)
                <div class="alert alert-warning">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <h5>Average Performance. Room for improvement.</h5>
                </div>
            @else
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h5>Needs Improvement. Please consult your teachers.</h5>
                </div>
            @endif
        </div>
    </div> <!-- end card-body -->
</div> <!-- end card -->
@endif
@endsection
