<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>About - EPAS-E LMS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="{{ dynamic_asset('css/reset.css') }}">
    @auth
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/header.css') }}">
    @else
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/public-header.css') }}">
    @endauth
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/info.css') }}">
</head>

<body class="auth-page-body">
    @auth
    @include('partials.navbar')
    @else
    @include('partials.header')
    @endauth

    <div class="content-container">
        <div class="content">
            <div class="page-header">
                <h1 class="display-4">About IETI Marikina</h1>
                <p class="lead">TESDA-Accredited Institution for Electronics Products Assembly and Servicing</p>
            </div>

            <!-- IETI Marikina Section -->
            <div class="row mb-5">
                <div class="col-md-8">
                    <h2><i class="fas fa-school me-2 text-primary"></i>IETI College Marikina</h2>
                    <p>IETI College of Science and Technology (Marikina), Inc. is a TESDA-accredited technical-vocational institution dedicated to providing quality education and skills training in electronics and technology. Located in the heart of Marikina City, we have been empowering students with industry-relevant skills for successful careers in the electronics industry.</p>

                    <div class="info-card">
                        <h4><i class="fas fa-award me-2"></i>Why Choose IETI Marikina?</h4>
                        <ul>
                            <li>TESDA-accredited programs and certification</li>
                            <li>Experienced instructors with industry background</li>
                            <li>Modern facilities and equipment</li>
                            <li>Hands-on training with real-world applications</li>
                            <li>Strong industry partnerships for job placement</li>
                            <li>Affordable tuition with scholarship opportunities</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tesda-card">
                        <h3><i class="fas fa-map-marker-alt me-2"></i>Visit Us</h3>
                        <p><strong>Address:</strong><br>34 Lark Street, Sta. Elena,<br>Marikina City, Philippines</p>
                        <p><strong>Contact:</strong><br>0917-120-7428<br>868-16-431</p>
                        <p><strong>Email:</strong><br>ietimarikina8@yahoo.com</p>
                        <a href="{{ route('contact') }}" class="btn btn-light btn-sm mt-2">
                            <i class="fas fa-envelope me-1"></i>Contact Us
                        </a>
                    </div>
                </div>
            </div>

            <!-- EPAS NC II Section -->
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="mb-4"><i class="fas fa-microchip me-2 text-primary"></i>What is EPAS NC II?</h2>
                    <p>Electronic Products Assembly and Servicing (EPAS) NC II is a technical-vocational qualification recognized by the Technical Education and Skills Development Authority (TESDA) in the Philippines. This program trains individuals to assemble, install, test, and service a wide range of electronic products - from consumer gadgets to industrial electronic modules.</p>
                </div>
            </div>

            <!-- Core Competencies -->
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="mb-4"><i class="fas fa-cogs me-2 text-primary"></i>Core Competencies</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="competency-list">
                                <li><i class="fas fa-check-circle"></i>Assemble electronic products</li>
                                <li><i class="fas fa-check-circle"></i>Service consumer electronic products and systems</li>
                                <li><i class="fas fa-check-circle"></i>Service industrial electronic modules and systems</li>
                                <li><i class="fas fa-check-circle"></i>Install and configure computers and peripherals</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="competency-list">
                                <li><i class="fas fa-check-circle"></i>Perform computer operations</li>
                                <li><i class="fas fa-check-circle"></i>Apply quality standards</li>
                                <li><i class="fas fa-check-circle"></i>Perform workplace safety practices</li>
                                <li><i class="fas fa-check-circle"></i>Practice occupational health and safety procedures</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Career Opportunities -->
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="mb-4"><i class="fas fa-briefcase me-2 text-primary"></i>Career Opportunities</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="career-card">
                                <div class="icon-container">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <h5>Electronics Assembler</h5>
                                <p>Assemble electronic components and products in manufacturing settings.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="career-card">
                                <div class="icon-container">
                                    <i class="fas fa-desktop"></i>
                                </div>
                                <h5>Service Technician</h5>
                                <p>Diagnose, repair, and maintain electronic devices and systems.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="career-card">
                                <div class="icon-container">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <h5>Electronics Technician</h5>
                                <p>Install, maintain, and repair electronic equipment in various industries.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Training Process -->
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="mb-4"><i class="fas fa-graduation-cap me-2 text-primary"></i>Training Process</h2>
                    <div class="timeline">
                        <div class="timeline-item">
                            <h5>Entry Requirements</h5>
                            <p>Must be at least 18 years old, able to communicate both orally and in writing, and physically and mentally fit. High school graduate or equivalent.</p>
                        </div>
                        <div class="timeline-item">
                            <h5>Training Duration</h5>
                            <p>268 hours of training, which includes both classroom instruction and hands-on practice. Can be completed in 2-3 months.</p>
                        </div>
                        <div class="timeline-item">
                            <h5>Competency Assessment</h5>
                            <p>Assessment includes written examination and practical demonstration of skills at a TESDA-accredited assessment center.</p>
                        </div>
                        <div class="timeline-item">
                            <h5>Certification</h5>
                            <p>Upon successful completion, graduates receive a National Certificate (NC II) from TESDA, recognized nationwide.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- About TESDA -->
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="mb-4"><i class="fas fa-certificate me-2 text-primary"></i>About TESDA</h2>
                    <p>The Technical Education and Skills Development Authority (TESDA) is the government agency tasked to manage and supervise technical education and skills development in the Philippines. Established through Republic Act No. 7796 in 1994, TESDA was created to encourage the full participation of and mobilize the industry, labor, local government units and technical-vocational institutions in the skills development of the country's human resources.</p>

                    <div class="accordion mt-4" id="tesdaAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    TESDA's Mission
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#tesdaAccordion">
                                <div class="accordion-body">
                                    To provide quality technical education and skills development programs that prepare individuals for employment, entrepreneurship, and lifelong learning in support of the inclusive growth strategy of the national government.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    TESDA's Vision
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#tesdaAccordion">
                                <div class="accordion-body">
                                    The transformational leader in the technical education and skills development of the Filipino workforce.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <h3><i class="fas fa-bullseye me-2"></i>Our Mission</h3>
                        <p>To provide comprehensive technical education in electronics assembly and servicing through innovative digital learning platforms, preparing students for successful careers in the electronics industry while maintaining the highest standards of TESDA certification requirements.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h3><i class="fas fa-eye me-2"></i>Our Vision</h3>
                        <p>To be the leading technical education institution in Marikina City, empowering students with hands-on skills and industry-relevant knowledge for successful careers in electronics assembly and servicing, contributing to the nation's skilled workforce.</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="tesda-card text-center">
                        <h3><i class="fas fa-rocket me-2"></i>Ready to Start Your Journey?</h3>
                        <p class="mb-4">Join IETI Marikina and become a certified electronics professional. Our doors are open for enrollment!</p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Register Now
                            </a>
                            <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-envelope me-2"></i>Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @auth
    <script src="{{ dynamic_asset('js/components/navbar.js')}}"></script>
    <!-- Logout Form for authenticated navbar -->
    <form id="logout-form" action="{{ dynamic_route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @else
    <!-- Dark Mode Script for Public Pages -->
    <script src="{{ dynamic_asset('js/components/public-darkmode.js')}}"></script>
    @endauth
</body>

</html>