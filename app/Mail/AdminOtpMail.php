<?php

namespace App\Mail;

use App\Models\AdminUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Deliberately NOT queued (no ShouldQueue): this sits on a login-critical
 * path where the admin is actively waiting for the code, and queuing it
 * would make delivery depend on a queue worker actually being up. Sending
 * synchronously guarantees it either goes out with the request or the
 * failure is visible immediately.
 */
class AdminOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdminUser $adminUser,
        public string $code,
        public int $expiresInSeconds,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->code} is your Venndor admin sign-in code",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-otp',
        );
    }
}
