<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskSheet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'information_sheet_id',
        'task_number',
        'title',
        'description',
        'instructions',
        'image_path',
        'estimated_duration',
        'difficulty_level',
    ];

    protected $casts = [
        'estimated_duration' => 'integer',
    ];

    public function informationSheet(): BelongsTo
    {
        return $this->belongsTo(InformationSheet::class);
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(TaskSheetObjective::class)->orderBy('order');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(TaskSheetMaterial::class)->orderBy('order');
    }

    public function safetyPrecautions(): HasMany
    {
        return $this->hasMany(TaskSheetSafetyPrecaution::class)->orderBy('order');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TaskSheetItem::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TaskSheetSubmission::class);
    }

    public function performanceCriteria(): HasMany
    {
        return $this->hasMany(TaskSheetPerformanceCriterion::class);
    }

    public function getObjectivesListAttribute(): array
    {
        return $this->objectives->pluck('objective')->toArray();
    }

    public function getMaterialsListAttribute(): array
    {
        return $this->materials->map(function ($item) {
            return [
                'name' => $item->material_name,
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }

    public function getSafetyPrecautionsListAttribute(): array
    {
        return $this->safetyPrecautions->pluck('precaution')->toArray();
    }

    public function getAverageCompletionTimeAttribute(): ?float
    {
        $submissions = $this->submissions()->whereNotNull('submitted_at')->get();
        if ($submissions->isEmpty()) {
            return null;
        }
        return $submissions->avg('time_taken');
    }
}
