<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\AdminUserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $mobile
 * @property string $role
 * @property bool $is_active
 * @property CarbonImmutable|null $mobile_verified_at
 * @property string|null $otp_code
 * @property CarbonImmutable|null $otp_expires_at
 * @property int $otp_sent_count
 */
class AdminUser extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<AdminUserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'otp_code',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'mobile_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Always store mobile numbers in canonical +234 form, however they were
     * typed (local "0..." or already-international "234..."), so a lookup
     * during login normalizes to the exact same value regardless of which
     * format the admin happens to type.
     *
     * @return Attribute<string|null, string|null>
     */
    protected function mobile(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value !== null ? self::normalizeMobile($value) : $value,
        );
    }

    public static function normalizeMobile(string $mobile): string
    {
        $digits = preg_replace('/\D/', '', $mobile) ?? '';

        if ($digits === '') {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '234'.substr($digits, 1);
        }

        if (! str_starts_with($digits, '234')) {
            return '234'.$digits;
        }

        return $digits;
    }
}
