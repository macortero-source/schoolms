@extends('layouts.app')

@section('title', 'Exams')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Exams Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Exams</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('exams.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create Exam
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('exams.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search exams..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="exam_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="quiz" {{ request('exam_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="midterm" {{ request('exam_type') == 'midterm' ? 'selected' : '' }}>Midterm</option>
                        <option value="final" {{ request('exam_type') == 'final' ? 'selected' : '' }}>Final</option>
                        <option value="assignment" {{ request('exam_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('exams.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Exams Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>Exams List ({{ $exams->total() }})
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Subject</th>
                        <th>Class</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Marks</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td>
                                <strong>{{ $exam->name }}</strong><br>
                                <small class="text-muted">{{ $exam->academic_year }}</small>
                            </td>
                            <td>{{ $exam->subject->name }}</td>
                            <td>{{ $exam->class->full_name }}</td>
                            <td>
                                <span class="badge bg-{{ $exam->exam_type_badge_class }}">
                                    {{ ucfirst($exam->exam_type) }}
                                </span>
                            </td>
                            <td>
                                {{ $exam->exam_date->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $exam->start_time ? $exam->start_time->format('h:i A') : '' }}</small>
                            </td>
                            <td>{{ $exam->total_marks }}</td>
                            <td>
                                @if($exam->isToday())
                                    <span class="badge bg-warning">Today</span>
                                @elseif($exam->isUpcoming())
                                    <span class="badge bg-info">Upcoming</span>
                                @else
                                    <span class="badge bg-secondary">Past</span>
                                @endif
                                @if($exam->is_published)
                                    <span class="badge bg-success">Published</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('grades.create', $exam) }}" class="btn btn-sm btn-success" title="Enter Grades">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No exams found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($exams->hasPages())
        <div class="card-footer">
            {{ $exams->links() }}
        </div>
    @endif
</div>
@endsection