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

<style>
    /* Navbar scroll state styles */
    .top-navbar.lobby-navbar {
        transition: all 0.3s ease;
    }

    /* When scrolled - add background and change text colors */
    .top-navbar.lobby-navbar.scrolled {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    }

    .top-navbar.lobby-navbar.scrolled .navbar-brand h2 {
        color: #1e293b !important;
    }

    .top-navbar.lobby-navbar.scrolled .navbar-brand p {
        color: #64748b !important;
    }

    .top-navbar.lobby-navbar.scrolled .logo {
        filter: none;
    }

    /* Dark mode scrolled state */
    .dark-mode .top-navbar.lobby-navbar.scrolled {
        background: rgba(30, 41, 59, 0.95) !important;
    }

    .dark-mode .top-navbar.lobby-navbar.scrolled .navbar-brand h2 {
        color: #f8f9fa !important;
    }

    .dark-mode .top-navbar.lobby-navbar.scrolled .navbar-brand p {
        color: #94a3b8 !important;
    }

    /* Alternative: Mix-blend-mode approach for the title (works on image backgrounds) */
    .navbar-title-blend {
        mix-blend-mode: difference;
    }

    /* Ensure navbar title stays visible on different backgrounds */
    @supports (mix-blend-mode: difference) {
        .top-navbar.lobby-navbar:not(.scrolled) .navbar-title-container {
            /* Use mix-blend-mode for hero sections with images */
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('mainNavbar');
        const scrollThreshold = 50; // Pixels to scroll before changing

        function handleScroll() {
            if (window.scrollY > scrollThreshold) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        // Initial check
        handleScroll();

        // Listen for scroll events
        window.addEventListener('scroll', handleScroll, {
            passive: true
        });
    });
</script>