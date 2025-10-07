@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="mb-4">
    <h1 class="page-title">Reports & Analytics</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Student Report Card -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                    <h5>Student Report Card</h5>
                    <p class="text-muted">Generate comprehensive report cards for individual students</p>
                </div>
                <a href="{{ route('reports.form', ['type' => 'student-report-card']) }}" class="btn btn-primary w-100">
                    <i class="fas fa-arrow-right me-2"></i>Generate Report
                </a>
            </div>
        </div>
    </div>

    <!-- Class Performance -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                    <h5>Class Performance Report</h5>
                    <p class="text-muted">Analyze overall class performance and rankings</p>
                </div>
                <a href="{{ route('reports.form', ['type' => 'class-performance']) }}" class="btn btn-success w-100">
                    <i class="fas fa-arrow-right me-2"></i>Generate Report
                </a>
            </div>
        </div>
    </div>

    <!-- Attendance Summary -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-calendar-check fa-3x text-info mb-3"></i>
                    <h5>Attendance Summary</h5>
                    <p class="text-muted">Detailed attendance reports with statistics</p>
                </div>
                <a href="{{ route('reports.form', ['type' => 'attendance-summary']) }}" class="btn btn-info w-100">
                    <i class="fas fa-arrow-right me-2"></i>Generate Report
                </a>
            </div>
        </div>
    </div>

    <!-- Exam Analysis -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-chart-pie fa-3x text-warning mb-3"></i>
                    <h5>Exam Analysis Report</h5>
                    <p class="text-muted">Comprehensive exam performance analysis</p>
                </div>
                <a href="{{ route('reports.form', ['type' => 'exam-analysis']) }}" class="btn btn-warning w-100">
                    <i class="fas fa-arrow-right me-2"></i>Generate Report
                </a>
            </div>
        </div>
    </div>

    <!-- Subject Performance -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-book fa-3x text-danger mb-3"></i>
                    <h5>Subject Performance Report</h5>
                    <p class="text-muted">Subject-wise performance across all classes</p>
                </div>
                <a href="{{ route('reports.form', ['type' => 'subject-performance']) }}" class="btn btn-danger w-100">
                    <i class="fas fa-arrow-right me-2"></i>Generate Report
                </a>
            </div>
        </div>
    </div>
</div>
@endsection