<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Add rate limiting (5 attempts per minute)
        $key = 'login:' . $request->ip();
        $maxAttempts = 5;
        $decaySeconds = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
            ]);
        }
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Check if user is approved
            if (Auth::user()->stat == 0) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is pending approval. Please contact administrator.',
                ]);
            }
            
            RateLimiter::clear($key);
            
            $user = Auth::user();
            $user->last_login = now();
            $user->save();
            
            return redirect()->intended('/dashboard');
        }

        // Increment login attempts
        RateLimiter::hit($key, $decaySeconds);
        
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}