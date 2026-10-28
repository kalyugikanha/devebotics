<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Resources\AuditLogs\Pages\ViewAuditLog;
use App\Filament\Resources\SecurityLogs\Pages\ListSecurityLogs;
use App\Models\AuditLog;
use App\Models\Service;
use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Database\Seeders\PermissionSeeder;
use Illuminate\Auth\Events\Failed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_model_changes_are_audited_without_sensitive_values(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $this->actingAs($admin);

        $service = Service::factory()->create(['title' => 'Audit Me']);
        $service->update(['title' => 'Audited']);

        $entry = AuditLog::query()->where('event', 'updated')->where('auditable_type', 'service')->latest('id')->firstOrFail();
        $this->assertSame($admin->id, $entry->user_id);
        $this->assertSame('Audit Me', $entry->old_values['title']);
        $this->assertSame('Audited', $entry->new_values['title']);

        $admin->update(['password' => 'N3w-Secure-Passw0rd!']);
        $userEntry = AuditLog::query()->where('auditable_type', 'user')->where('event', 'updated')->latest('id')->first();
        $this->assertTrue($userEntry === null || ! array_key_exists('password', (array) $userEntry->new_values));
    }

    public function test_audit_log_is_read_only(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $entry = AuditLog::query()->create(['event' => 'test.event', 'description' => 'x', 'created_at' => now()]);

        $this->assertFalse(Route::has('filament.admin.resources.audit-logs.create'));
        $this->assertFalse(Route::has('filament.admin.resources.audit-logs.edit'));
        $this->assertFalse(Gate::forUser($superAdmin)->allows('update', $entry));
        $this->assertFalse(Gate::forUser($superAdmin)->allows('delete', $entry));
        $this->assertFalse(Gate::forUser($superAdmin)->allows('create', AuditLog::class));

        $this->actingAs($superAdmin);

        Livewire::test(ListAuditLogs::class)
            ->assertCanSeeTableRecords([$entry])
            ->assertTableActionDoesNotExist('edit')
            ->assertTableActionDoesNotExist('delete')
            ->assertTableBulkActionDoesNotExist('delete');

        Livewire::test(ViewAuditLog::class, ['record' => $entry->getRouteKey()])->assertOk();
    }

    public function test_security_events_are_separated_and_permission_scoped(): void
    {
        $general = AuditLog::query()->create(['event' => 'updated', 'description' => 'Content change', 'created_at' => now()]);
        $security = AuditLog::query()->create(['event' => 'auth.failed', 'description' => 'Failed sign-in', 'is_security_event' => true, 'created_at' => now()]);

        $auditor = User::factory()->withRole('Analyst (read-only)')->create();
        $auditor->givePermissionTo('audit_logs.view');

        $this->actingAs($auditor);
        $this->get('/admin/audit-logs')->assertOk();
        $this->get('/admin/security-activity')->assertForbidden();
        $this->get('/admin/audit-logs/'.$security->id)->assertNotFound();

        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $this->actingAs($admin);

        Livewire::test(ListSecurityLogs::class)
            ->assertCanSeeTableRecords([$security])
            ->assertCanNotSeeTableRecords([$general]);
    }

    public function test_failed_logins_are_recorded_with_masked_email(): void
    {
        User::factory()->withRole(PermissionRegistry::ADMIN)->create(['email' => 'owner@example.com']);

        event(new Failed('web', null, ['email' => 'owner@example.com', 'password' => 'wrong']));

        $entry = AuditLog::query()->where('event', 'auth.failed')->firstOrFail();
        $this->assertTrue($entry->is_security_event);
        $this->assertStringContainsString('o***@example.com', (string) $entry->description);
        $this->assertStringNotContainsString('wrong', json_encode($entry->toArray()));
    }

    public function test_users_without_audit_permissions_are_denied(): void
    {
        $user = User::factory()->withRole('Content Manager')->create();

        $this->actingAs($user)->get('/admin/audit-logs')->assertForbidden();
        $this->actingAs($user)->get('/admin/security-activity')->assertForbidden();
    }
}
