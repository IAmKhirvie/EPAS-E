<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - EPAS-E</title>

  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      padding: 12px 20px;
      border-radius: 4px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: 1px solid #c3e6cb;
    }
    
    .alert-success .close-btn {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: #155724;
    }
  </style>
</head>
<body>
  <div class="logo-container">
    <img src="{{ asset('assets/EPAS-E.png') }}" alt="Logo" style="height:70px; margin:0;">
    <div class="title-container">
      <h1>EPAS-E</h1>
      <h2>Electronic Products Assembly and Servicing</h2>
    </div>
  </div>

  <!-- background slideshow container -->
  <div id="bgSlideshow" class="bg-slideshow" aria-hidden="true"></div>

  <div class="login-container">
    <h1 class="form-title">Create Account</h1>
    
    @if (session('status'))
      <div class="alert-success dismissable">
        <span>{{ session('status') }}</span>
        <button type="button" class="close-btn" aria-label="Dismiss">&times;</button>
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger dismissable">
        <ul style="margin: 0; padding-left: 20px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="close-btn" aria-label="Dismiss">&times;</button>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <div class="input">
        <input
          type="text"
          id="first_name"
          name="first_name"
          placeholder=" "
          value="{{ old('first_name') }}"
          required
          autofocus
          autocomplete="given-name">
        <label for="first_name">FIRST NAME</label>
      </div>

      <div class="input">
        <input
          type="text"
          id="middle_name"
          name="middle_name"
          placeholder=" "
          value="{{ old('middle_name') }}"
          autocomplete="additional-name">
        <label for="middle_name">MIDDLE NAME (Optional)</label>
      </div>

      <div class="input">
        <input
          type="text"
          id="last_name"
          name="last_name"
          placeholder=" "
          value="{{ old('last_name') }}"
          required
          autocomplete="family-name">
        <label for="last_name">LAST NAME</label>
      </div>

      <div class="input">
        <input
          type="text"
          id="ext_name"
          name="ext_name"
          placeholder=" "
          value="{{ old('ext_name') }}"
          autocomplete="honorific-suffix">
        <label for="ext_name">EXTENSION NAME (e.g. Jr., Sr., III)</label>
      </div>

      <div class="input">
        <input
          type="email"
          id="email"
          name="email"
          placeholder=" "
          value="{{ old('email') }}"
          required
          autocomplete="email">
        <label for="email">EMAIL</label>
      </div>

      <div class="input">
        <input
          type="password"
          id="password"
          name="password"
          placeholder=" "
          required
          autocomplete="new-password">
        <label for="password">PASSWORD</label>

        <button
          type="button"
          class="toggle pw-toggle"
          data-target="password"
          aria-label="Toggle password visibility"
          aria-pressed="false">
          <i class="fa fa-eye" aria-hidden="true"></i>
        </button>
      </div>

      <div class="input">
        <input
          type="password"
          id="password-confirm"
          name="password_confirmation"
          placeholder=" "
          required
          autocomplete="new-password">
        <label for="password-confirm">CONFIRM PASSWORD</label>

        <button
          type="button"
          class="toggle pw-toggle"
          data-target="password-confirm"
          aria-label="Toggle password visibility"
          aria-pressed="false">
          <i class="fa fa-eye" aria-hidden="true"></i>
        </button>
      </div>

      <div class="disclaimer" style="margin-bottom: 1rem; padding: 10px; background: #f8f9fa; border-radius: 4px;">
        <p style="margin: 0; font-size: 0.9rem; color: #6c757d;">
          <i class="fas fa-info-circle"></i> Your registration will be reviewed by an administrator before you can access the system.
        </p>
      </div>

      <button type="submit" class="btn-primary">Submit Registration</button>

      <div class="divider" role="separator" aria-orientation="horizontal">
        <span>or</span>
      </div>

      <div class="register" style="margin-top:1rem; text-align: center;">
        <p>Already have an account?</p> <a href="{{ route('login') }}">Login here</a>
      </div>
    </form>
  </div>

  <!-- Toast notification container -->
  <div id="toast" class="toast-notification">
    <div id="toastMessage"></div>
  </div>

  <script>
    // ---------- slideshow ----------
    (function () {
      const images = [
        "{{ asset('assets/epas1.jpg') }}",
        "{{ asset('assets/epas2.jpg') }}"
      ];
      const el = document.getElementById('bgSlideshow');
      let imgIndex = 0;
      if (el && images.length) {
        images.forEach(s => { const i = new Image(); i.src = s; });
        el.style.backgroundImage = `url('${images[0]}')`;
        setInterval(() => {
          imgIndex = (imgIndex + 1) % images.length;
          el.style.backgroundImage = `url('${images[imgIndex]}')`;
        }, 5000);
      }
    })();

    // ---------- password toggle ----------
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.pw-toggle').forEach(btn => {
        btn.type = 'button';

        btn.addEventListener('click', function() {
          var id = this.dataset.target;
          var input = document.getElementById(id);
          var icon = this.querySelector('i');
          if (!input) return;

          if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
              icon.classList.remove('fa-eye');
              icon.classList.add('fa-eye-slash');
            }
            this.setAttribute('aria-pressed', 'true');
          } else {
            input.type = 'password';
            if (icon) {
              icon.classList.remove('fa-eye-slash');
              icon.classList.add('fa-eye');
            }
            this.setAttribute('aria-pressed', 'false');
          }
        });

        // keyboard accessibility (Enter/Space)
        btn.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
          }
        });
      });

      // Handle dismissable alerts
      document.querySelectorAll('.alert.dismissable, .alert-success.dismissable').forEach(alert => {
        const closeBtn = alert.querySelector('.close-btn');

        // Manual dismiss
        if (closeBtn) {
          closeBtn.addEventListener('click', function() {
            fadeOutAndRemove(alert);
          });
        }

        // Auto dismiss after 5s
        setTimeout(() => {
          fadeOutAndRemove(alert);
        }, 5000);
      });

      // Fade out helper
      function fadeOutAndRemove(element) {
        element.style.transition = 'opacity 0.5s ease';
        element.style.opacity = '0';
        setTimeout(() => element.remove(), 500);
      }
    });
  </script>
</body>
</html>