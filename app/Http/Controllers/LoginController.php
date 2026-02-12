<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Http\Traits\RateLimitsLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use RateLimitsLogins;

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/student/dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $key = 'login:' . $request->ip();

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
                if (Auth::user()->role !== Roles::STUDENT) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'An Error Occurred please approach the administrator.',
                    ]);
                }

                if (Auth::user()->stat == 0) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your account is pending approval. Please contact administrator.',
                    ]);
                }

                // Clear rate limits on successful login
                $this->clearRateLimits($key);

                $user = Auth::user();
                $user->last_login = now();
                $user->save();

                $request->session()->regenerate();
                $request->session()->forget('url.intended');

                return redirect('/student/dashboard');
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('LoginController::login failed', [
                'error' => $e->getMessage(),
                'user' => auth()->id(),
            ]);
            return back()->with('error', 'Login failed. Please try again.');
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            session()->flush();

            return redirect('/login')->with('status', 'You have been logged out successfully.');
        } catch (\Exception $e) {
            Log::error('LoginController::logout failed', [
                'error' => $e->getMessage(),
                'user' => auth()->id(),
            ]);
            return redirect('/login');
        }
    }
}
