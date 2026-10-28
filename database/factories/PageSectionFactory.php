<?php

namespace Database\Factories;

use App\Enums\SectionBackground;
use App\Enums\SectionType;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageSection>
 */
class PageSectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'type' => SectionType::RichText,
            'title' => fake()->sentence(4),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'background' => SectionBackground::Default,
            'sort_order' => 0,
            'is_enabled' => true,
        ];
    }
}
