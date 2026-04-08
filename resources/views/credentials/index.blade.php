@extends('layouts.app')

@section('title', 'My Credentials')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">My Credentials</h1>
            <p class="text-muted">View your earned certificates, badges, and achievements</p>
        </div>
    </div>

    {{-- Stats Summary --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x text-warning mb-2"></i>
                    <h4 class="mb-0">{{ number_format($stats['total_points']) }}</h4>
                    <small class="text-muted">Total Points</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-fire fa-2x text-danger mb-2"></i>
                    <h4 class="mb-0">{{ $stats['current_streak'] }}</h4>
                    <small class="text-muted">Day Streak</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-award fa-2x text-primary mb-2"></i>
                    <h4 class="mb-0">{{ $stats['badges_earned'] }}</h4>
                    <small class="text-muted">Badges Earned</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-trophy fa-2x text-success mb-2"></i>
                    <h4 class="mb-0">#{{ $stats['rank'] }}</h4>
                    <small class="text-muted">Rank</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates" type="button" role="tab">
                <i class="fas fa-certificate me-1"></i> Certificates
                @if($certificates->count() > 0)
                <span class="badge bg-primary ms-1">{{ $certificates->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="badges-tab" data-bs-toggle="tab" data-bs-target="#badges" type="button" role="tab">
                <i class="fas fa-award me-1"></i> Badges
                @if($earnedBadges->count() > 0)
                <span class="badge bg-primary ms-1">{{ $earnedBadges->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="leaderboard-tab" data-bs-toggle="tab" data-bs-target="#leaderboard" type="button" role="tab">
                <i class="fas fa-trophy me-1"></i> Leaderboard
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Certificates Tab --}}
        <div class="tab-pane fade show active" id="certificates" role="tabpanel">
            @if($certificates->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-certificate fa-4x text-muted mb-3"></i>
                    <h5>No Certificates Yet</h5>
                    <p class="text-muted">Complete courses to earn certificates.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary">
                        Browse Courses
                    </a>
                </div>
            </div>
            @else
            <div class="row">
                @foreach($certificates as $certificate)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="certificate-icon me-3">
                                    <i class="fas fa-award fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">{{ $certificate->course->name ?? $certificate->title ?? 'Certificate' }}</h5>
                                    <small class="text-muted">{{ $certificate->certificate_number }}</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="badge bg-{{ $certificate->status === 'issued' ? 'success' : ($certificate->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $certificate->status)) }}
                                </span>
                            </div>

                            @if($certificate->issue_date)
                            <p class="text-muted small mb-2">
                                <i class="fas fa-calendar me-1"></i>
                                Issued: {{ $certificate->issue_date->format('M d, Y') }}
                            </p>
                            @endif
                        </div>

                        @if($certificate->status === 'issued')
                        <div class="card-footer bg-transparent border-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('certificates.show', $certificate) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-sm btn-primary flex-fill">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if(method_exists($certificates, 'hasPages') && $certificates->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $certificates->links() }}
            </div>
            @endif
            @endif
        </div>

        {{-- Leaderboard Tab --}}
        <div class="tab-pane fade" id="leaderboard" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="fas fa-trophy text-warning me-2"></i>Top Students</h6>
                </div>
                <div class="card-body p-0">
                    @if($leaderboard->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No leaderboard data yet.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">Rank</th>
                                    <th>Student</th>
                                    <th class="text-end">Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaderboard as $index => $student)
                                @php $isCurrentUser = $student->id === auth()->id(); @endphp
                                <tr class="{{ $isCurrentUser ? 'table-primary' : '' }}">
                                    <td class="text-center">
                                        @if($index === 0)
                                        <span class="text-warning fs-5"><i class="fas fa-crown"></i></span>
                                        @elseif($index === 1)
                                        <span class="text-secondary fs-6"><i class="fas fa-medal"></i></span>
                                        @elseif($index === 2)
                                        <span style="color: #cd7f32;" class="fs-6"><i class="fas fa-medal"></i></span>
                                        @else
                                        <span class="text-muted fw-bold">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($student->profile_image)
                                            <img src="{{ $student->profile_image_url }}" alt="" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                                            @else
                                            <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                <small class="text-muted fw-bold">{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</small>
                                            </div>
                                            @endif
                                            <span class="{{ $isCurrentUser ? 'fw-bold' : '' }}">
                                                {{ $student->first_name }} {{ $student->last_name }}
                                                @if($isCurrentUser)
                                                <span class="badge bg-primary ms-1">You</span>
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-warning">
                                            <i class="fas fa-star me-1"></i>{{ number_format($student->total_points) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Badges Tab --}}
        <div class="tab-pane fade" id="badges" role="tabpanel">
            @if($earnedBadges->isEmpty() && $unearnedBadges->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-award fa-4x text-muted mb-3"></i>
                    <h5>No Badges Available</h5>
                    <p class="text-muted">Badges will appear here as they become available.</p>
                </div>
            </div>
            @else
            {{-- Earned Badges --}}
            @if($earnedBadges->isNotEmpty())
            <h6 class="text-muted mb-3">
                <i class="fas fa-check-circle text-success me-1"></i> Earned ({{ $earnedBadges->count() }})
            </h6>
            <div class="row mb-4">
                @foreach($earnedBadges as $badge)
                <div class="col-6 col-md-4 col-lg-3 mb-3">
                    <div class="card h-100 border-0 shadow-sm badge-card badge-earned">
                        <div class="card-body text-center py-4">
                            <div class="badge-icon-wrapper mb-3" style="color: {{ $badge->color ?? '#ffc107' }};">
                                <i class="{{ $badge->icon ?? 'fas fa-medal' }} fa-3x"></i>
                            </div>
                            <h6 class="card-title mb-1">{{ $badge->name }}</h6>
                            <p class="text-muted small mb-2">{{ $badge->description }}</p>
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>
                                {{ $badge->pivot->earned_at ? $badge->pivot->earned_at->format('M d, Y') : 'Earned' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Unearned Badges --}}
            @if($unearnedBadges->isNotEmpty())
            <h6 class="text-muted mb-3">
                <i class="fas fa-lock text-secondary me-1"></i> Locked ({{ $unearnedBadges->count() }})
            </h6>
            <div class="row">
                @foreach($unearnedBadges as $badge)
                <div class="col-6 col-md-4 col-lg-3 mb-3">
                    <div class="card h-100 border-0 shadow-sm badge-card badge-locked">
                        <div class="card-body text-center py-4">
                            <div class="badge-icon-wrapper mb-3 text-muted" style="opacity: 0.4;">
                                <i class="{{ $badge->icon ?? 'fas fa-medal' }} fa-3x"></i>
                            </div>
                            <h6 class="card-title mb-1 text-muted">{{ $badge->name }}</h6>
                            <p class="text-muted small mb-2">{{ $badge->description }}</p>
                            <span class="badge bg-secondary">
                                <i class="fas fa-lock me-1"></i> Locked
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
    .badge-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .badge-card.badge-earned:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12) !important;
    }

    .badge-card.badge-locked {
        background: #f8f9fa;
    }

    .badge-icon-wrapper {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.03);
    }

    .nav-tabs .nav-link {
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        border-color: #dee2e6 #dee2e6 #fff;
    }
</style>
@endsection