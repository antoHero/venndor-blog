<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\AdminUser;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(PostStatus::cases());

        return [
            'title' => fake()->unique()->sentence(6),
            'excerpt' => fake()->sentence(20),
            'body' => collect((array) fake()->paragraphs(5))
                ->map(fn (string $paragraph) => "<p>{$paragraph}</p>")
                ->implode(''),
            'category_id' => Category::factory(),
            'admin_user_id' => AdminUser::factory(),
            'status' => $status,
            'published_at' => $status === PostStatus::Draft ? null : fake()->dateTimeBetween('-1 month', '+1 month'),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }
}
