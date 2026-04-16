@extends('layouts.app')

@section('title', $thread->title . ' - Forums')

@section('content')
<div class="content-area">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.category', $thread->category) }}">{{ $thread->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($thread->title, 40) }}</li>
        </ol>
    </nav>

    {{-- Thread Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex">
                    @if($thread->user->profile_image)
                    <img src="{{ $thread->user->profile_image_url }}" alt="" class="rounded-circle me-3" width="48" height="48" style="object-fit: cover;">
                    @else
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                        <span class="fw-bold text-primary">{{ substr($thread->user->first_name, 0, 1) }}{{ substr($thread->user->last_name, 0, 1) }}</span>
                    </div>
                    @endif
                    <div>
                        <h5 class="mb-1">
                            @if($thread->is_pinned)<span class="badge bg-warning text-dark me-1"><i class="fas fa-thumbtack"></i></span>@endif
                            @if($thread->is_locked)<span class="badge bg-secondary me-1"><i class="fas fa-lock"></i></span>@endif
                            {{ $thread->title }}
                        </h5>
                        <small class="text-muted">
                            {{ $thread->user->first_name }} {{ $thread->user->last_name }}
                            <span class="badge bg-light text-muted">{{ ucfirst($thread->user->role) }}</span>
                            &middot; {{ $thread->created_at->format('M d, Y g:i A') }}
                            &middot; <i class="fas fa-eye"></i> {{ $thread->views_count }} views
                        </small>
                    </div>
                </div>
                @if(in_array(Auth::user()->role, ['admin', 'instructor']))
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('forums.pin', $thread) }}" method="POST">
                                @csrf
                                <button class="dropdown-item"><i class="fas fa-thumbtack me-2"></i>{{ $thread->is_pinned ? 'Unpin' : 'Pin' }}</button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('forums.lock', $thread) }}" method="POST">
                                @csrf
                                <button class="dropdown-item"><i class="fas fa-lock me-2"></i>{{ $thread->is_locked ? 'Unlock' : 'Lock' }}</button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('forums.delete-thread', $thread) }}" method="POST" onsubmit="return confirm('Delete this thread?')">
                                @csrf @method('DELETE')
                                <button class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Delete</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endif
            </div>
            <div class="mt-3 basic-formatting">{!! nl2br(e($thread->body)) !!}</div>
        </div>
    </div>

    {{-- Replies --}}
    <h6 class="mb-3"><i class="fas fa-comments me-1"></i>{{ $thread->replies_count }} {{ Str::plural('Reply', $thread->replies_count) }}</h6>

    @foreach($posts as $post)
    <div class="card border-0 shadow-sm mb-3 {{ $post->is_best_answer ? 'border-start border-success border-3' : '' }}" id="post-{{ $post->id }}">
        <div class="card-body">
            @if($post->is_best_answer)
            <div class="badge bg-success mb-2"><i class="fas fa-check-circle me-1"></i>Best Answer</div>
            @endif
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-start">
                    @if($post->user->profile_image)
                    <img src="{{ $post->user->profile_image_url }}" alt="" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                    @else
                    <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <small class="fw-bold text-muted">{{ substr($post->user->first_name, 0, 1) }}{{ substr($post->user->last_name, 0, 1) }}</small>
                    </div>
                    @endif
                    <div>
                        <strong>{{ $post->user->first_name }} {{ $post->user->last_name }}</strong>
                        <span class="badge bg-light text-muted ms-1">{{ ucfirst($post->user->role) }}</span>
                        <small class="text-muted ms-2">{{ $post->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    {{-- Voting --}}
                    @php $userVote = $post->getUserVote(Auth::id()); @endphp
                    <form action="{{ route('forums.vote', $post) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="value" value="1">
                        <button type="submit" class="btn btn-sm {{ $userVote === 1 ? 'btn-success' : 'btn-outline-secondary' }} py-0 px-1">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                    </form>
                    <span class="fw-bold {{ $post->votes_count > 0 ? 'text-success' : ($post->votes_count < 0 ? 'text-danger' : 'text-muted') }}">{{ $post->votes_count }}</span>
                    <form action="{{ route('forums.vote', $post) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="value" value="-1">
                        <button type="submit" class="btn btn-sm {{ $userVote === -1 ? 'btn-danger' : 'btn-outline-secondary' }} py-0 px-1">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                    </form>

                    {{-- Best Answer (thread author or admin/instructor) --}}
                    @if(!$post->is_best_answer && (Auth::id() === $thread->user_id || in_array(Auth::user()->role, ['admin', 'instructor'])))
                    <form action="{{ route('forums.best-answer', [$thread, $post]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success py-0 px-1" title="Mark as best answer">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="mt-2 basic-formatting">{!! nl2br(e($post->body)) !!}</div>

            {{-- Nested Replies --}}
            @if($post->replies->isNotEmpty())
            <div class="ms-4 mt-3 ps-3 border-start">
                @foreach($post->replies as $reply)
                <div class="mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-center mb-1">
                        <strong class="small">{{ $reply->user->first_name }} {{ $reply->user->last_name }}</strong>
                        <small class="text-muted ms-2">{{ $reply->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="small">{!! nl2br(e($reply->body)) !!}</div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Reply to this post --}}
            @if(!$thread->is_locked)
            <div class="mt-2">
                <button class="btn btn-sm btn-link text-muted p-0" onclick="document.getElementById('reply-form-{{ $post->id }}').classList.toggle('d-none')">
                    <i class="fas fa-reply me-1"></i>Reply
                </button>
                <form action="{{ route('forums.reply', $thread) }}" method="POST" class="mt-2 d-none" id="reply-form-{{ $post->id }}">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $post->id }}">
                    <div class="input-group input-group-sm">
                        <input type="text" name="body" class="form-control" placeholder="Write a reply..." required minlength="3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    @if($posts->hasPages())
    <div class="d-flex justify-content-center mt-3">{{ $posts->links() }}</div>
    @endif

    {{-- Reply Form --}}
    @if(!$thread->is_locked)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent">
            <h6 class="mb-0"><i class="fas fa-reply me-1"></i>Post a Reply</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('forums.reply', $thread) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="body" class="form-control" rows="4" placeholder="Write your reply..." required minlength="3">{{ old('body') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-paper-plane me-1"></i>Post Reply
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-secondary text-center mt-4">
        <i class="fas fa-lock me-1"></i>This thread is locked. No new replies can be posted.
    </div>
    @endif
</div>
@endsection
