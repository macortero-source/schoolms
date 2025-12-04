@extends('layouts.app')

@section('title', 'Generate Report')

@section('content')
<div class="mb-4">
    <h1 class="page-title">Generate Report</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Generate</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-download me-2"></i>
                Report Parameters
            </div>
            <div class="card-body">
                @if($type == 'student-report-card')
                    <form action="{{ route('reports.student-report-card') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Student <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select" required>
                                <option value="">-- Select Student --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->student_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year" class="form-select" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Semester (Optional)</label>
                            <select name="semester" class="form-select">
                                <option value="">All Semesters</option>
                                <option value="Fall">Fall</option>
                                <option value="Spring">Spring</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Download PDF
                            </button>
                        </div>
                    </form>
                @endif

                @if($type == 'class-performance')
                    <form action="{{ route('reports.class-performance') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year" class="form-select" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-download me-2"></i>Download PDF
                            </button>
                        </div>
                    </form>
                @endif

                <!-- Add similar forms for other report types -->
            </div>
        </div>
    </div>
</div>
@endsection
