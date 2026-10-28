<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\SystemHealth;
use App\Models\FailedJob;
use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class SystemHealthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_system_health_is_super_admin_only(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create();
        $this->actingAs($admin)->get('/admin/system-health')->assertForbidden();

        $superAdmin = User::factory()->superAdmin()->create();
        $this->actingAs($superAdmin)->get('/admin/system-health')->assertOk()->assertSee('Scheduler heartbeat');
    }

    public function test_scheduler_heartbeat_and_failed_jobs_are_reported(): void
    {
        Cache::put('scheduler.heartbeat', now()->subMinute()->toIso8601String(), 600);

        DB::table('failed_jobs')->insert([
            'uuid' => 'b7d2b1a0-0000-4000-8000-000000000001',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['displayName' => 'App\\Jobs\\GenerateMediaVariants']),
            'exception' => "RuntimeException: Disk full\n#0 trace",
            'failed_at' => now(),
        ]);

        $this->actingAs(User::factory()->superAdmin()->create());

        $component = Livewire::test(SystemHealth::class)
            ->assertOk()
            ->assertSee('Running')
            ->assertSee('GenerateMediaVariants')
            ->assertSee('RuntimeException: Disk full');

        $this->assertTrue($component->instance()->schedulerHealthy());
    }

    public function test_failed_job_can_be_deleted_and_action_is_audited(): void
    {
        DB::table('failed_jobs')->insert([
            'uuid' => 'b7d2b1a0-0000-4000-8000-000000000002',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['displayName' => 'App\\Jobs\\Example']),
            'exception' => 'Exception: boom',
            'failed_at' => now(),
        ]);

        $this->actingAs(User::factory()->superAdmin()->create());

        $record = FailedJob::query()->firstOrFail();

        Livewire::test(SystemHealth::class)
            ->callTableAction('forget', $record)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('failed_jobs', ['uuid' => 'b7d2b1a0-0000-4000-8000-000000000002']);
        $this->assertDatabaseHas('audit_logs', ['event' => 'system.failed_job_deleted', 'is_security_event' => true]);
    }

    public function test_backup_action_queues_a_database_backup(): void
    {
        Artisan::spy();

        $this->actingAs(User::factory()->superAdmin()->create());

        Livewire::test(SystemHealth::class)->callAction('runBackup')->assertHasNoActionErrors();

        Artisan::shouldHaveReceived('queue')->with('backup:run', \Mockery::on(fn ($options) => ($options['--only-db'] ?? false) === true))->once();
        $this->assertDatabaseHas('audit_logs', ['event' => 'system.backup_queued']);
    }
}
