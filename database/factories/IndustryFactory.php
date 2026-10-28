<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Industry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Industry>
 */
class IndustryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'icon' => 'building-office-2',
            'excerpt' => fake()->sentence(15),
            'description' => '<p>'.fake()->paragraph().'</p>',
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => ContentStatus::Draft, 'published_at' => null]);
    }
}
