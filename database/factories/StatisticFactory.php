<?php

namespace Database\Factories;

use App\Models\Statistic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Statistic>
 */
class StatisticFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => fake()->words(2, true),
            'value' => fake()->numberBetween(5, 500),
            'suffix' => '+',
            'is_verified' => true,
            'is_active' => true,
        ];
    }
}
