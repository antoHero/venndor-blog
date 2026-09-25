<?php

namespace App\Filament\Auth;

use App\Models\AdminUser;
use App\Services\Otp\AdminOtpIssuer;
use App\Services\Otp\AdminOtpRequestThrottle;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use FilamentOtpLogin\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Hardened replacement for the package's own Login page.
 *
 * The stock implementation `firstOrCreate`s an AdminUser for whatever mobile
 * number is submitted — i.e. it silently self-registers anyone who fills in
 * the form. Registration must not be possible here, so this override only
 * ever looks up an existing, active AdminUser and never creates one.
 *
 * Unlike a consumer sign-up form, this fails *loudly* for an unrecognized
 * number rather than pretending a code was sent — this is a small,
 * known-team admin panel, not a public product where account-enumeration
 * protection is worth the confusing UX of a fake success message. A per-IP
 * and per-mobile request throttle (see AdminOtpRequestThrottle) still
 * applies either way, so the failure path can't be hammered to brute-force
 * discover valid numbers.
 */
class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $mobileColumn = config('filament-otp-login.mobile_column', 'mobile');
        $data = $this->form->getState();
        $mobile = AdminUser::normalizeMobile((string) ($data['mobile'] ?? ''));

        if (blank($mobile)) {
            throw ValidationException::withMessages([
                'data.mobile' => __('filament-otp-login::filament-otp-login.mobile_required'),
            ]);
        }

        $ip = request()->ip() ?? '0.0.0.0';
        $throttle = app(AdminOtpRequestThrottle::class);

        $wait = $throttle->secondsUntilIpAllowed($ip) ?? $throttle->secondsUntilMobileAllowed($mobile);

        if ($wait !== null) {
            Notification::make()
                ->title(__('filament-otp-login::filament-otp-login.wait_seconds', ['seconds' => $wait]))
                ->danger()
                ->send();

            return null;
        }

        $throttle->hitIp($ip);
        $throttle->markMobileSent($mobile);

        $user = AdminUser::query()
            ->where($mobileColumn, $mobile)
            ->where('is_active', true)
            ->first();

        if (! $user) {
            Log::warning('Admin OTP login requested for an unknown or inactive mobile number.', [
                'ip' => $ip,
            ]);

            throw ValidationException::withMessages([
                'data.mobile' => __('filament-otp-login::filament-otp-login.mobile_not_found'),
            ]);
        }

        app(AdminOtpIssuer::class)->issue($user);

        Notification::make()
            ->title(__('filament-otp-login::filament-otp-login.code_sent'))
            ->success()
            ->send();

        $routeName = 'filament.'.Filament::getCurrentPanel()->getId().'.auth.phone-verification-prompt';
        $this->redirect(route($routeName, ['mobile' => $mobile]), navigate: false);

        return null;
    }

    protected function getMobileFormComponent(): TextInput
    {
        return TextInput::make('mobile')
            ->label(__('filament-otp-login::filament-otp-login.mobile_label'))
            ->numeric()
            ->required()
            ->minLength(10)
            ->maxLength(15)
            ->autocomplete('tel')
            ->autofocus()
            ->placeholder('e.g. 08012345678')
            ->helperText('Nigerian mobile number — you can type it starting with 0 or 234.');
    }

    public function getSubheading(): Htmlable|string|null
    {
        return null;
    }
}
