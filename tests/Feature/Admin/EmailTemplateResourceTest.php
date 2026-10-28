<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\EmailTemplates\Pages\EditEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\ListEmailTemplates;
use App\Mail\TemplatedMail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Support\Mail\EmailTemplateRegistry;
use App\Support\Permissions\PermissionRegistry;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class EmailTemplateResourceTest extends TestCase
{
    use RefreshDatabase;

    private EmailTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        Mail::fake();

        $default = EmailTemplateRegistry::all()['inquiry_acknowledgement'];
        $this->template = EmailTemplate::query()->create([
            'key' => 'inquiry_acknowledgement',
            'name' => $default['name'],
            'subject' => $default['subject'],
            'heading' => $default['heading'],
            'body' => $default['body'],
            'action_label' => $default['action_label'],
            'is_active' => true,
        ]);
    }

    public function test_templates_can_be_edited_but_not_created_or_deleted(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        $this->assertFalse(Route::has('filament.admin.resources.email-templates.create'));

        Livewire::test(EditEmailTemplate::class, ['record' => $this->template->getRouteKey()])
            ->fillForm(['subject' => 'Thanks {{ name }} — ref {{ reference }}'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Thanks {{ name }} — ref {{ reference }}', $this->template->refresh()->subject);

        Livewire::test(ListEmailTemplates::class)->assertTableActionDoesNotExist('delete');
    }

    public function test_unknown_placeholders_are_rejected(): void
    {
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        Livewire::test(EditEmailTemplate::class, ['record' => $this->template->getRouteKey()])
            ->fillForm(['body' => 'Hello {{ password }}'])
            ->call('save')
            ->assertHasFormErrors(['body']);
    }

    public function test_send_test_queues_mail_to_the_current_user_and_logs_it(): void
    {
        $admin = User::factory()->withRole(PermissionRegistry::ADMIN)->create(['email' => 'admin@softec.example']);
        $this->actingAs($admin);

        Livewire::test(EditEmailTemplate::class, ['record' => $this->template->getRouteKey()])
            ->callAction('sendTest')
            ->assertHasNoActionErrors();

        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->hasTo('admin@softec.example') && $mail->templateKey === 'inquiry_acknowledgement');
        $this->assertSame(1, EmailLog::query()->where('template_key', 'inquiry_acknowledgement')->count());
    }

    public function test_restore_default_resets_content(): void
    {
        $this->template->update(['subject' => 'Changed']);
        $this->actingAs(User::factory()->withRole(PermissionRegistry::ADMIN)->create());

        Livewire::test(EditEmailTemplate::class, ['record' => $this->template->getRouteKey()])
            ->callAction('restoreDefault')
            ->assertHasNoActionErrors();

        $this->assertSame(EmailTemplateRegistry::all()['inquiry_acknowledgement']['subject'], $this->template->refresh()->subject);
    }
}
