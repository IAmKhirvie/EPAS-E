@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Email Verification Alert --}}
    @if(!Auth::user()->hasVerifiedEmail())
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-envelope-open-text me-3" style="font-size: 1.5rem;"></i>
        <div class="flex-grow-1">
            <strong>Email not verified!</strong> Please verify your email address to access all features.
            <form action="{{ route('settings.resend-verification') }}" method="POST" class="d-inline ms-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="fas fa-paper-plane me-1"></i> Resend Verification Email
                </button>
            </form>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Student ID Missing Banner --}}
    @if(strtolower(Auth::user()->role) === 'student' && empty(Auth::user()->student_id))
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-4" role="alert"
         id="studentIdBanner" data-user-id="{{ Auth::id() }}">
        <i class="fas fa-id-card me-3" style="font-size: 1.5rem;"></i>
        <div class="flex-grow-1">
            <strong>Student ID Not Assigned</strong><br>
            You don't have a Student ID yet. Please approach the admin to get one assigned.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                onclick="localStorage.setItem('studentIdBannerDismissed_{{ Auth::id() }}', 'true')"></button>
    </div>
    <script>
        (function() {
            var banner = document.getElementById('studentIdBanner');
            if (banner && localStorage.getItem('studentIdBannerDismissed_' + banner.dataset.userId)) {
                banner.remove();
            }
        })();
    </script>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                @if(strtolower(Auth::user()->role) === 'admin')
                    Admin Dashboard
                @elseif(strtolower(Auth::user()->role) === 'instructor')
                    Instructor Dashboard
                @else
                    Student Dashboard
                @endif
            </h1>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        @if(strtolower(Auth::user()->role) === 'admin')
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-primary" >{{ $totalStudents ?? 0 }}</div>
                        <div class="card-counter-label">Total Students</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-info" >{{ $totalInstructors ?? 0 }}</div>
                        <div class="card-counter-label">Instructors</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-success" >{{ $totalModules ?? 0 }}</div>
                        <div class="card-counter-label">Modules</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-warning" >{{ $pendingEvaluations ?? 0 }}</div>
                        <div class="card-counter-label">Pending</div>
                    </div>
                </div>
            </div>
        @elseif(strtolower(Auth::user()->role) === 'instructor')
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-primary" >{{ $totalStudents ?? 0 }}</div>
                        <div class="card-counter-label">My Students</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-success" >{{ $totalModules ?? 0 }}</div>
                        <div class="card-counter-label">Modules</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-warning" >{{ $pendingEvaluations ?? 0 }}</div>
                        <div class="card-counter-label">Pending</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-info" >{{ $ongoingBatches ?? 0 }}</div>
                        <div class="card-counter-label">Sections</div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-primary" >{{ $student_progress ?? 0 }}%</div>
                        <div class="card-counter-label">Progress</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-success" >{{ $finished_activities ?? '0/0' }}</div>
                        <div class="card-counter-label">Completed</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-info" >{{ $total_modules ?? 0 }}</div>
                        <div class="card-counter-label">Modules</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="card-counter text-warning" >{{ $average_grade ?? '0%' }}</div>
                        <div class="card-counter-label">Avg Grade</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content: Activity Feed (80%) | Pending (20%) -->
    <div class="row">
        <!-- Activity Feed - Left Side (80%) -->
        <div class="col-lg-9 col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-2">
                    <div class="row align-items-center g-2">
                        <div class="col-md-4">
                            <h6 class="mb-0">
                                <i class="fas fa-stream text-primary me-2"></i>Activity Feed
                            </h6>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex gap-2 flex-wrap">
                                <!-- Search -->
                                <div class="input-group input-group-sm flex-grow-1" style="min-width: 150px;">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="feed-search" placeholder="Search...">
                                </div>
                                <!-- Filter by Type -->
                                <select class="form-select form-select-sm" id="feed-filter-type" style="width: auto;">
                                    <option value="">All Types</option>
                                    <option value="announcement">Announcements</option>
                                    <option value="submission">Submissions</option>
                                    <option value="homework">Homework</option>
                                    <option value="quiz">Quiz/Self-Check</option>
                                    <option value="task">Task Sheet</option>
                                </select>
                                <!-- Sort -->
                                <select class="form-select form-select-sm" id="feed-sort" style="width: auto;">
                                    <option value="newest">Newest</option>
                                    <option value="oldest">Oldest</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" class="dashboard-feed-scroll">
                    <div id="activity-feed">
                        @php
                            // Combine announcements and submissions into activity feed
                            $feedItems = collect();

                            // Add announcements
                            if(isset($recentAnnouncements)) {
                                foreach($recentAnnouncements as $announcement) {
                                    $feedItems->push([
                                        'type' => 'announcement',
                                        'icon' => 'fas fa-bullhorn',
                                        'color' => '#0d6efd',
                                        'title' => $announcement->title,
                                        'content' => Str::limit(strip_tags($announcement->content ?? ''), 120),
                                        'user_name' => $announcement->user->full_name ?? 'System',
                                        'user_avatar' => $announcement->user->profile_image_url ?? null,
                                        'date' => $announcement->created_at,
                                        'url' => route('private.announcements.show', $announcement),
                                        'module' => null,
                                    ]);
                                }
                            }

                            // Add recent submissions (for instructors/admins)
                            if(isset($recentSubmissions) && !in_array(strtolower(Auth::user()->role), ['student'])) {
                                foreach($recentSubmissions as $submission) {
                                    $feedItems->push([
                                        'type' => 'submission',
                                        'subtype' => $submission['type'] ?? 'submission',
                                        'icon' => $submission['icon'] ?? 'fas fa-file-alt',
                                        'color' => $submission['color'] ?? '#6c757d',
                                        'title' => ($submission['student_name'] ?? 'Student') . ' submitted ' . ($submission['title'] ?? 'an activity'),
                                        'content' => 'Module: ' . ($submission['module'] ?? 'Unknown'),
                                        'user_name' => $submission['student_name'] ?? 'Unknown',
                                        'user_avatar' => $submission['student_avatar'] ?? null,
                                        'date' => $submission['submitted_at'] ?? now(),
                                        'url' => $submission['url'] ?? '#',
                                        'module' => $submission['module'] ?? null,
                                    ]);
                                }
                            }

                            // Sort by date descending
                            $feedItems = $feedItems->sortByDesc('date')->values();
                        @endphp

                        @if($feedItems->count() > 0)
                            @foreach($feedItems as $item)
                                <div class="feed-item border-bottom p-3"
                                     data-type="{{ $item['type'] }}"
                                     data-subtype="{{ $item['subtype'] ?? '' }}"
                                     data-module="{{ strtolower($item['module'] ?? '') }}"
                                     data-user="{{ strtolower($item['user_name'] ?? '') }}"
                                     data-date="{{ $item['date'] }}">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            @if($item['user_avatar'])
                                                <img src="{{ $item['user_avatar'] }}" class="rounded-circle" width="40" height="40" alt="{{ $item['user_name'] ?? 'User' }} avatar">
                                            @else
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="{{ $item['icon'] }}" style="color: {{ $item['color'] }}"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <span class="badge bg-{{ $item['type'] === 'announcement' ? 'primary' : 'success' }} bg-opacity-10 text-{{ $item['type'] === 'announcement' ? 'primary' : 'success' }} mb-1">
                                                        <i class="{{ $item['icon'] }} me-1"></i>{{ ucfirst($item['type']) }}
                                                    </span>
                                                    <h6 class="mb-1">
                                                        <a href="{{ $item['url'] }}" class="text-decoration-none text-dark">
                                                            {{ $item['title'] }}
                                                        </a>
                                                    </h6>
                                                </div>
                                                <small class="text-muted text-nowrap ms-2">
                                                    {{ \Carbon\Carbon::parse($item['date'])->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p class="text-muted small mb-1">{{ $item['content'] }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>{{ $item['user_name'] }}
                                                @if($item['module'])
                                                    <span class="mx-1">|</span>
                                                    <i class="fas fa-book me-1"></i>{{ $item['module'] }}
                                                @endif
                                            </small>
                                            @if($item['type'] === 'announcement')
                                            <div class="mt-2">
                                                <a href="{{ $item['url'] }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View Announcement
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-stream fa-3x mb-3"></i>
                                <p class="mb-0">No activity yet</p>
                            </div>
                        @endif
                    </div>
                    <div id="no-results" class="text-center py-5 text-muted" style="display: none;">
                        <i class="fas fa-search fa-2x mb-3"></i>
                        <p class="mb-0">No matching results</p>
                    </div>
                </div>
                @if(in_array(Auth::user()->role, ['admin', 'instructor']))
                <div class="card-footer bg-white text-center py-2">
                    <a href="{{ route('private.announcements.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>New Announcement
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Pending Activities - Right Side (20%) Sticky -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="sticky-sidebar">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-clock text-warning me-2"></i>Pending Requests
                            @php
                                $pendingCount = strtolower(Auth::user()->role) === 'student'
                                    ? (isset($pendingActivities) ? $pendingActivities->count() : 0)
                                    : (($pendingEvaluations ?? 0) + ($pendingRegistrationsCount ?? 0));
                            @endphp
                            @if($pendingCount > 0)
                                <span class="badge bg-warning text-dark">{{ $pendingCount }}</span>
                            @endif
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown">
                                <i class="fas fa-sort"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item pending-sort" href="#" data-sort="date-desc">Newest First</a></li>
                                <li><a class="dropdown-item pending-sort" href="#" data-sort="date-asc">Oldest First</a></li>
                                <li><a class="dropdown-item pending-sort" href="#" data-sort="type">By Type</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-0" class="dashboard-pending-scroll">
                        @if(strtolower(Auth::user()->role) === 'student')
                            {{-- Student Pending Activities --}}
                            @if(isset($pendingActivities) && $pendingActivities->count() > 0)
                                <div class="list-group list-group-flush" id="pending-list">
                                    @foreach($pendingActivities as $activity)
                                        <a href="{{ $activity['url'] ?? '#' }}" class="list-group-item list-group-item-action py-2 pending-item"
                                           data-type="{{ $activity['type'] ?? '' }}" data-date="{{ $activity['deadline'] ?? $activity['created_at'] ?? '' }}">
                                            <div class="d-flex align-items-center">
                                                <div class="activity-icon-sm me-2" style="background-color: {{ $activity['color'] }}20; color: {{ $activity['color'] }}">
                                                    <i class="{{ $activity['icon'] }}"></i>
                                                </div>
                                                <div class="flex-grow-1 min-width-0">
                                                    <div class="fw-medium text-truncate small">{{ $activity['title'] }}</div>
                                                    <small class="text-muted">{{ $activity['subtitle'] ?? '' }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle text-success mb-2"></i>
                                    <p class="mb-0 small">All caught up!</p>
                                </div>
                            @endif
                        @else
                            {{-- Instructor/Admin Pending Items --}}
                            @if((isset($pendingRegistrations) && $pendingRegistrations->count() > 0) ||
                                (isset($recentSubmissions) && $recentSubmissions->count() > 0))
                                <div class="list-group list-group-flush" id="pending-list">

                                    {{-- Pending Registrations --}}
                                    @if(isset($pendingRegistrations) && $pendingRegistrations->count() > 0)
                                        <div class="list-group-item bg-light py-1 border-bottom">
                                            <small class="text-muted fw-bold text-uppercase">Pending Registrations</small>
                                        </div>
                                        @foreach($pendingRegistrations as $registration)
                                            <div class="list-group-item py-2 pending-item" data-type="registration" data-date="{{ $registration->email_verified_at }}">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="activity-icon-sm me-2" style="background-color: #0d6efd20; color: #0d6efd">
                                                        <i class="fas fa-user-plus"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-width-0">
                                                        <div class="fw-medium text-truncate small">{{ $registration->full_name }}</div>
                                                        <small class="text-muted text-truncate d-block">{{ $registration->email }}</small>
                                                        <small class="text-success"><i class="fas fa-check-circle me-1"></i>Email verified</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <form action="{{ route('admin.registrations.approve', $registration) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                            <i class="fas fa-check me-1"></i>Approve
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('admin.registrations.show', $registration) }}" class="btn btn-outline-secondary btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- Pending Evaluations --}}
                                    @if(isset($recentSubmissions) && $recentSubmissions->count() > 0)
                                        <div class="list-group-item bg-light py-1 border-bottom">
                                            <small class="text-muted fw-bold text-uppercase">Pending Evaluations</small>
                                        </div>
                                        @foreach($recentSubmissions as $submission)
                                            <a href="{{ $submission['url'] ?? '#' }}" class="list-group-item list-group-item-action py-2 pending-item"
                                               data-type="{{ $submission['type'] ?? '' }}" data-date="{{ $submission['submitted_at'] ?? '' }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="activity-icon-sm me-2" style="background-color: {{ $submission['color'] }}20; color: {{ $submission['color'] }}">
                                                        <i class="{{ $submission['icon'] }}"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-width-0">
                                                        <div class="fw-medium text-truncate small">{{ $submission['student_name'] }}</div>
                                                        <small class="text-muted text-truncate d-block">{{ $submission['title'] }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle text-success mb-2"></i>
                                    <p class="mb-0 small">No pending items</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(strtolower(Auth::user()->role) === 'student')
    <!-- Student: Recent Completed Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success bg-opacity-10">
                    <h6 class="mb-0">
                        <i class="fas fa-check-circle text-success me-2"></i>Recently Completed
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if(isset($completedActivitiesList) && $completedActivitiesList->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Activity</th>
                                        <th>Module</th>
                                        <th>Score</th>
                                        <th>Completed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedActivitiesList->take(5) as $activity)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="activity-icon-sm me-2" style="background-color: {{ $activity['color'] }}20; color: {{ $activity['color'] }}">
                                                        <i class="{{ $activity['icon'] }}"></i>
                                                    </div>
                                                    <span>{{ $activity['title'] }}</span>
                                                </div>
                                            </td>
                                            <td><small class="text-muted">{{ $activity['subtitle'] }}</small></td>
                                            <td>
                                                <span class="badge {{ $activity['passed'] ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $activity['score'] }}
                                                </span>
                                            </td>
                                            <td><small class="text-muted">{{ \Carbon\Carbon::parse($activity['completed_at'])->diffForHumans() }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-tasks fa-2x mb-2"></i>
                            <p class="mb-0">No completed activities yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="{{ dynamic_asset('js/dashboard.js') }}"></script>
@endpush

@endsection
