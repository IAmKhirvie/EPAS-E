<!-- Bottom Navigation for Mobile -->
<nav class="bottom-nav" id="bottomNav">
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

    <a href="{{ dynamic_route('modules.index') }}" class="nav-link {{ Request::is('modules*') ? 'active' : '' }}">
        <i class="fas fa-book-open"></i>
        <span class="nav-label">Modules</span>
    </a>

    <a href="{{ route('settings.index') }}" class="nav-link {{ Request::routeIs('settings.*') ? 'active' : '' }}">
        <i class="fas fa-cog"></i>
        <span class="nav-label">Settings</span>
    </a>
</nav>

<style>
    /* Bottom nav hidden by default, shown only on mobile (1032px matches mobile.css) */
    .bottom-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--bottom-nav-bg, #ffffff);
        border-top: 1px solid var(--bottom-nav-border, #e5e7eb);
        justify-content: space-around;
        align-items: center;
        z-index: var(--z-navbar); /* 100 — below sidebar/backdrop */
        padding: 0 0.5rem;
        box-shadow: 0 -2px 10px var(--bottom-nav-shadow, rgba(0,0,0,0.05));
    }

    @media (max-width: 1032px) {
        .bottom-nav {
            display: flex;
        }

        body {
            padding-bottom: 70px !important;
        }
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
        transition: transform 0.2s ease;
    }

    .bottom-nav .nav-link.active i {
        transform: scale(1.1);
    }

    .bottom-nav .nav-label {
        font-size: 0.625rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        transition: color 0.2s ease;
    }

    /* Active press effect */
    .bottom-nav .nav-link:active {
        transform: scale(0.92);
    }

    /* Safe area padding for notched devices */
    @supports (padding: env(safe-area-inset-bottom)) {
        .bottom-nav {
            padding-bottom: env(safe-area-inset-bottom);
            height: calc(60px + env(safe-area-inset-bottom));
        }

        @media (max-width: 1032px) {
            body {
                padding-bottom: calc(70px + env(safe-area-inset-bottom)) !important;
            }
        }
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
            display: none !important;
        }
    }

    /* Landscape adjustments */
    @media (max-width: 1032px) and (orientation: landscape) {
        .bottom-nav {
            height: 48px;
        }

        .bottom-nav .nav-link i {
            font-size: 1.1rem;
            margin-bottom: 0.125rem;
        }

        .bottom-nav .nav-label {
            font-size: 0.5625rem;
        }
    }
</style>
