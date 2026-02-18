<?php

namespace Database\Factories;

use App\Models\ForumCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ForumCategoryFactory extends Factory
{
    protected $model = ForumCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'icon' => 'fas fa-comments',
            'color' => fake()->hexColor(),
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
            'is_announcement_category' => false,
            'admin_only_post' => false,
        ];
    }

    public function announcement(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_announcement_category' => true,
            'admin_only_post' => true,
        ]);
    }
}
