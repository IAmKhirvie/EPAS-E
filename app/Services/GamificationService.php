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
     * Badge tier constants (highest to lowest).
     */
    public const TIER_DIAMOND = 5;
    public const TIER_PLATINUM = 4;
    public const TIER_GOLD = 3;
    public const TIER_SILVER = 2;
    public const TIER_BRONZE = 1;

    /**
     * Tier display info.
     */
    public const TIERS = [
        self::TIER_BRONZE    => ['name' => 'Bronze',    'class' => 'bronze',    'color' => '#cd7f32', 'icon' => 'fa-medal'],
        self::TIER_SILVER    => ['name' => 'Silver',    'class' => 'silver',    'color' => '#c0c0c0', 'icon' => 'fa-medal'],
        self::TIER_GOLD      => ['name' => 'Gold',      'class' => 'gold',      'color' => '#ffd700', 'icon' => 'fa-medal'],
        self::TIER_PLATINUM  => ['name' => 'Platinum',  'class' => 'platinum',  'color' => '#e5e4e2', 'icon' => 'fa-gem'],
        self::TIER_DIAMOND   => ['name' => 'Diamond',   'class' => 'diamond',   'color' => '#b9f2ff', 'icon' => 'fa-crown'],
    ];

    /**
     * Hardcoded badge definitions with tiers.
     * Higher tiers require more effort/achievement.
     */
    public const BADGES = [
        'first_steps' => [
            'name' => 'First Steps',
            'icon' => 'fa-shoe-prints',
            'description' => 'Log in to EPAS-E for the first time.',
            'type' => 'milestone',
            'tiers' => [
                self::TIER_BRONZE => [
                    'criteria' => ['action' => 'login_count', 'value' => 1],
                ],
            ],
        ],
        'quick_starter' => [
            'name' => 'Quick Starter',
            'icon' => 'fa-rocket',
            'description' => 'Complete modules to show momentum.',
            'type' => 'milestone',
            'tiers' => [
                self::TIER_BRONZE   => ['criteria' => ['action' => 'modules_completed', 'value' => 1]],
                self::TIER_SILVER   => ['criteria' => ['action' => 'modules_completed', 'value' => 5]],
                self::TIER_GOLD     => ['criteria' => ['action' => 'modules_completed', 'value' => 10]],
                self::TIER_PLATINUM => ['criteria' => ['action' => 'modules_completed', 'value' => 25]],
                self::TIER_DIAMOND  => ['criteria' => ['action' => 'modules_completed', 'value' => 50]],
            ],
        ],
        'quiz_ace' => [
            'name' => 'Quiz Ace',
            'icon' => 'fa-brain',
            'description' => 'Score perfectly on self-check assessments.',
            'type' => 'academic',
            'tiers' => [
                self::TIER_BRONZE   => ['criteria' => ['action' => 'perfect_scores', 'value' => 1]],
                self::TIER_SILVER   => ['criteria' => ['action' => 'perfect_scores', 'value' => 5]],
                self::TIER_GOLD     => ['criteria' => ['action' => 'perfect_scores', 'value' => 15]],
                self::TIER_PLATINUM => ['criteria' => ['action' => 'perfect_scores', 'value' => 30]],
                self::TIER_DIAMOND  => ['criteria' => ['action' => 'perfect_scores', 'value' => 50]],
            ],
        ],
        'homework_hero' => [
            'name' => 'Homework Hero',
            'icon' => 'fa-book-open',
            'description' => 'Submit homework assignments consistently.',
            'type' => 'academic',
            'tiers' => [
                self::TIER_BRONZE   => ['criteria' => ['action' => 'homework_submitted', 'value' => 5]],
                self::TIER_SILVER   => ['criteria' => ['action' => 'homework_submitted', 'value' => 15]],
                self::TIER_GOLD     => ['criteria' => ['action' => 'homework_submitted', 'value' => 30]],
                self::TIER_PLATINUM => ['criteria' => ['action' => 'homework_submitted', 'value' => 50]],
                self::TIER_DIAMOND  => ['criteria' => ['action' => 'homework_submitted', 'value' => 100]],
            ],
        ],
        'week_warrior' => [
            'name' => 'Week Warrior',
            'icon' => 'fa-fire',
            'description' => 'Maintain a daily login streak.',
            'type' => 'streak',
            'tiers' => [
                self::TIER_BRONZE   => ['criteria' => ['action' => 'login_streak', 'value' => 7]],
                self::TIER_SILVER   => ['criteria' => ['action' => 'login_streak', 'value' => 14]],
                self::TIER_GOLD     => ['criteria' => ['action' => 'login_streak', 'value' => 30]],
                self::TIER_PLATINUM => ['criteria' => ['action' => 'login_streak', 'value' => 60]],
                self::TIER_DIAMOND  => ['criteria' => ['action' => 'login_streak', 'value' => 100]],
            ],
        ],
        'course_graduate' => [
            'name' => 'Course Graduate',
            'icon' => 'fa-graduation-cap',
            'description' => 'Complete entire courses.',
            'type' => 'milestone',
            'tiers' => [
                self::TIER_BRONZE   => ['criteria' => ['action' => 'courses_completed', 'value' => 1]],
                self::TIER_SILVER   => ['criteria' => ['action' => 'courses_completed', 'value' => 3]],
                self::TIER_GOLD     => ['criteria' => ['action' => 'courses_completed', 'value' => 5]],
                self::TIER_PLATINUM => ['criteria' => ['action' => 'courses_completed', 'value' => 10]],
                self::TIER_DIAMOND  => ['criteria' => ['action' => 'courses_completed', 'value' => 20]],
            ],
        ],
        'early_bird' => [
            'name' => 'Early Bird',
            'icon' => 'fa-dove',
            'description' => 'Submit homework well before the deadline.',
            'type' => 'milestone',
            'tiers' => [
                self::TIER_BRONZE   => ['criteria' => ['action' => 'hours_before_deadline', 'value' => 12]],
                self::TIER_SILVER   => ['criteria' => ['action' => 'hours_before_deadline', 'value' => 24]],
                self::TIER_GOLD     => ['criteria' => ['action' => 'hours_before_deadline', 'value' => 48]],
                self::TIER_PLATINUM => ['criteria' => ['action' => 'hours_before_deadline', 'value' => 72]],
                self::TIER_DIAMOND  => ['criteria' => ['action' => 'hours_before_deadline', 'value' => 120]],
            ],
        ],
        'all_rounder' => [
            'name' => 'All-Rounder',
            'icon' => 'fa-gem',
            'description' => 'Earn points across different assessment types.',
            'type' => 'academic',
            'tiers' => [
                self::TIER_BRONZE   => ['criteria' => ['action' => 'assessment_types', 'value' => 2]],
                self::TIER_SILVER   => ['criteria' => ['action' => 'assessment_types', 'value' => 3]],
                self::TIER_GOLD     => ['criteria' => ['action' => 'assessment_types', 'value' => 4]],
            ],
        ],
        'speed_demon' => [
            'name' => 'Speed Demon',
            'icon' => 'fa-bolt',
            'description' => 'Complete self-checks quickly with high scores.',
            'type' => 'academic',
            'tiers' => [
                self::TIER_BRONZE   => ['criteria' => ['action' => 'fast_perfect_score', 'value' => 3]],
                self::TIER_SILVER   => ['criteria' => ['action' => 'fast_perfect_score', 'value' => 10]],
                self::TIER_GOLD     => ['criteria' => ['action' => 'fast_perfect_score', 'value' => 25]],
            ],
        ],
        'consistency_king' => [
            'name' => 'Consistency King',
            'icon' => 'fa-chess-king',
            'description' => 'Log in and complete activities every week.',
            'type' => 'streak',
            'tiers' => [
                self::TIER_BRONZE   => ['criteria' => ['action' => 'weekly_active_weeks', 'value' => 4]],
                self::TIER_SILVER   => ['criteria' => ['action' => 'weekly_active_weeks', 'value' => 12]],
                self::TIER_GOLD     => ['criteria' => ['action' => 'weekly_active_weeks', 'value' => 26]],
                self::TIER_PLATINUM => ['criteria' => ['action' => 'weekly_active_weeks', 'value' => 52]],
            ],
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

    /**
     * Get tier info for a tier level.
     */
    public static function getTierInfo(int $tier): array
    {
        return self::TIERS[$tier] ?? self::TIERS[self::TIER_BRONZE];
    }

    /**
     * Get all tiers for a badge (from lowest to highest).
     */
    public static function getBadgeTiers(string $badgeKey): array
    {
        $badge = self::getBadge($badgeKey);
        if (!$badge) return [];

        $tiers = [];
        foreach ($badge['tiers'] as $tier => $data) {
            $tiers[$tier] = array_merge($data, self::getTierInfo($tier));
        }
        ksort($tiers);
        return $tiers;
    }

    /**
     * Format criteria for display.
     */
    public static function formatCriteria(array $criteria): string
    {
        $action = $criteria['action'] ?? '';
        $value = $criteria['value'] ?? 0;

        return match ($action) {
            'login_count' => "Log in {$value} time(s)",
            'modules_completed' => "Complete {$value} module(s)",
            'perfect_scores' => "Score perfectly on {$value} quiz(s)",
            'homework_submitted' => "Submit {$value} homework assignment(s)",
            'login_streak' => "Maintain a {$value}-day login streak",
            'courses_completed' => "Complete {$value} course(s)",
            'hours_before_deadline' => "Submit {$value} hour(s) before deadline",
            'assessment_types' => "Earn points in {$value} assessment type(s)",
            'fast_perfect_score' => "Complete {$value} perfect quiz(es) in under half the time",
            'weekly_active_weeks' => "Stay active for {$value} week(s)",
            default => "Achieve the requirement",
        };
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
     * Check if user qualifies for any badge tiers.
     */
    public function checkBadgeUnlocks(User $user): array
    {
        $earnedBadges = [];
        $earnedKeys = $user->earnedBadgeKeys();

        foreach (self::BADGES as $key => $definition) {
            foreach ($definition['tiers'] as $tier => $data) {
                $badgeKey = "{$key}_tier_{$tier}";
                if (in_array($badgeKey, $earnedKeys)) {
                    continue;
                }

                if ($this->checkBadgeCriteria($user, $key, $tier, $data['criteria'])) {
                    $this->awardBadgeByKey($user, $badgeKey);
                    $earnedBadges[] = $badgeKey;
                }
            }
        }

        return $earnedBadges;
    }

    /**
     * Check if user meets criteria for a specific badge tier.
     */
    protected function checkBadgeCriteria(User $user, string $badgeKey, int $tier, array $criteria): bool
    {
        $action = $criteria['action'] ?? '';
        $value = $criteria['value'] ?? 0;

        switch ($action) {
            case 'login_count':
                return $user->last_login !== null;

            case 'modules_completed':
                $count = $user->progress()
                    ->where('status', 'completed')
                    ->where('progressable_type', 'App\Models\Module')
                    ->count();
                return $count >= $value;

            case 'perfect_scores':
                $count = $user->progress()
                    ->whereNotNull('score')
                    ->whereNotNull('max_score')
                    ->whereColumn('score', '>=', 'max_score')
                    ->where('progressable_type', 'App\Models\SelfCheck')
                    ->count();
                return $count >= $value;

            case 'homework_submitted':
                return $user->homeworkSubmissions()->count() >= $value;

            case 'login_streak':
                return $user->current_streak >= $value;

            case 'courses_completed':
                $count = $user->progress()
                    ->where('status', 'completed')
                    ->where('progressable_type', 'App\Models\Course')
                    ->count();
                return $count >= $value;

            case 'hours_before_deadline':
                return $user->homeworkSubmissions()
                    ->join('homeworks', 'homeworks.id', '=', 'homework_submissions.homework_id')
                    ->whereRaw('TIMESTAMPDIFF(HOUR, submitted_at, due_date) >= ?', [$value])
                    ->count() >= 1;

            case 'assessment_types':
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
                return count($completedTypes) >= $value;

            case 'fast_perfect_score':
                // Perfect scores completed in under half the time limit
                return $user->progress()
                    ->whereNotNull('score')
                    ->whereNotNull('max_score')
                    ->whereColumn('score', '>=', 'max_score')
                    ->where('progressable_type', 'App\Models\SelfCheck')
                    ->whereNotNull('time_taken')
                    ->whereRaw('time_taken < (expected_time * 0.5)')
                    ->count() >= $value;

            case 'weekly_active_weeks':
                // Count distinct weeks where user was active
                $weeks = $user->points()
                    ->selectRaw('YEARWEEK(created_at) as week')
                    ->distinct()
                    ->count();
                return $weeks >= $value;

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
        $earnedKeys = $user->earnedBadgeKeys();

        foreach (['week_warrior'] as $badgeKey) {
            $badge = self::getBadge($badgeKey);
            if (!$badge) continue;

            foreach ($badge['tiers'] as $tier => $data) {
                $fullKey = "{$badgeKey}_tier_{$tier}";
                if (in_array($fullKey, $earnedKeys)) continue;

                if ($user->current_streak >= ($data['criteria']['value'] ?? 0)) {
                    $this->awardBadgeByKey($user, $fullKey);
                }
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
            'badges_earned' => count($user->earnedBadgeKeys()),
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
