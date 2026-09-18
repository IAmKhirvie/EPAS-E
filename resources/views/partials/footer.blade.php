<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-lg-4 mb-4">
                <h5><i class="fas fa-graduation-cap me-2"></i>Hasa LMS</h5>
                <p>Courses, lessons, assessments, and progress in one learning space.</p>
                <div class="social-links mt-3">
                    <a href="{{ route('contact') }}" class="text-light me-3" title="Contact">
                        <i class="fas fa-envelope fa-lg"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('welcome') }}"><i class="fas fa-home me-2"></i>Home</a></li>
                    <li><a href="{{ route('about') }}"><i class="fas fa-info-circle me-2"></i>About</a></li>
                    <li><a href="{{ route('contact') }}"><i class="fas fa-envelope me-2"></i>Contact</a></li>
                </ul>
            </div>

            <!-- Portal Access -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Portal Access</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('login') }}"><i class="fas fa-user-graduate me-2"></i>Student Login</a></li>
                    <li><a href="{{ route('private.login') }}"><i class="fas fa-chalkboard-teacher me-2"></i>Instructor Login</a></li>
                    <li><a href="{{ route('register') }}"><i class="fas fa-user-plus me-2"></i>Student Registration</a></li>
                    <li><a href="{{ route('password.request') }}"><i class="fas fa-key me-2"></i>Forgot Password</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 mb-4">
                <h5>Contact Info</h5>
                <ul class="list-unstyled footer-contact">
                    <li><i class="fas fa-envelope me-2"></i><a href="{{ route('contact') }}">Send a message</a></li>
                </ul>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <!-- Bottom Section -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0">&copy; {{ date('Y') }} Hasa LMS. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-muted">
                    <a href="{{ route('about') }}" class="text-light text-decoration-none me-3">About Us</a>
                    <a href="{{ route('contact') }}" class="text-light text-decoration-none me-3">Contact</a>
                </small>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer {
        background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%) !important;
        color: var(--theme-primary-ink) !important;
        padding: 3rem 0 1.5rem;
        margin-top: 3rem;
    }

    .footer h5 {
        color: var(--theme-primary-ink) !important;
        font-weight: 600;
        margin-bottom: 1.25rem;
    }

    .footer p,
    .footer li,
    .footer span {
        color: var(--theme-primary-ink) !important;
    }

    .footer-links li {
        margin-bottom: 0.75rem;
    }

    .footer-links a {
        color: var(--theme-primary-ink) !important;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .footer-links a:hover {
        color: var(--theme-secondary-ink) !important;
        transform: translateX(5px);
    }

    .footer-contact li {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        color: var(--theme-primary-ink) !important;
    }

    .footer-contact li i {
        color: var(--theme-secondary-ink);
        margin-top: 4px;
        min-width: 20px;
    }

    .footer-contact a {
        color: var(--theme-primary-ink) !important;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-contact a:hover {
        color: var(--theme-secondary-ink) !important;
    }

    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background: var(--theme-secondary);
        transform: translateY(-3px);
    }

    .footer hr {
        opacity: 0.2;
    }

    @media (max-width: 1032px) {
        .footer {
            padding: 2rem 1rem 1rem;
            margin-top: 0 !important;
        }

        .footer .row>div {
            text-align: center;
            margin-bottom: 1.5rem !important;
        }

        .footer-contact li {
            justify-content: center;
        }

        .footer-links a:hover {
            transform: none;
        }

        .social-links {
            justify-content: center;
            display: flex;
            gap: 0.5rem;
        }

        .footer h5 {
            font-size: 1.1rem;
            margin-bottom: 0.75rem !important;
        }

        .footer p,
        .footer li {
            font-size: 0.875rem;
        }

        .footer hr {
            margin: 1.5rem 0 !important;
        }

        /* Bottom section stack on mobile */
        .footer .row.align-items-center > div {
            text-align: center !important;
            margin-bottom: 0.5rem !important;
        }
    }

    @media (max-width: 480px) {
        .footer {
            padding: 1.5rem 0.75rem 0.75rem;
        }

        .footer .col-lg-4,
        .footer .col-lg-2,
        .footer .col-lg-3 {
            margin-bottom: 1rem !important;
        }

        .footer h5 {
            font-size: 1rem;
        }

        .footer p,
        .footer li,
        .footer a {
            font-size: 0.8125rem !important;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-contact li {
            margin-bottom: 0.75rem;
            font-size: 0.8125rem;
        }
    }
</style>
