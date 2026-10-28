<?php

namespace Database\Factories;

use App\Enums\InquiryPriority;
use App\Enums\InquiryStatus;
use App\Enums\InquiryType;
use App\Models\ContactInquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactInquiry>
 */
class ContactInquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => InquiryType::Quick,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company' => fake()->company(),
            'subject' => fake()->sentence(5),
            'message' => fake()->paragraph(),
            'status' => InquiryStatus::New,
            'priority' => InquiryPriority::Normal,
        ];
    }
}
