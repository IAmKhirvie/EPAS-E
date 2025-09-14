<!-- Top Navbar -->
<nav class="navbar" role="banner">
    <a class="navbar-brand" href="/">IETI</a>
    <div class="user-greeting" aria-live="polite">
        Welcome, {{ Auth::user()->name ?? 'Guest' }}
    </div>
</nav>
