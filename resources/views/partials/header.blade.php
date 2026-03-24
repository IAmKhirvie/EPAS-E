<!-- Unified Public Navbar (lobby, contact, about) — works for both guest and authenticated -->
<header class="top-navbar lobby-navbar" id="mainNavbar">

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
            <a class="icon-button" href="{{ route('lobby') }}" title="Home">
                <i class="fa-solid fa-house"></i>
            </a>
        </div>
        <div class="navbar-item">
            <a href="{{ route('about') }}" class="icon-button">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            </a>
        </div>
        <div class="navbar-item">
            <a href="{{ route('contact') }}" class="icon-button">
                <i class="fa-solid fa-phone" aria-hidden="true"></i>
            </a>
        </div>
        <div class="navbar-item">
            <button class="icon-button" id="dark-mode-toggle" aria-label="Toggle dark mode">
                <i class="fas fa-moon" id="dark-mode-icon" aria-hidden="true"></i>
            </button>
        </div>
        {{-- ═══ AUTHENTICATED: Notifications + User Avatar ═══ --}}
        @auth
        @php $user = Auth::user(); @endphp
        <!-- Dark Mode Toggle -->
        <div class="navbar-item">
            <a class="icon-button" href="{{ route('dashboard') }}" title="Contact">
                <i class="fas fa-chart-bar"></i>
            </a>
        </div>

        <!-- Notifications / Announcements -->
        <div class="navbar-item">
            <button class="icon-button" id="notifications-btn" title="Announcements" aria-label="Announcements">
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
                        <span>Announcements</span>
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
            {{-- Desktop: compact avatar button --}}
            <button class="user-button user-button--desktop" id="user-menu-btn">
                <div class="icon-button" id="desktop-avatar-trigger" title="Click to change photo">
                    <i class="fas fa-user"></i>
                </div>
            </button>
            {{-- Mobile: ID card strip button --}}
            <button class="user-button user-button--mobile" id="user-menu-btn-mobile">
                <div class="avatar" id="mobile-avatar-trigger" title="Tap to change photo">
                    <img src="{{ $user->profile_image_url }}" alt="User Avatar" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
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
                    {{-- Profile Header — ID card style --}}
                    <div class="user-card-header" id="dropdown-avatar-trigger" title="Tap to change photo"
                        style="cursor: pointer; {{ $user->profile_image ? 'background-image: url(' . $user->profile_image_url . ');' : '' }}">
                        <div class="user-card-header-overlay"></div>
                        @if(!$user->profile_image)
                        <span class="user-card-header-initials">{{ $user->initials }}</span>
                        @endif
                        <div class="user-card-header-info">
                            <h2 class="user-card-name">{{ $user->full_name }}</h2>
                            <h3 class="user-card-subtitle">
                                <span class="user-card-role-badge role-{{ $user->role }}">{{ $user->role_display }}</span>
                                @if($user->student_id)
                                <span class="user-card-id">ID: {{ $user->student_id }}</span>
                                @endif
                            </h3>
                        </div>
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
                        <div class="info-row">
                            <i class="fas fa-trophy" aria-hidden="true"></i>
                            <div class="info-content">
                                <span class="info-label">Points</span>
                                <span class="info-value">{{ number_format($user->total_points ?? 0) }} pts</span>
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
                    <a href="{{ route('about') }}" class="dropdown-item">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        About Us
                    </a>

                    <a href="{{ route('contact') }}" class="dropdown-item">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        Contact
                    </a>

                    <a href="{{ route('settings.index') }}" class="dropdown-item">
                        <i class="fas fa-cog" aria-hidden="true"></i>
                        Settings
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
        @endauth



        {{-- ═══ GUEST: Login Dropdown ═══ --}}
        @guest
        <!-- Login -->
        <div class="navbar-item">
            <button class="icon-button" id="login-dropdown-btn" title="Login">
                <i class="fa-solid fa-user"></i>
            </button>
            <div class="dropdown" id="login-dropdown">
                <div class="dropdown-content">
                    <div class="login-modal-header">
                        <h6>Choose Login Portal</h6>
                    </div>
                    <a class="dropdown-item login-option admin" href="{{ route('admin.login') }}">
                        <i class="fas fa-user-shield"></i>
                        <div>
                            <strong>Admin Login</strong>
                            <small class="d-block">System Administration</small>
                        </div>
                    </a>
                    <a class="dropdown-item login-option instructor" href="{{ route('instructor.login') }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <div>
                            <strong>Instructor Login</strong>
                            <small class="d-block">Teaching Portal</small>
                        </div>
                    </a>
                    <a class="dropdown-item login-option student" href="{{ route('login') }}">
                        <i class="fas fa-user-graduate"></i>
                        <div>
                            <strong>Student Login</strong>
                            <small class="d-block">Learning Portal</small>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item login-option" href="{{ route('register') }}">
                        <i class="fas fa-user-plus"></i>
                        <div>
                            <strong>Student Registration</strong>
                            <small class="d-block">Create new account</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        @endguest
    </div>
</header>

{{-- ═══ Auth Forms (outside header) ═══ --}}
@auth
<!-- Logout Form -->
<form id="logout-form" action="{{ dynamic_route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Avatar Upload Form (hidden) -->
<form id="navbar-avatar-form" action="{{ dynamic_route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" style="display: none;">
    @csrf
    <input type="file" id="navbar-avatar-upload" name="avatar" accept="image/*">
</form>

<!-- Avatar upload trigger script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var fileInput = document.getElementById('navbar-avatar-upload');
        var form = document.getElementById('navbar-avatar-form');
        if (!fileInput || !form) return;

        ['mobile-avatar-trigger', 'dropdown-avatar-trigger'].forEach(function(id) {
            var trigger = document.getElementById(id);
            if (!trigger) return;
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.click();
            });
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                form.submit();
            }
        });
    });
</script>

<!-- Dark mode toggle sync for user card -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.getElementById('user-card-dark-toggle');
        if (!toggle) return;
        toggle.checked = document.body.classList.contains('dark-mode');
        toggle.addEventListener('change', function() {
            var isDark = document.body.classList.contains('dark-mode');
            var newTheme = isDark ? 'light' : 'dark';
            document.body.classList.toggle('dark-mode', !isDark);
            document.documentElement.classList.toggle('dark-mode', !isDark);
            localStorage.setItem('theme', newTheme);
            var icon = document.getElementById('dark-mode-icon');
            if (icon) icon.className = !isDark ? 'fas fa-sun' : 'fas fa-moon';
            window.dispatchEvent(new CustomEvent('themeChange', {
                detail: {
                    theme: newTheme
                }
            }));
        });
        window.addEventListener('themeChange', function(e) {
            toggle.checked = (e.detail.theme === 'dark');
        });
        window.addEventListener('storage', function(e) {
            if (e.key === 'theme') toggle.checked = (e.newValue === 'dark');
        });
    });
</script>
@endauth

<!-- Title blend overlay — mirrors navbar layout so text aligns perfectly via CSS, no JS needed -->
<div class="title-blend" aria-hidden="true">
    <div class="title-blend-inner">
        <img src="{{ dynamic_asset('assets/EPAS-E.png') }}" alt="" class="title-blend-spacer">
        <div class="title-blend-text">
            <h2>EPAS-E</h2>
            <p>Electronic Products Assembly and Servicing</p>
        </div>
    </div>
</div>

<style>
    /* Hide original title (keeps layout space so navbar doesn't shift) */
    .lobby-navbar .navbar-title-container {
        visibility: hidden;
    }

    .title-blend {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        display: flex;
        align-items: center;
        padding: 1.5rem;
        z-index: 1031;
        pointer-events: none;
        mix-blend-mode: difference;
        font-family: 'Inter', sans-serif;
    }

    .title-blend-inner {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .title-blend-spacer {
        height: 60px;
        width: auto;
        border-right: 2px solid transparent;
        padding-right: 0.5rem;
        opacity: 0;
        flex-shrink: 0;
    }

    .title-blend-text h2 {
        font-size: 1.125rem;
        font-weight: 500;
        color: white;
        margin: 0;
        white-space: nowrap;
    }

    .title-blend-text p {
        font-size: 0.875rem;
        color: white;
        margin: 0;
        white-space: nowrap;
    }

    @media (max-width: 1032px) {
        .title-blend {
            padding: 0 0.75rem;
            height: 56px;
        }

        .title-blend-spacer {
            height: 36px;
        }

        .title-blend-inner {
            gap: 0.5rem;
        }

        .title-blend-text p {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .title-blend {
            padding: 0 0.375rem;
            height: 52px;
        }

        .title-blend-spacer {
            height: 28px;
        }

        .title-blend-text h2 {
            font-size: 0.875rem;
        }
    }

    @media (max-width: 380px) {
        .title-blend {
            padding: 0 0.25rem;
            height: 48px;
        }

        .title-blend-spacer {
            height: 24px;
            border-right-width: 1px;
            padding-right: 0.25rem;
        }

        .title-blend-inner {
            gap: 0.25rem;
        }

        .title-blend-text h2 {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 340px) {
        .title-blend {
            display: none;
        }
    }
</style>