<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorService;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function setup()
    {
        $user = auth()->user();

        if ($this->twoFactorService->isEnabled($user)) {
            return redirect()->route('two-factor.manage')
                ->with('info', 'Two-factor authentication is already enabled.');
        }

        $secret = $this->twoFactorService->generateSecret();
        session()->put('2fa_setup_secret', $secret);

        $qrCode = $this->twoFactorService->getQrCodeSvg($user, $secret);

        return view('auth.two-factor.setup', compact('secret', 'qrCode'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $secret = session('2fa_setup_secret');

        if (!$secret) {
            return redirect()->route('two-factor.setup')
                ->with('error', 'Setup session expired. Please try again.');
        }

        if (!$this->twoFactorService->enable($user, $secret, $request->code)) {
            return back()->with('error', 'Invalid verification code. Please try again.');
        }

        session()->forget('2fa_setup_secret');
        $backupCodes = $this->twoFactorService->getBackupCodes($user);

        return view('auth.two-factor.enabled', compact('backupCodes'));
    }

    public function manage()
    {
        $user = auth()->user();
        $isEnabled = $this->twoFactorService->isEnabled($user);

        return view('auth.two-factor.manage', compact('isEnabled'));
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $this->twoFactorService->disable(auth()->user());

        return redirect()->route('two-factor.manage')
            ->with('success', 'Two-factor authentication has been disabled.');
    }

    public function challenge()
    {
        if (!$this->twoFactorService->isEnabled(auth()->user())) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.two-factor.challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = auth()->user();

        if (!$this->twoFactorService->verifyForUser($user, $request->code)) {
            return back()->with('error', 'Invalid verification code.');
        }

        // Regenerate session ID after successful 2FA to prevent session fixation
        session()->regenerate();
        session()->put('2fa_verified', true);
        session()->put('2fa_verified_at', now());

        return redirect()->intended('/dashboard');
    }

    public function regenerateBackupCodes()
    {
        $codes = $this->twoFactorService->regenerateBackupCodes(auth()->user());

        return view('auth.two-factor.backup-codes', compact('codes'));
    }
}
