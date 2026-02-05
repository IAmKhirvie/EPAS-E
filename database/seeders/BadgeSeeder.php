<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name' => 'First Steps',
                'slug' => 'first-steps',
                'description' => 'Awarded for your first login to the system.',
                'icon' => 'star',
                'color' => '#3b82f6',
                'type' => 'achievement',
                'points_required' => 0,
                'criteria' => ['type' => 'first_login'],
                'order' => 1,
            ],
            [
                'name' => 'Bronze Scholar',
                'slug' => 'bronze-scholar',
                'description' => 'Complete your first module.',
                'icon' => 'academic-cap',
                'color' => '#cd7f32',
                'type' => 'milestone',
                'points_required' => 0,
                'criteria' => ['type' => 'modules_completed', 'count' => 1],
                'order' => 2,
            ],
            [
                'name' => 'Silver Scholar',
                'slug' => 'silver-scholar',
                'description' => 'Complete 5 modules.',
                'icon' => 'academic-cap',
                'color' => '#c0c0c0',
                'type' => 'milestone',
                'points_required' => 0,
                'criteria' => ['type' => 'modules_completed', 'count' => 5],
                'order' => 3,
            ],
            [
                'name' => 'Gold Scholar',
                'slug' => 'gold-scholar',
                'description' => 'Complete 10 modules.',
                'icon' => 'academic-cap',
                'color' => '#ffd700',
                'type' => 'milestone',
                'points_required' => 0,
                'criteria' => ['type' => 'modules_completed', 'count' => 10],
                'order' => 4,
            ],
            [
                'name' => 'Perfect Score',
                'slug' => 'perfect-score',
                'description' => 'Achieve a perfect score on any assessment.',
                'icon' => 'trophy',
                'color' => '#10b981',
                'type' => 'achievement',
                'points_required' => 0,
                'criteria' => ['type' => 'perfect_scores', 'count' => 1],
                'order' => 5,
            ],
            [
                'name' => 'Perfectionist',
                'slug' => 'perfectionist',
                'description' => 'Achieve 5 perfect scores.',
                'icon' => 'trophy',
                'color' => '#059669',
                'type' => 'achievement',
                'points_required' => 0,
                'criteria' => ['type' => 'perfect_scores', 'count' => 5],
                'order' => 6,
            ],
            [
                'name' => 'Week Warrior',
                'slug' => 'week-warrior',
                'description' => 'Maintain a 7-day learning streak.',
                'icon' => 'fire',
                'color' => '#f59e0b',
                'type' => 'streak',
                'points_required' => 0,
                'criteria' => ['type' => 'streak', 'days' => 7],
                'order' => 7,
            ],
            [
                'name' => 'Month Master',
                'slug' => 'month-master',
                'description' => 'Maintain a 30-day learning streak.',
                'icon' => 'fire',
                'color' => '#ef4444',
                'type' => 'streak',
                'points_required' => 0,
                'criteria' => ['type' => 'streak', 'days' => 30],
                'order' => 8,
            ],
            [
                'name' => 'Point Collector',
                'slug' => 'point-collector-100',
                'description' => 'Earn 100 points.',
                'icon' => 'currency-dollar',
                'color' => '#8b5cf6',
                'type' => 'milestone',
                'points_required' => 100,
                'criteria' => null,
                'order' => 9,
            ],
            [
                'name' => 'Point Hunter',
                'slug' => 'point-hunter-500',
                'description' => 'Earn 500 points.',
                'icon' => 'currency-dollar',
                'color' => '#7c3aed',
                'type' => 'milestone',
                'points_required' => 500,
                'criteria' => null,
                'order' => 10,
            ],
            [
                'name' => 'Point Champion',
                'slug' => 'point-champion-1000',
                'description' => 'Earn 1000 points.',
                'icon' => 'currency-dollar',
                'color' => '#6d28d9',
                'type' => 'milestone',
                'points_required' => 1000,
                'criteria' => null,
                'order' => 11,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['slug' => $badge['slug']],
                $badge
            );
        }
    }
}
