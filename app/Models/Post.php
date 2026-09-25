<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Models\Concerns\HasUniqueSlug;
use Carbon\CarbonImmutable;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string|null $body
 * @property int|null $category_id
 * @property int|null $admin_user_id
 * @property PostStatus $status
 * @property CarbonImmutable|null $published_at
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property-read Category|null $category
 * @property-read AdminUser|null $author
 * @property-read Collection<int, Tag> $tags
 */
class Post extends Model implements HasMedia
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, HasUniqueSlug, InteractsWithMedia, SoftDeletes;

    /**
     * How many days old a post may be and still be considered "recent"
     * (e.g. eligible for the "Recent" badge on the blog listing).
     */
    public const RECENT_WITHIN_DAYS = 3;

    private const WORDS_PER_MINUTE = 200;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'category_id',
        'admin_user_id',
        'status',
        'published_at',
        'seo_title',
        'seo_description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<AdminUser, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->where('published_at', '<=', now());
    }

    protected function slugSourceColumn(): string
    {
        return 'title';
    }

    /**
     * Estimated reading time, in whole minutes, based on the body's word
     * count at {@see WORDS_PER_MINUTE}. Always at least 1 minute.
     */
    public function readingTimeMinutes(): int
    {
        $wordCount = str_word_count(strip_tags((string) $this->body));

        return max(1, (int) ceil($wordCount / self::WORDS_PER_MINUTE));
    }

    /**
     * Whether this post should be reachable on the public site right now —
     * published, and its publish date has actually arrived. Used to 404 a
     * draft/scheduled post rather than let a guessed slug preview it.
     */
    public function isPubliclyVisible(): bool
    {
        return $this->status === PostStatus::Published
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    /**
     * Whether this post published within the last {@see RECENT_WITHIN_DAYS}
     * days — the cutoff for the "Recent" badge on the public blog listing.
     */
    public function isRecent(): bool
    {
        if (! $this->published_at) {
            return false;
        }

        return $this->published_at->greaterThanOrEqualTo(
            now()->subDays(self::RECENT_WITHIN_DAYS),
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('posts')
            ->useDisk(config('filesystems.media_disk', 'public'))
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * The featured image's public URL, or null if none has been uploaded.
     * Backed by Spatie Media Library (the `posts` collection) rather than a
     * plain DB column, so it works the same way whether the file lives on
     * local storage or Cloudflare R2.
     */
    public function featuredImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('posts') ?: null;
    }
}
