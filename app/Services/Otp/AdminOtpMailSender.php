<?php

namespace App\Services\Otp;

use App\Mail\AdminOtpMail;
use App\Models\AdminUser;
use FilamentOtpLogin\Contracts\OtpSenderInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Mail;

/**
 * Delivers the admin panel's OTP sign-in code by email rather than SMS,
 * since no SMS gateway is configured for this deployment. Swap the
 * `filament-otp-login.sender` config binding to a real SMS implementation
 * of {@see OtpSenderInterface} if one becomes available later.
 */
class AdminOtpMailSender implements OtpSenderInterface
{
    public function send(Authenticatable $user, string $code): void
    {
        if (! $user instanceof AdminUser) {
            return;
        }

        Mail::to($user->email)->send(new AdminOtpMail(
            adminUser: $user,
            code: $code,
            expiresInSeconds: (int) config('filament-otp-login.otp_expires_seconds', 300),
        ));
    }
}
