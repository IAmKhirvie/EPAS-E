@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Announcements</h1>
                @if(in_array(Auth::user()->role, ['admin', 'instructor']))
                    <a href="{{ route('private.announcements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Announcement
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @foreach($announcements as $announcement)
                <div class="card mb-4 {{ $announcement->is_urgent ? 'border-danger' : '' }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-3">
                            <h5 class="card-title mb-0 announcement-title">
                                @if($announcement->is_pinned)
                                    <i class="fas fa-thumbtack text-warning me-2"></i>
                                @endif
                                <a href="{{ route('private.announcements.show', $announcement->id) }}" 
                                   class="text-decoration-none {{ $announcement->is_urgent ? 'text-danger' : 'text-dark' }}">
                                    {{ $announcement->title }}
                                </a>
                                @if($announcement->is_urgent)
                                    <span class="badge bg-danger ms-2">URGENT</span>
                                @endif
                            </h5>
                            <small class="text-muted">
                                Posted by {{ $announcement->user->full_name ?? $announcement->user->name }} • 
                                {{ $announcement->created_at->format('M j, Y \a\t g:i A') }}
                                @if($announcement->publish_at && $announcement->publish_at->isFuture())
                                    • Scheduled for {{ $announcement->publish_at->format('M j, Y g:i A') }}
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="announcement-content">
                            {!! nl2br(e($announcement->content)) !!}
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ $announcement->comments->count() }} comments
                            </small>
                            <a href="{{ route('private.announcements.show', $announcement->id) }}" 
                               class="btn btn-sm btn-outline-primary">
                                View & Comment
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($announcements->count() === 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                        <h5>No Announcements Yet</h5>
                        <p class="text-muted">There are no announcements to display at the moment.</p>
                        @if(in_array(Auth::user()->role, ['admin', 'instructor']))
                            <a href="{{ route('private.announcements.create') }}" class="btn btn-primary">
                                Create First Announcement
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Pagination -->
            @if($announcements->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.announcement-title {
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
    line-height: 1.4;
}

.announcement-content {
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
}

.card-header {
    min-height: auto;
}

/* Responsive adjustments */
@media (max-width: 1032px) {
    .announcement-title {
        font-size: 1.1rem;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .card-header .btn {
        margin-top: 0.5rem;
        align-self: flex-end;
    }
}

@media (max-width: 576px) {
    .announcement-title {
        font-size: 1rem;
    }
    
    .d-flex.justify-content-between.align-items-center.mb-4 {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .d-flex.justify-content-between.align-items-center.mb-4 .btn {
        margin-top: 0.5rem;
    }
}
</style>
@endsection