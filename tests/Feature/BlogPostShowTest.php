<?php

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Inertia\Testing\AssertableInertia as Assert;

test('a published post is viewable at its slug', function () {
    $post = Post::factory()->published()->create(['title' => 'Hello World']);
    $tag = Tag::factory()->create(['name' => 'Weddings']);
    $post->tags()->attach($tag);

    $this->get("/{$post->slug}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('blog-post')
            ->where('post.slug', $post->slug)
            ->where('post.title', 'Hello World')
            ->where('post.tags', ['Weddings'])
        );
});

test('a draft post 404s on the public site instead of previewing', function () {
    $post = Post::factory()->draft()->create();

    $this->get("/{$post->slug}")->assertNotFound();
});

test('a scheduled post whose publish date has not arrived yet 404s', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::Scheduled,
        'published_at' => now()->addDay(),
    ]);

    $this->get("/{$post->slug}")->assertNotFound();
});

test('a soft-deleted post 404s', function () {
    $post = Post::factory()->published()->create();
    $post->delete();

    $this->get("/{$post->slug}")->assertNotFound();
});

test('related posts are limited to the same category, exclude the post itself, and cap at 3', function () {
    $category = Category::factory()->create();
    $otherCategory = Category::factory()->create();

    $post = Post::factory()->published()->create(['category_id' => $category->id]);

    $sameCategory = Post::factory()->count(4)->published()->create(['category_id' => $category->id]);
    Post::factory()->published()->create(['category_id' => $otherCategory->id]);

    $this->get("/{$post->slug}")
        ->assertInertia(fn (Assert $page) => $page
            ->component('blog-post')
            ->has('related', 3, fn (Assert $related) => $related
                ->where('category', $category->name)
                ->etc()
            )
        );

    expect($sameCategory)->toHaveCount(4);
});
