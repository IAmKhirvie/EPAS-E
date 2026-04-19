<?php

namespace App\Services;

use App\Constants\Achievements;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserProgress;
use App\Models\Module;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AchievementService
{
    protected GamificationService $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Check and award achievements based on a trigger event.
     */
    public function checkAndAward(User $user, string $trigger): ?UserAchievement
    {
        $awarded = null;

        try {
            $awarded = match ($trigger) {
                'first_login' => $this->checkFirstLogin($user),
                'streak' => $this->checkStreaks($user),
                'perfect_score' => $this->checkPerfectScore($user),
                'module_complete' => $this->checkModuleMaster($user),
                'course_complete' => $this->checkCourseComplete($user),
                'leaderboard' => $this->checkTopTen($user),
                default => null,
            };
        } catch (\Exception $e) {
            Log::error("Achievement check failed for user {$user->id}, trigger: {$trigger}", [
                'error' => $e->getMessage(),
            ]);
        }

        return $awarded;
    }

    /**
     * Get all achievements for a user (earned + locked).
     */
    public function getUserAchievements(User $user): Collection
    {
        $earned = UserAchievement::where('user_id', $user->id)
            ->pluck('earned_at', 'achievement_key')
            ->toArray();

        return collect(Achievements::all())->map(function ($definition, $key) use ($earned) {
            return array_merge($definition, [
                'key' => $key,
                'earned' => isset($earned[$key]),
                'earned_at' => $earned[$key] ?? null,
            ]);
        })->values();
    }

    protected function award(User $user, string $key): ?UserAchievement
    {
        if (UserAchievement::where('user_id', $user->id)->where('achievement_key', $key)->exists()) {
            return null;
        }

        $definition = Achievements::get($key);
        if (!$definition) {
            return null;
        }

        $achievement = UserAchievement::create([
            'user_id' => $user->id,
            'achievement_key' => $key,
            'earned_at' => now(),
        ]);

        // Award bonus points for earning the achievement
        $this->gamificationService->awardPoints(
            $user,
            $definition['points'],
            "Achievement unlocked: {$definition['name']}",
            $achievement
        );

        return $achievement;
    }

    protected function checkFirstLogin(User $user): ?UserAchievement
    {
        return $this->award($user, 'FIRST_LOGIN');
    }

    protected function checkStreaks(User $user): ?UserAchievement
    {
        $awarded = null;

        if ($user->current_streak >= 7) {
            $awarded = $this->award($user, 'STREAK_7') ?? $awarded;
        }
        if ($user->current_streak >= 30) {
            $awarded = $this->award($user, 'STREAK_30') ?? $awarded;
        }

        return $awarded;
    }

    protected function checkPerfectScore(User $user): ?UserAchievement
    {
        return $this->award($user, 'PERFECT_SCORE');
    }

    protected function checkModuleMaster(User $user): ?UserAchievement
    {
        return $this->award($user, 'MODULE_MASTER');
    }

    protected function checkCourseComplete(User $user): ?UserAchievement
    {
        return $this->award($user, 'COURSE_COMPLETE');
    }

    protected function checkTopTen(User $user): ?UserAchievement
    {
        $rank = $this->gamificationService->getUserRank($user);
        if ($rank <= 10) {
            return $this->award($user, 'TOP_10');
        }
        return null;
    }

}
