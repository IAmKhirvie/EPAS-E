@extends('layouts.app')

@section('title', 'Forums & Announcements - JOMS LMS')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-1">Forums & Announcements</h1>
                    <p class="text-muted mb-0">Stay updated and connect with the community</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('forums.search') }}" method="GET" class="d-flex">
                        <input type="text" name="q" class="form-control" placeholder="Search..." value="{{ request('q') }}">
                        <button type="submit" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <a href="{{ route('forums.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Post
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Announcements Section --}}
    @if($announcements->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Recent Announcements</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($announcements as $announcement)
                            @php
                                $isRead = $announcement->isReadByUser(Auth::user());
                            @endphp
                            <a href="{{ route('forums.thread', $announcement) }}"
                               class="list-group-item list-group-item-action {{ !$isRead ? 'bg-light' : '' }}">
                                <div class="d-flex align-items-start">
                                    <div class="announcement-icon me-3" style="background-color: {{ $announcement->category->color ?? '#dc3545' }}20; color: {{ $announcement->category->color ?? '#dc3545' }}">
                                        <i class="{{ $announcement->category->icon ?? 'fas fa-bullhorn' }}"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 {{ !$isRead ? 'fw-bold' : '' }}">
                                                    @if($announcement->is_pinned)
                                                        <i class="fas fa-thumbtack text-warning me-1"></i>
                                                    @endif
                                                    @if($announcement->is_urgent)
                                                        <span class="badge bg-danger me-1">URGENT</span>
                                                    @endif
                                                    {{ $announcement->title }}
                                                </h6>
                                                <p class="mb-1 text-muted small">{{ Str::limit(strip_tags($announcement->body), 100) }}</p>
                                            </div>
                                            @if(!$isRead)
                                                <span class="badge bg-primary ms-2">New</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>{{ $announcement->user->full_name }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-folder me-1"></i>{{ $announcement->category->name }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                                            @if($announcement->deadline)
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-calendar-times me-1 text-warning"></i>
                                                Deadline: {{ $announcement->deadline->format('M j, Y') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            {{-- Announcement Categories --}}
            @if($announcementCategories->count() > 0)
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Announcement Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($announcementCategories as $category)
                            <a href="{{ route('forums.category', $category) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div class="forum-category-icon me-3" style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                                        <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $category->name }}</h6>
                                        <p class="mb-0 text-muted small">{{ $category->description }}</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-secondary">{{ $category->threads_count }} posts</span>
                                        @if($category->admin_only_post)
                                            <br><small class="text-muted"><i class="fas fa-lock"></i> Staff only</small>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Discussion Categories --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Discussion Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($discussionCategories as $category)
                            <a href="{{ route('forums.category', $category) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div class="forum-category-icon me-3" style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                                        <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $category->name }}</h6>
                                        <p class="mb-0 text-muted small">{{ $category->description }}</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-secondary">{{ $category->threads_count }} threads</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <p class="mb-0 text-muted">No discussion categories available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Recent Discussions --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Discussions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentThreads as $thread)
                            <a href="{{ route('forums.thread', $thread) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <img src="{{ $thread->user->profile_image_url }}" alt="{{ $thread->user->full_name }}" class="rounded-circle me-2" width="32" height="32" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($thread->user->initials) }}&background=007fc9&color=fff&size=32'">
                                    <div class="flex-grow-1 min-width-0">
                                        <h6 class="mb-1 text-truncate">
                                            @if($thread->is_pinned)
                                                <i class="fas fa-thumbtack text-warning me-1"></i>
                                            @endif
                                            @if($thread->is_locked)
                                                <i class="fas fa-lock text-secondary me-1"></i>
                                            @endif
                                            {{ $thread->title }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $thread->user->full_name }} &middot; {{ $thread->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <p class="mb-0 text-muted">No recent discussions</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Forum Stats --}}
            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Forum Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Categories</span>
                        <strong>{{ $categories->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Threads</span>
                        <strong>{{ \App\Models\ForumThread::count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Posts</span>
                        <strong>{{ \App\Models\ForumPost::count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Active Users</span>
                        <strong>{{ \App\Models\User::where('stat', true)->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.forum-category-icon,
.announcement-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.min-width-0 {
    min-width: 0;
}
</style>
@endsection
