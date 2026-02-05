@extends('layouts.auth-layout')

@section('title', 'Login - EPAS-E LMS')

@section('content')
<div class="login-container">
    <h1 class="form-title">Login</h1>
    <form method="POST" action="{{ route('login') }}" id="loginForm" autocomplete="off">
        @csrf
        <x-login-fields autocompleteEmail="off" autocompletePassword="off" />

        <div class="form-row remember-row">
            <label class="remember-label">
                <input type="checkbox" name="remember" id="rememberMe">
                Remember me
            </label>
            <div style="margin-left: auto;">
                <a href="{{ route('password.request') }}" id="forgotPasswordLink">Forgot Password?</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" data-auto-dismiss="5000">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-dismiss="8000">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('verification_sent'))
            <div class="alert alert-info alert-dismissible fade show" role="alert" data-auto-dismiss="8000">
                <i class="fas fa-envelope me-2"></i>
                {{ session('verification_sent') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

<style>
    .remember-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.9rem;
        color: var(--primary);
    }
    .remember-label input[type="checkbox"] {
        width: auto;
        margin: 0;
        cursor: pointer;
    }
</style>

<script>
(function() {
    const STORAGE_KEY = 'rememberedEmail';

    // Load remembered email on page load
    const savedEmail = localStorage.getItem(STORAGE_KEY);
    if (savedEmail) {
        document.getElementById('login_email').value = savedEmail;
        document.getElementById('rememberMe').checked = true;
    }

    // Handle form submission
    document.getElementById('loginForm').addEventListener('submit', function() {
        const rememberMe = document.getElementById('rememberMe').checked;
        const email = document.getElementById('login_email').value;

        if (rememberMe && email) {
            // Save email only
            localStorage.setItem(STORAGE_KEY, email);
        } else {
            // Remove saved email
            localStorage.removeItem(STORAGE_KEY);
        }
    });

    // Clear saved email if user unchecks remember me
    document.getElementById('rememberMe').addEventListener('change', function() {
        if (!this.checked) {
            localStorage.removeItem(STORAGE_KEY);
        }
    });
})();
</script>
@endsection
