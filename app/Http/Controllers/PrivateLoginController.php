<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Http\Traits\RateLimitsLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateLoginController extends Controller
{
    use RateLimitsLogins;

    /**
     * Show admin login form
     */
    public function showAdminLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToRoleDashboard();
        }

        return view('private.admin-login');
    }

    /**
     * Show instructor login form
     */
    public function showInstructorLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToRoleDashboard();
        }

        return view('private.instructor-login');
    }

    /**
     * Handle admin login
     */
    public function adminLogin(Request $request)
    {
        return $this->handleLogin($request, Roles::ADMIN);
    }

    /**
     * Handle instructor login
     */
    public function instructorLogin(Request $request)
    {
        return $this->handleLogin($request, Roles::INSTRUCTOR);
    }

    /**
     * Handle login with role validation
     */
    protected function handleLogin(Request $request, string $expectedRole)
    {
        $key = "{$expectedRole}-login:" . $request->ip();

        // Check if locked out
        $lockout = $this->isLockedOut($key);
        if ($lockout['locked']) {
            return back()->withErrors([
                'email' => 'Too many failed login attempts. Please try again in ' . $this->formatTime($lockout['remaining']) . '.',
            ]);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // Validate role
            if ($expectedRole === Roles::ADMIN && $user->role !== Roles::ADMIN) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. This login is for administrators only.',
                ])->withInput($request->only('email'));
            }

            if ($expectedRole === Roles::INSTRUCTOR && $user->role !== Roles::INSTRUCTOR) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. This login is for instructors only.',
                ])->withInput($request->only('email'));
            }

            // Check if user is approved
            if ($user->stat == 0) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is pending approval. Please contact administrator.',
                ]);
            }

            // Clear rate limits on successful login
            $this->clearRateLimits($key);

            $user->last_login = now();
            $user->save();

            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            return redirect('/dashboard');
        }

        // Record failed attempt
        $this->recordFailedAttempt($key);

        // Show warning when approaching lockout
        $config = $this->getRateLimitConfig($key);
        $remaining = $config['max'] - $config['attempts'];
        $warning = '';

        if ($remaining > 0 && $remaining <= 2) {
            $nextTier = $this->getNextTierMessage($config['attempts'] + 1);
            $warning = ' (' . $remaining . ' attempt' . ($remaining > 1 ? 's' : '') . ' until ' . $nextTier . ' lockout)';
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.' . $warning,
        ])->withInput($request->only('email'));
    }

    private function redirectToRoleDashboard()
    {
        $user = Auth::user();
        if (Roles::canManageStudents($user->role)) {
            return redirect('/dashboard');
        }
        return redirect('/student/dashboard');
    }
}
