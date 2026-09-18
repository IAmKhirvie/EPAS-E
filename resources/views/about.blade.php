<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Hasa LMS</title>
    <link rel="icon" type="image/svg+xml" href="{{ dynamic_asset('assets/hasa-rizal.svg') }}">
    <link href="{{ dynamic_asset('vendor/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ dynamic_asset('vendor/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/base/reset.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/base/typography.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/header.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/public-header.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/info.css') }}">
    @vite(['resources/css/app.css'])
    @include('partials.theme-palette-head')
</head>
<body class="auth-page-body">
    @include('partials.header')

    <section class="page-hero">
        <div class="page-hero-badge"><i class="fas fa-book-open me-1"></i> Learn any subject</div>
        <h1>About Hasa</h1>
        <p>One place to organize courses, learn at your own pace, and track progress.</p>
    </section>
    <main class="info-page"><div class="info-container">
        <section class="info-section">
            <div class="info-section-header"><div class="section-icon"><i class="fas fa-graduation-cap"></i></div><h2>Learning for every course</h2></div>
            <p>Hasa is a learning management system for schools, training teams, and independent educators. Instructors can create courses and modules, share materials, and assess learning. Students can follow lessons, submit work, and see their progress.</p>
        </section>
        <section class="info-section">
            <div class="info-section-header"><div class="section-icon"><i class="fas fa-list-check"></i></div><h2>What you can do</h2></div>
            <div class="row"><div class="col-md-6"><ul class="competency-list">
                <li><i class="fas fa-check-circle"></i>Organize subjects into courses and modules</li>
                <li><i class="fas fa-check-circle"></i>Read lessons and learning materials</li>
                <li><i class="fas fa-check-circle"></i>Complete quizzes and assignments</li>
            </ul></div><div class="col-md-6"><ul class="competency-list">
                <li><i class="fas fa-check-circle"></i>Track completion and grades</li>
                <li><i class="fas fa-check-circle"></i>Communicate through announcements</li>
                <li><i class="fas fa-check-circle"></i>Issue course completion certificates</li>
            </ul></div></div>
        </section>
        <section class="info-section text-center"><h2>Ready to start?</h2><p>Browse your courses or create an account to begin learning.</p><a class="btn btn-primary" href="{{ route('register') }}">Create an account</a></section>
    </div></main>
    @include('partials.footer')
    <script src="{{ dynamic_asset('vendor/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ dynamic_asset('js/utils/dark-mode.js') }}"></script>
    <script src="{{ dynamic_asset('js/components/public-darkmode.js') }}"></script>
    @guest<script src="{{ dynamic_asset('js/public-header.js') }}"></script>@endguest
</body>
</html>
