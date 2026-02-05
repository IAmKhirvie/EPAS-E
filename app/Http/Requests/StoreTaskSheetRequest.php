<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskSheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_number' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'objectives' => 'required|array|min:1',
            'materials' => 'required|array|min:1',
            'safety_precautions' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items' => 'required|array|min:1',
            'items.*.part_name' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.expected_finding' => 'required|string',
            'items.*.acceptable_range' => 'required|string',
        ];
    }
}
