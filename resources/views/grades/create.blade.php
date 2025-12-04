@extends('layouts.app')

@section('title', 'Enter Grades')

@section('content')
<div class="mb-4">
    <h1 class="page-title">Enter Grades</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item active">Enter Grades</li>
        </ol>
    </nav>
</div>

<!-- Exam Details Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong><i class="fas fa-file-alt me-2"></i>Exam Name:</strong>
                <p class="mb-0">{{ $exam->name }}</p>
            </div>
            <div class="col-md-3">
                <strong><i class="fas fa-book me-2"></i>Subject:</strong>
                <p class="mb-0">{{ $exam->subject->name }}</p>
            </div>
            <div class="col-md-3">
                <strong><i class="fas fa-school me-2"></i>Class:</strong>
                <p class="mb-0">{{ $exam->class->full_name }}</p>
            </div>
            <div class="col-md-3">
                <strong><i class="fas fa-clipboard-check me-2"></i>Total Marks:</strong>
                <p class="mb-0">{{ $exam->total_marks }} (Pass: {{ $exam->passing_marks }})</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-3">
                <strong><i class="fas fa-calendar me-2"></i>Exam Date:</strong>
                <p class="mb-0">{{ $exam->exam_date->format('M d, Y') }}</p>
            </div>
            <div class="col-md-3">
                <strong><i class="fas fa-percentage me-2"></i>Passing %:</strong>
                <p class="mb-0">{{ number_format(($exam->passing_marks / $exam->total_marks) * 100, 1) }}%</p>
            </div>
            <div class="col-md-3">
                <strong><i class="fas fa-users me-2"></i>Total Students:</strong>
                <p class="mb-0">{{ $students->count() }}</p>
            </div>
            <div class="col-md-3">
                <strong><i class="fas fa-check-circle me-2"></i>Graded:</strong>
                <p class="mb-0">{{ $grades->count() }} / {{ $students->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Grade Entry Form -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-star me-2"></i>Enter Grades for {{ $students->count() }} Students</span>
        <div>
            <span class="badge bg-info">{{ $grades->count() }} Graded</span>
            <span class="badge bg-warning">{{ $students->count() - $grades->count() }} Pending</span>
        </div>
    </div>
    <div class="card-body">
        @if($students->isEmpty())
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <p class="mb-0">No active students found in this class.</p>
            </div>
        @else
            <form action="{{ route('grades.store-bulk', $exam) }}" method="POST" id="gradeForm">
                @csrf

                <!-- Instructions -->
                <div class="alert alert-info mb-4">
                    <strong><i class="fas fa-info-circle me-2"></i>Instructions:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Enter marks obtained for each student (0 to {{ $exam->total_marks }})</li>
                        <li>Grades and status will be calculated automatically</li>
                        <li>Students with marks below {{ $exam->passing_marks }} will be marked as "Fail"</li>
                        <li>Add remarks for any student (optional)</li>
                        <li>Click "Save All Grades" when done</li>
                    </ul>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Student Name</th>
                                <th style="width: 150px;">Student Number</th>
                                <th style="width: 150px;" class="text-center">
                                    Marks Obtained <span class="text-danger">*</span>
                                    <br><small class="text-muted">(Max: {{ $exam->total_marks }})</small>
                                </th>
                                <th style="width: 100px;" class="text-center">Grade</th>
                                <th style="width: 100px;" class="text-center">Status</th>
                                <th style="width: 250px;">Remarks (Optional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                @php
                                    $existingGrade = $grades->get($student->id);
                                    $marks = $existingGrade ? $existingGrade->marks_obtained : '';
                                    $remarks = $existingGrade ? $existingGrade->remarks : '';
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $student->user->profile_photo ? asset('storage/' . $student->user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name) . '&size=35' }}" 
                                                 alt="{{ $student->user->name }}"
                                                 class="rounded-circle me-2"
                                                 style="width: 35px; height: 35px; object-fit: cover;">
                                            <div>
                                                <strong>{{ $student->user->name }}</strong>
                                                @if($existingGrade)
                                                    <br><small class="text-success"><i class="fas fa-check-circle"></i> Previously graded</small>
                                                @endif
                                            </div>
                                        </div>
                                        <input type="hidden" name="grades[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $student->student_number }}</span>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               class="form-control marks-input @error('grades.'.$index.'.marks_obtained') is-invalid @enderror" 
                                               name="grades[{{ $index }}][marks_obtained]" 
                                               value="{{ old('grades.'.$index.'.marks_obtained', $marks) }}"
                                               min="0" 
                                               max="{{ $exam->total_marks }}"
                                               step="0.01"
                                               data-index="{{ $index }}"
                                               data-total="{{ $exam->total_marks }}"
                                               data-passing="{{ $exam->passing_marks }}"
                                               placeholder="0.00"
                                               required>
                                        @error('grades.'.$index.'.marks_obtained')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="text-center">
                                        <span class="grade-display-{{ $index }} badge bg-secondary">-</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-display-{{ $index }} badge bg-secondary">-</span>
                                    </td>
                                    <td>
                                        <input type="text" 
                                               class="form-control form-control-sm" 
                                               name="grades[{{ $index }}][remarks]" 
                                               value="{{ old('grades.'.$index.'.remarks', $remarks) }}"
                                               placeholder="Optional remarks">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="7" class="text-center">
                                    <strong>Total Students: {{ $students->count() }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Exam
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning me-2" onclick="fillSampleGrades()">
                            <i class="fas fa-magic me-2"></i>Fill Sample Grades (Testing)
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Save All Grades
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        border-bottom: 2px solid #dee2e6;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .marks-input {
        font-size: 1.1rem;
        font-weight: 600;
        text-align: center;
    }

    .marks-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.8rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalMarks = {{ $exam->total_marks }};
    const passingMarks = {{ $exam->passing_marks }};

    // Calculate grade based on percentage
    function calculateGrade(percentage) {
        if (percentage >= 90) return { grade: 'A+', class: 'success' };
        if (percentage >= 85) return { grade: 'A', class: 'success' };
        if (percentage >= 80) return { grade: 'B+', class: 'primary' };
        if (percentage >= 75) return { grade: 'B', class: 'primary' };
        if (percentage >= 70) return { grade: 'C+', class: 'info' };
        if (percentage >= 65) return { grade: 'C', class: 'info' };
        if (percentage >= 60) return { grade: 'D', class: 'warning' };
        return { grade: 'F', class: 'danger' };
    }

    // Update grade display when marks change
    document.querySelectorAll('.marks-input').forEach(input => {
        input.addEventListener('input', function() {
            const marks = parseFloat(this.value) || 0;
            const index = this.dataset.index;
            const total = parseFloat(this.dataset.total);
            const passing = parseFloat(this.dataset.passing);
            
            // Validate max marks
            if (marks > total) {
                this.value = total;
                alert(`Marks cannot exceed ${total}`);
                return;
            }

            // Calculate percentage
            const percentage = (marks / total) * 100;
            const gradeData = calculateGrade(percentage);
            const status = marks >= passing ? 'Pass' : 'Fail';
            const statusClass = marks >= passing ? 'success' : 'danger';

            // Update grade display
            const gradeDisplay = document.querySelector(`.grade-display-${index}`);
            if (gradeDisplay) {
                gradeDisplay.textContent = gradeData.grade;
                gradeDisplay.className = `badge bg-${gradeData.class}`;
            }

            // Update status display
            const statusDisplay = document.querySelector(`.status-display-${index}`);
            if (statusDisplay) {
                statusDisplay.textContent = status;
                statusDisplay.className = `badge bg-${statusClass}`;
            }
        });

        // Trigger on load for existing grades
        if (input.value) {
            input.dispatchEvent(new Event('input'));
        }
    });

    // Sample grade filler (for testing purposes)
    window.fillSampleGrades = function() {
        if (!confirm('This will fill random sample grades for testing. Continue?')) {
            return;
        }

        document.querySelectorAll('.marks-input').forEach(input => {
            const total = parseFloat(input.dataset.total);
            // Generate random marks between 40% and 95% of total
            const randomMarks = (Math.random() * 0.55 + 0.4) * total;
            input.value = randomMarks.toFixed(2);
            input.dispatchEvent(new Event('input'));
        });
    };

    // Form submission validation
    document.getElementById('gradeForm').addEventListener('submit', function(e) {
        let hasGrades = false;
        document.querySelectorAll('.marks-input').forEach(input => {
            if (input.value && input.value !== '') {
                hasGrades = true;
            }
        });

        if (!hasGrades) {
            e.preventDefault();
            alert('Please enter marks for at least one student before submitting.');
            return false;
        }

        // Confirm submission
        if (!confirm('Are you sure you want to save these grades?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
