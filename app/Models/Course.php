<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_name',
        'course_code',
        'description',
        'sector',
        'is_active',
        'order',
        'instructor_id',
        'target_sections',
        'certificate_template',
        'certificate_background',
        'certificate_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'certificate_config' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($course) {
            $course->modules()->each(function ($module) {
                $module->delete(); // This will trigger Module's deleting event
            });
        });
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function activeModules(): HasMany
    {
        return $this->hasMany(Module::class)->where('is_active', true)->orderBy('order');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope: courses visible to a given section.
     * Null/empty target_sections means visible to ALL sections.
     */
    public function scopeForSection($query, ?string $section)
    {
        if (!$section) {
            return $query;
        }

        return $query->where(function ($q) use ($section) {
            $q->whereNull('target_sections')
              ->orWhere('target_sections', '')
              ->orWhere('target_sections', $section)
              ->orWhere('target_sections', 'like', $section . ',%')
              ->orWhere('target_sections', 'like', '%,' . $section . ',%')
              ->orWhere('target_sections', 'like', '%,' . $section);
        });
    }
}