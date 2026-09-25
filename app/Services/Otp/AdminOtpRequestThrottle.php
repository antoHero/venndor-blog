<?php

namespace App\Services\Otp;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Two independent throttles on OTP *requests* (not verification attempts):
 * per-mobile (the package's own cooldown, read via its cache keys) and
 * per-IP. The per-mobile cooldown alone doesn't stop one client spraying
 * requests across many different mobile numbers, which is what the
 * per-IP limiter is for.
 */
class AdminOtpRequestThrottle
{
    public function secondsUntilIpAllowed(string $ip): ?int
    {
        $key = $this->ipKey($ip);
        $max = (int) config('filament-otp-login.request_max_attempts_per_ip', 10);

        if (! RateLimiter::tooManyAttempts($key, $max)) {
            return null;
        }

        return RateLimiter::availableIn($key);
    }

    public function hitIp(string $ip): void
    {
        RateLimiter::hit(
            $this->ipKey($ip),
            (int) config('filament-otp-login.request_decay_seconds_per_ip', 600),
        );
    }

    public function secondsUntilMobileAllowed(string $mobile): ?int
    {
        $key = $this->mobileKey($mobile);

        if (! Cache::has($key)) {
            return null;
        }

        $secondsLeft = (int) Cache::get($key.'_until', 0) - time();

        return $secondsLeft > 0 ? $secondsLeft : null;
    }

    public function markMobileSent(string $mobile): void
    {
        $blockSeconds = (int) config('filament-otp-login.request_block_seconds', 60);
        $key = $this->mobileKey($mobile);

        Cache::put($key, true, $blockSeconds);
        Cache::put($key.'_until', time() + $blockSeconds, $blockSeconds + 10);
    }

    private function ipKey(string $ip): string
    {
        return 'otp-request-ip:'.$ip;
    }

    private function mobileKey(string $mobile): string
    {
        $prefix = config('filament-otp-login.cache_prefix', 'filament_otp_send:');

        return $prefix.$mobile;
    }
}
