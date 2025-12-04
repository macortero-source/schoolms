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

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Note:</strong> Select a report type below to generate comprehensive PDF reports.
</div>

<div class="row">
    <!-- Report Cards -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                <h5>Student Report Card</h5>
                <p class="text-muted small">Individual student performance report with grades and attendance</p>
                <a href="{{ route('reports.form', ['type' => 'student-report-card']) }}" class="btn btn-primary btn-sm">
                    Generate <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                <h5>Class Performance</h5>
                <p class="text-muted small">Overall class analytics with rankings and statistics</p>
                <a href="{{ route('reports.form', ['type' => 'class-performance']) }}" class="btn btn-success btn-sm">
                    Generate <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-calendar-check fa-3x text-info mb-3"></i>
                <h5>Attendance Summary</h5>
                <p class="text-muted small">Detailed attendance reports for any date range</p>
                <a href="{{ route('reports.form', ['type' => 'attendance-summary']) }}" class="btn btn-info btn-sm">
                    Generate <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-chart-pie fa-3x text-warning mb-3"></i>
                <h5>Exam Analysis</h5>
                <p class="text-muted small">Comprehensive exam performance with grade distribution</p>
                <a href="{{ route('reports.form', ['type' => 'exam-analysis']) }}" class="btn btn-warning btn-sm">
                    Generate <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x text-danger mb-3"></i>
                <h5>Subject Performance</h5>
                <p class="text-muted small">Subject-wise performance across all classes</p>
                <a href="{{ route('reports.form', ['type' => 'subject-performance']) }}" class="btn btn-danger btn-sm">
                    Generate <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection