<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    /**
     * How many published posts to send to the public blog listing — the
     * newest is the featured post, the rest fill the grid (and are what
     * the client-side category/search filter runs against).
     */
    private const MAX_POSTS = 25;

    public function index(): Response
    {
        $posts = Post::query()
            ->published()
            ->with(['category', 'author', 'media'])
            ->orderByDesc('published_at')
            ->limit(self::MAX_POSTS)
            ->get();

        $featured = $posts->shift();

        return Inertia::render('welcome', [
            'featuredPost' => $featured ? $this->present($featured, tag: 'New Blog') : null,
            'posts' => $posts->map(fn (Post $post) => $this->present($post))->values(),
            'categories' => Category::query()->orderBy('name')->pluck('name'),
        ]);
    }

    public function show(Post $post): Response
    {
        // Implicit route-model-binding already excludes soft-deleted posts,
        // but not drafts/scheduled ones or a publish date still in the
        // future — those must 404, not leak a preview via a guessed slug.
        if (! $post->isPubliclyVisible()) {
            abort(404);
        }

        $post->loadMissing(['category', 'author', 'tags', 'media']);

        $related = Post::query()
            ->published()
            ->whereKeyNot($post->id)
            ->when(
                $post->category_id,
                fn ($query) => $query->where('category_id', $post->category_id),
            )
            ->with(['category', 'author', 'media'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $postData = [
            ...$this->present($post),
            'body' => $post->body,
            'author' => $post->author?->name,
            'tags' => $post->tags->pluck('name'),
            'seoTitle' => $post->seo_title ?: $post->title,
            'seoDescription' => $post->seo_description ?: $post->excerpt,
        ];

        // Rendered server-side in app.blade.php so WhatsApp/LinkedIn crawlers can read it
        View::share('meta', [
            'title' => $postData['seoTitle'],
            'description' => Str::limit(trim(strip_tags((string) $postData['seoDescription'])), 160),
            'image' => ! empty($postData['image']) ? url($postData['image']) : null,
            'url' => url()->current(),
        ]);

        return Inertia::render('blog-post', [
            'post' => $postData,
            'related' => $related->map(fn (Post $related) => $this->present($related))->values(),
        ]);
    }

    /**
     * @return array{id: string, slug: string, title: string, excerpt: ?string, image: ?string, date: ?string, readTime: string, category: string, tag: ?string}
     */
    private function present(Post $post, ?string $tag = null): array
    {
        return [
            'id' => (string) $post->id,
            'slug' => $post->slug,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'image' => $post->featuredImageUrl(),
            'date' => $post->published_at?->format('d M Y'),
            'readTime' => $post->readingTimeMinutes().' min read',
            'category' => optional($post->category)->name ?? 'Uncategorized',
            'tag' => $tag ?? ($post->isRecent() ? 'Recent' : null),
        ];
    }
}
