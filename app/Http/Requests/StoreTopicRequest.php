<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topic_number' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'parts' => 'nullable|array',
            'parts.*.title' => 'nullable|string|max:255',
            'parts.*.explanation' => 'nullable|string',
            'part_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:' . config('joms.uploads.max_image_size', 5120),
        ];
    }
}
