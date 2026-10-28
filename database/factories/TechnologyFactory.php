<?php

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word().' '.fake()->randomNumber(3),
            'category' => fake()->randomElement(['frontend', 'backend', 'cloud', 'ai_ml']),
            'is_active' => true,
        ];
    }
}
