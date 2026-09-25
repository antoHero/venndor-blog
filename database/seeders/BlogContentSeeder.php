<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\AdminUser;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;

/**
 * Demo posts spread across "recent" (within Post::RECENT_WITHIN_DAYS) and
 * older publish dates, so the public blog listing's featured post, "Recent"
 * badges, and reading-time calculation all have something real to show.
 */
class BlogContentSeeder extends Seeder
{
    public function run(): void
    {
        $author = AdminUser::query()->firstOrCreate(
            ['email' => 'editorial@venndor.test'],
            [
                'name' => 'Venndor Editorial',
                'mobile' => '2348000000001',
                'role' => 'admin',
                'is_active' => true,
            ],
        );

        $categories = Category::query()->pluck('id', 'name');

        // [category name, title, days since published (null = draft), body paragraphs]
        // Paragraph counts are deliberately varied so the reading-time
        // calculation (~200 words/minute) actually shows different values
        // across posts rather than every short demo post rounding to 1 min.
        $posts = [
            ['Insight', 'Event Categories Nigerians Love The Most', 0, 4],
            ['Ticket Hacks', 'The Ticketing Way For Corporate Gatherings', 1, 6],
            ['Venue Scouting', 'Five Questions To Ask Before You Book A Venue', 2, 3],
            ['Vendor Management', 'How To Vet A Caterer Before The Deposit', 5, 8],
            ['Galas', 'Behind The Scenes Of A Black-Tie Gala Budget', 9, 10],
            ['Insight', 'How To Organise And Curate Traditional Events', 16, 5],
            ['Ticket Hacks', 'Cutting No-Shows With Smarter Reminders', 27, 12],
            ['Venue Scouting', 'Outdoor Vs Indoor: Picking The Right Season', 45, 2],
            ['Galas', 'Draft: Next Quarter\'s Gala Season Preview', null, 4],
        ];

        foreach ($posts as [$categoryName, $title, $daysAgo, $paragraphs]) {
            $body = collect((array) fake()->paragraphs($paragraphs))
                ->map(fn (string $paragraph) => "<p>{$paragraph}</p>")
                ->implode('');

            Post::factory()
                ->when(
                    $daysAgo === null,
                    fn ($factory) => $factory->draft(),
                    fn ($factory) => $factory->state([
                        'status' => PostStatus::Published,
                        'published_at' => now()->subDays($daysAgo),
                    ]),
                )
                ->create([
                    'title' => $title,
                    'body' => $body,
                    'category_id' => $categories[$categoryName] ?? null,
                    'admin_user_id' => $author->id,
                ]);
        }
    }
}
