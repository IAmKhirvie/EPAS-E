@extends('layouts.app')

@section('title', 'Search Results - Forums')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-1">Search Results</h1>
                    <p class="text-muted mb-0">
                        @if($threads->total() > 0)
                            Found {{ $threads->total() }} result(s) for "{{ $query }}"
                        @else
                            No results found for "{{ $query }}"
                        @endif
                    </p>
                </div>
                <a href="{{ route('forums.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Forums
                </a>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('forums.search') }}" method="GET" class="d-flex">
                <input type="text" name="q" class="form-control" placeholder="Search threads..." value="{{ $query }}">
                <button type="submit" class="btn btn-primary ms-2">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </form>
        </div>
    </div>

    <!-- Results -->
    @if($threads->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @foreach($threads as $thread)
                    <a href="{{ route('forums.thread', $thread) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex">
                            <img src="{{ $thread->user->profile_image_url }}" alt="{{ $thread->user->full_name }}"
                                 class="rounded-circle me-3" width="48" height="48"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($thread->user->initials) }}&background=007fc9&color=fff&size=48'">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            @if($thread->is_pinned)
                                                <i class="fas fa-thumbtack text-warning me-1"></i>
                                            @endif
                                            @if($thread->is_locked)
                                                <i class="fas fa-lock text-secondary me-1"></i>
                                            @endif
                                            @if($thread->is_urgent ?? false)
                                                <span class="badge bg-danger me-1">URGENT</span>
                                            @endif
                                            {{ $thread->title }}
                                        </h6>
                                        <p class="mb-1 text-muted small">{{ Str::limit(strip_tags($thread->body), 150) }}</p>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge" style="background-color: {{ $thread->category->color ?? '#6c757d' }}">
                                            {{ $thread->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>{{ $thread->user->full_name }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-eye me-1"></i>{{ $thread->views_count }} views
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-comments me-1"></i>{{ $thread->replies_count }} replies
                                </small>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @if($threads->hasPages())
        <div class="card-footer">
            {{ $threads->appends(['q' => $query])->links() }}
        </div>
        @endif
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5>No Results Found</h5>
            <p class="text-muted">Try different keywords or check your spelling.</p>
            <a href="{{ route('forums.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Forums
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
