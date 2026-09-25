<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cleans up schema left behind by the now-removed 2FA/passkeys migrations
 * (Fortify features never used by this app — the Filament admin panel has
 * its own separate OTP-based auth entirely). Existence checks make this a
 * no-op on a fresh install, where those migrations never ran in the first
 * place.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('passkeys')) {
            Schema::drop('passkeys');
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'two_factor_secret')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn([
                    'two_factor_secret',
                    'two_factor_recovery_codes',
                    'two_factor_confirmed_at',
                ]);
            });
        }
    }

    public function down(): void
    {
        // Irreversible on purpose: these columns/table backed a feature
        // (Fortify 2FA/passkeys) that no longer exists in the app.
    }
};
