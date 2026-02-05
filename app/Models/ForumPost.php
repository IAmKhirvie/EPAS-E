<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'thread_id',
        'user_id',
        'parent_id',
        'body',
        'is_best_answer',
        'upvotes_count',
        'downvotes_count',
    ];

    protected $casts = [
        'is_best_answer' => 'boolean',
        'upvotes_count' => 'integer',
        'downvotes_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($post) {
            $post->thread->updateLastReply($post);
        });

        static::deleted(function ($post) {
            // Update thread reply count
            $post->thread->update([
                'replies_count' => $post->thread->posts()->count(),
            ]);
        });
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ForumPostVote::class, 'post_id');
    }

    public function upvotes(): HasMany
    {
        return $this->votes()->where('vote_type', 'up');
    }

    public function downvotes(): HasMany
    {
        return $this->votes()->where('vote_type', 'down');
    }

    public function getScoreAttribute(): int
    {
        return $this->upvotes_count - $this->downvotes_count;
    }

    public function hasVotedBy(User $user): ?string
    {
        $vote = $this->votes()->where('user_id', $user->id)->first();
        return $vote ? $vote->vote_type : null;
    }

    public function vote(User $user, string $type): void
    {
        $existingVote = $this->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->vote_type === $type) {
                // Remove vote if same type
                $existingVote->delete();
                $this->updateVoteCounts();
                return;
            }
            // Change vote type
            $existingVote->update(['vote_type' => $type]);
        } else {
            // Create new vote
            $this->votes()->create([
                'user_id' => $user->id,
                'vote_type' => $type,
            ]);
        }

        $this->updateVoteCounts();
    }

    public function updateVoteCounts(): void
    {
        $this->update([
            'upvotes_count' => $this->upvotes()->count(),
            'downvotes_count' => $this->downvotes()->count(),
        ]);
    }

    public function markAsBestAnswer(): void
    {
        // Unmark any existing best answer
        $this->thread->posts()->where('is_best_answer', true)->update(['is_best_answer' => false]);

        // Mark this as best answer
        $this->update(['is_best_answer' => true]);

        // Mark thread as resolved
        $this->thread->update(['is_resolved' => true]);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWithReplies($query)
    {
        return $query->with(['replies' => function ($q) {
            $q->with('user')->orderBy('created_at');
        }]);
    }
}
