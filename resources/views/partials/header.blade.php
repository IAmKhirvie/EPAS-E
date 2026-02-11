<!-- Lobby Navbar - Public -->
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

        <!-- About Icon -->
        <div class="navbar-item">
            <a class="icon-button" href="{{ route('about') }}" title="About">
                <i class="fa-solid fa-circle-info"></i>
            </a>
        </div>

        <!-- Contact Icon -->
        <div class="navbar-item">
            <a class="icon-button" href="{{ route('contact') }}" title="Contact">
                <i class="fa-solid fa-phone"></i>
            </a>
        </div>

        <!-- Dark Mode Toggle -->
        <div class="navbar-item">
            <button class="icon-button" id="dark-mode-toggle" title="Toggle Theme">
                <i class="fas fa-moon" id="dark-mode-icon"></i>
            </button>
        </div>

        <!-- Login Dropdown -->
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
    </div>
</header>

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
    /* Lobby navbar — transparent */
    .top-navbar.lobby-navbar {
        background: transparent !important;
        box-shadow: none !important;
    }

    /* Hide original title (keeps layout space so navbar doesn't shift) */
    .lobby-navbar .navbar-title-container {
        visibility: hidden;
    }

    /*
     * Blend overlay — mirrors .top-navbar layout exactly.
     * Uses same position/padding/flex so text lands in the same spot.
     * An invisible copy of the logo acts as spacer.
     */
    .title-blend {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        display: flex;
        align-items: center;
        padding: 1.5rem;              /* matches .top-navbar */
        z-index: 1031;
        pointer-events: none;
        mix-blend-mode: difference;
        font-family: 'Inter', sans-serif;
    }

    .title-blend-inner {
        display: flex;
        align-items: center;
        gap: 1rem;                    /* matches .navbar-logo-container */
    }

    /* Invisible logo spacer — same dimensions as .logo */
    .title-blend-spacer {
        height: 60px;
        width: auto;
        border-right: 2px solid transparent;
        padding-right: 0.5rem;
        opacity: 0;
        flex-shrink: 0;
    }

    .title-blend-text h2 {
        font-size: 1.125rem;          /* matches .navbar-brand h2 */
        font-weight: 500;
        color: white;
        margin: 0;
        white-space: nowrap;
    }

    .title-blend-text p {
        font-size: 0.875rem;          /* matches .navbar-brand p */
        color: white;
        margin: 0;
        white-space: nowrap;
    }

    /* ===== MOBILE — match public-header.css breakpoints ===== */
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

        /* Hide subtitle on mobile */
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