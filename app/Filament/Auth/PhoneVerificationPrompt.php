<?php

namespace App\Filament\Auth;

use App\Models\AdminUser;
use App\Services\Otp\AdminOtpIssuer;
use App\Services\Otp\AdminOtpRequestThrottle;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use FilamentOtpLogin\Models\OtpLog;
use FilamentOtpLogin\Pages\PhoneVerificationPrompt as BasePrompt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Hardened replacement for the package's own verification page:
 * - Compares against a hashed code (Login/AdminOtpIssuer never stores or
 *   logs the plaintext code) using a constant-time check.
 * - Caps verification attempts per mobile *and* per IP; exceeding either
 *   immediately invalidates the pending code, so a lockout can't be
 *   waited out — the admin has to request a new one.
 * - Only ever looks up an existing, active AdminUser (no auto-creation).
 */
class PhoneVerificationPrompt extends BasePrompt
{
    // Accepts $mobile as an optional parameter (rather than only ever
    // reading request()->query('mobile') internally) so tests can inject it
    // directly via livewire(PhoneVerificationPrompt::class, ['mobile' =>
    // $x]) — real requests never supply a matching route/mount parameter
    // named "mobile", so this falls back to the query string exactly as
    // before.
    public function mount(?string $mobile = null): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());

            return;
        }

        $this->mobile = $mobile ?? request()->query('mobile');

        if (blank($this->mobile)) {
            $this->redirect(Filament::getCurrentPanel()->getLoginUrl(), navigate: false);

            return;
        }

        $this->mobile = AdminUser::normalizeMobile($this->mobile);

        if (blank($this->mobile)) {
            $this->redirect(Filament::getCurrentPanel()->getLoginUrl(), navigate: false);

            return;
        }

        $mobileColumn = config('filament-otp-login.mobile_column', 'mobile');
        $user = AdminUser::where($mobileColumn, $this->mobile)
            ->where('is_active', true)
            ->first();

        if (! $user || ! $user->otp_code || ! $user->otp_expires_at || $user->otp_expires_at->isPast()) {
            $this->redirect(Filament::getCurrentPanel()->getLoginUrl(), navigate: false);

            return;
        }

        $this->form->fill();
    }

    public function verifyOtp(): RedirectResponse|LoginResponse|null
    {
        $mobileColumn = config('filament-otp-login.mobile_column', 'mobile');
        $data = $this->form->getState();
        $code = (string) ($data['otp'] ?? '');

        $mobileKey = 'otp-verify:'.$this->mobile;
        $ipKey = 'otp-verify-ip:'.(request()->ip() ?? '0.0.0.0');
        $maxAttempts = (int) config('filament-otp-login.verify_max_attempts', 5);
        $decaySeconds = (int) config('filament-otp-login.verify_decay_seconds', 300);

        if (RateLimiter::tooManyAttempts($mobileKey, $maxAttempts)
            || RateLimiter::tooManyAttempts($ipKey, $maxAttempts * 4)) {
            $this->lockOutPendingOtp();

            throw ValidationException::withMessages([
                'data.otp' => __('filament-otp-login::filament-otp-login.invalid_code'),
            ]);
        }

        $user = AdminUser::where($mobileColumn, $this->mobile)
            ->where('is_active', true)
            ->first();

        if (! $user) {
            $this->redirect(Filament::getCurrentPanel()->getLoginUrl(), navigate: false);

            return null;
        }

        $isValid = filled($user->otp_code)
            && $user->otp_expires_at
            && $user->otp_expires_at->isFuture()
            && Hash::check($code, $user->otp_code);

        if (! $isValid) {
            RateLimiter::hit($mobileKey, $decaySeconds);
            RateLimiter::hit($ipKey, $decaySeconds);

            throw ValidationException::withMessages([
                'data.otp' => __('filament-otp-login::filament-otp-login.invalid_code'),
            ]);
        }

        RateLimiter::clear($mobileKey);
        RateLimiter::clear($ipKey);

        $foreignKey = config('filament-otp-login.otp_log.user_foreign_key', 'admin_user_id');
        OtpLog::where($foreignKey, $user->getKey())
            ->where('is_used', false)
            ->update(['is_used' => true]);

        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->otp_sent_count = 0;
        $user->mobile_verified_at = $user->mobile_verified_at ?? now();
        $user->save();

        Filament::auth()->login($user);
        session()->regenerate();

        Log::info('Admin OTP login succeeded.', [
            'admin_user_id' => $user->getKey(),
            'ip' => request()->ip(),
        ]);

        return app(LoginResponse::class);
    }

    public function resendOtp(): void
    {
        $mobileColumn = config('filament-otp-login.mobile_column', 'mobile');
        $ip = request()->ip() ?? '0.0.0.0';
        $throttle = app(AdminOtpRequestThrottle::class);

        $wait = $throttle->secondsUntilIpAllowed($ip) ?? $throttle->secondsUntilMobileAllowed($this->mobile);

        if ($wait !== null) {
            Notification::make()
                ->title(__('filament-otp-login::filament-otp-login.wait_seconds', ['seconds' => $wait]))
                ->danger()
                ->send();

            return;
        }

        $user = AdminUser::where($mobileColumn, $this->mobile)
            ->where('is_active', true)
            ->first();

        if (! $user) {
            $this->redirect(Filament::getCurrentPanel()->getLoginUrl(), navigate: false);

            return;
        }

        $throttle->hitIp($ip);
        $throttle->markMobileSent($this->mobile);

        app(AdminOtpIssuer::class)->issue($user);

        Notification::make()
            ->title(__('filament-otp-login::filament-otp-login.code_resent'))
            ->success()
            ->send();

        $this->redirect(
            route('filament.'.Filament::getCurrentPanel()->getId().'.auth.phone-verification-prompt', ['mobile' => $this->mobile]),
            navigate: false,
        );
    }

    private function lockOutPendingOtp(): void
    {
        $mobileColumn = config('filament-otp-login.mobile_column', 'mobile');
        $user = AdminUser::where($mobileColumn, $this->mobile)->first();

        if (! $user) {
            return;
        }

        app(AdminOtpIssuer::class)->invalidate($user);

        Log::warning('Admin OTP verification locked out after repeated failed attempts.', [
            'admin_user_id' => $user->getKey(),
            'ip' => request()->ip(),
        ]);
    }
}
