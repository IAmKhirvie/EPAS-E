<?php

namespace App\Services;

use App\Constants\Roles;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Hardcoded badge definitions.
     * Keys are used in the user_badges table as badge_key.
     */
    public const BADGES = [
        'first_steps' => [
            'name' => 'First Steps',
            'icon' => 'fa-shoe-prints',
            'description' => 'Log in to EPAS-E for the first time.',
            'type' => 'milestone',
        ],
        'quick_starter' => [
            'name' => 'Quick Starter',
            'icon' => 'fa-rocket',
            'description' => 'Complete your first module.',
            'type' => 'milestone',
        ],
        'quiz_ace' => [
            'name' => 'Quiz Ace',
            'icon' => 'fa-brain',
            'description' => 'Score 100% on a self-check assessment.',
            'type' => 'academic',
        ],
        'perfect_streak' => [
            'name' => 'Perfect Streak',
            'icon' => 'fa-star',
            'description' => 'Achieve 5 perfect quiz scores.',
            'type' => 'academic',
        ],
        'homework_hero' => [
            'name' => 'Homework Hero',
            'icon' => 'fa-book-open',
            'description' => 'Submit 10 homework assignments.',
            'type' => 'academic',
        ],
        'week_warrior' => [
            'name' => 'Week Warrior',
            'icon' => 'fa-fire',
            'description' => 'Maintain a 7-day login streak.',
            'type' => 'streak',
        ],
        'monthly_master' => [
            'name' => 'Monthly Master',
            'icon' => 'fa-crown',
            'description' => 'Maintain a 30-day login streak.',
            'type' => 'streak',
        ],
        'course_graduate' => [
            'name' => 'Course Graduate',
            'icon' => 'fa-graduation-cap',
            'description' => 'Complete an entire course.',
            'type' => 'milestone',
        ],
        'early_bird' => [
            'name' => 'Early Bird',
            'icon' => 'fa-dove',
            'description' => 'Submit an assignment 3 days before the deadline.',
            'type' => 'milestone',
        ],
        'all_rounder' => [
            'name' => 'All-Rounder',
            'icon' => 'fa-gem',
            'description' => 'Earn points in all 4 assessment types.',
            'type' => 'academic',
        ],
    ];

    public static function getPoints(): array
    {
        return config('joms.gamification.points', [
            'topic_complete' => 10,
            'self_check_pass' => 25,
            'homework_submit' => 15,
            'perfect_score' => 50,
            'daily_login' => 5,
            'module_complete' => 100,
            'course_complete' => 500,
        ]);
    }

    /**
     * Get all badge definitions.
     */
    public static function getAllBadges(): array
    {
        return self::BADGES;
    }

    /**
     * Get a single badge definition by key.
     */
    public static function getBadge(string $key): ?array
    {
        return self::BADGES[$key] ?? null;
    }

    public function awardPoints(User $user, int $points, string $reason, $pointable = null): UserPoint
    {
        return DB::transaction(function () use ($user, $points, $reason, $pointable) {
            $userPoint = UserPoint::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => 'earned',
                'reason' => $reason,
                'pointable_type' => $pointable ? get_class($pointable) : null,
                'pointable_id' => $pointable?->id,
            ]);

            $user->increment('total_points', $points);
            $user->refresh();

            $this->checkBadgeUnlocks($user);

            return $userPoint;
        });
    }

    public function awardForActivity(User $user, string $activity, $pointable = null): ?UserPoint
    {
        $allPoints = self::getPoints();
        if (!isset($allPoints[$activity])) {
            return null;
        }

        $points = $allPoints[$activity];
        $reason = $this->getActivityReason($activity);

        return $this->awardPoints($user, $points, $reason, $pointable);
    }

    public function recordDailyLogin(User $user): void
    {
        $today = now()->toDateString();

        if ($user->last_activity_date === $today) {
            return;
        }

        DB::transaction(function () use ($user, $today) {
            $user = User::lockForUpdate()->find($user->id);

            if ($user->last_activity_date === $today) {
                return;
            }

            $yesterday = now()->subDay()->toDateString();
            if ($user->last_activity_date === $yesterday) {
                $user->current_streak = $user->current_streak + 1;
            } else {
                $user->current_streak = 1;
            }

            $user->last_activity_date = $today;
            $user->save();
        });

        $user->refresh();

        $this->awardForActivity($user, 'daily_login');
        $this->checkStreakBadges($user);
    }

    /**
     * Check if user qualifies for any hardcoded badges.
     */
    public function checkBadgeUnlocks(User $user): array
    {
        $earnedBadges = [];
        $earnedKeys = $user->earnedBadgeKeys();

        foreach (self::BADGES as $key => $definition) {
            if (in_array($key, $earnedKeys)) {
                continue;
            }

            if ($this->checkBadgeCriteria($user, $key)) {
                $this->awardBadgeByKey($user, $key);
                $earnedBadges[] = $key;
            }
        }

        return $earnedBadges;
    }

    /**
     * Check if user meets criteria for a specific badge key.
     */
    protected function checkBadgeCriteria(User $user, string $badgeKey): bool
    {
        switch ($badgeKey) {
            case 'first_steps':
                return $user->last_login !== null;

            case 'quick_starter':
                return $user->progress()
                    ->where('status', 'completed')
                    ->where('progressable_type', 'App\Models\Module')
                    ->count() >= 1;

            case 'quiz_ace':
                return $user->progress()
                    ->whereNotNull('score')
                    ->whereNotNull('max_score')
                    ->whereColumn('score', '>=', 'max_score')
                    ->where('progressable_type', 'App\Models\SelfCheck')
                    ->count() >= 1;

            case 'perfect_streak':
                return $user->progress()
                    ->whereNotNull('score')
                    ->whereNotNull('max_score')
                    ->whereColumn('score', '>=', 'max_score')
                    ->where('progressable_type', 'App\Models\SelfCheck')
                    ->count() >= 5;

            case 'homework_hero':
                return $user->progress()
                    ->where('progressable_type', 'App\Models\Homework')
                    ->count() >= 10;

            case 'week_warrior':
                return $user->current_streak >= 7;

            case 'monthly_master':
                return $user->current_streak >= 30;

            case 'course_graduate':
                return $user->progress()
                    ->where('status', 'completed')
                    ->where('progressable_type', 'App\Models\Course')
                    ->count() >= 1;

            case 'early_bird':
                // Check if any homework was submitted >= 3 days before due date
                return $user->homeworkSubmissions()
                    ->join('homeworks', 'homeworks.id', '=', 'homework_submissions.homework_id')
                    ->whereRaw('submitted_at <= DATE_SUB(due_date, INTERVAL 3 DAY)')
                    ->count() >= 1;

            case 'all_rounder':
                $types = [
                    'App\Models\SelfCheck',
                    'App\Models\Homework',
                    'App\Models\TaskSheet',
                    'App\Models\JobSheet',
                ];
                $completedTypes = $user->progress()
                    ->whereIn('progressable_type', $types)
                    ->distinct('progressable_type')
                    ->pluck('progressable_type')
                    ->toArray();
                return count($completedTypes) >= 4;

            default:
                return false;
        }
    }

    /**
     * Award a badge to a user by hardcoded key.
     */
    public function awardBadgeByKey(User $user, string $badgeKey): UserBadge
    {
        return UserBadge::firstOrCreate(
            [
                'user_id' => $user->id,
                'badge_key' => $badgeKey,
            ],
            [
                'earned_at' => now(),
                'metadata' => [
                    'total_points_at_earn' => $user->total_points,
                ],
            ]
        );
    }

    protected function checkStreakBadges(User $user): void
    {
        $streakKeys = ['week_warrior' => 7, 'monthly_master' => 30];
        $earnedKeys = $user->earnedBadgeKeys();

        foreach ($streakKeys as $key => $days) {
            if (!in_array($key, $earnedKeys) && $user->current_streak >= $days) {
                $this->awardBadgeByKey($user, $key);
            }
        }
    }

    public function getLeaderboard(int $limit = 10): \Illuminate\Support\Collection
    {
        return User::where('role', Roles::STUDENT)
            ->where('stat', 1)
            ->orderByDesc('total_points')
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'total_points', 'profile_image']);
    }

    public function getUserStats(User $user): array
    {
        return [
            'total_points' => $user->total_points,
            'current_streak' => $user->current_streak,
            'badges_earned' => $user->earnedBadgeKeys()->count(),
            'rank' => $this->getUserRank($user),
        ];
    }

    public function getUserRank(User $user): int
    {
        return User::where('role', Roles::STUDENT)
            ->where('stat', 1)
            ->where('total_points', '>', $user->total_points)
            ->count() + 1;
    }

    protected function getActivityReason(string $activity): string
    {
        return match ($activity) {
            'topic_complete' => 'Completed a topic',
            'self_check_pass' => 'Passed a self-check assessment',
            'homework_submit' => 'Submitted homework',
            'perfect_score' => 'Achieved a perfect score',
            'daily_login' => 'Daily login bonus',
            'module_complete' => 'Completed a module',
            'course_complete' => 'Completed a course',
            default => 'Activity completed',
        };
    }
}
