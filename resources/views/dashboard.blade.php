@extends('layouts.app')

@section('title', 'Dashboard - EPAS-E')

@push('styles')
<link rel="stylesheet" href="{{ dynamic_asset('css/pages/dashboard.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- Alerts --}}
    @if(!Auth::user()->hasVerifiedEmail())
    <div class="dashboard-alert warning" role="alert">
        <i class="fas fa-envelope-open-text"></i>
        <div class="alert-content">
            <strong>Email not verified!</strong> Please verify your email to access all features.
            <form action="{{ route('settings.resend-verification') }}" method="POST" class="d-inline ms-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="fas fa-paper-plane me-1"></i>Resend
                </button>
            </form>
        </div>
        <button type="button" class="btn-close" onclick="this.closest('.dashboard-alert').remove()"></button>
    </div>
    @endif

    @if(Auth::user()->role === \App\Constants\Roles::STUDENT && empty(Auth::user()->student_id))
    <div class="dashboard-alert info" role="alert" id="studentIdBanner" data-user-id="{{ Auth::id() }}">
        <i class="fas fa-id-card"></i>
        <div class="alert-content">
            <strong>Student ID Not Assigned</strong> — Please approach the admin to get one assigned.
        </div>
        <button type="button" class="btn-close" onclick="this.closest('.dashboard-alert').remove(); localStorage.setItem('studentIdBannerDismissed_{{ Auth::id() }}', 'true')"></button>
    </div>
    <script>
        (function() {
            var banner = document.getElementById('studentIdBanner');
            if (banner && localStorage.getItem('studentIdBannerDismissed_' + banner.dataset.userId)) banner.remove();
        })();
    </script>
    @endif

    @if(session('success'))
    <div class="dashboard-alert" style="background:rgba(25,135,84,0.08);border-color:rgba(25,135,84,0.25);color:#0f5132" role="alert">
        <i class="fas fa-check-circle"></i>
        <div class="alert-content">{{ session('success') }}</div>
        <button type="button" class="btn-close" onclick="this.closest('.dashboard-alert').remove()"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="dashboard-alert" style="background:rgba(220,53,69,0.08);border-color:rgba(220,53,69,0.25);color:#842029" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        <div class="alert-content">{{ $errors->first() }}</div>
        <button type="button" class="btn-close" onclick="this.closest('.dashboard-alert').remove()"></button>
    </div>
    @endif

    {{-- Header --}}
    <div class="dashboard-header">
        <div class="dashboard-header-left">
            <h1>
                <i class="fas fa-chart-bar me-2"></i>
                @if(Auth::user()->role === \App\Constants\Roles::ADMIN)
                    Admin Dashboard
                @elseif(Auth::user()->role === \App\Constants\Roles::INSTRUCTOR)
                    Instructor Dashboard
                @else
                    My Dashboard
                @endif
            </h1>
            <p>Welcome back, {{ Auth::user()->first_name }}! Here's what's happening.</p>
        </div>
        <div class="dashboard-header-actions">
            <span class="role-badge {{ Auth::user()->role }}">
                <i class="fas fa-{{ Auth::user()->role === \App\Constants\Roles::ADMIN ? 'shield-alt' : (Auth::user()->role === \App\Constants\Roles::INSTRUCTOR ? 'chalkboard-teacher' : 'user-graduate') }}"></i>
                {{ ucfirst(Auth::user()->role) }}
            </span>
            @if(in_array(Auth::user()->role, [\App\Constants\Roles::ADMIN, \App\Constants\Roles::INSTRUCTOR]))
            <a href="{{ route('private.announcements.create') }}" class="btn btn-primary" style="border-radius:50px;">
                <i class="fas fa-plus me-1"></i>Announcement
            </a>
            @endif
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-cards">
        @if(Auth::user()->role === \App\Constants\Roles::ADMIN)
            <div class="stat-card primary">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-card-value">{{ $totalStudents ?? 0 }}</div>
                    <div class="stat-card-label">Total Students</div>
                </div>
            </div>
            <div class="stat-card info">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="stat-card-value">{{ $totalInstructors ?? 0 }}</div>
                    <div class="stat-card-label">Instructors</div>
                </div>
            </div>
            <div class="stat-card success">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-book-open"></i></div>
                    <div class="stat-card-value">{{ $totalModules ?? 0 }}</div>
                    <div class="stat-card-label">Modules</div>
                </div>
            </div>
            <div class="stat-card warning">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-card-value">{{ $pendingEvaluations ?? 0 }}</div>
                    <div class="stat-card-label">Pending</div>
                </div>
            </div>
        @elseif(Auth::user()->role === \App\Constants\Roles::INSTRUCTOR)
            <div class="stat-card primary">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-card-value">{{ $totalStudents ?? 0 }}</div>
                    <div class="stat-card-label">My Students</div>
                </div>
            </div>
            <div class="stat-card success">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-book-open"></i></div>
                    <div class="stat-card-value">{{ $totalModules ?? 0 }}</div>
                    <div class="stat-card-label">Modules</div>
                </div>
            </div>
            <div class="stat-card warning">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-file-signature"></i></div>
                    <div class="stat-card-value">{{ $pendingEvaluations ?? 0 }}</div>
                    <div class="stat-card-label">Pending</div>
                </div>
            </div>
            <div class="stat-card info">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-layer-group"></i></div>
                    <div class="stat-card-value">{{ $ongoingBatches ?? 0 }}</div>
                    <div class="stat-card-label">Sections</div>
                </div>
            </div>
        @else
            <div class="stat-card primary">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-card-value" id="student-progress-text" data-progress="{{ $student_progress ?? 0 }}">{{ $student_progress ?? 0 }}%</div>
                    <div class="stat-card-label">Progress</div>
                </div>
            </div>
            <div class="stat-card success">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-check-double"></i></div>
                    <div class="stat-card-value" id="finished-activities" data-activities="{{ $finished_activities ?? '0/0' }}">{{ $finished_activities ?? '0/0' }}</div>
                    <div class="stat-card-label">Completed</div>
                </div>
            </div>
            <div class="stat-card info">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-book-open"></i></div>
                    <div class="stat-card-value" id="total-modules-count" data-modules="{{ $total_modules ?? 0 }}">{{ $total_modules ?? 0 }}</div>
                    <div class="stat-card-label">Modules</div>
                </div>
            </div>
            <div class="stat-card purple">
                <div class="stat-card-decor"></div>
                <div class="stat-card-content">
                    <div class="stat-card-icon"><i class="fas fa-star"></i></div>
                    <div class="stat-card-value" id="average-grade" data-grade="{{ $average_grade ?? '0%' }}">{{ $average_grade ?? '0%' }}</div>
                    <div class="stat-card-label">Avg Grade</div>
                </div>
            </div>
        @endif
    </div>

    {{-- Main Content + Sidebar --}}
    <div class="dashboard-page-wrapper">
        <div class="dashboard-main">
            {{-- Announcements Feed --}}
            <div class="feed-widget">
                <div class="feed-widget-header">
                    <h3><i class="fas fa-bullhorn"></i> Announcements</h3>
                    <div class="feed-toolbar">
                        <div class="feed-search">
                            <i class="fas fa-search"></i>
                            <input type="text" id="feed-search" placeholder="Search feed..." autocomplete="off">
                        </div>
                        <select class="feed-select" id="feed-filter-type">
                            <option value="">All Types</option>
                            <option value="announcement">Announcements</option>
                            <option value="submission">Submissions</option>
                            <option value="homework">Homework</option>
                            <option value="quiz">Quiz</option>
                            <option value="task">Task Sheet</option>
                        </select>
                        <select class="feed-select" id="feed-sort">
                            <option value="newest">Newest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>
                </div>
                <div class="feed-widget-body" id="activity-feed">
                    @php
                    $feedItems = collect();

                    if(isset($recentAnnouncements)) {
                        foreach($recentAnnouncements as $announcement) {
                            $feedItems->push([
                                'type' => 'announcement',
                                'icon' => 'fas fa-bullhorn',
                                'color' => '#0c3a2d',
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

                    if(isset($recentSubmissions) && Auth::user()->role !== \App\Constants\Roles::STUDENT) {
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

                    $feedItems = $feedItems->sortByDesc('date')->values();
                    @endphp

                    @if($feedItems->count() > 0)
                        @foreach($feedItems as $item)
                        <div class="feed-item"
                            data-type="{{ $item['type'] }}"
                            data-subtype="{{ $item['subtype'] ?? '' }}"
                            data-module="{{ strtolower($item['module'] ?? '') }}"
                            data-user="{{ strtolower($item['user_name'] ?? '') }}"
                            data-date="{{ $item['date'] }}">
                            <div class="feed-item-row">
                                <div class="feed-avatar" style="background: {{ $item['color'] }}20; color: {{ $item['color'] }}">
                                    @if($item['user_avatar'])
                                        <img src="{{ $item['user_avatar'] }}" alt="">
                                    @else
                                        <i class="{{ $item['icon'] }}"></i>
                                    @endif
                                </div>
                                <div class="feed-content">
                                    <span class="feed-type-badge {{ $item['type'] }}">
                                        <i class="{{ $item['icon'] }}"></i>
                                        {{ ucfirst($item['type']) }}
                                    </span>
                                    <div class="feed-title">
                                        <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                                    </div>
                                    <div class="feed-excerpt">{{ $item['content'] }}</div>
                                    <div class="feed-meta">
                                        <span><i class="fas fa-user"></i> {{ $item['user_name'] }}</span>
                                        @if($item['module'])
                                        <span><i class="fas fa-book"></i> {{ $item['module'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="feed-time">{{ \Carbon\Carbon::parse($item['date'])->diffForHumans() }}</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="feed-empty">
                            <i class="fas fa-stream"></i>
                            <p>No activity yet</p>
                        </div>
                    @endif
                </div>
                <div id="no-results" class="feed-empty" style="display: none;">
                    <i class="fas fa-search"></i>
                    <p>No matching results</p>
                </div>
                @if(in_array(Auth::user()->role, [\App\Constants\Roles::ADMIN, \App\Constants\Roles::INSTRUCTOR]))
                <div class="feed-widget-footer">
                    <a href="{{ route('private.announcements.create') }}" class="btn btn-primary btn-sm" style="border-radius:50px;">
                        <i class="fas fa-plus me-1"></i>New Announcement
                    </a>
                </div>
                @endif
            </div>

            {{-- Student: Recently Completed --}}
            @if(Auth::user()->role === \App\Constants\Roles::STUDENT)
            <div class="completed-widget">
                <div class="completed-widget-header">
                    <h3><i class="fas fa-check-circle"></i> Recently Completed</h3>
                </div>
                @if(isset($completedActivitiesList) && $completedActivitiesList->count() > 0)
                <table class="completed-table">
                    <thead>
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
                                <div class="d-flex align-items-center gap-2">
                                    <span class="activity-dot" style="background: {{ $activity['color'] }}15; color: {{ $activity['color'] }}">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </span>
                                    {{ $activity['title'] }}
                                </div>
                            </td>
                            <td><span style="color:var(--text-muted);font-size:0.8rem">{{ $activity['subtitle'] }}</span></td>
                            <td>
                                <span class="score-badge {{ $activity['passed'] ? 'passed' : 'failed' }}">
                                    {{ $activity['score'] }}
                                </span>
                            </td>
                            <td><span style="color:var(--text-muted);font-size:0.8rem">{{ \Carbon\Carbon::parse($activity['completed_at'])->diffForHumans() }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="feed-empty">
                    <i class="fas fa-tasks"></i>
                    <p>No completed activities yet</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Right Sidebar --}}
        <aside class="dashboard-sidebar">
            {{-- Mini Calendar --}}
            <div class="sidebar-widget">
                <div class="sidebar-widget-header">
                    <h3><i class="fas fa-calendar-alt"></i> Calendar</h3>
                </div>
                <div class="sidebar-widget-body">
                    <div class="mini-calendar">
                        <div class="calendar-header">
                            <h4 id="calendarMonth">{{ now()->format('F Y') }}</h4>
                            <div class="calendar-nav">
                                <button id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                                <button id="nextMonth"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                        <div class="calendar-grid" id="calendarGrid"></div>
                    </div>
                </div>
            </div>

            {{-- Pending Tasks / Activities --}}
            <div class="sidebar-widget">
                <div class="sidebar-widget-header">
                    <h3>
                        @if(Auth::user()->role === \App\Constants\Roles::STUDENT)
                            <i class="fas fa-tasks"></i> My Tasks
                        @elseif(Auth::user()->role === \App\Constants\Roles::ADMIN)
                            <i class="fas fa-bell"></i> Pending
                        @else
                            <i class="fas fa-calendar-check"></i> Upcoming
                        @endif
                    </h3>
                    @php
                    $pendingCount = Auth::user()->role === \App\Constants\Roles::STUDENT
                        ? ((isset($pendingActivities) ? $pendingActivities->count() : 0) + ($upcomingDeadlinesCount ?? 0))
                        : (Auth::user()->role === \App\Constants\Roles::ADMIN
                            ? (($pendingEvaluations ?? 0) + ($pendingRegistrationsCount ?? 0))
                            : (($upcomingDeadlinesCount ?? 0) + ($pendingEvaluations ?? 0)));
                    @endphp
                    @if($pendingCount > 0)
                    <span class="badge-count">{{ $pendingCount }}</span>
                    @endif
                </div>
                <div class="sidebar-widget-body" style="padding:0.75rem;">
                    @if(Auth::user()->role === \App\Constants\Roles::STUDENT)
                        {{-- Student Tasks --}}
                        @if((isset($pendingActivities) && $pendingActivities->count() > 0) || (isset($upcomingDeadlines) && $upcomingDeadlines->count() > 0))
                        <div class="sidebar-task-list" id="pending-list">
                            @if(isset($upcomingDeadlines) && $upcomingDeadlines->count() > 0)
                                @foreach($upcomingDeadlines as $deadline)
                                <a href="{{ $deadline['url'] ?? '#' }}" class="sidebar-task-item pending-item" data-type="deadline" data-date="{{ $deadline['due_date'] }}">
                                    <div class="task-item-icon deadline">
                                        <i class="{{ $deadline['icon'] }}"></i>
                                    </div>
                                    <div class="task-item-info">
                                        <div class="task-item-title">{{ $deadline['title'] }}</div>
                                        <div class="task-item-subtitle">{{ $deadline['subtitle'] }}</div>
                                        <div class="task-item-due">
                                            <i class="fas fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($deadline['due_date'])->format('M d, h:i A') }}
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            @endif
                            @if(isset($pendingActivities) && $pendingActivities->count() > 0)
                                @foreach($pendingActivities as $activity)
                                <a href="{{ $activity['url'] ?? '#' }}" class="sidebar-task-item pending-item" data-type="{{ $activity['type'] ?? '' }}" data-date="{{ $activity['deadline'] ?? $activity['created_at'] ?? '' }}">
                                    <div class="task-item-icon {{ $activity['type'] ?? 'task' }}">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </div>
                                    <div class="task-item-info">
                                        <div class="task-item-title">{{ $activity['title'] }}</div>
                                        <div class="task-item-subtitle">{{ $activity['subtitle'] ?? '' }}</div>
                                    </div>
                                </a>
                                @endforeach
                            @endif
                        </div>
                        @else
                        <div class="sidebar-empty">
                            <i class="fas fa-check-circle" style="color:#198754"></i>
                            <p>All caught up!</p>
                        </div>
                        @endif

                    @else
                        {{-- Admin/Instructor Tasks --}}
                        @if((Auth::user()->role === \App\Constants\Roles::INSTRUCTOR && isset($upcomingDeadlines) && $upcomingDeadlines->count() > 0) ||
                            (isset($pendingRegistrations) && $pendingRegistrations->count() > 0) ||
                            (isset($recentSubmissions) && count($recentSubmissions) > 0))
                        <div class="sidebar-task-list" id="pending-list">
                            {{-- Instructor Deadlines --}}
                            @if(Auth::user()->role === \App\Constants\Roles::INSTRUCTOR && isset($upcomingDeadlines) && $upcomingDeadlines->count() > 0)
                                @foreach($upcomingDeadlines as $deadline)
                                <a href="{{ $deadline['url'] ?? '#' }}" class="sidebar-task-item pending-item" data-type="deadline" data-date="{{ $deadline['due_date'] }}">
                                    <div class="task-item-icon deadline">
                                        <i class="{{ $deadline['icon'] }}"></i>
                                    </div>
                                    <div class="task-item-info">
                                        <div class="task-item-title">{{ $deadline['title'] }}</div>
                                        <div class="task-item-subtitle">{{ $deadline['subtitle'] }}</div>
                                        <div class="task-item-due">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($deadline['due_date'])->format('M d, h:i A') }}
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            @endif

                            {{-- Admin Registrations --}}
                            @if(Auth::user()->role === \App\Constants\Roles::ADMIN && isset($pendingRegistrations) && $pendingRegistrations->count() > 0)
                                @foreach($pendingRegistrations as $registration)
                                <div class="sidebar-task-item pending-item" data-type="registration" data-date="{{ $registration->email_verified_at }}">
                                    <div class="task-item-icon registration">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="task-item-info">
                                        <div class="task-item-title">{{ $registration->full_name }}</div>
                                        <div class="task-item-subtitle">{{ $registration->email }}</div>
                                        <div class="registration-actions">
                                            <form action="{{ route('admin.registrations.approve', $registration) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i>Approve</button>
                                            </form>
                                            <a href="{{ route('admin.registrations.show', $registration) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-eye"></i></a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif

                            {{-- Pending Evaluations --}}
                            @if(isset($recentSubmissions) && count($recentSubmissions) > 0)
                                @foreach(collect($recentSubmissions)->take(8) as $submission)
                                <a href="{{ $submission['url'] ?? '#' }}" class="sidebar-task-item pending-item" data-type="{{ $submission['type'] ?? '' }}" data-date="{{ $submission['submitted_at'] ?? '' }}">
                                    <div class="task-item-icon evaluation">
                                        <i class="{{ $submission['icon'] }}"></i>
                                    </div>
                                    <div class="task-item-info">
                                        <div class="task-item-title">{{ $submission['student_name'] }}</div>
                                        <div class="task-item-subtitle">{{ $submission['title'] }}</div>
                                    </div>
                                </a>
                                @endforeach
                            @endif
                        </div>
                        @else
                        <div class="sidebar-empty">
                            <i class="fas fa-check-circle" style="color:#198754"></i>
                            <p>
                                @if(Auth::user()->role === \App\Constants\Roles::INSTRUCTOR)
                                    No upcoming deadlines
                                @else
                                    No pending requests
                                @endif
                            </p>
                        </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="sidebar-widget">
                <div class="sidebar-widget-header">
                    <h3><i class="fas fa-chart-pie"></i> Quick Stats</h3>
                </div>
                <div class="sidebar-widget-body">
                    <div class="quick-stats">
                        @if(Auth::user()->role === \App\Constants\Roles::STUDENT)
                        <div class="quick-stat-row">
                            <span class="quick-stat-label"><i class="fas fa-bullseye"></i> Progress</span>
                            <span class="quick-stat-value">{{ $student_progress ?? 0 }}%</span>
                        </div>
                        <div class="quick-stat-row">
                            <span class="quick-stat-label"><i class="fas fa-check"></i> Completed</span>
                            <span class="quick-stat-value">{{ $finished_activities ?? '0/0' }}</span>
                        </div>
                        <div class="quick-stat-row">
                            <span class="quick-stat-label"><i class="fas fa-star"></i> Avg Grade</span>
                            <span class="quick-stat-value">{{ $average_grade ?? '0%' }}</span>
                        </div>
                        @else
                        <div class="quick-stat-row">
                            <span class="quick-stat-label"><i class="fas fa-users"></i> Students</span>
                            <span class="quick-stat-value">{{ $totalStudents ?? 0 }}</span>
                        </div>
                        <div class="quick-stat-row">
                            <span class="quick-stat-label"><i class="fas fa-book"></i> Modules</span>
                            <span class="quick-stat-value">{{ $totalModules ?? 0 }}</span>
                        </div>
                        <div class="quick-stat-row">
                            <span class="quick-stat-label"><i class="fas fa-file-signature"></i> Pending</span>
                            <span class="quick-stat-value">{{ $pendingEvaluations ?? 0 }}</span>
                        </div>
                        @if(Auth::user()->role === \App\Constants\Roles::ADMIN)
                        <div class="quick-stat-row">
                            <span class="quick-stat-label"><i class="fas fa-user-plus"></i> Registrations</span>
                            <span class="quick-stat-value">{{ $pendingRegistrationsCount ?? 0 }}</span>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ dynamic_asset('js/dashboard.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mini Calendar
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarMonth = document.getElementById('calendarMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    if (!calendarGrid) return;

    let currentDate = new Date();

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        calendarMonth.textContent = currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDay = firstDay.getDay();
        const daysInMonth = lastDay.getDate();

        const today = new Date();
        const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;

        let html = '';
        const days = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
        days.forEach(day => { html += '<div class="calendar-day-label">' + day + '</div>'; });

        const prevMonthDays = new Date(year, month, 0).getDate();
        for (let i = startDay - 1; i >= 0; i--) {
            html += '<div class="calendar-day other-month">' + (prevMonthDays - i) + '</div>';
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const isToday = isCurrentMonth && day === today.getDate();
            html += '<div class="calendar-day' + (isToday ? ' today' : '') + '">' + day + '</div>';
        }

        const remaining = 42 - (startDay + daysInMonth);
        for (let i = 1; i <= remaining; i++) {
            html += '<div class="calendar-day other-month">' + i + '</div>';
        }

        calendarGrid.innerHTML = html;
    }

    renderCalendar();

    if (prevMonthBtn) prevMonthBtn.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    if (nextMonthBtn) nextMonthBtn.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });
});
</script>
@endpush
