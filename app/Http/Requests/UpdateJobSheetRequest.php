<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobSheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_number' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'required|array|min:1',
            'tools_required' => 'required|array|min:1',
            'safety_requirements' => 'required|array|min:1',
            'reference_materials' => 'nullable|array',
        ];
    }
}
