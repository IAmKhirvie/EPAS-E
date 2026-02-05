<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelfCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'information_sheet_id',
        'check_number',
        'title',
        'description',
        'instructions',
        'time_limit',
        'passing_score',
        'total_points',
        'is_active',
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'total_points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function informationSheet(): BelongsTo
    {
        return $this->belongsTo(InformationSheet::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SelfCheckQuestion::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(SelfCheckSubmission::class);
    }

    public function getQuestionCountAttribute(): int
    {
        return $this->questions()->count();
    }

    public function getAverageScoreAttribute(): ?float
    {
        $submissions = $this->submissions()->whereNotNull('percentage')->get();
        if ($submissions->isEmpty()) {
            return null;
        }
        return $submissions->avg('percentage');
    }

    public function getCompletionRateAttribute(): ?float
    {
        $totalUsers = User::count();
        $completedUsers = $this->submissions()->distinct('user_id')->count();
        return $totalUsers > 0 ? ($completedUsers / $totalUsers) * 100 : 0;
    }
}
