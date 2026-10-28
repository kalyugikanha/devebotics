<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->slug(3);

        return [
            'disk' => 'public',
            'path' => 'media/test/'.$name.'.jpg',
            'original_name' => $name.'.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => fake()->numberBetween(10_000, 900_000),
            'width' => 1600,
            'height' => 900,
            'title' => fake()->sentence(3),
            'alt_text' => fake()->sentence(6),
        ];
    }
}
