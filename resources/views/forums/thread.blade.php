@extends('layouts.app')

@section('title', $thread->title . ' - Forums - JOMS LMS')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.category', $thread->category) }}">{{ $thread->category->name }}</a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 200px;">{{ $thread->title }}</li>
        </ol>
    </nav>

    <!-- Thread Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($thread->is_pinned)
                            <span class="badge bg-primary"><i class="fas fa-thumbtack"></i> Pinned</span>
                        @endif
                        @if($thread->is_locked)
                            <span class="badge bg-secondary"><i class="fas fa-lock"></i> Locked</span>
                        @endif
                        @if($thread->is_resolved)
                            <span class="badge bg-success"><i class="fas fa-check"></i> Resolved</span>
                        @endif
                    </div>
                    <h1 class="h3 mb-2">{{ $thread->title }}</h1>
                    <div class="d-flex align-items-center gap-3 text-muted">
                        <span><i class="fas fa-eye me-1"></i>{{ number_format($thread->views_count) }} views</span>
                        <span><i class="fas fa-comment me-1"></i>{{ $thread->replies_count }} replies</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if($isSubscribed)
                        <form action="{{ route('forums.unsubscribe', $thread) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-bell-slash me-1"></i>Unsubscribe
                            </button>
                        </form>
                    @else
                        <form action="{{ route('forums.subscribe', $thread) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-bell me-1"></i>Subscribe
                            </button>
                        </form>
                    @endif
                    @if(auth()->user()->isAdmin() || auth()->user()->isInstructor())
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('forums.toggle-pin', $thread) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-thumbtack me-2"></i>{{ $thread->is_pinned ? 'Unpin' : 'Pin' }} Thread
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('forums.toggle-lock', $thread) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-lock me-2"></i>{{ $thread->is_locked ? 'Unlock' : 'Lock' }} Thread
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Original Post -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex">
                <img src="{{ $thread->user->profile_image_url }}" alt="" class="rounded-circle me-3" width="48" height="48">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $thread->user->full_name }}</strong>
                            <span class="badge bg-light text-dark ms-2">{{ ucfirst($thread->user->role) }}</span>
                            <br>
                            <small class="text-muted">{{ $thread->created_at->format('M j, Y \a\t g:i A') }}</small>
                        </div>
                    </div>
                    <div class="thread-content">
                        {!! $thread->body !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Replies -->
    <h5 class="mb-3"><i class="fas fa-comments me-2"></i>Replies ({{ $thread->replies_count }})</h5>

    @forelse($posts as $post)
        <div class="card mb-3 {{ $post->is_best_answer ? 'border-success' : '' }}" id="post-{{ $post->id }}">
            @if($post->is_best_answer)
                <div class="card-header bg-success text-white py-2">
                    <i class="fas fa-check-circle me-2"></i>Best Answer
                </div>
            @endif
            <div class="card-body">
                <div class="d-flex">
                    <img src="{{ $post->user->profile_image_url }}" alt="" class="rounded-circle me-3" width="40" height="40">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $post->user->full_name }}</strong>
                                <span class="badge bg-light text-dark ms-2">{{ ucfirst($post->user->role) }}</span>
                                <br>
                                <small class="text-muted">{{ $post->created_at->format('M j, Y \a\t g:i A') }}</small>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <!-- Voting -->
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('forums.vote', $post) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="vote_type" value="up">
                                        <button type="submit" class="btn btn-outline-success {{ $post->hasVotedBy(auth()->user()) === 'up' ? 'active' : '' }}">
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                    </form>
                                    <span class="btn btn-outline-secondary disabled">{{ $post->score }}</span>
                                    <form action="{{ route('forums.vote', $post) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="vote_type" value="down">
                                        <button type="submit" class="btn btn-outline-danger {{ $post->hasVotedBy(auth()->user()) === 'down' ? 'active' : '' }}">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Actions Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($thread->user_id === auth()->id() && !$post->is_best_answer)
                                            <li>
                                                <form action="{{ route('forums.best-answer', $post) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-check me-2"></i>Mark as Best Answer
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if($post->user_id === auth()->id() || auth()->user()->isAdmin())
                                            <li>
                                                <form action="{{ route('forums.delete-post', $post) }}" method="POST" onsubmit="return confirm('Delete this post?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="post-content">
                            {!! $post->body !!}
                        </div>

                        <!-- Nested Replies -->
                        @if($post->replies->count() > 0)
                            <div class="nested-replies mt-3 ps-4 border-start">
                                @foreach($post->replies as $reply)
                                    <div class="d-flex mb-3" id="post-{{ $reply->id }}">
                                        <img src="{{ $reply->user->profile_image_url }}" alt="" class="rounded-circle me-2" width="32" height="32">
                                        <div class="flex-grow-1">
                                            <div class="mb-1">
                                                <strong>{{ $reply->user->full_name }}</strong>
                                                <small class="text-muted ms-2">{{ $reply->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="reply-content small">
                                                {!! $reply->body !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <p class="mb-0 text-muted">No replies yet. Be the first to reply!</p>
            </div>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $posts->links() }}
    </div>

    <!-- Reply Form -->
    @if(!$thread->is_locked)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-reply me-2"></i>Post a Reply</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('forums.reply', $thread) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="body" class="form-control" rows="4" placeholder="Write your reply..." required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Post Reply
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-warning mt-4">
            <i class="fas fa-lock me-2"></i>This thread is locked. No new replies can be posted.
        </div>
    @endif
</div>

<style>
.thread-content, .post-content, .reply-content {
    line-height: 1.7;
}

.nested-replies {
    border-color: #e9ecef !important;
}
</style>
@endsection
