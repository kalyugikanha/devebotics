<?php

namespace Database\Factories;

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    public function definition(): array
    {
        $email = fake()->safeEmail();

        return [
            'source' => 'client_form',
            'client_name' => fake()->name(),
            'company_name' => fake()->company(),
            'email' => $email,
            'email_hash' => Testimonial::hashEmail($email),
            'rating' => fake()->numberBetween(4, 5),
            'feedback' => fake()->paragraph(),
            'publish_permission' => true,
            'display_identity_consent' => true,
            'status' => TestimonialStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => TestimonialStatus::Approved]);
    }
}
