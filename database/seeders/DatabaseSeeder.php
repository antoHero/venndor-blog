<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Matches the category filters already shown on the public blog
        // landing page, so the two stay in sync from day one. Slug is
        // passed explicitly since this seeder disables model events
        // (WithoutModelEvents), which the auto-slug hook relies on.
        collect(['Vendor Management', 'Ticket Hacks', 'Venue Scouting', 'Galas', 'Insight'])
            ->each(fn (string $name) => Category::query()->firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)],
            ));

        $this->call(BlogContentSeeder::class);
    }
}
