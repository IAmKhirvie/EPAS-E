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
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ dynamic_asset('css/base/reset.css') }}">
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

            <!-- Developer Credits -->
            <div class="row mt-5 mb-5">
                <div class="col-12">
                    <h2 class="mb-4"><i class="fas fa-code me-2 text-primary"></i>Developed By</h2>

                    <div class="info-card developer-card mb-4">
                        <div class="developer-profile">
                            <div class="developer-icon">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <div class="developer-info">
                                <h4>Khirvie Clifford N. Bautista</h4>
                                <p class="developer-desc">
                                    Designed and developed the EPAS-E Learning Management System for
                                    IETI College of Science and Technology (Marikina), Inc. — providing a modern
                                    digital learning platform for TESDA-accredited electronics education.
                                </p>

                                <div class="developer-roles mt-3">
                                    <span class="dev-role-badge"><i class="fas fa-layer-group me-1"></i>Full-Stack Developer</span>
                                    <span class="dev-role-badge"><i class="fas fa-palette me-1"></i>UI/UX Designer</span>
                                    <span class="dev-role-badge"><i class="fas fa-server me-1"></i>Backend Developer</span>
                                    <span class="dev-role-badge"><i class="fas fa-paint-brush me-1"></i>Frontend Developer</span>
                                    <span class="dev-role-badge"><i class="fas fa-database me-1"></i>Database Architect</span>
                                    <span class="dev-role-badge"><i class="fas fa-shield-alt me-1"></i>Security Engineer</span>
                                    <span class="dev-role-badge"><i class="fas fa-vial me-1"></i>QA / Testing</span>
                                    <span class="dev-role-badge"><i class="fas fa-project-diagram me-1"></i>Project Manager</span>
                                    <span class="dev-role-badge"><i class="fas fa-mobile-alt me-1"></i>Responsive Design</span>
                                    <span class="dev-role-badge"><i class="fas fa-cogs me-1"></i>System Administrator</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <h5><i class="fas fa-tools me-2 text-primary"></i>Tools Used</h5>
                        <div class="tools-grid mt-3">
                            <div class="tool-item">
                                <i class="fas fa-robot"></i>
                                <span>Claude (Anthropic)</span>
                            </div>
                            <div class="tool-item">
                                <i class="fas fa-robot"></i>
                                <span>DeepSeek</span>
                            </div>
                            <div class="tool-item">
                                <i class="fas fa-robot"></i>
                                <span>ChatGPT (OpenAI)</span>
                            </div>
                            <div class="tool-item">
                                <i class="fas fa-robot"></i>
                                <span>Qwen (Alibaba)</span>
                            </div>
                            <div class="tool-item">
                                <i class="fas fa-code"></i>
                                <span>Visual Studio Code</span>
                            </div>
                            <div class="tool-item">
                                <i class="fab fa-laravel"></i>
                                <span>Laravel 12</span>
                            </div>
                            <div class="tool-item">
                                <i class="fab fa-bootstrap"></i>
                                <span>Bootstrap 5</span>
                            </div>
                            <div class="tool-item">
                                <i class="fab fa-php"></i>
                                <span>PHP</span>
                            </div>
                            <div class="tool-item">
                                <i class="fab fa-js-square"></i>
                                <span>JavaScript</span>
                            </div>
                            <div class="tool-item">
                                <i class="fab fa-css3-alt"></i>
                                <span>CSS3</span>
                            </div>
                            <div class="tool-item">
                                <i class="fab fa-html5"></i>
                                <span>HTML5</span>
                            </div>
                            <div class="tool-item">
                                <i class="fas fa-database"></i>
                                <span>MySQL</span>
                            </div>
                            <div class="tool-item">
                                <i class="fab fa-git-alt"></i>
                                <span>Git</span>
                            </div>
                            <div class="tool-item">
                                <i class="fab fa-github"></i>
                                <span>GitHub</span>
                            </div>
                            <div class="tool-item">
                                <i class="fab fa-figma"></i>
                                <span>Figma</span>
                            </div>
                            <div class="tool-item">
                                <i class="fas fa-terminal"></i>
                                <span>Composer</span>
                            </div>
                        </div>
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

    <style>
        .developer-profile {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
        }

        .developer-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .developer-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .developer-info h4 {
            margin-bottom: 0.25rem;
        }

        .developer-desc {
            font-size: 0.9rem;
            color: var(--text-secondary) !important;
            margin-bottom: 0;
            line-height: 1.6;
        }

        /* Role badges */
        .developer-roles {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .dev-role-badge {
            display: inline-flex;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.3rem 0.65rem;
            border-radius: 9999px;
            background: var(--primary-foreground);
            color: var(--primary) !important;
            border: 1px solid var(--primary);
            white-space: nowrap;
        }

        .dev-role-badge i {
            font-size: 0.65rem;
        }

        /* Tools grid */
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 0.75rem;
        }

        .tool-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 0.85rem;
            border-radius: var(--border-radius);
            background: var(--background);
            border: 1px solid var(--border-color);
            font-size: 0.85rem;
            color: var(--text-primary) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .tool-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .tool-item i {
            font-size: 1.1rem;
            color: var(--primary) !important;
            width: 1.25rem;
            text-align: center;
            flex-shrink: 0;
        }

        @media (max-width: 480px) {
            .developer-profile {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .developer-icon {
                width: 48px;
                height: 48px;
            }

            .developer-icon i {
                font-size: 1.25rem;
            }

            .developer-roles {
                justify-content: center;
            }

            .dev-role-badge {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
            }

            .tools-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }

            .tool-item {
                padding: 0.5rem 0.65rem;
                font-size: 0.8rem;
            }

            .tool-item i {
                font-size: 1rem;
            }

            .tool-item:hover {
                transform: none;
            }
        }
    </style>

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
    <script src="{{ dynamic_asset('js/public-header.js')}}"></script>
    <script src="{{ dynamic_asset('js/utils/dark-mode.js')}}"></script>
    <script src="{{ dynamic_asset('js/components/public-darkmode.js')}}"></script>
    @endauth
</body>

</html>