<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Mail\TemplatedMail;
use App\Models\AuditLog;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Support\Permissions\PermissionRegistry;
use Database\Seeders\PermissionSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        Mail::fake();
    }

    public function test_administrator_can_invite_a_user_and_an_invitation_email_is_queued(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $role = Role::findByName('Content Manager', 'web');

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Priya Sharma',
                'email' => 'priya@example.com',
                'job_title' => 'Editor',
                'role_ids' => [$role->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::query()->where('email', 'priya@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('Content Manager'));
        $this->assertNotNull($user->invited_at);
        $this->assertTrue($user->is_active);

        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail): bool => $mail->templateKey === 'user_invitation'
            && $mail->hasTo('priya@example.com')
            && str_contains((string) $mail->actionUrl, '/admin/password-reset/reset'));

        $this->assertDatabaseHas('audit_logs', ['event' => 'user.roles_changed', 'auditable_id' => $user->id, 'is_security_event' => true]);
    }

    public function test_the_last_active_super_admin_cannot_be_deleted_or_deactivated(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();

        $this->assertTrue(UserPolicy::isLastActiveSuperAdmin($superAdmin));
        $this->assertFalse(Gate::forUser($superAdmin)->allows('delete', $superAdmin));
        $this->assertFalse(Gate::forUser($admin)->allows('delete', $superAdmin));
        $this->assertFalse(Gate::forUser($superAdmin)->allows('toggleActive', $superAdmin));

        $this->actingAs($superAdmin);

        Livewire::test(ListUsers::class)
            ->assertActionHidden(TestAction::make('deactivate')->table($superAdmin))
            ->assertActionHidden(TestAction::make('delete')->table($superAdmin));

        // With a second super admin, the first one can be deactivated by the other.
        $second = User::factory()->superAdmin()->create();
        $this->assertTrue(Gate::forUser($second)->allows('toggleActive', $superAdmin));
    }

    public function test_deactivation_signs_the_user_out_of_every_session(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $editor = User::factory()->withRole('Blog Editor')->create();

        DB::table('sessions')->insert([
            'id' => 'session-1', 'user_id' => $editor->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'test', 'payload' => 'x', 'last_activity' => time(),
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('deactivate')->table($editor))
            ->assertHasNoActionErrors();

        $this->assertFalse($editor->refresh()->is_active);
        $this->assertNotNull($editor->deactivated_at);
        $this->assertDatabaseMissing('sessions', ['user_id' => $editor->id]);
        $this->assertTrue(AuditLog::query()->where('event', 'user.deactivated')->where('auditable_id', $editor->id)->exists());
    }

    public function test_users_cannot_change_their_own_roles(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $superAdminRole = Role::findByName(PermissionRegistry::SUPER_ADMIN, 'web');

        $this->assertFalse(Gate::forUser($admin)->allows('assignRoles', $admin));

        $this->actingAs($admin);

        Livewire::test(EditUser::class, ['record' => $admin->getRouteKey()])
            ->assertFormFieldHidden('role_ids')
            ->set('data.role_ids', [$superAdminRole->id])
            ->call('save');

        $this->assertFalse($admin->refresh()->hasRole(PermissionRegistry::SUPER_ADMIN));
        $this->assertTrue($admin->hasRole(PermissionRegistry::ADMIN));
    }

    public function test_inactive_users_cannot_access_the_panel(): void
    {
        $user = User::factory()->withRole(PermissionRegistry::ADMIN)->inactive()->create();

        $this->actingAs($user)->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_users_without_permission_cannot_open_user_management(): void
    {
        $user = User::factory()->withRole('Content Manager')->create();

        $this->actingAs($user)->get('/admin/users')->assertForbidden();
        $this->actingAs($user)->get('/admin/users/create')->assertForbidden();
    }

    public function test_only_super_admins_can_reset_another_users_two_factor_authentication(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $target = User::factory()->withRole('Blog Author')->create(['has_email_authentication' => true]);

        $this->actingAs($admin);
        Livewire::test(ListUsers::class)->assertActionHidden(TestAction::make('resetMfa')->table($target));

        $this->actingAs($superAdmin);
        Livewire::test(ListUsers::class)->callAction(TestAction::make('resetMfa')->table($target));

        $this->assertFalse($target->refresh()->hasMultiFactorAuthentication());
    }
}
