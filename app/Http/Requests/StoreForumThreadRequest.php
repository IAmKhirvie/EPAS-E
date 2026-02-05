<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreForumThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:10',
            'is_urgent' => 'boolean',
            'is_pinned' => 'boolean',
            'target_roles' => 'nullable|string',
            'deadline' => 'nullable|date|after:now',
            'publish_at' => 'nullable|date',
        ];
    }
}
