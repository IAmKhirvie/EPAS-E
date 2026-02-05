<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ForumCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
        'is_announcement_category',
        'admin_only_post',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'is_announcement_category' => 'boolean',
        'admin_only_post' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function threads(): HasMany
    {
        return $this->hasMany(ForumThread::class, 'category_id');
    }

    public function activeThreads(): HasMany
    {
        return $this->threads()->whereNull('deleted_at');
    }

    public function getThreadsCountAttribute(): int
    {
        return $this->threads()->count();
    }

    public function getPostsCountAttribute(): int
    {
        return ForumPost::whereIn('thread_id', $this->threads()->pluck('id'))->count();
    }

    public function getLatestThreadAttribute()
    {
        return $this->threads()->latest()->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeAnnouncementCategories($query)
    {
        return $query->where('is_announcement_category', true);
    }

    public function scopeDiscussionCategories($query)
    {
        return $query->where('is_announcement_category', false);
    }

    /**
     * Check if user can post in this category
     */
    public function canUserPost($user): bool
    {
        if (!$this->admin_only_post) {
            return true;
        }
        return in_array($user->role, ['admin', 'instructor']);
    }
}
