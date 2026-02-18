<?php

namespace App\Models;

use App\Traits\HasCommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ForumThread extends Model
{
    use HasFactory, SoftDeletes, HasCommonScopes;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'body',
        'is_pinned',
        'is_locked',
        'is_resolved',
        'is_announcement',
        'is_urgent',
        'target_roles',
        'deadline',
        'publish_at',
        'announcement_priority',
        'announcement_expires_at',
        'views_count',
        'replies_count',
        'last_reply_at',
        'last_reply_user_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_resolved' => 'boolean',
        'is_announcement' => 'boolean',
        'is_urgent' => 'boolean',
        'announcement_priority' => 'integer',
        'announcement_expires_at' => 'datetime',
        'deadline' => 'datetime',
        'publish_at' => 'datetime',
        'views_count' => 'integer',
        'replies_count' => 'integer',
        'last_reply_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($thread) {
            if (empty($thread->slug)) {
                $thread->slug = Str::slug($thread->title) . '-' . Str::random(6);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastReplyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_reply_user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'thread_id');
    }

    public function replies(): HasMany
    {
        return $this->posts()->whereNull('parent_id');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'forum_thread_subscriptions')
            ->withTimestamps();
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function updateLastReply(ForumPost $post): void
    {
        $this->update([
            'last_reply_at' => $post->created_at,
            'last_reply_user_id' => $post->user_id,
            'replies_count' => $this->posts()->count(),
        ]);
    }

    public function getBestAnswerAttribute()
    {
        return $this->posts()->where('is_best_answer', true)->first();
    }

    public function isSubscribedBy(User $user): bool
    {
        return $this->subscribers()->where('user_id', $user->id)->exists();
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeNotPinned($query)
    {
        return $query->where('is_pinned', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('is_pinned', 'desc')
            ->orderBy('last_reply_at', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    // Announcement scopes
    public function scopeAnnouncements($query)
    {
        return $query->where('is_announcement', true)
            ->where(function ($q) {
                $q->whereNull('announcement_expires_at')
                    ->orWhere('announcement_expires_at', '>', now());
            })
            ->orderBy('announcement_priority', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function scopeNotAnnouncements($query)
    {
        return $query->where('is_announcement', false);
    }

    public function getIsActiveAnnouncementAttribute(): bool
    {
        if (!$this->is_announcement) {
            return false;
        }

        if ($this->announcement_expires_at === null) {
            return true;
        }

        return $this->announcement_expires_at->isFuture();
    }

    /**
     * Users who have read this thread
     */
    public function readByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'forum_thread_reads', 'thread_id', 'user_id')
            ->withTimestamps()
            ->withPivot('read_at');
    }

    /**
     * Mark thread as read by user
     */
    public function markAsRead($userId = null): void
    {
        $userId = $userId ?: auth()->id();
        if (!$userId) return;

        $this->readByUsers()->syncWithoutDetaching([
            $userId => ['read_at' => now()]
        ]);
    }

    /**
     * Check if thread is read by user
     */
    public function isReadByUser($user): bool
    {
        if (!$this->relationLoaded('readByUsers')) {
            return $this->readByUsers()->where('user_id', $user->id)->exists();
        }
        return $this->readByUsers->contains($user->id);
    }

    /**
     * Scope for filtering by user role
     */
    public function scopeForUser($query, $user)
    {
        $role = $user->role;

        return $query->where(function ($q) use ($role) {
            $q->where('target_roles', 'all')
              ->orWhereNull('target_roles')
              ->orWhere('target_roles', $role)
              ->orWhere('target_roles', 'like', $role . ',%')
              ->orWhere('target_roles', 'like', '%,' . $role . ',%')
              ->orWhere('target_roles', 'like', '%,' . $role);
        });
    }

    /**
     * Scope for urgent announcements
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Scope for threads from announcement categories
     */
    public function scopeFromAnnouncementCategories($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->where('is_announcement_category', true);
        });
    }
}
