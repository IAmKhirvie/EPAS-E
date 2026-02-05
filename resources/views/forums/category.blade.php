@extends('layouts.app')

@section('title', $category->name . ' - Forums - JOMS LMS')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="{{ $category->icon }}" style="color: {{ $category->color }}"></i>
                        {{ $category->name }}
                    </h1>
                    <p class="text-muted mb-0">{{ $category->description }}</p>
                </div>
                <a href="{{ route('forums.create', ['category' => $category->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Thread
                </a>
            </div>
        </div>
    </div>

    <!-- Threads List -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60%">Thread</th>
                            <th class="text-center d-none d-md-table-cell">Replies</th>
                            <th class="text-center d-none d-md-table-cell">Views</th>
                            <th class="d-none d-lg-table-cell">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($threads as $thread)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-start">
                                        <img src="{{ $thread->user->profile_image_url }}" alt="" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                @if($thread->is_pinned)
                                                    <span class="badge bg-primary"><i class="fas fa-thumbtack"></i></span>
                                                @endif
                                                @if($thread->is_locked)
                                                    <span class="badge bg-secondary"><i class="fas fa-lock"></i></span>
                                                @endif
                                                @if($thread->is_resolved)
                                                    <span class="badge bg-success"><i class="fas fa-check"></i> Resolved</span>
                                                @endif
                                                <a href="{{ route('forums.thread', $thread) }}" class="text-decoration-none fw-semibold">
                                                    {{ $thread->title }}
                                                </a>
                                            </div>
                                            <small class="text-muted">
                                                by {{ $thread->user->full_name }} &middot; {{ $thread->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle d-none d-md-table-cell">
                                    <span class="badge bg-light text-dark">{{ $thread->replies_count }}</span>
                                </td>
                                <td class="text-center align-middle d-none d-md-table-cell">
                                    <span class="text-muted">{{ number_format($thread->views_count) }}</span>
                                </td>
                                <td class="align-middle d-none d-lg-table-cell">
                                    @if($thread->last_reply_at)
                                        <small class="text-muted">
                                            {{ $thread->last_reply_at->diffForHumans() }}<br>
                                            by {{ $thread->lastReplyUser?->full_name ?? 'Unknown' }}
                                        </small>
                                    @else
                                        <small class="text-muted">No replies yet</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="mb-0 text-muted">No threads in this category yet.</p>
                                    <a href="{{ route('forums.create', ['category' => $category->id]) }}" class="btn btn-primary mt-3">
                                        Start the first thread
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $threads->links() }}
    </div>
</div>
@endsection
