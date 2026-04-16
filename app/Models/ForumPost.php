<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'thread_id', 'user_id', 'parent_id', 'body',
        'is_best_answer', 'votes_count',
    ];

    protected $casts = [
        'is_best_answer' => 'boolean',
    ];

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
        return $this->hasMany(ForumPost::class, 'parent_id')->orderBy('created_at');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ForumPostVote::class, 'post_id');
    }

    public function getUserVote(int $userId): ?int
    {
        return $this->votes()->where('user_id', $userId)->value('value');
    }
}
