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

    <!-- Left side - Logo and Title -->
    <div class="navbar-left-2">
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
                <i class="fas fa-newspaper" aria-hidden="true"></i>
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
            <button class="user-button" id="user-menu-btn">
                <div class="avatar">
                    @php
                    $user = Auth::user();
                    @endphp
                    <img id="navbar-avatar" src="{{ $user->profile_image_url }}" alt="User Avatar" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <span class="avatar-fallback" id="navbar-fallback" style="display: {{ $user->profile_image ? 'none' : 'flex' }};">{{ $user->initials }}</span>
                </div>
            </button>
            <div class="dropdown" id="user-dropdown">
                <div class="dropdown-content">
                    <div class="dropdown-header" id="dropdown-username">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('about') }}" class="dropdown-item">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        About Us
                    </a>
                    <a href="{{ route('contact') }}" class="dropdown-item">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        Contact Us
                    </a>
                    @if(!$user->hasVerifiedEmail())
                    <a href="{{ route('settings.index') }}#profile" class="dropdown-item text-warning">
                        <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                        Verify Email
                    </a>
                    @endif
                    <a href="{{ route('settings.index') }}" class="dropdown-item">
                        <i class="fas fa-cog" aria-hidden="true"></i>
                        Settings
                    </a>
                    <div class="dropdown-divider"></div>
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