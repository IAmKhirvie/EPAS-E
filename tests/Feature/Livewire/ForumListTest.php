<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ForumList;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ForumListTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ForumList::class)
            ->assertStatus(200)
            ->assertSee('Forums');
    }

    public function test_shows_live_badge(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ForumList::class)
            ->assertSee('Live');
    }

    public function test_search_filters_threads(): void
    {
        $user = User::factory()->create();
        $category = ForumCategory::factory()->create();

        ForumThread::factory()->create([
            'title' => 'Unique Thread Title',
            'category_id' => $category->id,
            'user_id' => $user->id,
            'target_roles' => 'all',
        ]);
        ForumThread::factory()->create([
            'title' => 'Other Thread',
            'category_id' => $category->id,
            'user_id' => $user->id,
            'target_roles' => 'all',
        ]);

        Livewire::actingAs($user)
            ->test(ForumList::class)
            ->set('search', 'Unique Thread')
            ->assertSee('Unique Thread Title')
            ->assertDontSee('Other Thread');
    }

    public function test_category_filter_works(): void
    {
        $user = User::factory()->create();
        $cat1 = ForumCategory::factory()->create(['name' => 'Cat1']);
        $cat2 = ForumCategory::factory()->create(['name' => 'Cat2']);

        ForumThread::factory()->create([
            'title' => 'Cat1 Thread',
            'category_id' => $cat1->id,
            'user_id' => $user->id,
            'target_roles' => 'all',
        ]);
        ForumThread::factory()->create([
            'title' => 'Cat2 Thread',
            'category_id' => $cat2->id,
            'user_id' => $user->id,
            'target_roles' => 'all',
        ]);

        Livewire::actingAs($user)
            ->test(ForumList::class)
            ->set('categoryFilter', (string) $cat1->id)
            ->assertSee('Cat1 Thread')
            ->assertDontSee('Cat2 Thread');
    }

    public function test_type_filter_for_announcements(): void
    {
        $user = User::factory()->create();
        $announcementCat = ForumCategory::factory()->announcement()->create();
        $discussionCat = ForumCategory::factory()->create();

        ForumThread::factory()->create([
            'title' => 'Announcement Thread',
            'category_id' => $announcementCat->id,
            'user_id' => $user->id,
            'target_roles' => 'all',
        ]);
        ForumThread::factory()->create([
            'title' => 'Discussion Thread',
            'category_id' => $discussionCat->id,
            'user_id' => $user->id,
            'target_roles' => 'all',
        ]);

        Livewire::actingAs($user)
            ->test(ForumList::class)
            ->set('typeFilter', 'announcements')
            ->assertSee('Announcement Thread')
            ->assertDontSee('Discussion Thread');
    }
}
