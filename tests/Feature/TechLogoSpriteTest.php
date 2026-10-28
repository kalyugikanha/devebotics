<?php

namespace Tests\Feature;

use App\Support\Art\TechLogoSprite;
use Database\Seeders\CoreContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechLogoSpriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_page_defines_the_logos_it_uses(): void
    {
        $this->seed(CoreContentSeeder::class);

        foreach ([1, 2] as $visit) {
            $html = $this->get('/')->assertOk()->getContent();

            preg_match_all('/<use href="#(tl-[a-z0-9]+)"/', $html, $used);
            preg_match_all('/<symbol id="(tl-[a-z0-9]+)"/', $html, $defined);

            $this->assertNotEmpty($used[1], "Visit {$visit} shows technology logos");
            $this->assertSame([], array_values(array_diff(array_unique($used[1]), $defined[1])), "Visit {$visit} references undefined logos");
            $this->assertSame(count($defined[1]), count(array_unique($defined[1])), 'Each logo path is sent once');
        }
    }

    public function test_a_logo_path_is_sent_once_and_reused(): void
    {
        $sprite = app(TechLogoSprite::class);

        $first = (string) $sprite->render('laravel', 'size-5');
        $second = (string) $sprite->render('laravel', 'size-7');

        $this->assertStringContainsString('<symbol id="tl-laravel"', $first);
        $this->assertStringContainsString('<use href="#tl-laravel"/>', $first);
        $this->assertStringNotContainsString('<symbol', $second);
        $this->assertStringContainsString('<use href="#tl-laravel"/>', $second);
        $this->assertStringContainsString('class="size-7"', $second);
    }

    public function test_unknown_or_path_like_names_render_nothing(): void
    {
        $sprite = app(TechLogoSprite::class);

        $this->assertSame('', (string) $sprite->render('../../.env'));
        $this->assertSame('', (string) $sprite->render('not-a-logo'));
    }
}
