@extends('layouts.app')

@section('title', 'Mark Attendance')

@section('content')
<div class="mb-4">
    <h1 class="page-title">Attendance Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Attendance</li>
        </ol>
    </nav>
</div>


<!-- Class Selection -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-2"></i>Select Class and Date
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.index') }}">
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" id="class_id" class="form-select" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Load
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($selectedClass)
<!-- Attendance Form -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-users me-2"></i>
            {{ $selectedClass->full_name }} - {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}
        </span>
        <span class="badge bg-info">{{ $students->count() }} Students</span>
    </div>
    <div class="card-body">
        @if($students->isEmpty())
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <p class="mb-0">No active students in this class.</p>
            </div>
        @else
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">

                <!-- Quick Actions -->
                <div class="mb-4 p-3 quick-actions d-flex align-items-center">
    <strong class="me-3">Quick Actions:</strong>
    <button type="button" class="btn btn-sm btn-success me-2" onclick="markAll('present')">
        <i class="fas fa-check-circle me-1"></i>Mark All Present
    </button>
    <button type="button" class="btn btn-sm btn-danger me-2" onclick="markAll('absent')">
        <i class="fas fa-times-circle me-1"></i>Mark All Absent
    </button>
    <button type="button" class="btn btn-sm btn-warning" onclick="markAll('late')">
        <i class="fas fa-clock me-1"></i>Mark All Late
    </button>
</div>


                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Student</th>
                                <th style="width: 300px;">Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                @php
                                    $existingAttendance = $attendance->get($student->id);
                                    $status = $existingAttendance ? $existingAttendance->status : 'present';
                                    $remarks = $existingAttendance ? $existingAttendance->remarks : '';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $student->user->profile_photo ? asset('storage/' . $student->user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name) }}" 
                                                 alt="{{ $student->user->name }}"
                                                 class="rounded-circle me-2"
                                                 style="width: 35px; height: 35px; object-fit: cover;">
                                            <div>
                                                <strong>{{ $student->user->name }}</strong><br>
                                                <small class="text-muted">{{ $student->student_number }}</small>
                                            </div>
                                        </div>
                                        <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="attendance[{{ $index }}][status]" 
                                                   id="present_{{ $student->id }}" value="present" 
                                                   {{ $status == 'present' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-success" for="present_{{ $student->id }}">
                                                <i class="fas fa-check me-1"></i>Present
                                            </label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $index }}][status]" 
                                                   id="absent_{{ $student->id }}" value="absent"
                                                   {{ $status == 'absent' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="absent_{{ $student->id }}">
                                                <i class="fas fa-times me-1"></i>Absent
                                            </label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $index }}][status]" 
                                                   id="late_{{ $student->id }}" value="late"
                                                   {{ $status == 'late' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-warning" for="late_{{ $student->id }}">
                                                <i class="fas fa-clock me-1"></i>Late
                                            </label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $index }}][status]" 
                                                   id="excused_{{ $student->id }}" value="excused"
                                                   {{ $status == 'excused' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-info" for="excused_{{ $student->id }}">
                                                <i class="fas fa-file-medical me-1"></i>Excused
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="attendance[{{ $index }}][remarks]" 
                                               value="{{ $remarks }}"
                                               placeholder="Optional remarks">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
    <a href="{{ route('attendance.report') }}" class="btn btn-secondary">
        <i class="fas fa-chart-bar me-2"></i>View Reports
    </a>
    <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save me-2"></i>Save Attendance
    </button>
</div>

            </form>
        @endif
    </div>
</div>
@endif

@push('scripts')
<script>
function markAll(status) {
    document.querySelectorAll('input[type="radio"][value="' + status + '"]').forEach(function(radio) {
        radio.checked = true;
    });
}
</script>
@endpush
@endsection