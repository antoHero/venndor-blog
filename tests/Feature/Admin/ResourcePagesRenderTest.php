<?php

use App\Filament\Resources\AdminUsers\AdminUserResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Tags\TagResource;
use App\Models\AdminUser;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Filament\Facades\Filament;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    Filament::bootCurrentPanel();

    $this->actingAs(AdminUser::factory()->admin()->create(), 'admin');
});

test('the admin dashboard renders', function () {
    $this->get('/admin')->assertOk();
});

test('post resource pages render', function () {
    $post = Post::factory()->create();

    $this->get(PostResource::getUrl('index'))->assertOk();
    $this->get(PostResource::getUrl('create'))->assertOk();
    $this->get(PostResource::getUrl('edit', ['record' => $post]))->assertOk();
});

test('category resource pages render', function () {
    $category = Category::factory()->create();

    $this->get(CategoryResource::getUrl('index'))->assertOk();
    $this->get(CategoryResource::getUrl('create'))->assertOk();
    $this->get(CategoryResource::getUrl('edit', ['record' => $category]))->assertOk();
});

test('tag resource pages render', function () {
    $tag = Tag::factory()->create();

    $this->get(TagResource::getUrl('index'))->assertOk();
    $this->get(TagResource::getUrl('create'))->assertOk();
    $this->get(TagResource::getUrl('edit', ['record' => $tag]))->assertOk();
});

test('admin user (team) resource pages render', function () {
    $teamMember = AdminUser::factory()->create();

    $this->get(AdminUserResource::getUrl('index'))->assertOk();
    $this->get(AdminUserResource::getUrl('create'))->assertOk();
    $this->get(AdminUserResource::getUrl('edit', ['record' => $teamMember]))->assertOk();
});
