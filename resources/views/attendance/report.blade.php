@extends('layouts.app')

@section('title', 'Attendance Report')

@section('content')
<div class="mb-4">
    <h1 class="page-title">Attendance Report</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
            <li class="breadcrumb-item active">Report</li>
        </ol>
    </nav>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-2"></i>Filter Options
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.report') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="class_id" class="form-label">Class</label>
                    <select name="class_id" id="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>

                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($selectedClass)
<!-- Report Results -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-chart-bar me-2"></i>
            Attendance Report - {{ $selectedClass->full_name }}
        </span>
        <div>
            <a href="{{ route('attendance.export', request()->all()) }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel me-1"></i>Export to Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th class="text-center">Total Days</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Absent</th>
                        <th class="text-center">Late</th>
                        <th class="text-center">Attendance %</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendanceData as $data)
                        <tr>
                            <td>
                                <strong>{{ $data['student']->user->name }}</strong><br>
                                <small class="text-muted">{{ $data['student']->student_number }}</small>
                            </td>
                            <td class="text-center">{{ $data['total_days'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $data['present'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger">{{ $data['absent'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning">{{ $data['late'] }}</span>
                            </td>
                            <td class="text-center">
                                <strong class="text-{{ $data['percentage'] >= 75 ? 'success' : 'danger' }}">
                                    {{ number_format($data['percentage'], 1) }}%
                                </strong>
                            </td>
                            <td class="text-center">
                                @if($data['percentage'] >= 90)
                                    <span class="badge bg-success">Excellent</span>
                                @elseif($data['percentage'] >= 75)
                                    <span class="badge bg-info">Good</span>
                                @elseif($data['percentage'] >= 60)
                                    <span class="badge bg-warning">Average</span>
                                @else
                                    <span class="badge bg-danger">Poor</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No attendance data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle fa-2x mb-2"></i>
    <p class="mb-0">Please select a class to view attendance report.</p>
</div>
@endif
@endsection