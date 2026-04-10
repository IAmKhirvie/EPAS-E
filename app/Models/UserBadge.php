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
        'badge_key',
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

    /**
     * Parse badge_key to get base key and tier.
     * Format: "badgekey_tier_N" where N is tier level.
     */
    public function getBadgeDataAttribute(): array
    {
        $key = $this->badge_key;

        // Check if it's a tiered badge (ends with _tier_N)
        if (preg_match('/^(.+)_tier_(\d+)$/', $key, $matches)) {
            $baseKey = $matches[1];
            $tier = (int) $matches[2];

            $badge = GamificationService::getBadge($baseKey);
            if ($badge) {
                $tierInfo = GamificationService::getTierInfo($tier);
                return [
                    'base_key' => $baseKey,
                    'tier' => $tier,
                    'name' => $badge['name'],
                    'tier_name' => $tierInfo['name'],
                    'full_name' => $badge['name'] . ' (' . $tierInfo['name'] . ')',
                    'icon' => $badge['icon'],
                    'description' => $badge['description'],
                    'color' => $tierInfo['color'],
                    'tier_class' => $tierInfo['class'],
                ];
            }
        }

        // Non-tiered badge fallback
        $badge = GamificationService::getBadge($key);
        return $badge ? array_merge($badge, ['tier' => null, 'full_name' => $badge['name']]) : [
            'name' => 'Unknown Badge',
            'icon' => 'fa-question-circle',
            'description' => 'A special achievement.',
            'full_name' => 'Unknown Badge',
            'color' => '#999',
            'tier_class' => 'bronze',
        ];
    }
}
