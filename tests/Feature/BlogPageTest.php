<?php

use App\Enums\PostStatus;
use App\Models\Post;
use Inertia\Testing\AssertableInertia as Assert;

test('draft and scheduled posts never appear on the public blog page', function () {
    $draft = Post::factory()->draft()->create(['title' => 'A Draft Post']);
    $scheduled = Post::factory()->create([
        'title' => 'A Scheduled Post',
        'status' => PostStatus::Scheduled,
        'published_at' => now()->addDay(),
    ]);
    $published = Post::factory()->published()->create(['title' => 'A Published Post']);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('welcome')
            ->where('featuredPost.title', $published->title)
            ->where('posts', [])
        );

    expect($draft->fresh())->not->toBeNull();
    expect($scheduled->fresh())->not->toBeNull();
});

test('the featured post is always the most recently published post', function () {
    Post::factory()->published()->create(['title' => 'Older Post', 'published_at' => now()->subDays(10)]);
    $newest = Post::factory()->published()->create(['title' => 'Newest Post', 'published_at' => now()->subHour()]);
    Post::factory()->published()->create(['title' => 'Middle Post', 'published_at' => now()->subDays(2)]);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('welcome')
            ->where('featuredPost.title', $newest->title)
            ->where('featuredPost.tag', 'New Blog')
        );
});

test('posts published within 3 days are tagged Recent, older posts are not', function () {
    $withinWindow = Post::factory()->published()->create([
        'title' => 'Within Window',
        'published_at' => now()->subDays(3)->addMinute(),
    ]);
    $justOutsideWindow = Post::factory()->published()->create([
        'title' => 'Just Outside Window',
        'published_at' => now()->subDays(3)->subMinute(),
    ]);

    // The most recent of the two becomes the featured post (always tagged
    // "New Blog"), so a third, even newer post pushes both into the grid
    // where the Recent-tag rule is what's under test.
    Post::factory()->published()->create(['published_at' => now()]);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('welcome')
            ->where('posts.0.title', $withinWindow->title)
            ->where('posts.0.tag', 'Recent')
            ->where('posts.1.title', $justOutsideWindow->title)
            ->where('posts.1.tag', null)
        );
});

test('reading time is calculated from the post body word count', function () {
    $shortPost = Post::factory()->published()->create([
        'title' => 'Short Post',
        'body' => '<p>'.implode(' ', array_fill(0, 50, 'word')).'</p>',
        'published_at' => now()->subMinute(),
    ]);
    $longPost = Post::factory()->published()->create([
        'title' => 'Long Post',
        'body' => '<p>'.implode(' ', array_fill(0, 650, 'word')).'</p>',
        'published_at' => now(),
    ]);

    expect($shortPost->readingTimeMinutes())->toBe(1);
    expect($longPost->readingTimeMinutes())->toBe(4);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('welcome')
            ->where('featuredPost.readTime', '4 min read')
            ->where('posts.0.readTime', '1 min read')
        );
});
