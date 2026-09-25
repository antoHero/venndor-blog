<?php

use App\Enums\PostStatus;
use App\Filament\Resources\AdminUsers\AdminUserResource;
use App\Models\AdminUser;
use App\Models\Post;
use Filament\Facades\Filament;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    Filament::bootCurrentPanel();
});

test('editors cannot access team management, admins can', function () {
    $editor = AdminUser::factory()->create();
    $admin = AdminUser::factory()->admin()->create();

    $this->actingAs($editor, 'admin');
    $this->get(AdminUserResource::getUrl('index'))->assertForbidden();

    $this->actingAs($admin, 'admin');
    $this->get(AdminUserResource::getUrl('index'))->assertOk();
});

test('an admin cannot delete their own account', function () {
    $admin = AdminUser::factory()->admin()->create();

    expect($admin->can('delete', $admin))->toBeFalse();
});

test('an admin can delete another account', function () {
    $admin = AdminUser::factory()->admin()->create();
    $other = AdminUser::factory()->create();

    expect($admin->can('delete', $other))->toBeTrue();
});

test('an editor can delete their own draft post but not someone else\'s', function () {
    $editor = AdminUser::factory()->create();
    $otherEditor = AdminUser::factory()->create();

    $ownDraft = Post::factory()->draft()->create(['admin_user_id' => $editor->id]);
    $othersDraft = Post::factory()->draft()->create(['admin_user_id' => $otherEditor->id]);

    expect($editor->can('delete', $ownDraft))->toBeTrue();
    expect($editor->can('delete', $othersDraft))->toBeFalse();
});

test('an editor cannot delete their own post once it has been published', function () {
    $editor = AdminUser::factory()->create();
    $published = Post::factory()->create([
        'admin_user_id' => $editor->id,
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);

    expect($editor->can('delete', $published))->toBeFalse();
});

test('an admin can delete any post regardless of author or status', function () {
    $admin = AdminUser::factory()->admin()->create();
    $editor = AdminUser::factory()->create();

    $published = Post::factory()->create([
        'admin_user_id' => $editor->id,
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);

    expect($admin->can('delete', $published))->toBeTrue();
});
