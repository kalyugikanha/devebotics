<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Enums\PricingModel;
use App\Enums\ProductAvailability;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' Suite',
            'tagline' => fake()->sentence(6),
            'summary' => fake()->sentence(18),
            'description' => '<p>'.fake()->paragraph().'</p>',
            'features' => [['title' => 'Dashboards', 'description' => fake()->sentence(), 'icon' => 'chart-bar']],
            'pricing_model' => PricingModel::QuoteOnly,
            'availability' => ProductAvailability::Available,
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => ContentStatus::Draft, 'published_at' => null]);
    }
}
