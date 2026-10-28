<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Portfolio>
 */
class PortfolioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(4),
            'client_label' => 'A '.fake()->word().' company',
            'summary' => fake()->sentence(18),
            'challenge' => '<p>'.fake()->paragraph().'</p>',
            'solution' => '<p>'.fake()->paragraph().'</p>',
            'metrics' => [
                ['label' => 'Faster processing', 'value' => '40%', 'is_verified' => true],
                ['label' => 'Unverified claim', 'value' => '10x', 'is_verified' => false],
            ],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => ContentStatus::Draft, 'published_at' => null]);
    }
}
