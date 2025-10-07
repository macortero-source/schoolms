<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject');
        
        return [
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:20', Rule::unique('subjects', 'code')->ignore($subjectId)],
            'description' => 'nullable|string',
            'type' => 'required|in:core,elective,optional',
            'credit_hours' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
        ];
    }
}