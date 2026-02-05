<?php

namespace App\Services;

use App\Constants\Roles;
use App\Models\User;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GamificationService
{
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

    public function awardPoints(User $user, int $points, string $reason, $pointable = null): UserPoint
    {
        $userPoint = UserPoint::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => 'earned',
            'reason' => $reason,
            'pointable_type' => $pointable ? get_class($pointable) : null,
            'pointable_id' => $pointable?->id,
        ]);

        $user->increment('total_points', $points);

        // Check for badge unlocks
        $this->checkBadgeUnlocks($user);

        return $userPoint;
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

        if ($user->last_activity_date !== $today) {
            // Update streak
            $yesterday = now()->subDay()->toDateString();
            if ($user->last_activity_date === $yesterday) {
                $user->increment('current_streak');
            } else {
                $user->current_streak = 1;
            }

            $user->last_activity_date = $today;
            $user->save();

            // Award daily login points
            $this->awardForActivity($user, 'daily_login');

            // Check streak badges
            $this->checkStreakBadges($user);
        }
    }

    public function checkBadgeUnlocks(User $user): array
    {
        $earnedBadges = [];
        $unlockedBadges = $user->badges()->pluck('badges.id')->toArray();

        $badges = Badge::active()
            ->whereNotIn('id', $unlockedBadges)
            ->get();

        foreach ($badges as $badge) {
            if ($this->checkBadgeCriteria($user, $badge)) {
                $this->awardBadge($user, $badge);
                $earnedBadges[] = $badge;
            }
        }

        return $earnedBadges;
    }

    protected function checkBadgeCriteria(User $user, Badge $badge): bool
    {
        // Check points-based badges
        if ($badge->points_required > 0 && $user->total_points >= $badge->points_required) {
            return true;
        }

        // Check custom criteria
        $criteria = $badge->criteria;
        if (!$criteria) {
            return false;
        }

        switch ($criteria['type'] ?? null) {
            case 'first_login':
                return $user->last_login !== null;

            case 'modules_completed':
                $count = $user->progress()
                    ->where('status', 'completed')
                    ->where('progressable_type', 'App\\Models\\Module')
                    ->count();
                return $count >= ($criteria['count'] ?? 1);

            case 'perfect_scores':
                $count = $user->progress()
                    ->whereNotNull('score')
                    ->whereColumn('score', '=', 'max_score')
                    ->count();
                return $count >= ($criteria['count'] ?? 1);

            case 'streak':
                return $user->current_streak >= ($criteria['days'] ?? 7);

            default:
                return false;
        }
    }

    public function awardBadge(User $user, Badge $badge): UserBadge
    {
        return UserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $badge->id,
            'earned_at' => now(),
            'metadata' => [
                'total_points_at_earn' => $user->total_points,
            ],
        ]);
    }

    protected function checkStreakBadges(User $user): void
    {
        $streakBadges = Badge::active()
            ->byType('streak')
            ->whereNotIn('id', $user->badges()->pluck('badges.id'))
            ->get();

        foreach ($streakBadges as $badge) {
            $requiredDays = $badge->criteria['days'] ?? 0;
            if ($user->current_streak >= $requiredDays) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    public function getLeaderboard(int $limit = 10): \Illuminate\Support\Collection
    {
        return User::where('role', Roles::STUDENT)
            ->where('stat', true)
            ->orderByDesc('total_points')
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'total_points', 'profile_image']);
    }

    public function getUserStats(User $user): array
    {
        return [
            'total_points' => $user->total_points,
            'current_streak' => $user->current_streak,
            'badges_earned' => $user->badges()->count(),
            'rank' => $this->getUserRank($user),
        ];
    }

    public function getUserRank(User $user): int
    {
        return User::where('role', Roles::STUDENT)
            ->where('stat', true)
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
