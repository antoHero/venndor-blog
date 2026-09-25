<?php

test('security headers are always present', function () {
    $this->get('/')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy');
});

test('CSP is not enforced outside production, so Vite dev tooling is never fighting it', function () {
    expect(app()->environment())->toBe('testing');

    $this->get('/')->assertHeaderMissing('Content-Security-Policy');
});

test('CSP is enforced in production and trusts the configured media disk origin', function () {
    app()->detectEnvironment(fn () => 'production');
    config(['filesystems.media_disk' => 'r2']);
    config(['filesystems.disks.r2.url' => 'https://assets.example.com']);

    $response = $this->get('/');

    $response->assertHeader('Content-Security-Policy');

    $csp = $response->headers->get('Content-Security-Policy');

    expect($csp)
        ->toContain("frame-ancestors 'none'")
        ->toContain('https://assets.example.com')
        ->not->toContain('[::1]')
        ->not->toContain('5174');

    app()->detectEnvironment(fn () => 'testing');
});
