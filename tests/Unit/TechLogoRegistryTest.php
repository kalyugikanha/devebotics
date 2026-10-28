<?php

namespace Tests\Unit;

use App\Enums\TechnologyCategory;
use App\Support\Art\TechLogoRegistry;
use PHPUnit\Framework\TestCase;

class TechLogoRegistryTest extends TestCase
{
    public function test_known_technologies_use_bundled_logos_including_aliases(): void
    {
        $this->assertSame('laravel', TechLogoRegistry::resolve('laravel', 'Laravel')['name']);
        $this->assertSame('nodedotjs', TechLogoRegistry::resolve('nodejs', 'Node.js')['name']);
        $this->assertSame('react', TechLogoRegistry::resolve('react-native', 'React Native')['name']);
        $this->assertSame('logo', TechLogoRegistry::resolve('Google Cloud', 'Google Cloud')['type']);
    }

    public function test_capabilities_get_icons_and_unbundled_brands_get_initials(): void
    {
        $this->assertSame(['icon', 'circle-stack'], array_values(array_slice(TechLogoRegistry::resolve('vector-databases', 'Vector databases', TechnologyCategory::AiMl), 0, 2)));

        $azure = TechLogoRegistry::resolve('microsoft-azure', 'Microsoft Azure', 'cloud');
        $this->assertSame('initials', $azure['type']);
        $this->assertSame('Az', $azure['name']);

        $this->assertSame('QL', TechLogoRegistry::resolve('quantum-ledger', 'Quantum Ledger')['name']);
    }

    public function test_colours_stay_visible_in_both_themes(): void
    {
        $next = TechLogoRegistry::resolve('nextjs', 'Next.js');
        $this->assertSame('#000000', $next['color']);
        $this->assertSame('#E9EEF9', $next['dark']);

        $javascript = TechLogoRegistry::resolve('javascript', 'JavaScript');
        $this->assertNotSame('#F7DF1E', $javascript['color'], 'Light yellow is darkened for white backgrounds.');
    }

    public function test_every_bundled_logo_is_a_single_safe_path(): void
    {
        $directory = dirname(__DIR__, 2).'/resources/svg/tech';

        foreach (array_keys(TechLogoRegistry::options()) as $file) {
            $svg = file_get_contents("{$directory}/{$file}.svg");

            $this->assertNotFalse($svg, "Missing logo file {$file}.svg");
            $this->assertMatchesRegularExpression('#^<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="[MmLlHhVvCcSsQqTtAaZz0-9.,\-\s eE]+"/></svg>\s*$#', $svg, $file);
        }
    }
}
