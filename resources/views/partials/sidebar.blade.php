<!-- Left Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-content">
        <!-- User Profile Card -->
        <div class="sidebar-profile">
            <div class="profile-inner">
                @php
                $user = Auth::user();
                @endphp

                <form id="avatar-form" action="{{ dynamic_route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    <input type="file" id="avatar-upload" name="avatar" accept="image/png, image/jpeg">
                </form>

                <div class="avatar" data-tooltip="User Profile" onclick="document.getElementById('avatar-upload').click()">
                    <img src="{{ $user->profile_image_url }}" alt="User Avatar" id="sidebar-avatar" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <span class="avatar-fallback" id="sidebar-fallback" style="display: {{ $user->profile_image ? 'none' : 'flex' }};">{{ $user->initials }}</span>
                </div>

                <div class="profile-info">
                    <h3 id="sidebar-username">{{ $user->first_name }} {{ $user->last_name }}</h3>
                    <p class="profile-role" id="sidebar-role">{{ ucfirst($user->role) }}</p>
                    @if(!$user->hasVerifiedEmail())
                    <a href="{{ route('settings.index') }}#profile" class="email-verify-badge" title="Click to verify your email">
                        <i class="fas fa-exclamation-circle"></i> Email not verified
                    </a>
                    @endif
                </div>

                <div class="profile-id">
                    @if($user->student_id)
                    ID: <span id="sidebar-employee-id">{{ $user->student_id }}</span>
                    @else
                    ID: <span id="sidebar-employee-id">{{ $user->id }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <div class="sidebar-section">
            <div class="sidebar-label">Main Menu</div>
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                    <i class="fas fa-chart-bar"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('courses.index') }}" class="nav-item {{ Request::is('courses*') ? 'active' : '' }}" data-tooltip="Courses">
                    <i class="fas fa-book"></i>
                    <span>Courses</span>
                </a>

                <a href="{{ route('grades.index') }}" class="nav-item {{ Request::is('grades*') ? 'active' : '' }}" data-tooltip="Grades">
                    <i class="fas fa-graduation-cap"></i>
                    <span>{{ Auth::user()->role === 'student' ? 'My Grades' : 'Grades' }}</span>
                </a>

                @if(in_array(strtolower(Auth::user()->role), ['admin', 'instructor']))
                <a href="{{ route('analytics.dashboard') }}" class="nav-item {{ Request::is('analytics*') ? 'active' : '' }}" data-tooltip="Analytics">
                    <i class="fas fa-chart-pie"></i>
                    <span>Analytics</span>
                </a>
                @endif
            </nav>
        </div>

        <!-- Content Management for Admin and Instructors -->
        @if(in_array(strtolower(Auth::user()->role), ['admin', 'instructor']))
        <div class="sidebar-section">
            <div class="sidebar-label">Content</div>
            <nav class="sidebar-nav">
                <a href="{{ route('content.management') }}" class="nav-item {{ Request::is('content-management*') ? 'active' : '' }}" data-tooltip="Content Management">
                    <i class="fas fa-cubes"></i>
                    <span>Content Management</span>
                </a>
            </nav>
        </div>
        @endif

        <!-- For Admin -->
        @if(strtolower(Auth::user()->role) === 'admin')
        <div class="sidebar-section">
            <div class="sidebar-label">Administration</div>
            <nav class="sidebar-nav">
                @php
                $pendingRegistrations = \App\Models\Registration::whereIn('status', ['pending', 'email_verified'])->count();
                @endphp
                <a href="{{ route('admin.registrations.index') }}" class="nav-item {{ Request::is('admin/registrations*') ? 'active' : '' }}" data-tooltip="Registrations">
                    <i class="fas fa-user-clock"></i>
                    <span>Registrations</span>
                    @if($pendingRegistrations > 0)
                    <span class="nav-badge">{{ $pendingRegistrations }}</span>
                    @endif
                </a>

                <!-- Users with Flyout Sub-menu -->
                <div class="nav-item-flyout {{ Request::is('private/users*') || Request::is('private/students*') || Request::is('private/instructors*') ? 'active' : '' }}">
                    <a href="javascript:void(0)" class="nav-item has-flyout" data-tooltip="Users">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                        <i class="fas fa-chevron-right nav-arrow-right"></i>
                    </a>
                    <div class="flyout-menu">
                        <div class="flyout-header">Users</div>
                        <a href="{{ route('private.users.index') }}" class="flyout-item {{ Request::is('private/users*') && !Request::is('*instructors*') && !Request::is('*students*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            <span>All Users</span>
                        </a>
                        <a href="{{ route('private.students.index') }}" class="flyout-item {{ Request::is('private/students*') ? 'active' : '' }}">
                            <i class="fas fa-user-graduate"></i>
                            <span>Students</span>
                        </a>
                        <a href="{{ route('private.instructors.index') }}" class="flyout-item {{ Request::is('private/instructors*') ? 'active' : '' }}">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Instructors</span>
                        </a>
                    </div>
                </div>

                <!-- Classes with Flyout Sub-menu -->
                <div class="nav-item-flyout {{ Request::is('class-management*') || Request::is('enrollment-requests*') ? 'active' : '' }}">
                    <a href="javascript:void(0)" class="nav-item has-flyout" data-tooltip="Classes">
                        <i class="fas fa-chalkboard"></i>
                        <span>Classes</span>
                        <i class="fas fa-chevron-right nav-arrow-right"></i>
                    </a>
                    <div class="flyout-menu">
                        <div class="flyout-header">Classes</div>
                        <a href="{{ route('class-management.index') }}" class="flyout-item {{ Request::is('class-management*') ? 'active' : '' }}">
                            <i class="fas fa-sitemap"></i>
                            <span>All Classes</span>
                        </a>
                        <a href="{{ route('enrollment-requests.index') }}" class="flyout-item {{ Request::is('enrollment-requests*') ? 'active' : '' }}">
                            <i class="fas fa-user-plus"></i>
                            <span>Enrollments</span>
                        </a>
                    </div>
                </div>

            </nav>
        </div>
        @endif

        <!-- For Instructors -->
        @if(in_array(strtolower(Auth::user()->role), ['instructor']))
        <div class="sidebar-section">
            <div class="sidebar-label">Teaching</div>
            <nav class="sidebar-nav">
                <a href="{{ route('class-management.index') }}" class="nav-item {{ Request::is('class-management*') ? 'active' : '' }}" data-tooltip="My Classes">
                    <i class="fas fa-chalkboard"></i>
                    <span>My Classes</span>
                </a>

                <a href="{{ route('private.students.index') }}" class="nav-item {{ Request::is('private/students*') ? 'active' : '' }}" data-tooltip="My Students">
                    <i class="fas fa-user-graduate"></i>
                    <span>My Students</span>
                </a>

                <a href="{{ route('enrollment-requests.index') }}" class="nav-item {{ Request::is('enrollment-requests*') ? 'active' : '' }}" data-tooltip="Enrollment Requests">
                    <i class="fas fa-user-plus"></i>
                    <span>Enrollments</span>
                </a>
            </nav>
        </div>
        @endif

        <!-- Help & Support -->
        <div class="sidebar-section">
            <nav class="sidebar-nav">
                <a href="{{ route('help-support') }}" class="nav-item {{ Request::is('help-support*') ? 'active' : '' }}" data-tooltip="Help & Support">
                    <i class="fas fa-question-circle"></i>
                    <span>Help & Support</span>
                </a>
            </nav>
        </div>
    </div>
</aside>
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get sidebar element for positioning
        const sidebar = document.getElementById('sidebar');

        // ===== TOOLTIP POSITIONING FOR COLLAPSED SIDEBAR =====
        // Since tooltips use position:fixed to escape overflow clipping,
        // we need JS to set their vertical position on hover.
        function setupTooltipPositioning() {
            const navItems = sidebar.querySelectorAll('.nav-item[data-tooltip]');
            navItems.forEach(function(item) {
                item.addEventListener('mouseenter', function() {
                    if (!sidebar.classList.contains('collapsed')) return;
                    if (window.innerWidth < 1032) return; // no tooltips on mobile
                    const rect = this.getBoundingClientRect();
                    const centerY = rect.top + rect.height / 2;
                    // Set CSS custom properties for tooltip positioning
                    this.style.setProperty('--tooltip-top', centerY + 'px');
                });
            });

            // Also handle avatar tooltip
            const avatar = sidebar.querySelector('.avatar[data-tooltip]');
            if (avatar) {
                avatar.addEventListener('mouseenter', function() {
                    if (!sidebar.classList.contains('collapsed')) return;
                    if (window.innerWidth < 1032) return;
                    const rect = this.getBoundingClientRect();
                    const centerY = rect.top + rect.height / 2;
                    this.style.setProperty('--tooltip-top', centerY + 'px');
                });
            }
        }
        setupTooltipPositioning();

        // Handle flyout menu clicks
        document.querySelectorAll('.nav-item.has-flyout').forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const flyoutGroup = this.closest('.nav-item-flyout');
                const flyoutMenu = flyoutGroup.querySelector('.flyout-menu');
                const isOpen = flyoutGroup.classList.contains('open');

                // Close all other flyouts first
                document.querySelectorAll('.nav-item-flyout.open').forEach(function(openFlyout) {
                    if (openFlyout !== flyoutGroup) {
                        openFlyout.classList.remove('open');
                    }
                });

                // Toggle current flyout
                if (isOpen) {
                    flyoutGroup.classList.remove('open');
                } else {
                    flyoutGroup.classList.add('open');

                    // Position the flyout menu
                    const triggerRect = this.getBoundingClientRect();
                    const sidebarRect = sidebar.getBoundingClientRect();

                    flyoutMenu.style.left = sidebarRect.right + 8 + 'px';
                    flyoutMenu.style.top = triggerRect.top + 'px';

                    // Ensure flyout doesn't go off screen
                    const flyoutRect = flyoutMenu.getBoundingClientRect();
                    if (flyoutRect.bottom > window.innerHeight) {
                        flyoutMenu.style.top = (window.innerHeight - flyoutRect.height - 10) + 'px';
                    }
                }
            });
        });

        // Close flyouts when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-item-flyout')) {
                document.querySelectorAll('.nav-item-flyout.open').forEach(function(openFlyout) {
                    openFlyout.classList.remove('open');
                });
            }
        });

        // Close flyouts when pressing Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.nav-item-flyout.open').forEach(function(openFlyout) {
                    openFlyout.classList.remove('open');
                });
            }
        });
    });
</script>