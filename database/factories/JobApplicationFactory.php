<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\JobOpening;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_opening_id' => JobOpening::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'cover_letter' => fake()->paragraph(),
            'status' => ApplicationStatus::New,
        ];
    }
}
