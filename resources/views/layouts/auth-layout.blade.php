<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <title>@yield('title', 'EPAS-E LMS')</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- App CSS -->
  <link rel="stylesheet" href="{{ dynamic_asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ dynamic_asset('css/base/reset.css') }}">
  <link rel="stylesheet" href="{{ dynamic_asset('css/base/typography.css') }}">
  <link rel="stylesheet" href="{{ dynamic_asset('css/layout/header.css') }}">
  <link rel="stylesheet" href="{{ dynamic_asset('css/layout/public-header.css') }}">
  <link rel="stylesheet" href="{{ dynamic_asset('css/pages/auth.css') }}">
  <link rel="stylesheet" href="{{ dynamic_asset('css/components/buttons.css') }}">
  <link rel="stylesheet" href="{{ dynamic_asset('css/components/forms.css') }}">
  <link rel="stylesheet" href="{{ dynamic_asset('css/components/utilities.css') }}">
  <link rel="stylesheet" href="{{ dynamic_asset('css/components/error-popup.css') }}">

  @vite(['resources/css/app.css'])
  
  <style>
    /* Slideshow styles from lobby */
    .slideshow-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }
    
    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transform: scale(1.1);
        transition: transform 10s ease, opacity 1.5s ease;
    }
    
    .slide::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.85) 0%, rgba(15, 23, 42, 0.7) 100%);
    }
    
    .slide.active {
        opacity: 1;
        transform: scale(1);
    }

    /* Additional auth page styling */
    .auth-page-body {
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }
  </style>
</head>
<body class="auth-page-body">
  @include('partials.header')

    <!-- In auth-layout.blade.php -->
    <div class="slideshow-container" id="authSlideshow">
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

  <div class="auth-content-container">
    @yield('content')
  </div>

  <footer class="mobile-auth-footer">
    @yield('footer', '&copy; ' . date('Y') . ' IETI. All rights reserved.')
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Utility Scripts -->
  <script src="{{ dynamic_asset('js/utils/slideshow.js')}}"></script>
  <script src="{{ dynamic_asset('js/utils/dark-mode.js')}}"></script>

  <!-- Auth & Header Scripts -->
  <script src="{{ dynamic_asset('js/auth.js')}}"></script>
  <script src="{{ dynamic_asset('js/public-header.js')}}"></script>

  <!-- Initialize dark mode for auth pages -->
  <script>if (window.initDarkMode) window.initDarkMode();</script>

  <!-- Global Error Popup Handler -->
  <div class="error-popup-overlay" id="errorPopupOverlay">
    <div class="error-popup">
      <div class="error-popup-header">
        <div class="error-popup-icon error" id="errorPopupIcon"><i class="fas fa-exclamation-circle"></i></div>
        <h3 class="error-popup-title" id="errorPopupTitle">Error</h3>
      </div>
      <div class="error-popup-body" id="errorPopupBody">An error occurred.</div>
      <div class="error-popup-footer">
        <button class="error-popup-btn error-popup-btn-primary" id="errorPopupCloseBtn">OK</button>
      </div>
    </div>
  </div>

  <script src="{{ dynamic_asset('js/error-popup.js') }}"></script>
  <script>
    // Show server-side flash messages via error popup
    @if(session('error_popup'))
      window.showErrorPopup(@json(session('error_popup')), @json(session('error_code') ? 'Error ' . session('error_code') : 'Error'), 'error');
    @endif
    @if(session('error'))
      window.showErrorPopup(@json(session('error')), 'Error', 'error');
    @endif
    @if(session('success'))
      window.showErrorPopup(@json(session('success')), 'Success', 'info');
    @endif
    @if(session('warning'))
      window.showErrorPopup(@json(session('warning')), 'Warning', 'warning');
    @endif
    {{-- Debug info → console only (never visible in DOM) --}}
    @if(session('error_debug'))
      console.groupCollapsed('%c[JOMS Server Debug]%c Error details (not shown to user)',
        'color: #dc3545; font-weight: bold;', 'color: #6c757d;'
      );
      console.error(@json(session('error_debug')));
      console.log('Page:', @json(request()->fullUrl()));
      console.log('Time:', new Date().toISOString());
      console.groupEnd();
    @endif
  </script>

  <!-- Additional Scripts -->
  @stack('scripts')

  <!-- Modal Functions -->
  <script>
    function openTermsModal() {
        const modal = document.getElementById('termsModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function openPrivacyModal() {
        const modal = document.getElementById('privacyModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.style.display = 'none';
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.style.display = 'none';
            });
        }
    });
  </script>
  
  <!-- Modals Stack -->
  @stack('modals')
</body>
</html>