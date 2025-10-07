<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code',
            'description' => 'nullable|string',
            'type' => 'required|in:core,elective,optional',
            'credit_hours' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Subject name is required.',
            'code.required' => 'Subject code is required.',
            'code.unique' => 'This subject code is already in use.',
            'type.required' => 'Subject type is required.',
            'credit_hours.required' => 'Credit hours are required.',
        ];
    }
}