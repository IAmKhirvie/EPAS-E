<?php

namespace App\Livewire;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ForumList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    public string $typeFilter = ''; // 'announcements', 'discussions', ''

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => '', 'as' => 'category'],
        'typeFilter' => ['except' => '', 'as' => 'type'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    private function getQuery()
    {
        $user = Auth::user();

        $query = ForumThread::with(['user', 'category'])
            ->withCount('posts')
            ->forUser($user)
            ->published();

        // Category filter
        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        // Type filter
        if ($this->typeFilter === 'announcements') {
            $query->fromAnnouncementCategories();
        } elseif ($this->typeFilter === 'discussions') {
            $query->whereHas('category', fn ($q) => $q->where('is_announcement_category', false));
        }

        // Search
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('is_pinned', 'desc')
            ->orderBy('last_reply_at', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function render()
    {
        $user = Auth::user();

        $categories = ForumCategory::active()
            ->ordered()
            ->withCount('threads')
            ->get();

        $canCreateThread = true; // All authenticated users can create threads

        return view('livewire.forum-list', [
            'threads' => $this->getQuery()->paginate(20),
            'categories' => $categories,
            'canCreateThread' => $canCreateThread,
        ]);
    }
}
