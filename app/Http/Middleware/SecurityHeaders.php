<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Apply defence-in-depth response headers to every request.
     *
     * This is not a substitute for output escaping (Blade/React already
     * escape by default) — it's a second layer that contains the blast
     * radius if something upstream is ever missed: clickjacking, MIME
     * sniffing, referrer leakage, and loading this app's admin panel
     * inside a third-party frame are all blocked outright.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
        );

        // CSP is a production hardening measure, not something local dev
        // needs protection from — and Vite's dev server keeps finding new
        // ways to violate a strict one (HMR websocket, IPv6 literal source
        // syntax Chromium rejects outright, blob: workers, ...). Rather
        // than keep chasing each new dev-mode edge case, it's simply not
        // enforced outside production. The other headers above don't
        // interfere with dev tooling, so they stay on everywhere.
        if (app()->environment('production')) {
            $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());
        }

        if (app()->environment('production') && $request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains',
            );
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        // Wherever uploads actually get served from (R2, a custom domain
        // fronting it, or local /storage) — derived from config so this
        // stays correct if R2_URL ever changes, instead of a domain
        // hardcoded here going stale.
        $mediaOrigin = $this->mediaDiskOrigin();
        $imgExtra = $mediaOrigin ? " {$mediaOrigin}" : '';
        $connectExtra = $mediaOrigin ? " {$mediaOrigin}" : '';

        $directives = [
            "default-src 'self'",
            // Alpine.js (bundled by Livewire/Filament) evaluates directive
            // expressions via `new Function()`, which requires unsafe-eval
            // under a strict CSP unless swapped for Alpine's CSP build.
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "font-src 'self' https://fonts.bunny.net data:",
            // ui-avatars.com is Filament's default fallback avatar for the
            // account widget when a user has no photo of their own.
            "img-src 'self' data: blob: https://ui-avatars.com{$imgExtra}",
            "connect-src 'self'{$connectExtra}",
            // Filament's file-upload field spawns a Web Worker from a
            // blob: URL (client-side image processing). Without this,
            // browsers fall back to script-src for workers, which doesn't
            // allow blob:, and silently block the worker.
            "worker-src 'self' blob:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ];

        return implode('; ', $directives);
    }

    /**
     * The scheme+host (no path) that uploaded media is actually served
     * from, based on whichever disk `filesystems.media_disk` points at —
     * e.g. R2_URL for the "r2" disk. Null if that disk has no configured
     * URL (nothing to add beyond 'self').
     */
    private function mediaDiskOrigin(): ?string
    {
        $disk = config('filesystems.media_disk');
        $url = config("filesystems.disks.{$disk}.url");

        if (! $url) {
            return null;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);

        if (! $scheme || ! $host) {
            return null;
        }

        $port = parse_url($url, PHP_URL_PORT);

        return "{$scheme}://{$host}".($port ? ":{$port}" : '');
    }
}
