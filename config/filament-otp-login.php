<?php

use App\Models\AdminUser;
use App\Services\Otp\AdminOtpMailSender;

return [
    'user_model' => env('FILAMENT_OTP_LOGIN_USER_MODEL', AdminUser::class),

    'otp_log' => [
        'table' => env('FILAMENT_OTP_LOGIN_OTP_LOG_TABLE', 'admin_otp_logs'),
        'user_foreign_key' => env('FILAMENT_OTP_LOGIN_USER_FOREIGN_KEY', 'admin_user_id'),
    ],

    'mobile_column' => env('FILAMENT_OTP_LOGIN_MOBILE_COLUMN', 'mobile'),

    'sender' => env('FILAMENT_OTP_LOGIN_SENDER', AdminOtpMailSender::class),

    'otp_length' => (int) env('FILAMENT_OTP_LOGIN_OTP_LENGTH', 6),

    'otp_expires_seconds' => (int) env('FILAMENT_OTP_LOGIN_OTP_EXPIRES_SECONDS', 300),

    'request_block_seconds' => (int) env('FILAMENT_OTP_LOGIN_REQUEST_BLOCK_SECONDS', 60),

    // A fresh, hashed code is issued on every send (see App\Filament\Auth\Login /
    // PhoneVerificationPrompt), so the package's "resend the same code" feature is
    // intentionally not used — reusing a code across resends only widens its
    // exposure window. This value is kept for reference but has no effect.
    'same_otp_max_sends' => (int) env('FILAMENT_OTP_LOGIN_SAME_OTP_MAX_SENDS', 3),

    // Custom hardening on top of the package defaults (see App\Filament\Auth\*).
    'verify_max_attempts' => (int) env('FILAMENT_OTP_LOGIN_VERIFY_MAX_ATTEMPTS', 5),
    'verify_decay_seconds' => (int) env('FILAMENT_OTP_LOGIN_VERIFY_DECAY_SECONDS', 300),
    'request_max_attempts_per_ip' => (int) env('FILAMENT_OTP_LOGIN_REQUEST_MAX_ATTEMPTS_PER_IP', 10),
    'request_decay_seconds_per_ip' => (int) env('FILAMENT_OTP_LOGIN_REQUEST_DECAY_SECONDS_PER_IP', 600),

    'cache_prefix' => env('FILAMENT_OTP_LOGIN_CACHE_PREFIX', 'filament_otp_send:'),
];
