<?php

namespace App\Policies;

use App\Enums\PostStatus;
use App\Models\AdminUser;
use App\Models\Post;

class PostPolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return true;
    }

    public function view(AdminUser $user, Post $post): bool
    {
        return true;
    }

    public function create(AdminUser $user): bool
    {
        return true;
    }

    public function update(AdminUser $user, Post $post): bool
    {
        return $user->isAdmin() || $user->id === $post->admin_user_id;
    }

    /**
     * Editors may delete their own drafts; only admins can delete a post
     * that has ever been published, or one written by someone else.
     */
    public function delete(AdminUser $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $post->admin_user_id && $post->status === PostStatus::Draft;
    }

    public function restore(AdminUser $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(AdminUser $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    /**
     * Bulk actions operate on an arbitrary selection that can mix posts an
     * editor doesn't individually have rights to, so — unlike the single
     * delete() above — these are admin-only rather than re-deriving
     * per-record ownership/status rules for a batch.
     */
    public function deleteAny(AdminUser $user): bool
    {
        return $user->isAdmin();
    }

    public function restoreAny(AdminUser $user): bool
    {
        return $user->isAdmin();
    }

    public function forceDeleteAny(AdminUser $user): bool
    {
        return $user->isAdmin();
    }
}
