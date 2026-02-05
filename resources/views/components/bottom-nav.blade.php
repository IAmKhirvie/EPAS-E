<!-- Bottom Navigation for Mobile -->
<nav class="bottom-nav d-md-none" id="bottomNav">
    <a href="{{ dynamic_route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span class="nav-label">Home</span>
    </a>

    <a href="{{ dynamic_route('courses.index') }}" class="nav-link {{ Request::is('courses*') ? 'active' : '' }}">
        <i class="fas fa-book"></i>
        <span class="nav-label">Courses</span>
    </a>

    <a href="{{ dynamic_route('grades.index') }}" class="nav-link {{ Request::is('grades*') ? 'active' : '' }}">
        <i class="fas fa-graduation-cap"></i>
        <span class="nav-label">Grades</span>
    </a>

    <a href="{{ dynamic_route('private.announcements.index') }}" class="nav-link {{ Request::is('announcements*') ? 'active' : '' }}">
        <i class="fas fa-bullhorn"></i>
        <span class="nav-label">News</span>
    </a>

    <button type="button" class="nav-link" id="mobileMenuToggle" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
        <i class="fas fa-bars"></i>
        <span class="nav-label">Menu</span>
    </button>
</nav>

<style>
    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bottom-nav-bg, #ffffff);
        border-top: 1px solid var(--bottom-nav-border, #e5e7eb);
        display: flex;
        justify-content: space-around;
        align-items: center;
        z-index: 1040;
        padding: 0 0.5rem;
        box-shadow: 0 -2px 10px var(--bottom-nav-shadow, rgba(0,0,0,0.05));
    }

    .bottom-nav .nav-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--bottom-nav-text, #6b7280);
        text-decoration: none;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s;
        flex: 1;
        max-width: 80px;
        position: relative;
        background: none;
        border: none;
    }

    .bottom-nav .nav-link:hover,
    .bottom-nav .nav-link:focus {
        color: var(--bs-primary);
        background: rgba(79, 70, 229, 0.05);
    }

    .bottom-nav .nav-link.active {
        color: var(--bs-primary);
    }

    .bottom-nav .nav-link.active::after {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 20px;
        height: 3px;
        background: var(--bs-primary);
        border-radius: 0 0 3px 3px;
    }

    .bottom-nav .nav-link i {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    .bottom-nav .nav-label {
        font-size: 0.625rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .bottom-nav .badge-dot {
        position: absolute;
        top: 0.25rem;
        right: 50%;
        transform: translateX(100%);
        width: 8px;
        height: 8px;
        background: var(--bs-danger);
        border-radius: 50%;
        border: 2px solid var(--bottom-nav-bg, #ffffff);
    }

    /* Dark mode support */
    .dark-mode {
        --bottom-nav-bg: #1e293b;
        --bottom-nav-border: #334155;
        --bottom-nav-shadow: rgba(0,0,0,0.3);
        --bottom-nav-text: #94a3b8;
    }

    /* Hide bottom nav when keyboard is open on mobile */
    @media (max-height: 500px) {
        .bottom-nav {
            display: none;
        }
    }

    /* Add padding to main content for bottom nav */
    @media (max-width: 767.98px) {
        body {
            padding-bottom: 70px;
        }
    }
</style>
