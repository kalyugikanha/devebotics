<?php

namespace Tests\Unit;

use App\Support\Settings\SettingsRegistry;
use App\Support\Theme\AdminPalette;
use Filament\Support\View\Components\ButtonComponent;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPaletteTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function brandColours(): array
    {
        return [
            'default primary' => [SettingsRegistry::defaultTheme()['light']['primary']],
            'violet' => ['#7C3AED'],
            'teal' => ['#0E7490'],
            'blue' => ['#2563EB'],
        ];
    }

    #[DataProvider('brandColours')]
    public function test_primary_buttons_use_the_exact_brand_colour_with_light_text(string $hex): void
    {
        $palette = AdminPalette::fromHex($hex);
        $buttons = ButtonComponent::make()->getColorMap($palette);

        $this->assertSame(600, $buttons['bg'], 'Primary buttons should use the brand colour, not a pastel tint.');
        $this->assertSame(500, $buttons['hover:bg']);
        $this->assertContains($buttons['text'], [0, 50], 'Button text should be white (or near-white).');
    }
}
