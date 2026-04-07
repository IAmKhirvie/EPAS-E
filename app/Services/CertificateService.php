<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Certificate;
use App\Mail\CertificateIssued;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CertificateService
{
    /**
     * Available certificate templates.
     */
    public const TEMPLATES = [
        'default' => 'Default (Blue)',
        'gold' => 'Gold Premium',
        'modern' => 'Modern Minimal',
        'formal' => 'Formal/Traditional',
        'custom' => 'Custom Background',
    ];

    /**
     * Get available templates list.
     */
    public function getAvailableTemplates(): array
    {
        return self::TEMPLATES;
    }

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

        // Get template from course or use default
        $template = $course->certificate_template ?? 'default';

        // Create certificate record
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_number' => $certificateNumber,
            'title' => "Certificate of Completion - {$course->course_name}",
            'description' => "This certifies that {$user->full_name} has successfully completed the {$course->course_name} course.",
            'issue_date' => now(),
            'status' => 'issued',
            'template_used' => $template,
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

    public function generatePdf(Certificate $certificate, ?string $template = null): string
    {
        $certificate->load(['user', 'course']);

        // Determine which template to use
        $template = $template ?? $certificate->template_used ?? $certificate->course->certificate_template ?? 'default';

        // Get certificate config from course
        $config = $certificate->course->certificate_config ?? [];

        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course,
            'issue_date' => $certificate->issue_date->format('F d, Y'),
            'certificate_number' => $certificate->certificate_number,
            'config' => $config,
        ];

        // Use template-specific view or fallback to default
        $viewName = "certificates.templates.{$template}";
        if (!view()->exists($viewName)) {
            $viewName = 'certificates.templates.default';
        }

        $pdf = Pdf::loadView($viewName, $data)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isRemoteEnabled' => true]);

        // If course has custom background, we could apply it here
        // (requires additional implementation for background images)

        // Save to storage
        $filename = "certificates/{$certificate->certificate_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        // Update certificate record
        $certificate->update(['pdf_path' => $filename]);

        return $filename;
    }

    /**
     * Preview a certificate template without saving.
     */
    public function previewTemplate(string $template, User $user, Course $course): \Barryvdh\DomPDF\PDF
    {
        $config = $course->certificate_config ?? [];

        $data = [
            'certificate' => null,
            'user' => $user,
            'course' => $course,
            'issue_date' => now()->format('F d, Y'),
            'certificate_number' => 'CERT-PREVIEW-XXXXX',
            'config' => $config,
        ];

        $viewName = "certificates.templates.{$template}";
        if (!view()->exists($viewName)) {
            $viewName = 'certificates.templates.default';
        }

        return Pdf::loadView($viewName, $data)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isRemoteEnabled' => true]);
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

    /**
     * Generate a module-level certificate (requires approval workflow).
     */
    public function generateModuleCertificate(User $user, Module $module, array $metadata = []): Certificate
    {
        // Check if certificate already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->whereNotIn('status', [Certificate::STATUS_REJECTED, Certificate::STATUS_REVOKED])
            ->first();

        if ($existing) {
            return $existing;
        }

        $course = $module->course;
        $certificateNumber = Certificate::generateCertificateNumber();
        $template = $course->certificate_template ?? 'default';

        // Create certificate with pending status (needs instructor approval first)
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'module_id' => $module->id,
            'certificate_number' => $certificateNumber,
            'title' => "Certificate of Completion - {$module->module_title}",
            'description' => "This certifies that {$user->full_name} has successfully completed the {$module->module_title} module.",
            'status' => Certificate::STATUS_PENDING_INSTRUCTOR,
            'template_used' => $template,
            'requested_at' => now(),
            'metadata' => array_merge([
                'completion_date' => now()->toDateString(),
                'course_name' => $course->course_name,
                'module_name' => $module->module_title,
                'student_name' => $user->full_name,
            ], $metadata),
        ]);

        Log::info("Module certificate created for user {$user->id}, module {$module->id}", [
            'certificate_id' => $certificate->id,
        ]);

        return $certificate;
    }

    /**
     * Instructor approves a certificate.
     */
    public function instructorApprove(Certificate $certificate, User $instructor): bool
    {
        if ($certificate->status !== Certificate::STATUS_PENDING_INSTRUCTOR) {
            return false;
        }

        $certificate->update([
            'status' => Certificate::STATUS_PENDING_ADMIN,
            'instructor_approved_by' => $instructor->id,
            'instructor_approved_at' => now(),
        ]);

        Log::info("Certificate {$certificate->id} approved by instructor {$instructor->id}");

        return true;
    }

    /**
     * Admin approves a certificate (final approval - issues the certificate).
     */
    public function adminApprove(Certificate $certificate, User $admin): bool
    {
        if ($certificate->status !== Certificate::STATUS_PENDING_ADMIN) {
            return false;
        }

        $certificate->update([
            'status' => Certificate::STATUS_ISSUED,
            'admin_approved_by' => $admin->id,
            'admin_approved_at' => now(),
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'issue_date' => now(),
        ]);

        // Generate the PDF
        $this->generatePdf($certificate);

        // Send email notification
        $this->sendCertificateEmail($certificate);

        Log::info("Certificate {$certificate->id} approved by admin {$admin->id} and issued");

        return true;
    }

    /**
     * Send certificate email notification to user.
     */
    public function sendCertificateEmail(Certificate $certificate, bool $force = false): bool
    {
        try {
            $certificate->load(['user', 'module', 'course']);

            if (!$certificate->user || !$certificate->user->email) {
                Log::warning("Cannot send certificate email - user has no email", [
                    'certificate_id' => $certificate->id,
                ]);
                return false;
            }

            // Check if already sent (unless forced)
            if (!$force && ($certificate->metadata['email_sent'] ?? false)) {
                Log::info("Certificate email already sent", ['certificate_id' => $certificate->id]);
                return true;
            }

            Mail::to($certificate->user->email)->send(new CertificateIssued($certificate));

            // Update metadata to track that email was sent
            $certificate->update([
                'metadata' => array_merge($certificate->metadata ?? [], [
                    'email_sent' => true,
                    'email_sent_at' => now()->toDateTimeString(),
                ]),
            ]);

            Log::info("Certificate email sent", [
                'certificate_id' => $certificate->id,
                'user_email' => $certificate->user->email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send certificate email", [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Reject a certificate request.
     */
    public function rejectCertificate(Certificate $certificate, User $rejectedBy, string $reason = null): bool
    {
        $certificate->update([
            'status' => Certificate::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'metadata' => array_merge($certificate->metadata ?? [], [
                'rejected_at' => now()->toDateTimeString(),
                'rejected_by' => $rejectedBy->id,
                'rejection_reason' => $reason,
            ]),
        ]);

        Log::info("Certificate {$certificate->id} rejected by user {$rejectedBy->id}");

        return true;
    }

    /**
     * Manually release/issue a certificate (bypasses approval workflow - for testing/admin use).
     */
    public function manualRelease(User $user, Module $module, User $issuedBy, array $metadata = []): Certificate
    {
        // Check if certificate already exists and is issued
        $existing = Certificate::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->where('status', Certificate::STATUS_ISSUED)
            ->first();

        if ($existing) {
            return $existing;
        }

        $course = $module->course;
        $certificateNumber = Certificate::generateCertificateNumber();
        $template = $course->certificate_template ?? 'default';

        // Create certificate directly as issued
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'module_id' => $module->id,
            'certificate_number' => $certificateNumber,
            'title' => "Certificate of Completion - {$module->module_title}",
            'description' => "This certifies that {$user->full_name} has successfully completed the {$module->module_title} module.",
            'issue_date' => now(),
            'status' => Certificate::STATUS_ISSUED,
            'template_used' => $template,
            'requested_at' => now(),
            'instructor_approved_by' => $issuedBy->id,
            'instructor_approved_at' => now(),
            'admin_approved_by' => $issuedBy->id,
            'admin_approved_at' => now(),
            'approved_by' => $issuedBy->id,
            'approved_at' => now(),
            'metadata' => array_merge([
                'completion_date' => now()->toDateString(),
                'course_name' => $course->course_name,
                'module_name' => $module->module_title,
                'student_name' => $user->full_name,
                'manual_release' => true,
                'issued_by' => $issuedBy->full_name,
            ], $metadata),
        ]);

        // Generate the PDF
        $this->generatePdf($certificate);

        // Send email notification
        $this->sendCertificateEmail($certificate);

        Log::info("Certificate manually released for user {$user->id}, module {$module->id} by {$issuedBy->id}", [
            'certificate_id' => $certificate->id,
        ]);

        return $certificate;
    }

    /**
     * Get pending certificates for instructor approval.
     */
    public function getPendingForInstructor(): \Illuminate\Database\Eloquent\Collection
    {
        return Certificate::pendingInstructor()
            ->with(['user', 'course', 'module'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get pending certificates for admin approval.
     */
    public function getPendingForAdmin(): \Illuminate\Database\Eloquent\Collection
    {
        return Certificate::pendingAdmin()
            ->with(['user', 'course', 'module', 'instructorApprovedBy'])
            ->orderBy('instructor_approved_at', 'asc')
            ->get();
    }

    /**
     * Get all certificates for a module.
     */
    public function getModuleCertificates(Module $module): \Illuminate\Database\Eloquent\Collection
    {
        return Certificate::forModule($module->id)
            ->with(['user'])
            ->orderByDesc('issue_date')
            ->get();
    }
}
