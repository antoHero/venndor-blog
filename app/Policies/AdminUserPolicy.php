<?php

namespace App\Policies;

use App\Models\AdminUser;

/**
 * Team management — deciding who else can sign in to this panel — is
 * restricted to admins. Editors never see this resource at all.
 */
class AdminUserPolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return $user->isAdmin();
    }

    public function view(AdminUser $user, AdminUser $adminUser): bool
    {
        return $user->isAdmin();
    }

    public function create(AdminUser $user): bool
    {
        return $user->isAdmin();
    }

    public function update(AdminUser $user, AdminUser $adminUser): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admins may deactivate/remove other accounts, but never their own —
     * that would either lock every admin out or let the last admin
     * orphan the panel with no one able to manage access.
     */
    public function delete(AdminUser $user, AdminUser $adminUser): bool
    {
        return $user->isAdmin() && $user->id !== $adminUser->id;
    }
}
