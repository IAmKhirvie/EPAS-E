<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <title>Login Page</title>

  <!-- Production: use secure_asset -->
  <!-- <link rel="stylesheet" href="{{ secure_asset('css/auth.css') }}"> -->
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

  <!-- bootstrap css for buttons and forms -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
  <div class="logo-container">
    <!-- Production: use secure_asset -->
    <!-- <img src="{{ secure_asset('assets/EPAS-E.png') }}" alt="Logo" style="height:70px; margin:0;"> -->
    <img src="{{ asset('assets/EPAS-E.png') }}" alt="Logo" style="height:70px; margin:0;">

    <div class="title-container">
      <h1>EPAS-E</h1>
      <h2>Electronic Products Assembly and Servicing</h2>
    </div>
  </div>

  <!-- background slideshow container -->
  <div id="bgSlideshow" class="bg-slideshow" aria-hidden="true"></div>

  <div class="login-container">
    <h1 class="form-title">Login</h1>
    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="input">
        <input
          type="email"
          id="login_email"
          name="email"
          placeholder=" "
          value="{{ old('email', Cookie::get('remembered_email')) }}"
          required
          autofocus
          autocomplete="email">
        <label for="login_email">EMAIL</label>
      </div>

      <div class="input">
        <input
          type="password"
          id="login_password"
          name="password"
          placeholder=" "
          required
          autocomplete="current-password">
        <label for="login_password">PASSWORD</label>

        <button
          type="button"
          class="toggle pw-toggle"
          data-target="login_password"
          aria-label="Toggle password visibility"
          aria-pressed="false">
          <i class="fa fa-eye" aria-hidden="true"></i>
        </button>
      </div>

      <div class="form-row remember-row" style="display:flex; align-items:center; gap:.5rem; margin-bottom:1rem;">
        <div style="margin-left:auto;">
          <a href="#" id="forgotPasswordLink">Forgot Password?</a>
        </div>
      </div>
      @if ($errors->any())
        <div class="alert alert-danger dismissable">
          {{ $errors->first() }}
          <button type="button" class="close-btn" aria-label="Dismiss">&times;</button>
        </div>
      @endif


      @if (session('status'))
        <div class="alert alert-success">
          {{ session('status') }}
        </div>
      @endif
      <button type="submit" class="btn-primary">Login</button>

      <div class="divider" role="separator" aria-orientation="horizontal">
        <span>or</span>
      </div>

      <div class="register">
        <p>Don't have an account?</p> <a href="{{ route('register') }}">Register here</a>
      </div>
    </form>
  </div>

  <!-- Scripts -->
  <script>
    // ---------- slideshow (added from revised version) ----------
    (function () {
      const images = [
        "{{ asset('assets/epas1.jpg') }}",
        "{{ asset('assets/epas2.jpg') }}",
        "{{ asset('assets/epas3.jpg') }}",
        "{{ asset('assets/epas4.jpg') }}"
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
    (function () {
      function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
      }

      ready(function () {
        document.querySelectorAll('.pw-toggle').forEach(btn => {
          btn.type = 'button';

          btn.addEventListener('click', function () {
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
          btn.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              this.click();
            }
          });
        });
      });
    })();

    // ---------- Forgot Password and Register placeholders ----------
    document.addEventListener('DOMContentLoaded', function() {
      const forgotPasswordLink = document.getElementById('forgotPasswordLink');
      const registerLink = document.getElementById('registerLink');
      
      if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', function(e) {
          e.preventDefault();
          showToast('Password reset functionality will be implemented soon.', 'info');
        });
      }
    });

    //---- Error Dismiss ----
    document.addEventListener('DOMContentLoaded', function() {
      // Handle dismissable error alerts
      document.querySelectorAll('.alert.dismissable').forEach(alert => {
        const closeBtn = alert.querySelector('.close-btn');

        // Manual dismiss
        if (closeBtn) {
          closeBtn.addEventListener('click', function() {
            fadeOutAndRemove(alert);
          });
        }

        // Auto dismiss after 3s
        setTimeout(() => {
          fadeOutAndRemove(alert);
        }, 3000);
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