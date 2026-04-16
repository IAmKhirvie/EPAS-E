<?php

namespace App\Services;

use App\Constants\Roles;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;

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
