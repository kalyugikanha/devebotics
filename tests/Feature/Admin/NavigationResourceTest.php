<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\NavigationMenus\Pages\CreateNavigationMenu;
use App\Filament\Resources\NavigationMenus\Pages\EditNavigationMenu;
use App\Filament\Resources\NavigationMenus\RelationManagers\ItemsRelationManager;
use App\Models\NavigationMenu;
use App\Models\Service;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NavigationResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_menus_are_unique_per_location(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());
        NavigationMenu::query()->create(['name' => 'Header', 'location' => 'header', 'is_active' => true]);

        Livewire::test(CreateNavigationMenu::class)
            ->fillForm(['name' => 'Second header', 'location' => 'header'])
            ->call('create')
            ->assertHasFormErrors(['location' => 'unique']);
    }

    public function test_items_can_link_to_routes_records_and_urls(): void
    {
        $this->actingAs(User::factory()->withRole('Content Manager')->create());
        $menu = NavigationMenu::query()->create(['name' => 'Header', 'location' => 'header', 'is_active' => true]);
        $service = Service::factory()->create();

        $manager = fn () => Livewire::test(ItemsRelationManager::class, ['ownerRecord' => $menu, 'pageClass' => EditNavigationMenu::class]);

        $manager()->assertOk()
            ->callAction(TestAction::make(CreateAction::class)->table(), ['label' => 'Blog', 'link_type' => 'route', 'route_name' => 'blog.index', 'is_active' => true])
            ->assertHasNoFormErrors();

        $manager()
            ->callAction(TestAction::make(CreateAction::class)->table(), ['label' => 'Our service', 'link_type' => 'linkable', 'linkable_type' => 'service', 'linkable_id' => $service->id, 'is_active' => true])
            ->assertHasNoFormErrors();

        $manager()
            ->callAction(TestAction::make(CreateAction::class)->table(), ['label' => 'Evil', 'link_type' => 'url', 'url' => 'javascript:alert(1)'])
            ->assertHasFormErrors(['url']);

        $blog = $menu->allItems()->where('label', 'Blog')->firstOrFail();
        $this->assertSame(route('blog.index'), $blog->resolveUrl());

        $linked = $menu->allItems()->where('label', 'Our service')->firstOrFail();
        $this->assertSame($service->publicUrl(), $linked->resolveUrl());
        $this->assertNull($linked->route_name);

        // Switching to a custom URL clears the previous target.
        $manager()
            ->callAction(TestAction::make(EditAction::class)->table($linked), ['link_type' => 'url', 'url' => 'https://example.com', 'parent_id' => $blog->id])
            ->assertHasNoFormErrors();

        $linked->refresh();
        $this->assertNull($linked->linkable_id);
        $this->assertSame('https://example.com', $linked->resolveUrl());
        $this->assertSame($blog->id, $linked->parent_id);
    }

    public function test_navigation_requires_permission(): void
    {
        $this->actingAs(User::factory()->withRole('Blog Author')->create())
            ->get('/admin/navigation-menus')
            ->assertForbidden();
    }
}
