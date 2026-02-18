<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPAS-E LMS - Electronic Products Assembly and Servicing</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body>
    @auth
    @include('partials.navbar')
    @else
    @include('partials.header')
    @endauth

    <!-- Hero Section with Slideshow -->
    <section class="hero-section">
        <!-- Slideshow Container -->
        <div class="slideshow-container" id="heroSlideshow">
            @php
            $slides = [
            'epas1.jpg',
            'epas2.jpg',
            'epas3.jpg',
            'epas4.jpg'
            ];
            @endphp

            @foreach($slides as $index => $slide)
            <div class="slide {{ $index === 0 ? 'active' : '' }}"
                style="background-image: url('{{ dynamic_asset("assets/{$slide}") }}');"></div>
            @endforeach
        </div>

        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title fade-in-up">EPAS-E Learning Management System</h1>
                        <p class="hero-subtitle fade-in-up delay-1">
                            Electronic Products Assembly and Servicing - Empowering students with hands-on technical education and digital learning experiences.
                        </p>
                        <div class="fade-in-up delay-2">
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg me-3">
                                <i class="fas fa-user-plus me-2"></i>Register Now
                            </a>
                            <a href="#features" class="btn btn-outline-light btn-lg">
                                <i class="fa-solid fa-circle-question me-2"></i>About Us
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-features">
                        <div class="row g-3">
                            <div class="col-md-6 fade-in-up delay-1">
                                <div class="feature-card">
                                    <i class="fas fa-laptop-code feature-icon"></i>
                                    <h4>Digital Learning</h4>
                                    <p>Access courses and materials anytime, anywhere</p>
                                </div>
                            </div>
                            <div class="col-md-6 fade-in-up delay-2">
                                <div class="feature-card">
                                    <i class="fas fa-tools feature-icon"></i>
                                    <h4>Practical Skills</h4>
                                    <p>Hands-on training in electronics assembly</p>
                                </div>
                            </div>
                            <div class="col-md-6 fade-in-up delay-2">
                                <div class="feature-card">
                                    <i class="fas fa-chart-line feature-icon"></i>
                                    <h4>Progress Tracking</h4>
                                    <p>Monitor your learning journey</p>
                                </div>
                            </div>
                            <div class="col-md-6 fade-in-up delay-3">
                                <div class="feature-card">
                                    <i class="fas fa-certificate feature-icon"></i>
                                    <h4>Certification</h4>
                                    <p>Earn recognized qualifications</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose EPAS-E LMS?</h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body">
                            <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                            <h4 class="card-title">Mobile Friendly</h4>
                            <p class="card-text">Access your courses on any device with our responsive design that works perfectly on smartphones, tablets, and desktops.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body">
                            <i class="fas fa-bolt fa-3x text-warning mb-3"></i>
                            <h4 class="card-title">Fast & Reliable</h4>
                            <p class="card-text">Experience lightning-fast performance with our optimized platform that ensures smooth learning without interruptions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body">
                            <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                            <h4 class="card-title">Secure Platform</h4>
                            <p class="card-text">Your data is protected with enterprise-grade security measures and regular backups.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-6 stat-item">
                    <div class="stat-number">{{ $totalStudents ?? 0 }}</div>
                    <div class="stat-label">Active Students</div>
                </div>
                <div class="col-md-3 col-6 stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Expert Instructors</div>
                </div>
                <div class="col-md-3 col-6 stat-item">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Courses Available</div>
                </div>
                <div class="col-md-3 col-6 stat-item">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-title text-start">About EPAS-E LMS</h2>
                    <p class="lead">
                        The Electronic Products Assembly and Servicing Learning Management System is designed to provide
                        comprehensive technical education in electronics assembly, repair, and maintenance.
                    </p>
                    <p>
                        Our platform combines theoretical knowledge with practical hands-on training, preparing students
                        for successful careers in the electronics industry. With state-of-the-art digital tools and
                        experienced instructors, we're revolutionizing technical education.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-rocket me-2"></i>Start Learning
                        </a>
                        <a href="#contact" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-envelope me-2"></i>Contact Us
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ps-lg-4 mt-4 mt-lg-0">
                        <img src="{{ dynamic_asset('assets/epas1.jpg') }}" alt="EPAS-E Demo" class="img-fluid rounded shadow"
                            onerror="this.onerror=null; this.src='https://placehold.co/800x600?text=EPAS-E'">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Hero slideshow -->
    <script src="{{ dynamic_asset('js/lobby.js')}}"></script>

    @auth
    <script src="{{ dynamic_asset('js/components/navbar.js')}}"></script>
    @else
    <script src="{{ dynamic_asset('js/public-header.js')}}"></script>
    <script src="{{ dynamic_asset('js/utils/dark-mode.js')}}"></script>
    <script src="{{ dynamic_asset('js/utils/slideshow.js')}}"></script>
    <script src="{{ dynamic_asset('js/components/public-darkmode.js')}}"></script>
    @endauth
</body>

</html>