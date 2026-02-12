<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index()
    {
        $certificates = $this->certificateService->getUserCertificates(auth()->user());

        return view('certificates.index', compact('certificates'));
    }

    public function show(Certificate $certificate)
    {
        $this->authorize('view', $certificate);

        return view('certificates.show', compact('certificate'));
    }

    public function download(Certificate $certificate)
    {
        try {
            $this->authorize('view', $certificate);

            return $this->certificateService->downloadPdf($certificate);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('CertificateController::download failed', [
                'error' => $e->getMessage(),
                'user' => auth()->id(),
                'certificate_id' => $certificate->id,
            ]);
            return back()->with('error', 'Download failed. Please try again.');
        }
    }

    public function verify(Request $request)
    {
        try {
            $request->validate([
                'certificate_number' => 'required|string|max:100|regex:/^[A-Z0-9\-]+$/i',
            ]);

            $certificate = $this->certificateService->verifyCertificate(
                $request->certificate_number
            );

            if (!$certificate) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Certificate not found or invalid.',
                ], 404);
            }

            return response()->json([
                'valid' => true,
                'certificate' => [
                    'number' => $certificate->certificate_number,
                    'title' => $certificate->title,
                    'recipient' => $certificate->user->full_name,
                    'course' => $certificate->course->course_name,
                    'issue_date' => $certificate->issue_date->format('F d, Y'),
                    'status' => $certificate->status,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('CertificateController::verify failed', [
                'error' => $e->getMessage(),
                'user' => auth()->id(),
            ]);
            return response()->json(['error' => 'Verification failed. Please try again.'], 500);
        }
    }

    public function generate(Course $course)
    {
        try {
            $user = auth()->user();

            // Check if course is complete
            if (!$this->certificateService->checkCourseCompletion($user, $course)) {
                return back()->with('error', 'You must complete all modules before receiving a certificate.');
            }

            $certificate = DB::transaction(function () use ($user, $course) {
                // Lock to prevent duplicate certificate creation from concurrent requests
                $existing = Certificate::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->where('status', 'issued')
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $existing;
                }

                return $this->certificateService->generateCertificate($user, $course);
            });

            return redirect()->route('certificates.show', $certificate)
                ->with('success', 'Certificate generated successfully!');
        } catch (\Exception $e) {
            Log::error('CertificateController::generate failed', [
                'error' => $e->getMessage(),
                'user' => auth()->id(),
                'course_id' => $course->id,
            ]);
            return back()->with('error', 'Certificate generation failed. Please try again.');
        }
    }

    // Admin methods
    public function adminIndex()
    {
        $certificates = Certificate::with(['user', 'course'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.certificates.index', compact('certificates'));
    }

    public function revoke(Certificate $certificate, Request $request)
    {
        try {
            $this->certificateService->revokeCertificate(
                $certificate,
                $request->get('reason')
            );

            return back()->with('success', 'Certificate revoked successfully.');
        } catch (\Exception $e) {
            Log::error('CertificateController::revoke failed', [
                'error' => $e->getMessage(),
                'user' => auth()->id(),
                'certificate_id' => $certificate->id,
            ]);
            return back()->with('error', 'Certificate revocation failed. Please try again.');
        }
    }
}
