@extends('layouts.app')

@section('title', 'Discussion Forums')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-comments me-2"></i>Discussion Forums</h4>
    </div>

    {{-- Categories --}}
    <div class="row mb-4">
        @forelse($categories as $category)
        <div class="col-md-6 col-lg-4 mb-3">
            <a href="{{ route('forums.category', $category) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 forum-category-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 45px; height: 45px; background: {{ $category->color }}20;">
                                <i class="{{ $category->icon }}" style="color: {{ $category->color }}; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-dark">{{ $category->name }}</h6>
                                <small class="text-muted">{{ $category->threads_count }} {{ Str::plural('thread', $category->threads_count) }}</small>
                            </div>
                        </div>
                        @if($category->description)
                        <p class="text-muted small mb-0">{{ Str::limit($category->description, 80) }}</p>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No forum categories yet.</p>
                    @if(in_array(Auth::user()->role, ['admin', 'instructor']))
                    <p class="text-muted small">Create categories in the database to get started.</p>
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Recent Threads --}}
    @if($recentThreads->isNotEmpty())
    <h5 class="mb-3"><i class="fas fa-clock me-2 text-muted"></i>Recent Discussions</h5>
    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            @foreach($recentThreads as $thread)
            <a href="{{ route('forums.show', $thread) }}" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-start">
                        @if($thread->user->profile_image)
                        <img src="{{ $thread->user->profile_image_url }}" alt="" class="rounded-circle me-2 mt-1" width="28" height="28" style="object-fit: cover;">
                        @else
                        <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center me-2 mt-1" style="width: 28px; height: 28px;">
                            <small class="fw-bold text-muted" style="font-size: 0.65rem;">{{ substr($thread->user->first_name, 0, 1) }}{{ substr($thread->user->last_name, 0, 1) }}</small>
                        </div>
                        @endif
                        <div>
                            <div class="fw-semibold text-dark">
                                @if($thread->is_pinned)<i class="fas fa-thumbtack text-warning me-1" title="Pinned"></i>@endif
                                @if($thread->is_locked)<i class="fas fa-lock text-muted me-1" title="Locked"></i>@endif
                                {{ $thread->title }}
                            </div>
                            <small class="text-muted">
                                {{ $thread->user->first_name }} {{ $thread->user->last_name }}
                                <span class="badge rounded-pill" style="background: {{ $thread->category->color }}20; color: {{ $thread->category->color }};">{{ $thread->category->name }}</span>
                                &middot; {{ $thread->last_reply_at?->diffForHumans() ?? $thread->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    <div class="text-end text-nowrap">
                        <small class="text-muted"><i class="fas fa-comment me-1"></i>{{ $thread->replies_count }}</small>
                        <small class="text-muted ms-2"><i class="fas fa-eye me-1"></i>{{ $thread->views_count }}</small>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
.forum-category-card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
.forum-category-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; }
</style>
@endsection
