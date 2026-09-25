<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Category;

class CategoryPolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return true;
    }

    public function view(AdminUser $user, Category $category): bool
    {
        return true;
    }

    public function create(AdminUser $user): bool
    {
        return true;
    }

    public function update(AdminUser $user, Category $category): bool
    {
        return true;
    }

    public function delete(AdminUser $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(AdminUser $user): bool
    {
        return $user->isAdmin();
    }
}
