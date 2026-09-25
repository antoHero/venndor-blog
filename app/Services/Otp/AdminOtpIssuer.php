<?php

namespace App\Services\Otp;

use App\Models\AdminUser;
use FilamentOtpLogin\Contracts\OtpSenderInterface;
use FilamentOtpLogin\Models\OtpLog;
use Illuminate\Support\Facades\Hash;

/**
 * Issues a fresh, hashed OTP for an admin user. Unlike the package default,
 * a new code is generated on every send (login or resend) instead of
 * resending the same code — this keeps each code's exposure window to a
 * single delivery and shortens the window an intercepted code stays valid.
 */
class AdminOtpIssuer
{
    public function issue(AdminUser $user): void
    {
        $length = (int) config('filament-otp-login.otp_length', 6);
        $expiresSeconds = (int) config('filament-otp-login.otp_expires_seconds', 300);
        $foreignKey = config('filament-otp-login.otp_log.user_foreign_key', 'admin_user_id');

        $code = str_pad(
            (string) random_int(0, (int) str_repeat('9', $length)),
            $length,
            '0',
            STR_PAD_LEFT,
        );

        // Superseding a still-live, unused code is a legitimate request for a
        // fresh one, not a failed guess — it does not touch the verification
        // attempt budget.
        OtpLog::where($foreignKey, $user->getKey())
            ->where('is_used', false)
            ->update(['is_used' => true]);

        $user->otp_code = Hash::make($code);
        $user->otp_expires_at = now()->addSeconds($expiresSeconds);
        $user->otp_sent_count = (int) $user->otp_sent_count + 1;
        $user->save();

        OtpLog::create([
            $foreignKey => $user->getKey(),
            'otp_code_hash' => $user->otp_code,
            'ip' => request()->ip() ?? '0.0.0.0',
            'is_used' => false,
        ]);

        app(OtpSenderInterface::class)->send($user, $code);
    }

    public function invalidate(AdminUser $user): void
    {
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();
    }
}
