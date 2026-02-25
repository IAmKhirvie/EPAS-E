<?php

namespace App\Livewire;

use App\Constants\Roles;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ForumList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $categoryFilter = '';
    public string $typeFilter = ''; // 'announcements', 'discussions', ''
    public array $selectedThreads = [];
    public bool $selectAll = false;

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

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedThreads = $value
            ? $this->getQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    public function bulkLock(): void
    {
        if (Auth::user()->role !== Roles::ADMIN) {
            session()->flash('error', 'Only administrators can lock threads.');
            return;
        }

        $count = ForumThread::whereIn('id', $this->selectedThreads)
            ->where('is_locked', false)
            ->update(['is_locked' => true]);

        $this->selectedThreads = [];
        $this->selectAll = false;
        session()->flash('success', "{$count} thread(s) locked.");
    }

    public function bulkDelete(): void
    {
        if (Auth::user()->role !== Roles::ADMIN) {
            session()->flash('error', 'Only administrators can delete threads.');
            return;
        }

        $count = 0;
        $threads = ForumThread::whereIn('id', $this->selectedThreads)->get();
        foreach ($threads as $thread) {
            $thread->delete();
            $count++;
        }

        $this->selectedThreads = [];
        $this->selectAll = false;
        session()->flash('success', "{$count} thread(s) deleted.");
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

        $isAdmin = $user->role === Roles::ADMIN;

        return view('livewire.forum-list', [
            'threads' => $this->getQuery()->paginate(20),
            'categories' => $categories,
            'canCreateThread' => $canCreateThread,
            'isAdmin' => $isAdmin,
        ]);
    }
}
