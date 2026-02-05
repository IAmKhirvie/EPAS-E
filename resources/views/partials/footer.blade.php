<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-lg-4 mb-4">
                <h5><i class="fas fa-graduation-cap me-2"></i>EPAS-E LMS</h5>
                <p>Electronic Products Assembly and Servicing Learning Management System by IETI College Marikina - A TESDA-Accredited Institution.</p>
                <div class="social-links mt-3">
                    <a href="https://www.facebook.com/ieti.marikina" target="_blank" class="text-light me-3" title="Facebook">
                        <i class="fab fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="mailto:ietimarikina8@yahoo.com" class="text-light me-3" title="Email">
                        <i class="fas fa-envelope fa-lg"></i>
                    </a>
                    <a href="tel:09171207428" class="text-light" title="Call Us">
                        <i class="fas fa-phone fa-lg"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('lobby') }}"><i class="fas fa-home me-2"></i>Home</a></li>
                    <li><a href="{{ route('about') }}"><i class="fas fa-info-circle me-2"></i>About</a></li>
                    <li><a href="{{ route('contact') }}"><i class="fas fa-envelope me-2"></i>Contact</a></li>
                    <li><a href="{{ route('contact') }}#faqAccordion"><i class="fas fa-question-circle me-2"></i>FAQ</a></li>
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
                    <li>
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <span>34 Lark Street, Sta. Elena,<br>Marikina City, Philippines</span>
                    </li>
                    <li>
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:09171207428">0917-120-7428</a>
                    </li>
                    <li>
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:8681643">868-16-431</a>
                    </li>
                    <li>
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:ietimarikina8@yahoo.com">ietimarikina8@yahoo.com</a>
                    </li>
                    <li>
                        <i class="fas fa-clock me-2"></i>
                        <span>Mon-Fri: 8AM-5PM<br>Sat: 8AM-12PM</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <!-- Bottom Section -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0">&copy; {{ date('Y') }} EPAS-E LMS. All rights reserved.</p>
                <small class="text-muted">IETI College of Science and Technology (Marikina), Inc.</small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-muted">
                    <a href="{{ route('about') }}" class="text-light text-decoration-none me-3">About Us</a>
                    <a href="{{ route('contact') }}" class="text-light text-decoration-none me-3">Contact</a>
                    <span class="text-muted">TESDA Accredited</span>
                </small>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
        color: #f8f9fa !important;
        padding: 3rem 0 1.5rem;
        margin-top: 3rem;
    }

    .footer h5 {
        color: #f8f9fa !important;
        font-weight: 600;
        margin-bottom: 1.25rem;
    }

    .footer p,
    .footer li,
    .footer span {
        color: #94a3b8 !important;
    }

    .footer-links li {
        margin-bottom: 0.75rem;
    }

    .footer-links a {
        color: #94a3b8 !important;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .footer-links a:hover {
        color: #3b82f6 !important;
        transform: translateX(5px);
    }

    .footer-contact li {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        color: #94a3b8 !important;
    }

    .footer-contact li i {
        color: #3b82f6;
        margin-top: 4px;
        min-width: 20px;
    }

    .footer-contact a {
        color: #94a3b8 !important;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-contact a:hover {
        color: #3b82f6 !important;
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
        background: #3b82f6;
        transform: translateY(-3px);
    }

    .footer hr {
        opacity: 0.2;
    }

    @media (max-width: 1032px) {
        .footer {
            padding: 2rem 0 1rem;
        }

        .footer .row>div {
            text-align: center;
        }

        .footer-contact li {
            justify-content: center;
        }

        .footer-links a:hover {
            transform: none;
        }
    }
</style>