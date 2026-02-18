<?php

namespace Database\Factories;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ForumThreadFactory extends Factory
{
    protected $model = ForumThread::class;

    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'category_id' => ForumCategory::factory(),
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'body' => fake()->paragraphs(3, true),
            'is_pinned' => false,
            'is_locked' => false,
            'is_announcement' => false,
            'target_roles' => 'all',
            'views_count' => fake()->numberBetween(0, 100),
            'replies_count' => 0,
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_locked' => true,
        ]);
    }

    public function announcement(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_announcement' => true,
        ]);
    }
}
