@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title">My Attendance</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attendance</li>
            </ol>
        </nav>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-gradient-primary text-white text-center">
                <i class="fas fa-calendar-day fa-2x mb-2"></i>
                <h3>{{ $stats['total_days'] }}</h3>
                <p>Total Days</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-success text-white text-center">
                <i class="fas fa-check fa-2x mb-2"></i>
                <h3>{{ $stats['present'] }}</h3>
                <p>Present</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-danger text-white text-center">
                <i class="fas fa-times fa-2x mb-2"></i>
                <h3>{{ $stats['absent'] }}</h3>
                <p>Absent</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-info text-white text-center">
                <i class="fas fa-percentage fa-2x mb-2"></i>
                <h3>{{ $stats['percentage'] }}%</h3>
                <p>Attendance Rate</p>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>Attendance Records</span>
            <small class="text-muted">
                Period: {{ $startDate->format('M d, Y') }} – {{ $endDate->format('M d, Y') }}
            </small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Marked By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendance as $record)
                            @php
                                $badgeClass = match($record->status) {
                                    'present' => 'success',
                                    'absent' => 'danger',
                                    'late' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $badgeClass }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td>{{ $record->remarks ?? '-' }}</td>
                                <td>{{ optional($record->markedByTeacher)->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fas fa-calendar-times fa-2x mb-2"></i><br>
                                    No attendance records found for this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-3 d-flex justify-content-center">
            {{ $attendance->links() }}
        </div>
    </div>
</div>
@endsection
