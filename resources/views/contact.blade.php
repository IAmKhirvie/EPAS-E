<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Hasa LMS</title>
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
        <div class="page-hero-badge"><i class="fas fa-envelope me-1"></i> Get in touch</div>
        <h1>Contact Hasa</h1>
        <p>Questions about your account, courses, or enrollment? Send us a message.</p>
    </section>
    <main class="info-page"><div class="info-container">
        <section class="info-section"><div class="row justify-content-center"><div class="col-lg-8">
            <div class="info-section-header"><div class="section-icon"><i class="fas fa-paper-plane"></i></div><h2>Send a message</h2></div>
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf
                <div class="mb-3"><label for="name" class="form-label">Name</label><input id="name" name="name" class="form-control" value="{{ old('name') }}" required></div>
                <div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required></div>
                <div class="mb-3"><label for="subject" class="form-label">Subject</label><input id="subject" name="subject" class="form-control" value="{{ old('subject') }}" required></div>
                <div class="mb-3"><label for="message" class="form-label">Message</label><textarea id="message" name="message" rows="6" class="form-control" required>{{ old('message') }}</textarea></div>
                <button type="submit" class="btn btn-primary">Send message</button>
            </form>
        </div></div></section>
    </div></main>
    @include('partials.footer')
    <script src="{{ dynamic_asset('vendor/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ dynamic_asset('js/utils/dark-mode.js') }}"></script>
    <script src="{{ dynamic_asset('js/components/public-darkmode.js') }}"></script>
    @guest<script src="{{ dynamic_asset('js/public-header.js') }}"></script>@endguest
</body>
</html>
