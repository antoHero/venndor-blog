<?php

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileUnacceptableForCollection;

beforeEach(function () {
    // Pin this explicitly rather than trust whatever MEDIA_DISK happens to
    // be in the real .env (e.g. once it's set to "r2" for production use)
    // — otherwise Storage::fake('public') below silently fails to
    // intercept anything, and these tests would attempt real network
    // calls against the actual configured disk.
    config(['filesystems.media_disk' => 'public']);
    Storage::fake('public');
});

test('a post has no featured image url until one is uploaded', function () {
    $post = Post::factory()->create();

    expect($post->featuredImageUrl())->toBeNull();
});

test('uploading a featured image makes it retrievable by url', function () {
    $post = Post::factory()->create();

    $post->addMedia(UploadedFile::fake()->image('cover.jpg', 800, 600))
        ->toMediaCollection('posts');

    expect($post->fresh()->featuredImageUrl())
        ->not->toBeNull()
        ->toContain('cover');
});

test('the posts collection only ever keeps one featured image', function () {
    $post = Post::factory()->create();

    $post->addMedia(UploadedFile::fake()->image('first.jpg'))->toMediaCollection('posts');
    $post->addMedia(UploadedFile::fake()->image('second.jpg'))->toMediaCollection('posts');

    expect($post->fresh()->getMedia('posts'))->toHaveCount(1);
    expect($post->fresh()->featuredImageUrl())->toContain('second');
});

test('non-image files are rejected by the posts media collection', function () {
    $post = Post::factory()->create();

    expect(fn () => $post->addMedia(UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'))
        ->toMediaCollection('posts'))
        ->toThrow(FileUnacceptableForCollection::class);
});

test('uploaded media is stored on whichever disk the MEDIA_DISK config points at', function () {
    $post = Post::factory()->create();
    $post->addMedia(UploadedFile::fake()->image('cover.jpg'))->toMediaCollection('posts');

    // Proves the config value actually drives storage (not hardcoded to
    // "public" in registerMediaCollections()) — this is the same wiring
    // that lets MEDIA_DISK=r2 move uploads to Cloudflare R2 in production
    // without any code change.
    expect($post->fresh()->getFirstMedia('posts')->disk)
        ->toBe(config('filesystems.media_disk'));
});
