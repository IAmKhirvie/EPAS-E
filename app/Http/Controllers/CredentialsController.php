<?php

namespace App\Http\Controllers;

use App\Services\CertificateService;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;

class CredentialsController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index()
    {
        $user = Auth::user();

        $certificates = $this->certificateService->getUserCertificates($user);

        $gamificationService = app(GamificationService::class);
        $stats = $gamificationService->getUserStats($user);
        $leaderboard = $gamificationService->getLeaderboard(20);

        return view('credentials.index', compact(
            'certificates',
            'stats',
            'leaderboard'
        ));
    }
}
