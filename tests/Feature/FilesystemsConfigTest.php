<?php

test('s3-compatible media disks set a long-lived, immutable Cache-Control on upload', function () {
    foreach (['s3', 'r2'] as $disk) {
        $options = config("filesystems.disks.{$disk}.options");

        expect($options['CacheControl'] ?? null)
            ->toBe('public, max-age=31536000, immutable');
    }
});
