<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Auto-generates a unique `slug` from the model's `name`/`title` attribute
 * whenever it's left blank, so Filament resources don't have to duplicate
 * this logic in every form.
 */
trait HasUniqueSlug
{
    public static function bootHasUniqueSlug(): void
    {
        static::creating(function (self $model): void {
            if (filled($model->slug)) {
                return;
            }

            $model->slug = $model->generateUniqueSlug();
        });
    }

    protected function generateUniqueSlug(): string
    {
        $source = $this->{$this->slugSourceColumn()};
        $base = Str::slug($source) ?: Str::random(8);
        $slug = $base;
        $suffix = 1;

        while (static::query()->where('slug', $slug)->exists()) {
            $suffix++;
            $slug = "{$base}-{$suffix}";
        }

        return $slug;
    }

    protected function slugSourceColumn(): string
    {
        return 'name';
    }
}
