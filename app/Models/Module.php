<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($module) {
            $module->informationSheets()->each(function ($sheet) {
                $sheet->delete();
            });
        });
    }

    protected $fillable = [
        'course_id',
        'sector',
        'qualification_title',
        'unit_of_competency',
        'module_title',
        'module_number',
        'module_name',
        'table_of_contents',
        'how_to_use_cblm',
        'introduction',
        'learning_outcomes',
        'is_active',
        'order',
        'images',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'images' => 'array',
    ];

    // Add this relationship
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function informationSheets(): HasMany
    {
        return $this->hasMany(InformationSheet::class)->orderBy('order');
    }

    /**
     * Get prerequisites for this module.
     */
    public function prerequisites(): HasMany
    {
        return $this->hasMany(ModulePrerequisite::class, 'module_id');
    }

    /**
     * Get modules that require this module as a prerequisite.
     */
    public function dependentModules(): HasMany
    {
        return $this->hasMany(ModulePrerequisite::class, 'prerequisite_module_id');
    }

    /**
     * Check if a user has completed this module.
     */
    public function isCompletedBy(User $user): bool
    {
        // Check if user has completed all required content
        $sheets = $this->informationSheets;
        if ($sheets->isEmpty()) {
            return false;
        }

        foreach ($sheets as $sheet) {
            // Check if user has completed self-checks, task sheets, etc.
            // This is a simplified check - you may want to add more conditions
            $selfChecks = $sheet->selfChecks;
            foreach ($selfChecks as $selfCheck) {
                $submission = $selfCheck->submissions()->where('user_id', $user->id)->first();
                if (!$submission || !$submission->is_passed) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the grade for a specific user.
     */
    public function getGradeFor(User $user): ?float
    {
        // Calculate average grade from submissions
        $sheets = $this->informationSheets;
        $totalScore = 0;
        $count = 0;

        foreach ($sheets as $sheet) {
            $selfChecks = $sheet->selfChecks;
            foreach ($selfChecks as $selfCheck) {
                $submission = $selfCheck->submissions()->where('user_id', $user->id)->first();
                if ($submission && $submission->score !== null) {
                    $totalScore += $submission->score;
                    $count++;
                }
            }
        }

        return $count > 0 ? round($totalScore / $count, 2) : null;
    }
}