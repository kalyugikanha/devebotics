<?php

namespace Tests\Feature\Public;

use App\Models\Redirect;
use App\Models\User;
use App\Support\Settings\SettingsRegistry;
use App\Support\Theme\ThemeManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_send_a_strict_nonce_based_csp_and_hardening_headers(): void
    {
        $response = $this->get('/contact');

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertMatchesRegularExpression("/script-src 'nonce-[A-Za-z0-9]{32}' 'strict-dynamic'/", $csp);
        $this->assertStringNotContainsString("'unsafe-eval'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);

        $response->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        preg_match("/'nonce-([A-Za-z0-9]{32})'/", $csp, $matches);
        $response->assertSee('nonce="'.$matches[1].'"', false);
    }

    public function test_managed_redirects_and_gone_responses(): void
    {
        Redirect::query()->create(['from_path' => '/old-services', 'to_url' => '/services', 'status_code' => 301]);
        Redirect::query()->create(['from_path' => '/retired', 'to_url' => null, 'status_code' => 410]);
        Redirect::query()->create(['from_path' => '/paused', 'to_url' => '/services', 'status_code' => 301, 'is_active' => false]);

        $this->get('/old-services?utm_source=x')->assertStatus(301)->assertRedirect(url('/services').'?utm_source=x');
        $this->get('/retired')->assertStatus(410);
        $this->get('/paused')->assertNotFound();
        $this->assertSame(1, Redirect::query()->where('from_path', '/old-services')->value('hits'));
    }

    public function test_private_uploads_are_not_web_accessible(): void
    {
        Storage::disk('local')->put('inquiries/secret.pdf', 'secret');

        $this->get('/storage/inquiries/secret.pdf')->assertNotFound();
    }

    public function test_published_theme_colours_are_rendered_and_drafts_are_private(): void
    {
        $theme = app(ThemeManager::class)->published();
        $theme['light']['primary'] = '#123456';
        settings()->set('theme', 'draft', $theme);

        $this->get('/')->assertDontSee('--brand-primary:#123456', false);

        $this->withSession([ThemeManager::PREVIEW_SESSION_KEY => true])->get('/')->assertDontSee('--brand-primary:#123456', false);

        $this->actingAs(User::factory()->withRole('Content Manager')->create())->get('/theme-preview/start')->assertForbidden();

        $designer = User::factory()->withRole('Administrator')->create();
        $this->actingAs($designer)->get('/theme-preview/start')->assertRedirect('/');
        $this->actingAs($designer)->get('/')->assertSee('--brand-primary:#123456', false)->assertSee('Previewing the');
    }

    public function test_invalid_theme_values_cannot_inject_css(): void
    {
        $theme = app(ThemeManager::class)->published();
        $theme['light']['primary'] = 'red;}</style><script>alert(1)</script>';
        settings()->set('theme', 'published', $theme);

        $this->get('/')->assertDontSee('<script>alert(1)</script>', false)->assertSee('--brand-primary:'.SettingsRegistry::defaultTheme()['light']['primary'], false);
    }
}
