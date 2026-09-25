<?php

// Applied to every object PUT against S3-compatible disks (s3, r2), so both
// browsers and the CDN cache uploaded media long-term. Safe to cache
// forever: Spatie Media Library gives every upload its own UUID-named path,
// so a path is never reused for different content — no invalidation needed.
$uploadedMediaCacheControl = 'public, max-age=31536000, immutable';

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Media Upload Disk
    |--------------------------------------------------------------------------
    |
    | Which disk post images (Spatie Media Library) and rich-text editor
    | attachments are stored on. Defaults to the local "public" disk so
    | development and testing work with zero external setup; set
    | MEDIA_DISK=r2 once the R2_* variables below are filled in to switch
    | uploads to Cloudflare R2 without changing any application code.
    |
    */

    'media_disk' => env('MEDIA_DISK', 'r2'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim((string) env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
            'options' => [
                'CacheControl' => $uploadedMediaCacheControl,
            ],
        ],

        // Cloudflare R2 — S3-compatible, so it uses the same "s3" driver.
        // Get these from the Cloudflare dashboard: R2 > (bucket) > Settings
        // for the endpoint, and R2 > Manage API Tokens for the key/secret.
        // R2_URL is the bucket's public base URL (a custom domain, or the
        // r2.dev subdomain if public access is enabled on the bucket).
        'r2' => [
            'driver' => 's3',
            'key' => env('R2_ACCESS_KEY_ID'),
            'secret' => env('R2_SECRET_ACCESS_KEY'),
            'region' => env('R2_REGION', 'auto'),
            'bucket' => env('R2_BUCKET'),
            'url' => env('R2_URL'),
            'endpoint' => env('R2_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
            'options' => [
                'CacheControl' => $uploadedMediaCacheControl,
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
