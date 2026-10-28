<?php

namespace Tests\Feature\Admin;

use App\Enums\InquiryActivityType;
use App\Enums\InquiryStatus;
use App\Filament\Resources\ContactInquiries\Pages\ListContactInquiries;
use App\Filament\Resources\ContactInquiries\Pages\ViewContactInquiry;
use App\Models\AuditLog;
use App\Models\ContactInquiry;
use App\Models\User;
use App\Support\Csv\CsvExport;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InquiryCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_agent_only_sees_inquiries_assigned_to_them(): void
    {
        $agent = User::factory()->withRole('Support Agent')->create();
        $otherAgent = User::factory()->withRole('Support Agent')->create();

        $mine = ContactInquiry::factory()->create(['assigned_to' => $agent->id]);
        $theirs = ContactInquiry::factory()->create(['assigned_to' => $otherAgent->id]);
        $unassigned = ContactInquiry::factory()->create();

        $this->actingAs($agent);

        Livewire::test(ListContactInquiries::class)
            ->set('activeTab', 'all')
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$theirs, $unassigned]);

        $this->get(route('filament.admin.resources.contact-inquiries.view', $mine))->assertOk();
        $this->get(route('filament.admin.resources.contact-inquiries.view', $theirs))->assertNotFound();
        $this->get(route('files.inquiry-attachment', $theirs))->assertForbidden();
    }

    public function test_sales_manager_assigns_an_inquiry_and_the_assignee_is_notified(): void
    {
        $manager = User::factory()->withRole('Sales Manager')->create();
        $agent = User::factory()->withRole('Support Agent')->create();
        $inquiry = ContactInquiry::factory()->create();

        $this->actingAs($manager);

        Livewire::test(ViewContactInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->callAction('assign', data: ['assigned_to' => $agent->id])
            ->assertHasNoActionErrors();

        $this->assertSame($agent->id, $inquiry->refresh()->assigned_to);
        $this->assertDatabaseHas('inquiry_activities', ['contact_inquiry_id' => $inquiry->id, 'type' => InquiryActivityType::Assigned->value, 'user_id' => $manager->id]);
        $this->assertSame(1, $agent->notifications()->count());
    }

    public function test_status_note_and_follow_up_are_recorded_on_the_timeline(): void
    {
        $manager = User::factory()->withRole('Sales Manager')->create();
        $inquiry = ContactInquiry::factory()->create();

        $this->actingAs($manager);

        Livewire::test(ViewContactInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->callAction('changeStatus', data: ['status' => InquiryStatus::Qualified->value, 'note' => 'Budget confirmed'])
            ->callAction('addNote', data: ['note' => 'Called the client'])
            ->callAction(TestAction::make('scheduleFollowUp'), data: ['follow_up_at' => now()->addDays(3)->format('Y-m-d H:i:s')]);

        $inquiry->refresh();
        $this->assertSame(InquiryStatus::Qualified, $inquiry->status);
        $this->assertNotNull($inquiry->follow_up_at);
        $this->assertSame(3, $inquiry->activities()->count());
    }

    public function test_support_agent_cannot_assign_or_export(): void
    {
        $agent = User::factory()->withRole('Support Agent')->create();
        $inquiry = ContactInquiry::factory()->create(['assigned_to' => $agent->id]);

        $this->actingAs($agent);

        Livewire::test(ViewContactInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->assertActionHidden('assign');

        Livewire::test(ListContactInquiries::class)
            ->assertActionHidden('exportCsv');
    }

    public function test_csv_export_is_authorised_audited_and_escapes_formulas(): void
    {
        $manager = User::factory()->withRole('Sales Manager')->create();
        ContactInquiry::factory()->create(['name' => '=HYPERLINK("http://evil.test","x")', 'company' => '+cmd']);

        $this->actingAs($manager);

        $component = Livewire::test(ListContactInquiries::class)
            ->set('activeTab', 'all')
            ->callAction('exportCsv')
            ->assertFileDownloaded();

        $csv = base64_decode((string) data_get($component->effects, 'download.content'));

        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringContainsString("'+cmd", $csv);
        $this->assertTrue(AuditLog::query()->where('event', 'inquiries.exported')->exists());
    }

    public function test_csv_cell_escaping(): void
    {
        $this->assertSame("'=1+1", CsvExport::escapeCell('=1+1'));
        $this->assertSame("'-5", CsvExport::escapeCell('-5'));
        $this->assertSame("'@SUM(A1)", CsvExport::escapeCell('@SUM(A1)'));
        $this->assertSame('Hello', CsvExport::escapeCell('Hello'));
        $this->assertSame('new', CsvExport::escapeCell(InquiryStatus::New));
    }

    public function test_inquiries_cannot_be_created_from_the_admin(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get('/admin/contact-inquiries/create')->assertNotFound();
    }
}
