<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Author;
use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'author_id' => Author::factory(),
            'title' => fake()->unique()->sentence(6),
            'excerpt' => fake()->sentence(20),
            'body' => '<h2>Introduction</h2><p>'.fake()->paragraphs(3, true).'</p><h2>Details</h2><p>'.fake()->paragraph().'</p>',
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => PostStatus::Draft, 'published_at' => null]);
    }

    public function inReview(): static
    {
        return $this->state(fn () => ['status' => PostStatus::InReview, 'published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['status' => PostStatus::Scheduled, 'published_at' => now()->addDays(3)]);
    }
}
