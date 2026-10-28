<?php

namespace Tests\Feature;

use App\Support\Art\SpotRegistry;
use Tests\TestCase;

class SpotIllustrationsTest extends TestCase
{
    public function test_every_spot_renders_safe_inline_svg(): void
    {
        foreach (array_keys(SpotRegistry::all()) as $name) {
            $html = (string) $this->blade('<x-spot :name="$name" class="aspect-[10/7]" />', ['name' => $name]);

            $this->assertStringContainsString('class="spot relative aspect-[10/7]"', $html, $name);
            $this->assertStringContainsString('aria-hidden="true"', $html, $name);
            $this->assertStringContainsString('viewBox="0 0 200 140"', $html, $name);
            $this->assertStringNotContainsString('<style', $html, $name);
            $this->assertStringNotContainsString('<script', $html, $name);
            $this->assertStringNotContainsString('{{', $html, $name);
        }
    }

    public function test_two_spots_on_one_page_get_unique_ids(): void
    {
        $html = (string) $this->blade('<x-spot name="workflow" /><x-spot name="workflow" />');

        preg_match_all('/id="(spot-[a-z0-9]+)-sh"/', $html, $matches);

        $this->assertCount(2, $matches[1]);
        $this->assertNotSame($matches[1][0], $matches[1][1]);
    }

    public function test_unknown_names_fall_back_to_the_generic_spot(): void
    {
        $this->blade('<x-spot name="../../layouts/app" />')
            ->assertSee('viewBox="0 0 200 140"', false)
            ->assertDontSee('<html', false);

        $this->assertStringContainsString(
            'stroke-opacity=".4" stroke-dasharray="2 6"',
            (string) $this->blade('<x-spot name="nope" />'),
        );
    }
}
