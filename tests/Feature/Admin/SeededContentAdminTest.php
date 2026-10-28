<?php

namespace Tests\Feature\Admin;

use App\Enums\SectionType;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\RelationManagers\SectionsRelationManager;
use App\Models\Page;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CoreContentSeeder;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\PermissionSeeder;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Seeded content must load and save cleanly through the admin forms.
 */
class SeededContentAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');

        $this->seed([PermissionSeeder::class, CoreContentSeeder::class, DemoContentSeeder::class]);
    }

    public function test_seeded_records_open_in_the_admin(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create());

        $home = Page::query()->where('slug', 'home')->firstOrFail();

        $this->get('/admin/pages/'.$home->id.'/edit')->assertOk();
        $this->get('/admin/products/'.Product::query()->firstOrFail()->id.'/edit')->assertOk();
        $this->get('/admin/case-studies/'.Portfolio::query()->firstOrFail()->id.'/edit')->assertOk();
        $this->get('/admin/navigation-menus')->assertOk();
        $this->get('/admin/media')->assertOk();
    }

    public function test_seeded_homepage_sections_can_be_edited_and_saved(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());

        $home = Page::query()->where('slug', 'home')->firstOrFail();
        $manager = fn () => Livewire::test(SectionsRelationManager::class, ['ownerRecord' => $home, 'pageClass' => EditPage::class]);

        foreach ([SectionType::Hero, SectionType::WhyChoose, SectionType::Logos, SectionType::Cta, SectionType::Services] as $type) {
            $section = $home->sections()->where('type', $type->value)->firstOrFail();
            $before = $section->data;

            $manager()
                ->callAction(TestAction::make(EditAction::class)->table($section), ['title' => $section->title.' (edited)'])
                ->assertHasNoFormErrors();

            $section->refresh();
            $this->assertStringEndsWith('(edited)', (string) $section->title);
            $this->assertEquals($before, $section->data, $type->value.' data changed unexpectedly on save.');
        }
    }
}
