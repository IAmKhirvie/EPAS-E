<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SelfCheckQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'self_check_id',
        'question_text',
        'question_type',
        'points',
        'correct_answer',
        'explanation',
        'order',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
    ];

    public function selfCheck(): BelongsTo
    {
        return $this->belongsTo(SelfCheck::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(SelfCheckQuestionOption::class)->orderBy('order');
    }

    public function submissionAnswers(): HasMany
    {
        return $this->hasMany(SelfCheckSubmissionAnswer::class);
    }

    public function getFormattedOptionsAttribute(): array
    {
        return $this->options->mapWithKeys(function ($option) {
            return [$option->option_letter => $option->option_text];
        })->toArray();
    }

    public function getCorrectAnswerFormattedAttribute(): string
    {
        if ($this->question_type === 'multiple_choice') {
            $options = $this->formatted_options;
            return $options[$this->correct_answer] ?? $this->correct_answer;
        }

        return $this->correct_answer;
    }
}
