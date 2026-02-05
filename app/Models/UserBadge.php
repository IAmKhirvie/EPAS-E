<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBadge extends Model
{
    use SoftDeletes;

    protected $table = 'user_badges';

    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at',
        'metadata',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
