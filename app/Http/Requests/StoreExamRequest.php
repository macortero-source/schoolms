<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isTeacher();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'exam_type' => 'required|in:quiz,midterm,final,assignment,practical',
            'exam_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0|lte:total_marks',
            'academic_year' => 'required|string|max:20',
            'semester' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'room_number' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Exam name is required.',
            'subject_id.required' => 'Please select a subject.',
            'class_id.required' => 'Please select a class.',
            'total_marks.required' => 'Total marks are required.',
            'passing_marks.lte' => 'Passing marks cannot exceed total marks.',
            'end_time.after' => 'End time must be after start time.',
        ];
    }
}