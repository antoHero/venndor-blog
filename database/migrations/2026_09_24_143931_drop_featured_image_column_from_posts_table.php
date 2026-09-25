<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The featured image is now stored via Spatie Media Library (the `posts`
 * collection on the `media` table) instead of a plain path column, so this
 * app can support Cloudflare R2 and other disks the same way local storage
 * works. This column has never been written to since that switch.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn('featured_image');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->string('featured_image')->nullable();
        });
    }
};
