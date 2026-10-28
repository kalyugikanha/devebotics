<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\ThemeSettings;
use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use App\Support\Settings\SettingsRegistry;
use App\Support\Theme\ThemeManager;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ThemeSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_saving_a_draft_does_not_change_the_published_theme_until_published(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());
        $originalPrimary = SettingsRegistry::defaultTheme()['light']['primary'];

        Livewire::test(ThemeSettings::class)
            ->assertOk()
            ->fillForm(['light.primary' => '#1D4ED8', 'default_mode' => 'dark'])
            ->call('saveDraft')
            ->assertHasNoFormErrors();

        $manager = app(ThemeManager::class);
        $this->assertSame('#1D4ED8', $manager->draft()['light']['primary']);
        $this->assertSame($originalPrimary, $manager->published()['light']['primary']);

        Livewire::test(ThemeSettings::class)->callAction('publish')->assertHasNoActionErrors();

        $published = app(ThemeManager::class)->published();
        $this->assertSame('#1D4ED8', $published['light']['primary']);
        $this->assertSame('dark', $published['default_mode']);
        $this->assertStringContainsString('--brand-primary:#1D4ED8', app(ThemeManager::class)->css($published));
        $this->assertDatabaseHas('audit_logs', ['event' => 'theme.published']);
    }

    public function test_invalid_colours_are_rejected(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        Livewire::test(ThemeSettings::class)
            ->fillForm(['light.primary' => 'red;}body{display:none'])
            ->call('saveDraft')
            ->assertHasFormErrors(['light.primary']);
    }

    public function test_contrast_warnings_are_shown_for_low_contrast_palettes(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        Livewire::test(ThemeSettings::class)
            ->assertSee('meet WCAG 2.2 AA')
            ->fillForm(['light.foreground' => '#EEEEEE'])
            ->assertSee('Body text on background');

        $warnings = app(ThemeManager::class)->contrastWarnings(array_replace_recursive(SettingsRegistry::defaultTheme(), ['light' => ['foreground' => '#EEEEEE']]));
        $this->assertNotEmpty($warnings);
    }

    public function test_draft_can_be_reset_and_brand_defaults_restored(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        Livewire::test(ThemeSettings::class)
            ->fillForm(['light.primary' => '#000000'])
            ->call('saveDraft')
            ->callAction('resetDraft')
            ->assertFormSet(['light.primary' => SettingsRegistry::defaultTheme()['light']['primary']]);

        $this->assertSame(SettingsRegistry::defaultTheme()['light']['primary'], app(ThemeManager::class)->draft()['light']['primary']);
    }

    public function test_theme_preview_is_restricted_to_appearance_managers(): void
    {
        $author = User::factory()->withRole('Blog Author')->create();
        $this->actingAs($author)->get('/admin/theme-settings')->assertForbidden();
        $this->actingAs($author)->get(route('theme-preview.start'))->assertForbidden();

        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $this->actingAs($admin)->get(route('theme-preview.start'))->assertRedirect(route('home'));
        $this->assertTrue((bool) session(ThemeManager::PREVIEW_SESSION_KEY));
    }
}
