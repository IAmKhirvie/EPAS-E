<!-- Top Navbar - Private-->
<header class="top-navbar">

    <!-- Left side - Hamburger and Title -->
    <div class="navbar-left">
        <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        <div class="navbar-brand">
            <h2>EPAS-E</h2>
            <p>Electronic Products Assembly and Servicing</p>
        </div>
    </div>

    <!-- Left side - Logo and Title (mobile) -->
    <div class="navbar-left-2">
        <button class="mobile-sidebar-toggle" id="mobile-sidebar-toggle" aria-label="Open menu">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        <a class="navbar-brand" href="{{ route('lobby') }}">
            <div class="navbar-logo-container">
                <img src="{{ dynamic_asset('assets/EPAS-E.png') }}" alt="EPAS-E LMS" class="logo">
                <div class="navbar-title-container">
                    <h2>EPAS-E</h2>
                    <p>Electronic Products Assembly and Servicing</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Right side actions - Rounded container -->
    <div class="navbar-right">
        <!-- Home Icon -->
        <div class="navbar-item">
            <a class="icon-button" href="{{ route('lobby') }}" aria-label="Home">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
            </a>
        </div>

        <!-- Dark Mode Toggle -->
        <div class="navbar-item">
            <button class="icon-button" id="dark-mode-toggle" aria-label="Toggle dark mode">
                <i class="fas fa-moon" id="dark-mode-icon" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Notifications / Activity Feed -->
        <div class="navbar-item">
            <button class="icon-button" id="notifications-btn" title="Activity Feed" aria-label="Activity Feed">
                <i class="fas fa-bell" aria-hidden="true"></i>
                @if(isset($recentAnnouncementsCount) && $recentAnnouncementsCount > 0)
                <span class="notification-badge" id="notification-badge">
                    {{ $recentAnnouncementsCount }}
                </span>
                @endif
            </button>
            <div class="popover notifications-popover" id="notifications-popover">
                <div class="popover-header">
                    <div class="header-left">
                        <i class="fas fa-bullhorn me-2" aria-hidden="true"></i>
                        <span>Activity Feed</span>
                    </div>
                    <a href="{{ route('private.announcements.index') }}" class="view-all-btn">
                        <i class="fas fa-list me-1" aria-hidden="true"></i>
                        View All
                    </a>
                </div>
                <div class="notifications-list" id="notifications-list">
                    @php
                    $notifications = isset($recentAnnouncements) ? $recentAnnouncements : collect();
                    @endphp

                    @if($notifications->count() > 0)
                    @foreach($notifications as $announcement)
                    @php
                    $hasDeadline = !empty($announcement->deadline);
                    @endphp
                    <div class="notification-item {{ $announcement->is_urgent ?? false ? 'urgent' : '' }}"
                        data-announcement-id="{{ $announcement->id }}"
                        data-deadline="{{ $announcement->deadline ?? '' }}"
                        data-created-at="{{ $announcement->created_at->timestamp }}">
                        <a href="{{ route('private.announcements.show', $announcement) }}"
                            class="notification-link">
                            <div class="notification-icon">
                                @if($announcement->is_urgent ?? false)
                                <i class="fas fa-exclamation-circle urgent-icon"></i>
                                @elseif($announcement->is_pinned ?? false)
                                <i class="fas fa-thumbtack pinned-icon"></i>
                                @else
                                <i class="fas fa-bell regular-icon"></i>
                                @endif
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    {{ Str::limit($announcement->title, 45) }}
                                </div>
                                <div class="notification-message">
                                    {{ Str::limit(strip_tags($announcement->content ?? $announcement->body ?? ''), 70) }}
                                </div>
                                <div class="notification-meta">
                                    @if($hasDeadline)
                                    <span class="notification-deadline">
                                        <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                        Due: {{ $announcement->deadline->format('M j') }}
                                    </span>
                                    @endif
                                    <span class="notification-time">
                                        <i class="fas fa-calendar me-1" aria-hidden="true"></i>
                                        {{ $announcement->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                    @else
                    <div class="notification-item empty">
                        <div class="notification-content text-center py-4">
                            <div class="empty-icon">
                                <i class="fas fa-inbox" aria-hidden="true"></i>
                            </div>
                            <div class="empty-text">No announcements yet</div>
                            <div class="empty-subtext">Check back later for updates</div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="popover-footer">
                    <a href="{{ route('private.announcements.index') }}" class="view-all-link-footer">
                        <i class="fas fa-th-list me-2" aria-hidden="true"></i>
                        View All Announcements
                        <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- User menu -->
        <div class="navbar-item">
            @php $user = Auth::user(); @endphp
            {{-- Desktop: compact avatar button --}}
            <button class="user-button user-button--desktop" id="user-menu-btn">
                <div class="avatar">
                    <img id="navbar-avatar" src="{{ $user->profile_image_url }}" alt="User Avatar" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <span class="avatar-fallback" id="navbar-fallback" style="display: {{ $user->profile_image ? 'none' : 'flex' }};">{{ $user->initials }}</span>
                </div>
            </button>
            {{-- Mobile: ID card strip button --}}
            <button class="user-button user-button--mobile" id="user-menu-btn-mobile">
                <div class="avatar">
                    <img src="{{ $user->profile_image_url }}" alt="Avatar" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <span class="avatar-fallback" style="display: {{ $user->profile_image ? 'none' : 'flex' }};">{{ $user->initials }}</span>
                </div>
                <div class="user-button__info">
                    <span class="user-button__name">{{ $user->first_name }}</span>
                    <span class="user-button__role">{{ ucfirst($user->role) }}</span>
                </div>
                <i class="fas fa-chevron-down user-button__arrow"></i>
            </button>
            <div class="dropdown" id="user-dropdown">
                <div class="dropdown-content">
                    {{-- Profile Header —  ID card style --}}
                    <div class="user-card-header">
                        <div class="user-card-avatar">
                            <img src="{{ $user->profile_image_url }}" alt="Avatar"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <span class="avatar-fallback"
                                  style="display: {{ $user->profile_image ? 'none' : 'flex' }};">{{ $user->initials }}</span>
                        </div>
                        <h2 class="user-card-name">{{ $user->full_name }}</h2>
                        <h3 class="user-card-subtitle">
                            <span class="user-card-role-badge role-{{ $user->role }}">{{ $user->role_display }}</span>
                            @if($user->student_id)
                            <span class="user-card-id">ID: {{ $user->student_id }}</span>
                            @endif
                        </h3>
                    </div>

                    <div class="dropdown-divider"></div>

                    {{-- User Info Section --}}
                    <div class="user-card-info-section">
                        <div class="info-row">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                            <div class="info-content">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $user->email }}</span>
                            </div>
                        </div>
                        @if($user->department)
                        <div class="info-row">
                            <i class="fas fa-building" aria-hidden="true"></i>
                            <div class="info-content">
                                <span class="info-label">Department</span>
                                <span class="info-value">{{ $user->department->name }}</span>
                            </div>
                        </div>
                        @endif
                        @if($user->section)
                        <div class="info-row">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            <div class="info-content">
                                <span class="info-label">Section</span>
                                <span class="info-value">{{ $user->section }}</span>
                            </div>
                        </div>
                        @endif
                        @if($user->room_number)
                        <div class="info-row">
                            <i class="fas fa-door-open" aria-hidden="true"></i>
                            <div class="info-content">
                                <span class="info-label">Room</span>
                                <span class="info-value">{{ $user->room_number }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="info-row">
                            <i class="fas fa-trophy" aria-hidden="true"></i>
                            <div class="info-content">
                                <span class="info-label">Points</span>
                                <span class="info-value">{{ number_format($user->total_points ?? 0) }} pts</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-circle-check" aria-hidden="true"></i>
                            <div class="info-content">
                                <span class="info-label">Status</span>
                                <span class="info-value status-{{ $user->stat ? 'active' : 'pending' }}">{{ $user->status_display }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown-divider"></div>

                    {{-- Dark Mode Toggle --}}
                    <div class="user-card-toggles">
                        <div class="toggle-row">
                            <div class="toggle-label">
                                <i class="fas fa-moon" aria-hidden="true"></i>
                                <span>Dark Mode</span>
                            </div>
                            <label class="toggle-switch" for="user-card-dark-toggle">
                                <input type="checkbox" id="user-card-dark-toggle">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="dropdown-divider"></div>

                    {{-- Verify Email Warning --}}
                    @if(!$user->hasVerifiedEmail())
                    <a href="{{ route('settings.index') }}#profile" class="dropdown-item text-warning">
                        <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                        Verify Email
                    </a>
                    @endif

                    {{-- Navigation Links --}}
                    <a href="{{ route('settings.index') }}" class="dropdown-item">
                        <i class="fas fa-cog" aria-hidden="true"></i>
                        Settings
                    </a>
                    <a href="{{ route('about') }}" class="dropdown-item">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        About EPAS-E
                    </a>
                    <a href="{{ route('contact') }}" class="dropdown-item">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        Contact Us
                    </a>
                    <a href="{{ route('help-support') }}" class="dropdown-item">
                        <i class="fas fa-question-circle" aria-hidden="true"></i>
                        Help & Support
                    </a>

                    <div class="dropdown-divider"></div>

                    {{-- Logout --}}
                    <button class="dropdown-item text-danger" id="logout-btn">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Logout Form -->
<form id="logout-form" action="{{ dynamic_route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Dark mode toggle sync for user card -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('user-card-dark-toggle');
    if (!toggle) return;

    // Sync initial state
    toggle.checked = document.body.classList.contains('dark-mode');

    // Toggle dark mode when switch changes
    toggle.addEventListener('change', function() {
        var isDark = document.body.classList.contains('dark-mode');
        var newTheme = isDark ? 'light' : 'dark';
        document.body.classList.toggle('dark-mode', !isDark);
        document.documentElement.classList.toggle('dark-mode', !isDark);
        localStorage.setItem('theme', newTheme);
        // Update the navbar icon
        var icon = document.getElementById('dark-mode-icon');
        if (icon) icon.className = !isDark ? 'fas fa-sun' : 'fas fa-moon';
        // Notify other components
        window.dispatchEvent(new CustomEvent('themeChange', { detail: { theme: newTheme } }));
    });

    // Sync when theme changes from other sources
    window.addEventListener('themeChange', function(e) {
        toggle.checked = (e.detail.theme === 'dark');
    });

    window.addEventListener('storage', function(e) {
        if (e.key === 'theme') {
            toggle.checked = (e.newValue === 'dark');
        }
    });
});
</script>