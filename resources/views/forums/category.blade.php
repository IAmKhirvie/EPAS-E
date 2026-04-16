@extends('layouts.app')

@section('title', $category->name . ' - Forums')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                </ol>
            </nav>
            <h4 class="mb-0"><i class="{{ $category->icon }} me-2" style="color: {{ $category->color }}"></i>{{ $category->name }}</h4>
            @if($category->description)
            <p class="text-muted small mb-0">{{ $category->description }}</p>
            @endif
        </div>
        <a href="{{ route('forums.create-thread', $category) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> New Thread
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        @if($threads->isEmpty())
        <div class="card-body text-center py-5">
            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
            <p class="text-muted">No threads yet. Be the first to start a discussion!</p>
            <a href="{{ route('forums.create-thread', $category) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Create Thread
            </a>
        </div>
        @else
        <div class="list-group list-group-flush">
            @foreach($threads as $thread)
            <a href="{{ route('forums.show', $thread) }}" class="list-group-item list-group-item-action py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-start">
                        @if($thread->user->profile_image)
                        <img src="{{ $thread->user->profile_image_url }}" alt="" class="rounded-circle me-3 mt-1" width="36" height="36" style="object-fit: cover;">
                        @else
                        <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center me-3 mt-1" style="width: 36px; height: 36px;">
                            <small class="fw-bold text-muted">{{ substr($thread->user->first_name, 0, 1) }}{{ substr($thread->user->last_name, 0, 1) }}</small>
                        </div>
                        @endif
                        <div>
                            <div class="fw-semibold text-dark">
                                @if($thread->is_pinned)<span class="badge bg-warning text-dark me-1"><i class="fas fa-thumbtack"></i> Pinned</span>@endif
                                @if($thread->is_locked)<span class="badge bg-secondary me-1"><i class="fas fa-lock"></i></span>@endif
                                {{ $thread->title }}
                            </div>
                            <small class="text-muted">
                                by {{ $thread->user->first_name }} {{ $thread->user->last_name }}
                                &middot; {{ $thread->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    <div class="text-end text-nowrap">
                        <div><small class="text-muted"><i class="fas fa-comment"></i> {{ $thread->posts_count }}</small></div>
                        <div><small class="text-muted"><i class="fas fa-eye"></i> {{ $thread->views_count }}</small></div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>

    @if($threads->hasPages())
    <div class="d-flex justify-content-center mt-4">{{ $threads->links() }}</div>
    @endif
</div>
@endsection
