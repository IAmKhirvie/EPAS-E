<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function generateCertificate(User $user, Course $course, array $metadata = []): Certificate
    {
        // Check if certificate already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'issued')
            ->first();

        if ($existing) {
            return $existing;
        }

        // Generate unique certificate number
        $certificateNumber = Certificate::generateCertificateNumber();

        // Create certificate record
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_number' => $certificateNumber,
            'title' => "Certificate of Completion - {$course->course_name}",
            'description' => "This certifies that {$user->full_name} has successfully completed the {$course->course_name} course.",
            'issue_date' => now(),
            'status' => 'issued',
            'metadata' => array_merge([
                'completion_date' => now()->toDateString(),
                'course_name' => $course->course_name,
                'student_name' => $user->full_name,
            ], $metadata),
        ]);

        // Generate PDF
        $this->generatePdf($certificate);

        return $certificate;
    }

    public function generatePdf(Certificate $certificate): string
    {
        $certificate->load(['user', 'course']);

        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course,
            'issue_date' => $certificate->issue_date->format('F d, Y'),
            'certificate_number' => $certificate->certificate_number,
        ];

        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isRemoteEnabled' => true]);

        // Save to storage
        $filename = "certificates/{$certificate->certificate_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        // Update certificate record
        $certificate->update(['pdf_path' => $filename]);

        return $filename;
    }

    public function downloadPdf(Certificate $certificate)
    {
        if (!$certificate->pdf_path || !Storage::disk('public')->exists($certificate->pdf_path)) {
            $this->generatePdf($certificate);
        }

        return Storage::disk('public')->download(
            $certificate->pdf_path,
            "Certificate-{$certificate->certificate_number}.pdf"
        );
    }

    public function verifyCertificate(string $certificateNumber): ?Certificate
    {
        return Certificate::where('certificate_number', $certificateNumber)
            ->where('status', 'issued')
            ->with(['user', 'course'])
            ->first();
    }

    public function revokeCertificate(Certificate $certificate, string $reason = null): bool
    {
        $certificate->update([
            'status' => 'revoked',
            'metadata' => array_merge($certificate->metadata ?? [], [
                'revoked_at' => now()->toDateTimeString(),
                'revoke_reason' => $reason,
            ]),
        ]);

        return true;
    }

    public function getUserCertificates(User $user)
    {
        return Certificate::forUser($user->id)
            ->issued()
            ->with('course')
            ->orderByDesc('issue_date')
            ->get();
    }

    public function checkCourseCompletion(User $user, Course $course): bool
    {
        // Get all modules for the course
        $modules = $course->modules()->where('is_active', true)->get();

        if ($modules->isEmpty()) {
            return false;
        }

        // Check if all modules are completed
        foreach ($modules as $module) {
            $progress = $user->progress()
                ->where('progressable_type', 'App\\Models\\Module')
                ->where('progressable_id', $module->id)
                ->where('status', 'completed')
                ->first();

            if (!$progress) {
                return false;
            }
        }

        return true;
    }
}
