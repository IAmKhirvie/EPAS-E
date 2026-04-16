<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General Discussion',
                'slug' => 'general-discussion',
                'description' => 'Chat about anything related to EPAS and electronics.',
                'icon' => 'fas fa-comments',
                'color' => '#0c3a2d',
                'order' => 1,
            ],
            [
                'name' => 'Electronics Q&A',
                'slug' => 'electronics-qa',
                'description' => 'Ask questions about electronic components, circuits, and theory.',
                'icon' => 'fas fa-microchip',
                'color' => '#2563eb',
                'order' => 2,
            ],
            [
                'name' => 'Practical Skills',
                'slug' => 'practical-skills',
                'description' => 'Discuss soldering, PCB making, assembly techniques, and troubleshooting.',
                'icon' => 'fas fa-tools',
                'color' => '#d97706',
                'order' => 3,
            ],
            [
                'name' => 'Study Tips & Resources',
                'slug' => 'study-tips',
                'description' => 'Share study strategies, resources, and preparation tips for NC II.',
                'icon' => 'fas fa-lightbulb',
                'color' => '#7c3aed',
                'order' => 4,
            ],
            [
                'name' => 'Help & Support',
                'slug' => 'help-support',
                'description' => 'Get help with the EPAS-E platform, account issues, or technical problems.',
                'icon' => 'fas fa-question-circle',
                'color' => '#dc2626',
                'order' => 5,
            ],
        ];

        foreach ($categories as $cat) {
            ForumCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
