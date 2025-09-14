<!-- Sidebar -->
<aside class="sidebar" role="complementary" aria-label="Sidebar Navigation">
    <div class="app-title">IETI</div>

    <div class="user-info">
        <h5>{{ auth()->user()->last_name }}, {{ auth()->user()->first_name }}</h5>
        <small>{{ ucfirst(auth()->user()->role ?? 'guest') }}</small>
    </div>

    <nav>
        <a href="/dashboard" class="{{ Request::is('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="/users" class="{{ Request::is('users') ? 'active' : '' }}">Users</a>
        <a href="/about" class="{{ Request::is('about') ? 'active' : '' }}">About</a>
        <a href="/contact" class="{{ Request::is('contact') ? 'active' : '' }}">Contact</a>
        <a href="/help" class="{{ Request::is('help') ? 'active' : '' }}">Help</a>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
        </form>
    </nav>
</aside>