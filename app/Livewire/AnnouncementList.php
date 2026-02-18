<?php

namespace App\Livewire;

use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementList extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    private function getQuery()
    {
        $user = Auth::user();

        $query = Announcement::with(['user', 'comments.user'])
            ->forUser($user);

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function render()
    {
        $user = Auth::user();
        $canCreate = in_array($user->role, ['admin', 'instructor']);

        return view('livewire.announcement-list', [
            'announcements' => $this->getQuery()->paginate(10),
            'canCreate' => $canCreate,
        ]);
    }
}
