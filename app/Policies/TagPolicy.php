<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Tag;

class TagPolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return true;
    }

    public function view(AdminUser $user, Tag $tag): bool
    {
        return true;
    }

    public function create(AdminUser $user): bool
    {
        return true;
    }

    public function update(AdminUser $user, Tag $tag): bool
    {
        return true;
    }

    public function delete(AdminUser $user, Tag $tag): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(AdminUser $user): bool
    {
        return $user->isAdmin();
    }
}
