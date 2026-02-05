<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSelfCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'check_number' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,multiple_select,true_false,fill_blank,short_answer,numeric,matching,ordering,classification,image_choice,image_identification,hotspot,image_labeling,audio_question,video_question,drag_drop,slider',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'nullable',
            'questions.*.explanation' => 'nullable|string',
        ];
    }
}
