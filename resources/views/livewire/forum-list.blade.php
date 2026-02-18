<div wire:poll.30s>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div class="d-flex align-items-center gap-2">
            <h4 class="mb-0">Forums</h4>
            <span class="badge bg-success" title="Auto-refreshes every 30 seconds">
                <i class="fas fa-circle fa-xs me-1"></i> Live
            </span>
        </div>
        @if($canCreateThread)
            <a href="{{ route('forums.create-thread') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> New Thread
            </a>
        @endif
    </div>

    {{-- Search & Filters --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <div class="flex-grow-1" style="min-width: 200px;">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm"
                placeholder="Search threads...">
        </div>
        <select wire:model.live="categoryFilter" class="form-select form-select-sm" style="max-width: 180px;">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->threads_count }})</option>
            @endforeach
        </select>
        <select wire:model.live="typeFilter" class="form-select form-select-sm" style="max-width: 160px;">
            <option value="">All Types</option>
            <option value="announcements">Announcements</option>
            <option value="discussions">Discussions</option>
        </select>
    </div>

    {{-- Loading --}}
    <div wire:loading class="text-center py-2">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    {{-- Thread List --}}
    <div wire:loading.class="opacity-50">
        @forelse($threads as $thread)
            <div class="card mb-2 {{ $thread->is_pinned ? 'border-warning' : '' }} {{ $thread->is_locked ? 'opacity-75' : '' }}">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                @if($thread->is_pinned)
                                    <i class="fas fa-thumbtack text-warning" title="Pinned"></i>
                                @endif
                                @if($thread->is_locked)
                                    <i class="fas fa-lock text-muted" title="Locked"></i>
                                @endif
                                @if($thread->is_urgent)
                                    <span class="badge bg-danger">Urgent</span>
                                @endif
                                @if($thread->is_announcement)
                                    <span class="badge bg-info">Announcement</span>
                                @endif
                                <a href="{{ route('forums.thread', $thread) }}" class="text-decoration-none fw-medium">
                                    {{ $thread->title }}
                                </a>
                            </div>
                            <small class="text-muted">
                                <span class="badge bg-light text-dark">{{ $thread->category?->name }}</span>
                                <span class="mx-1">&bull;</span>
                                {{ $thread->user?->full_name }}
                                <span class="mx-1">&bull;</span>
                                {{ $thread->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="d-flex gap-3 text-muted small text-end">
                            <div title="Replies">
                                <i class="fas fa-comment"></i> {{ $thread->posts_count ?? 0 }}
                            </div>
                            <div title="Views">
                                <i class="fas fa-eye"></i> {{ $thread->views_count ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-comments fa-3x mb-3 opacity-50"></i>
                <p>No threads found.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($threads->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">
                Showing {{ $threads->firstItem() ?? 0 }}-{{ $threads->lastItem() ?? 0 }} of {{ $threads->total() }}
            </small>
            {{ $threads->links() }}
        </div>
    @endif
</div>
