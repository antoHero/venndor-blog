<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * The only way an admin/editor account can ever be created — there is no
 * public registration surface for the Filament panel. Run this from a
 * trusted operator shell (e.g. `php artisan admin:create`).
 */
#[Signature('admin:create {--name=} {--email=} {--mobile=} {--role=editor}')]
#[Description('Provision a new admin dashboard user (the only way to create one — there is no self-registration).')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Full name');
        $email = $this->option('email') ?: $this->ask('Email address');
        $mobile = $this->option('mobile') ?: $this->ask('Mobile number (used to sign in — you can type it starting with 0 or 234)');
        $role = $this->option('role') ?: $this->choice('Role', ['editor', 'admin'], 0);

        // Normalized before validation (not just before saving) so the
        // uniqueness check compares like-for-like — otherwise "0801..." and
        // an already-registered "234801..." look like two different
        // numbers here, and the real conflict only surfaces as a raw
        // database error when AdminUser's own mobile mutator normalizes it.
        $mobile = AdminUser::normalizeMobile((string) $mobile);

        $validator = Validator::make(
            compact('name', 'email', 'mobile', 'role'),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:admin_users,email'],
                'mobile' => ['required', 'digits_between:10,15', 'unique:admin_users,mobile'],
                'role' => ['required', 'in:admin,editor'],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $data = $validator->validated();

        $user = AdminUser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'role' => $data['role'],
            'is_active' => true,
        ]);

        $this->info("Admin user #{$user->id} created: {$user->name} ({$user->role}).");
        $this->line("They can sign in at /admin with mobile {$user->mobile} — a one-time code will be emailed to {$user->email}.");

        return self::SUCCESS;
    }
}
