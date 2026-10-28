<?php

namespace Tests\Feature\Admin;

use App\Enums\ContentStatus;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        $this->get('/admin/services')->assertRedirect('/admin/login');
    }

    public function test_content_manager_can_list_create_and_edit_services(): void
    {
        $user = User::factory()->withRole('Content Manager')->create();
        $existing = Service::factory()->create(['title' => 'Existing Service']);

        $this->actingAs($user);

        Livewire::test(ListServices::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$existing]);

        Livewire::test(CreateService::class)
            ->fillForm([
                'title' => 'Quantum Consulting',
                'slug' => 'quantum-consulting',
                'excerpt' => 'Advisory services for quantum-ready architectures.',
                'status' => ContentStatus::Published->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $service = Service::query()->where('slug', 'quantum-consulting')->firstOrFail();
        $this->assertSame(ContentStatus::Published, $service->status);
        $this->assertSame($user->id, $service->created_by);

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->assertOk()
            ->fillForm(['title' => 'Quantum Advisory'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Quantum Advisory', $service->refresh()->title);
    }

    public function test_illustration_accepts_only_whitelisted_names(): void
    {
        $user = User::factory()->withRole('Content Manager')->create();
        $service = Service::factory()->create();

        $this->actingAs($user);

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->fillForm(['illustration' => '../../layouts/app'])
            ->call('save')
            ->assertHasFormErrors(['illustration']);

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->fillForm(['illustration' => 'cloud'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('cloud', $service->refresh()->illustration);
    }

    public function test_seo_manager_can_edit_seo_but_not_content_fields(): void
    {
        $user = User::factory()->withRole('SEO Manager')->create();
        $service = Service::factory()->create(['title' => 'Original Title']);

        $this->actingAs($user);

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->assertOk()
            ->assertFormFieldDisabled('title')
            ->fillForm([
                'title' => 'Tampered Title',
                'seo.meta_title' => 'Better SEO title',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $service->refresh();
        $this->assertSame('Original Title', $service->title);
        $this->assertSame('Better SEO title', $service->seo?->meta_title);
    }

    public function test_users_without_service_permissions_are_forbidden(): void
    {
        $user = User::factory()->withRole('Support Agent')->create();

        $this->actingAs($user)->get('/admin/services')->assertForbidden();
    }

    public function test_changing_a_published_slug_creates_a_redirect(): void
    {
        $service = Service::factory()->create(['slug' => 'old-slug']);

        $service->update(['slug' => 'new-slug']);

        $this->assertDatabaseHas('redirects', ['from_path' => '/services/old-slug', 'to_url' => '/services/new-slug', 'status_code' => 301]);
    }
}
