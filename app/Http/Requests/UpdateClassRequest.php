<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRequest extends FormRequest
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
            'is_active' => 'boolean',
        ];
    }
}