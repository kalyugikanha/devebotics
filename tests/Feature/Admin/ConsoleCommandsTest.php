<?php

namespace Tests\Feature\Admin;

use App\Enums\InquiryStatus;
use App\Models\AuditLog;
use App\Models\ContactInquiry;
use App\Models\EmailLog;
use App\Models\JobApplication;
use App\Models\Service;
use App\Models\Statistic;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConsoleCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_purge_demo_removes_only_demo_records(): void
    {
        $demoService = Service::factory()->create(['is_demo' => true]);
        $realService = Service::factory()->create(['is_demo' => false]);
        $demoStat = Statistic::factory()->create(['is_demo' => true]);
        $realStat = Statistic::factory()->create();
        $demoReview = Testimonial::factory()->approved()->create(['is_demo' => true]);

        $this->artisan('softec:purge-demo', ['--force' => true])->assertSuccessful();

        $this->assertDatabaseMissing('services', ['id' => $demoService->id]);
        $this->assertDatabaseHas('services', ['id' => $realService->id]);
        $this->assertDatabaseMissing('statistics', ['id' => $demoStat->id]);
        $this->assertDatabaseHas('statistics', ['id' => $realStat->id]);
        $this->assertDatabaseMissing('testimonials', ['id' => $demoReview->id]);
        $this->assertDatabaseHas('audit_logs', ['event' => 'system.demo_purged']);
    }

    public function test_purge_demo_asks_for_confirmation(): void
    {
        Service::factory()->create(['is_demo' => true]);

        $this->artisan('softec:purge-demo')
            ->expectsConfirmation('Permanently delete these demo records?', 'no')
            ->assertFailed();

        $this->assertSame(1, Service::query()->where('is_demo', true)->count());
    }

    public function test_prune_data_enforces_retention_and_removes_private_files(): void
    {
        Storage::fake('local');
        config(['softec.uploads.private_disk' => 'local']);
        settings()->setMany('privacy', ['inquiry_retention_days' => 90, 'application_retention_days' => 90]);

        Storage::disk('local')->put('inquiries/old.pdf', 'x');
        Storage::disk('local')->put('resumes/old.pdf', 'x');

        $oldClosed = ContactInquiry::factory()->create(['status' => InquiryStatus::Closed, 'attachment_path' => 'inquiries/old.pdf']);
        $oldClosed->forceFill(['closed_at' => now()->subDays(120), 'created_at' => now()->subDays(130)])->saveQuietly();
        $oldOpen = ContactInquiry::factory()->create(['status' => InquiryStatus::Qualified]);
        $oldOpen->forceFill(['created_at' => now()->subDays(400)])->saveQuietly();
        $recentClosed = ContactInquiry::factory()->create(['status' => InquiryStatus::Closed]);

        $oldApplication = JobApplication::factory()->create(['resume_path' => 'resumes/old.pdf']);
        $oldApplication->forceFill(['created_at' => now()->subDays(100)])->saveQuietly();
        $recentApplication = JobApplication::factory()->create();

        AuditLog::query()->create(['event' => 'updated', 'description' => 'old', 'created_at' => now()->subDays(400)]);
        AuditLog::query()->create(['event' => 'updated', 'description' => 'recent', 'created_at' => now()->subDays(10)]);
        EmailLog::query()->create(['mailable' => 'TemplatedMail', 'recipient_masked' => 'a***@x.com', 'status' => 'sent'])->forceFill(['created_at' => now()->subDays(400)])->saveQuietly();

        $this->artisan('softec:prune-data', ['--dry-run' => true])->assertSuccessful();
        $this->assertDatabaseHas('contact_inquiries', ['id' => $oldClosed->id]);

        $this->artisan('softec:prune-data')->assertSuccessful();

        $this->assertDatabaseMissing('contact_inquiries', ['id' => $oldClosed->id]);
        $this->assertDatabaseHas('contact_inquiries', ['id' => $oldOpen->id]);
        $this->assertDatabaseHas('contact_inquiries', ['id' => $recentClosed->id]);
        $this->assertDatabaseMissing('job_applications', ['id' => $oldApplication->id]);
        $this->assertDatabaseHas('job_applications', ['id' => $recentApplication->id]);
        Storage::disk('local')->assertMissing('inquiries/old.pdf');
        Storage::disk('local')->assertMissing('resumes/old.pdf');
        $this->assertDatabaseMissing('audit_logs', ['description' => 'old']);
        $this->assertDatabaseHas('audit_logs', ['description' => 'recent']);
        $this->assertSame(0, EmailLog::query()->count());
    }

    public function test_scheduler_registers_maintenance_tasks(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('softec:publish-scheduled')
            ->expectsOutputToContain('scheduler-heartbeat')
            ->expectsOutputToContain('softec:prune-data')
            ->assertSuccessful();
    }
}
