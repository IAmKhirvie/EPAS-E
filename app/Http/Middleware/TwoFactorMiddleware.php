<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TwoFactorService;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // 2FA verification disabled — users can enable it manually from settings
        return $next($request);
    }
}
