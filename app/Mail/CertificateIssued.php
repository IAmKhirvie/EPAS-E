<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateIssued extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Certificate $certificate;

    /**
     * Create a new message instance.
     */
    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Your Certificate Has Been Issued - ' . ($this->certificate->module->module_title ?? 'EPAS-E'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.certificate-issued',
            with: [
                'certificate' => $this->certificate,
                'user' => $this->certificate->user,
                'module' => $this->certificate->module,
                'course' => $this->certificate->course,
                'downloadUrl' => route('certificates.download', $this->certificate),
                'viewUrl' => route('certificates.show', $this->certificate),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
