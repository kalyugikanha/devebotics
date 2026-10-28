<?php

namespace Tests\Feature\Admin;

use App\Enums\ContentStatus;
use App\Enums\SectionType;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\RelationManagers\SectionsRelationManager;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PageResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_manager_can_create_and_edit_pages(): void
    {
        $user = User::factory()->withRole('Content Manager')->create();
        $this->actingAs($user);

        Livewire::test(ListPages::class)->assertOk();

        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Partner Program',
                'slug' => 'partner-program',
                'template' => 'landing',
                'status' => ContentStatus::Draft->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $page = Page::query()->where('slug', 'partner-program')->firstOrFail();

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['excerpt' => 'Grow with Softec Ai Solution.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Grow with Softec Ai Solution.', $page->refresh()->excerpt);
    }

    public function test_reserved_slugs_are_rejected(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());

        Livewire::test(CreatePage::class)
            ->fillForm(['title' => 'Blog', 'slug' => 'blog', 'template' => 'default', 'status' => ContentStatus::Draft->value])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    }

    public function test_system_pages_cannot_be_deleted_and_keep_their_slug(): void
    {
        $user = User::factory()->withRole('Content Manager')->create();
        $page = Page::factory()->create(['title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'template' => 'legal']);
        $page->forceFill(['is_system' => true])->saveQuietly();

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $page));

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->assertActionHidden(DeleteAction::class)
            ->assertFormFieldDisabled('slug')
            ->fillForm(['slug' => 'something-else'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('privacy-policy', $page->refresh()->slug);
    }

    public function test_sections_can_be_created_edited_and_reordered(): void
    {
        $user = User::factory()->withRole('Content Manager')->create();
        $page = Page::factory()->create(['template' => 'landing']);
        $first = PageSection::factory()->for($page)->create(['sort_order' => 0, 'title' => 'First']);

        $this->actingAs($user);

        Livewire::test(SectionsRelationManager::class, ['ownerRecord' => $page, 'pageClass' => EditPage::class])
            ->assertOk()
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'type' => SectionType::Cta->value,
                'title' => 'Ready to talk?',
                'data' => [
                    'primary_cta' => ['label' => 'Contact us', 'url' => '/contact'],
                    'secondary_cta' => ['label' => 'Docs', 'url' => 'https://example.com/docs'],
                ],
                'background' => 'gradient',
                'is_enabled' => true,
            ])
            ->assertHasNoFormErrors();

        $cta = $page->sections()->where('type', SectionType::Cta->value)->firstOrFail();
        $this->assertSame('/contact', $cta->setting('primary_cta.url'));
        $this->assertSame(1, $cta->sort_order);

        Livewire::test(SectionsRelationManager::class, ['ownerRecord' => $page, 'pageClass' => EditPage::class])
            ->callAction(TestAction::make(EditAction::class)->table($cta), [
                'data' => ['primary_cta' => ['label' => 'Bad', 'url' => 'javascript:alert(1)']],
            ])
            ->assertHasFormErrors(['data.primary_cta.url']);

        Livewire::test(SectionsRelationManager::class, ['ownerRecord' => $page, 'pageClass' => EditPage::class])
            ->call('reorderTable', [(string) $cta->getKey(), (string) $first->getKey()]);

        $this->assertLessThan($first->refresh()->sort_order, $cta->refresh()->sort_order);
    }

    public function test_items_sections_store_repeater_items(): void
    {
        $user = User::factory()->withRole('Content Manager')->create();
        $page = Page::factory()->create(['template' => 'landing']);

        $this->actingAs($user);

        Livewire::test(SectionsRelationManager::class, ['ownerRecord' => $page, 'pageClass' => EditPage::class])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'type' => SectionType::EngagementModels->value,
                'title' => 'Engagement models',
                'background' => 'default',
                'data' => ['items' => [['title' => 'Dedicated team', 'best_for' => 'Long-term products', 'features' => ['Flexible'], 'highlighted' => true]]],
            ])
            ->assertHasNoFormErrors();

        $section = $page->sections()->firstOrFail();
        $this->assertSame('Dedicated team', $section->setting('items.0.title'));
        $this->assertSame(['Flexible'], $section->setting('items.0.features'));
    }

    public function test_users_without_page_permissions_are_forbidden(): void
    {
        $this->actingAs(User::factory()->withRole('Sales Manager')->create())
            ->get('/admin/pages')
            ->assertForbidden();
    }
}
