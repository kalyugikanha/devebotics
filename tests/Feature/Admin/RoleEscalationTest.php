<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use App\Support\Permissions\EscalationGuard;
use App\Support\Permissions\PermissionRegistry;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleEscalationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        Mail::fake();
    }

    public function test_assignable_roles_exclude_super_admin_and_roles_with_permissions_the_actor_lacks(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();

        $assignable = app(EscalationGuard::class)->assignableRoles($admin)->pluck('name');

        $this->assertNotContains(PermissionRegistry::SUPER_ADMIN, $assignable);
        $this->assertContains('Content Manager', $assignable);
        $this->assertContains(PermissionRegistry::ADMIN, $assignable);
    }

    public function test_tampered_payload_cannot_assign_super_admin_on_create(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $superAdminRole = Role::findByName(PermissionRegistry::SUPER_ADMIN, 'web');

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Mallory', 'email' => 'mallory@example.com'])
            ->set('data.role_ids', [$superAdminRole->id])
            ->call('create')
            ->assertHasFormErrors(['role_ids.0']);

        $this->assertDatabaseMissing('users', ['email' => 'mallory@example.com']);
    }

    public function test_tampered_payload_cannot_assign_a_role_with_permissions_the_actor_lacks(): void
    {
        $salesManager = User::factory()->withRole('Sales Manager')->create();
        $salesManager->givePermissionTo(['users.view', 'users.update', 'roles.manage']);
        $target = User::factory()->withRole('Support Agent')->create();
        $contentManager = Role::findByName('Content Manager', 'web');

        $this->expectException(ValidationException::class);

        app(EscalationGuard::class)->resolveRoleIds($salesManager, $target, [$contentManager->id]);
    }

    public function test_guard_rejects_super_admin_role_even_when_form_validation_is_bypassed(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $superAdminRole = Role::findByName(PermissionRegistry::SUPER_ADMIN, 'web');

        try {
            app(EscalationGuard::class)->resolveRoleIds($admin, null, [$superAdminRole->id], 'data.role_ids');
            $this->fail('Expected a validation exception.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('data.role_ids', $exception->errors());
        }
    }

    public function test_roles_outside_the_actors_reach_are_preserved_when_editing(): void
    {
        $actor = User::factory()->withRole('HR Manager')->create();
        $actor->givePermissionTo(['users.view', 'users.update', 'roles.manage']);
        $target = User::factory()->withRole('Content Manager')->create();
        $hrRole = Role::findByName('HR Manager', 'web');

        $ids = app(EscalationGuard::class)->resolveRoleIds($actor, $target, [$hrRole->id]);

        $this->assertContains(Role::findByName('Content Manager', 'web')->id, $ids);
        $this->assertContains($hrRole->id, $ids);
    }

    public function test_edit_user_rejects_tampered_super_admin_role_for_administrators(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $target = User::factory()->withRole('Blog Author')->create();
        $superAdminRole = Role::findByName(PermissionRegistry::SUPER_ADMIN, 'web');

        $this->actingAs($admin);

        Livewire::test(EditUser::class, ['record' => $target->getRouteKey()])
            ->set('data.role_ids', [$superAdminRole->id])
            ->call('save')
            ->assertHasFormErrors(['role_ids.0']);

        $this->assertFalse($target->refresh()->hasRole(PermissionRegistry::SUPER_ADMIN));
    }

    public function test_role_editor_cannot_grant_permissions_the_actor_does_not_hold(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();

        $this->actingAs($admin);

        Livewire::test(CreateRole::class)
            ->fillForm(['name' => 'Ops'])
            ->set('data.'.RoleForm::fieldFor('Settings'), ['system.manage', 'settings.manage'])
            ->call('create')
            ->assertHasFormErrors();

        $this->assertDatabaseMissing('roles', ['name' => 'Ops']);
    }

    public function test_role_editor_grants_held_permissions_and_logs_the_change(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();

        $this->actingAs($admin);

        Livewire::test(CreateRole::class)
            ->fillForm([
                'name' => 'Marketing',
                RoleForm::fieldFor('Website content') => ['pages.view', 'pages.manage'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $role = Role::findByName('Marketing', 'web');
        $this->assertEqualsCanonicalizing(['pages.view', 'pages.manage'], $role->permissions->pluck('name')->all());
        $this->assertDatabaseHas('audit_logs', ['event' => 'role.permissions_changed', 'is_security_event' => true]);
    }

    public function test_permissions_the_actor_cannot_grant_keep_their_state_on_edit(): void
    {
        $role = Role::create(['name' => 'Ops', 'guard_name' => 'web']);
        $role->syncPermissions(['system.manage', 'pages.view']);
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();

        $this->actingAs($admin);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->set('data.'.RoleForm::fieldFor('Settings'), [])
            ->set('data.'.RoleForm::fieldFor('Website content'), ['pages.view', 'pages.manage'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEqualsCanonicalizing(['system.manage', 'pages.view', 'pages.manage'], $role->refresh()->permissions->pluck('name')->all());
    }

    public function test_super_admin_role_is_locked_and_built_in_roles_cannot_be_deleted(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $superRole = Role::findByName(PermissionRegistry::SUPER_ADMIN, 'web');
        $adminRole = Role::findByName(PermissionRegistry::ADMIN, 'web');

        $this->actingAs($superAdmin)->get('/admin/roles/'.$superRole->id.'/edit')->assertForbidden();
        $this->assertFalse($superAdmin->can('delete', $adminRole));
        $this->assertFalse($superAdmin->can('delete', $superRole));
    }

    public function test_roles_page_requires_roles_manage(): void
    {
        $user = User::factory()->withRole('Content Manager')->create();

        $this->actingAs($user)->get('/admin/roles')->assertForbidden();
    }
}
