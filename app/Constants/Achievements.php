<?php

namespace App\Constants;

class Achievements
{
    public const DEFINITIONS = [
        'FIRST_LOGIN' => [
            'name' => 'First Steps',
            'description' => 'Logged in for the first time',
            'icon' => 'fas fa-door-open',
            'points' => 10,
        ],
        'STREAK_7' => [
            'name' => 'Week Warrior',
            'description' => '7-day login streak',
            'icon' => 'fas fa-fire',
            'points' => 50,
        ],
        'STREAK_30' => [
            'name' => 'Monthly Maven',
            'description' => '30-day login streak',
            'icon' => 'fas fa-fire-alt',
            'points' => 200,
        ],
        'PERFECT_SCORE' => [
            'name' => 'Perfectionist',
            'description' => 'Scored 100% on any assessment',
            'icon' => 'fas fa-bullseye',
            'points' => 25,
        ],
        'MODULE_MASTER' => [
            'name' => 'Module Master',
            'description' => 'Completed all activities in a module',
            'icon' => 'fas fa-medal',
            'points' => 50,
        ],
        'COURSE_COMPLETE' => [
            'name' => 'Graduate',
            'description' => 'Completed an entire course',
            'icon' => 'fas fa-graduation-cap',
            'points' => 100,
        ],
        'TOP_10' => [
            'name' => 'Top 10',
            'description' => 'Ranked in the top 10 on the leaderboard',
            'icon' => 'fas fa-crown',
            'points' => 75,
        ],
    ];

    public static function get(string $key): ?array
    {
        return self::DEFINITIONS[$key] ?? null;
    }

    public static function all(): array
    {
        return self::DEFINITIONS;
    }
}
