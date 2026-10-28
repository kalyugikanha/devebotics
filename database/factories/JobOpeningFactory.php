<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Enums\EmploymentType;
use App\Enums\WorkplaceType;
use App\Models\JobOpening;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobOpening>
 */
class JobOpeningFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->jobTitle(),
            'department' => 'Engineering',
            'location' => fake()->city(),
            'employment_type' => EmploymentType::FullTime,
            'workplace_type' => WorkplaceType::Hybrid,
            'summary' => fake()->sentence(15),
            'description' => '<p>'.fake()->paragraph().'</p>',
            'responsibilities' => [['title' => 'Build features', 'description' => fake()->sentence()]],
            'requirements' => [['title' => '3+ years experience', 'description' => fake()->sentence()]],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
            'valid_through' => now()->addMonth(),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => ['valid_through' => now()->subDay()]);
    }
}
