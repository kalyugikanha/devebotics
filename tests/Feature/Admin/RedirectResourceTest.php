<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Redirects\Pages\CreateRedirect;
use App\Filament\Resources\Redirects\Pages\EditRedirect;
use App\Models\Redirect;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RedirectResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->actingAs(User::factory()->withRole('SEO Manager')->create());
    }

    public function test_seo_manager_can_create_a_redirect(): void
    {
        Livewire::test(CreateRedirect::class)
            ->fillForm(['from_path' => '/old-services/', 'to_url' => '/services', 'status_code' => 301])
            ->call('create')
            ->assertHasNoFormErrors();

        $redirect = Redirect::query()->where('from_path', '/old-services')->firstOrFail();
        $this->assertSame('/services', $redirect->to_url);
        $this->assertFalse($redirect->is_automatic);

        $this->get('/old-services?ref=x')->assertRedirect(url('/services').'?ref=x');
    }

    public function test_self_redirects_and_loops_are_rejected(): void
    {
        Livewire::test(CreateRedirect::class)
            ->fillForm(['from_path' => '/a', 'to_url' => '/a', 'status_code' => 301])
            ->call('create')
            ->assertHasFormErrors(['to_url']);

        Redirect::query()->create(['from_path' => '/b', 'to_url' => '/c', 'status_code' => 301]);
        Redirect::query()->create(['from_path' => '/c', 'to_url' => '/a', 'status_code' => 301]);

        Livewire::test(CreateRedirect::class)
            ->fillForm(['from_path' => '/a', 'to_url' => '/b', 'status_code' => 301])
            ->call('create')
            ->assertHasFormErrors(['to_url']);
    }

    public function test_external_destinations_must_use_https(): void
    {
        Livewire::test(CreateRedirect::class)
            ->fillForm(['from_path' => '/x', 'to_url' => 'http://insecure.example', 'status_code' => 302])
            ->call('create')
            ->assertHasFormErrors(['to_url']);

        Livewire::test(CreateRedirect::class)
            ->fillForm(['from_path' => '/x', 'to_url' => 'javascript:alert(1)', 'status_code' => 302])
            ->call('create')
            ->assertHasFormErrors(['to_url']);
    }

    public function test_gone_redirects_return_410_without_destination(): void
    {
        Livewire::test(CreateRedirect::class)
            ->fillForm(['from_path' => '/retired-product', 'status_code' => 410])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertNull(Redirect::query()->where('from_path', '/retired-product')->value('to_url'));
        $this->get('/retired-product')->assertStatus(410);
    }

    public function test_editing_keeps_loop_protection(): void
    {
        $first = Redirect::query()->create(['from_path' => '/one', 'to_url' => '/two', 'status_code' => 301]);
        Redirect::query()->create(['from_path' => '/two', 'to_url' => '/three', 'status_code' => 301]);

        Livewire::test(EditRedirect::class, ['record' => $first->getRouteKey()])
            ->assertOk()
            ->fillForm(['to_url' => '/two'])
            ->call('save')
            ->assertHasNoFormErrors();

        $third = Redirect::query()->create(['from_path' => '/three', 'to_url' => '/four', 'status_code' => 301]);

        Livewire::test(EditRedirect::class, ['record' => $third->getRouteKey()])
            ->fillForm(['to_url' => '/one'])
            ->call('save')
            ->assertHasFormErrors(['to_url']);
    }

    public function test_redirects_require_permission(): void
    {
        $this->actingAs(User::factory()->withRole('Blog Author')->create())
            ->get('/admin/redirects')
            ->assertForbidden();
    }
}
