<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Announcement Categories (admin/instructor only can post)
            [
                'name' => 'Announcements',
                'slug' => 'announcements',
                'description' => 'Official announcements from instructors and administrators',
                'icon' => 'fas fa-bullhorn',
                'color' => '#dc3545',
                'order' => 1,
                'is_active' => true,
                'is_announcement_category' => true,
                'admin_only_post' => true,
            ],
            [
                'name' => 'New Module Updates',
                'slug' => 'new-module-updates',
                'description' => 'Notifications about new modules, lessons, and content updates',
                'icon' => 'fas fa-book-open',
                'color' => '#0d6efd',
                'order' => 2,
                'is_active' => true,
                'is_announcement_category' => true,
                'admin_only_post' => true,
            ],
            [
                'name' => 'Schedule & Deadlines',
                'slug' => 'schedule-deadlines',
                'description' => 'Important dates, deadlines, and schedule changes',
                'icon' => 'fas fa-calendar-alt',
                'color' => '#fd7e14',
                'order' => 3,
                'is_active' => true,
                'is_announcement_category' => true,
                'admin_only_post' => true,
            ],

            // Discussion Categories (everyone can post)
            [
                'name' => 'General Discussion',
                'slug' => 'general-discussion',
                'description' => 'General topics and conversations',
                'icon' => 'fas fa-comments',
                'color' => '#6c757d',
                'order' => 4,
                'is_active' => true,
                'is_announcement_category' => false,
                'admin_only_post' => false,
            ],
            [
                'name' => 'Q&A / Help',
                'slug' => 'qa-help',
                'description' => 'Ask questions and get help from the community',
                'icon' => 'fas fa-question-circle',
                'color' => '#198754',
                'order' => 5,
                'is_active' => true,
                'is_announcement_category' => false,
                'admin_only_post' => false,
            ],
            [
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'description' => 'Technical issues, troubleshooting, and system help',
                'icon' => 'fas fa-tools',
                'color' => '#0dcaf0',
                'order' => 6,
                'is_active' => true,
                'is_announcement_category' => false,
                'admin_only_post' => false,
            ],
            [
                'name' => 'Study Resources',
                'slug' => 'study-resources',
                'description' => 'Share study materials, tips, and resources',
                'icon' => 'fas fa-lightbulb',
                'color' => '#ffc107',
                'order' => 7,
                'is_active' => true,
                'is_announcement_category' => false,
                'admin_only_post' => false,
            ],
        ];

        foreach ($categories as $category) {
            ForumCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
