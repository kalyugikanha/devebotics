<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Settings\AnimationSettings;
use App\Filament\Pages\Settings\CompanySettings;
use App\Filament\Pages\Settings\FormsPrivacySettings;
use App\Filament\Pages\Settings\IntegrationSettings;
use App\Filament\Pages\Settings\SeoSettings;
use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use App\Support\Theme\MotionConfig;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_company_settings_are_saved_and_cached_values_update(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        $this->assertSame('Softec Ai Solution', settings('company.name'));

        Livewire::test(CompanySettings::class)
            ->assertOk()
            ->fillForm([
                'company.name' => 'Softec Ai Solution Ltd',
                'contact.email' => 'hello@softec.example',
                'contact.phone' => '+1 555 0100',
                'contact.business_hours' => [['days' => 'Mon – Fri', 'hours' => '09:00 – 18:00']],
                'social.linkedin' => 'https://www.linkedin.com/company/softec',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Softec Ai Solution Ltd', settings('company.name'));
        $this->assertSame('hello@softec.example', settings('contact.email'));
        $this->assertSame('https://www.linkedin.com/company/softec', settings('social.linkedin'));
        $this->assertDatabaseHas('audit_logs', ['event' => 'settings.updated']);
    }

    public function test_company_settings_validate_urls_emails_and_map_embed(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        Livewire::test(CompanySettings::class)
            ->fillForm([
                'contact.email' => 'not-an-email',
                'contact.map_embed_url' => 'https://evil.example/maps/embed?x=1',
                'social.x' => 'javascript:alert(1)',
                'company.header_cta_url' => 'javascript:alert(1)',
            ])
            ->call('save')
            ->assertHasFormErrors(['contact.email', 'contact.map_embed_url', 'social.x', 'company.header_cta_url']);
    }

    public function test_forms_privacy_settings_validate_recipient_emails(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        Livewire::test(FormsPrivacySettings::class)
            ->fillForm(['forms.inquiry_recipients' => ['sales@softec.example', 'bad-address']])
            ->call('save')
            ->assertHasFormErrors(['forms.inquiry_recipients.1']);

        Livewire::test(FormsPrivacySettings::class)
            ->fillForm(['forms.inquiry_recipients' => ['sales@softec.example'], 'features.newsletter_enabled' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(['sales@softec.example'], settings('forms.inquiry_recipients'));
        $this->assertFalse((bool) settings('features.newsletter_enabled'));
    }

    public function test_integration_toggles_cannot_be_enabled_without_environment_secrets(): void
    {
        config(['softec.turnstile.site_key' => null, 'softec.turnstile.secret_key' => null, 'softec.ai.anthropic_api_key' => null]);
        $this->actingAs(User::factory()->superAdmin()->create());

        Livewire::test(IntegrationSettings::class)
            ->fillForm(['integrations.ga4_measurement_id' => 'G-AB12CD34EF'])
            ->set('data.integrations.turnstile_enabled', true)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertDontSee('secret-value');

        $this->assertSame('G-AB12CD34EF', settings('integrations.ga4_measurement_id'));
        $this->assertFalse((bool) settings('integrations.turnstile_enabled'));

        Livewire::test(IntegrationSettings::class)
            ->fillForm(['integrations.ga4_measurement_id' => 'UA-12345'])
            ->call('save')
            ->assertHasFormErrors(['integrations.ga4_measurement_id']);
    }

    public function test_integration_page_never_renders_secret_values(): void
    {
        config(['softec.turnstile.site_key' => 'site-key-123', 'softec.turnstile.secret_key' => 'super-secret-turnstile-value', 'softec.ai.anthropic_api_key' => 'sk-ant-secret-value']);
        $this->actingAs(User::factory()->superAdmin()->create());

        Livewire::test(IntegrationSettings::class)
            ->assertOk()
            ->assertDontSee('super-secret-turnstile-value')
            ->assertDontSee('sk-ant-secret-value');
    }

    public function test_seo_settings_validate_robots_paths_and_tokens(): void
    {
        $this->actingAs(User::factory()->withRole('SEO Manager')->create());

        Livewire::test(SeoSettings::class)
            ->fillForm(['seo.robots_disallow' => ['/private', 'no-slash'], 'seo.google_site_verification' => '<script>'])
            ->call('save')
            ->assertHasFormErrors(['seo.robots_disallow.1', 'seo.google_site_verification']);

        Livewire::test(SeoSettings::class)
            ->fillForm(['seo.robots_disallow' => ['/private'], 'seo.title_suffix' => ' — Softec'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(['/private'], settings('seo.robots_disallow'));
    }

    public function test_animation_settings_accept_only_whitelisted_presets(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        Livewire::test(AnimationSettings::class)
            ->fillForm(['animation.hero_preset' => 'blur', 'animation.duration_scale' => 1.2, 'animation.parallax' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $config = app(MotionConfig::class)->toArray();
        $this->assertSame('blur', $config['hero']);
        $this->assertFalse($config['parallax']);
        $this->assertEqualsWithDelta(1.2, $config['durationScale'], 0.001);

        Livewire::test(AnimationSettings::class)
            ->fillForm(['animation.hero_preset' => '<script>alert(1)</script>'])
            ->call('save')
            ->assertHasFormErrors(['animation.hero_preset']);
    }

    public function test_settings_pages_are_denied_without_permission(): void
    {
        $author = User::factory()->withRole('Blog Author')->create();
        $this->actingAs($author);

        foreach (['/admin/settings/company', '/admin/settings/forms-privacy', '/admin/settings/integrations', '/admin/settings/seo', '/admin/settings/animation', '/admin/theme-settings', '/admin/email-templates', '/admin/email-logs', '/admin/redirects'] as $url) {
            $this->get($url)->assertForbidden();
        }

        $seo = User::factory()->withRole('SEO Manager')->create();
        $this->actingAs($seo);
        $this->get('/admin/settings/seo')->assertOk();
        $this->get('/admin/redirects')->assertOk();
        $this->get('/admin/settings/company')->assertForbidden();
    }
}
