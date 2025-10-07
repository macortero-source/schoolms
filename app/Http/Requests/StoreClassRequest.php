<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'grade_level' => 'required|string|max:20',
            'section' => 'nullable|string|max:10',
            'class_teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string',
            'room_number' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Class name is required.',
            'grade_level.required' => 'Grade level is required.',
            'capacity.required' => 'Class capacity is required.',
            'capacity.min' => 'Capacity must be at least 1.',
        ];
    }
}