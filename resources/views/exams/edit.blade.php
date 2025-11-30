@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Exam</h1>

    <form action="{{ route('exams.update', $exam->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Exam Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Exam Name</label>
            <input type="text" name="name" id="name" class="form-control"
                   value="{{ old('name', $exam->name) }}" required>
        </div>

        <!-- Subject -->
        <div class="mb-3">
            <label for="subject_id" class="form-label">Subject</label>
            <select name="subject_id" id="subject_id" class="form-select" required>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}"
                        {{ $exam->subject_id == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Class -->
        <div class="mb-3">
            <label for="class_id" class="form-label">Class</label>
            <select name="class_id" id="class_id" class="form-select" required>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}"
                        {{ $exam->class_id == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Exam Date -->
        <div class="mb-3">
            <label for="exam_date" class="form-label">Exam Date</label>
            <input type="date" name="exam_date" id="exam_date" class="form-control"
                   value="{{ old('exam_date', $exam->exam_date) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Exam</button>
    </form>
</div>
@endsection
