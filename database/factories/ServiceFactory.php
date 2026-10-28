<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true).' development',
            'tagline' => fake()->sentence(5),
            'excerpt' => fake()->sentence(18),
            'overview' => fake()->paragraph(),
            'description' => '<h2>Overview</h2><p>'.fake()->paragraph().'</p>',
            'icon' => 'code-bracket',
            'benefits' => [['title' => 'Faster delivery', 'description' => fake()->sentence(), 'icon' => 'bolt']],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => ContentStatus::Draft, 'published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['status' => ContentStatus::Published, 'published_at' => now()->addWeek()]);
    }
}
