@extends('layouts.app')

@section('title', 'Student Attendance')

@section('content')
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1>Attendance Details</h1>
            <p>{{ optional($student->user)->name ?? 'Student' }}</p>
        </div>
        <i class="fas fa-calendar-check background-icon"></i>
    </div>

    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
                <li class="breadcrumb-item active">{{ optional($student->user)->name ?? 'Student' }}</li>
            </ol>
        </nav>

        <!-- Profile Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex align-items-center">
                <img src="{{ optional($student->user)->profile_photo 
                    ? asset('storage/' . optional($student->user)->profile_photo) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode(optional($student->user)->name ?? 'Student') }}"
                    alt="Profile"
                    class="rounded-circle me-3"
                    style="width: 80px; height: 80px; object-fit: cover;">
                <div>
                    <h4 class="mb-1">{{ optional($student->user)->name ?? 'Unknown Student' }}</h4>
                    <p class="mb-0 text-muted">
                        Student #: {{ $student->student_number }} <br>
                        Class: {{ optional($student->class)->full_name ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="gradient-card text-center">
                    <h3>{{ $stats['total_days'] }}</h3>
                    <p>Total Days</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="gradient-card text-center">
                    <h3>{{ $stats['present'] }}</h3>
                    <p>Present</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="gradient-card text-center">
                    <h3>{{ $stats['absent'] }}</h3>
                    <p>Absent</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="gradient-card text-center">
                    <h3>{{ $stats['percentage'] }}%</h3>
                    <p>Attendance %</p>
                </div>
            </div>
        </div>

        <!-- Attendance Records -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2"></i>Attendance Records</span>
                <small class="text-muted">
                    Period: {{ $startDate->toDateString() }} – {{ $endDate->toDateString() }}
                </small>
            </div>
            <div class="card-body p-0">
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
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $record->status === 'present' ? 'success' : ($record->status === 'absent' ? 'danger' : ($record->status === 'late' ? 'warning' : 'info')) }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td>{{ $record->remarks ?? '-' }}</td>
                                <td>{{ optional($record->markedByTeacher)->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No attendance records found.</td>
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
@endsection
