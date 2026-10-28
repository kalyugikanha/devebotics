<?php

namespace Tests\Feature\Public;

use App\Enums\InquiryStatus;
use App\Enums\InquiryTopic;
use App\Mail\TemplatedMail;
use App\Models\ConsentRecord;
use App\Models\ContactInquiry;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\CoreContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\SubmitsPublicForms;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase, SubmitsPublicForms;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');
        Mail::fake();
        $this->seed(CoreContentSeeder::class);
        settings()->setMany('forms', ['inquiry_recipients' => ['sales@example.test']]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validQuick(array $overrides = []): array
    {
        return $this->withSpamProtection(array_merge([
            'type' => 'quick',
            'topic' => 'sales',
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.test',
            'subject' => 'Website rebuild',
            'message' => 'We would like to rebuild our customer portal with better performance.',
            'privacy_consent' => '1',
        ], $overrides));
    }

    public function test_valid_inquiry_is_stored_with_reference_activity_consent_and_notifications(): void
    {
        $sales = User::factory()->withRole('Sales Manager')->create();

        $response = $this->post('/contact', $this->validQuick(['service_id' => Service::query()->value('id')]));

        $inquiry = ContactInquiry::query()->sole();
        $response->assertRedirect(route('contact.create'))->assertSessionHas('inquiry_reference', $inquiry->reference);

        $this->assertMatchesRegularExpression('/^SAI-\d{4}-[2-9A-HJ-NP-Z]{6}$/', $inquiry->reference);
        $this->assertSame(InquiryStatus::New, $inquiry->status);
        $this->assertNotNull($inquiry->ip_hash);
        $this->assertNotSame('127.0.0.1', $inquiry->ip_hash);
        $this->assertSame(1, $inquiry->activities()->count());
        $this->assertSame(1, ConsentRecord::query()->where('consentable_id', $inquiry->id)->where('consentable_type', 'contact_inquiry')->count());

        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->templateKey === 'inquiry_notification' && $mail->hasTo('sales@example.test'));
        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->templateKey === 'inquiry_acknowledgement' && $mail->hasTo('ada@example.test'));
        $this->assertSame(1, $sales->notifications()->count());
        $this->assertDatabaseCount('email_logs', 2);
    }

    public function test_validation_errors_are_returned(): void
    {
        $this->post('/contact', $this->withSpamProtection(['type' => 'quick', 'email' => 'not-an-email', 'message' => 'short']))
            ->assertSessionHasErrors(['topic', 'name', 'email', 'message', 'privacy_consent']);

        $this->assertDatabaseCount('contact_inquiries', 0);
    }

    public function test_topic_must_be_a_known_value_and_project_briefs_are_always_sales(): void
    {
        $this->post('/contact', $this->validQuick(['topic' => 'hacking']))->assertSessionHasErrors('topic');
        $this->assertDatabaseCount('contact_inquiries', 0);

        $this->post('/contact', $this->validQuick(['topic' => 'support']))->assertSessionHasNoErrors();
        $this->assertSame(InquiryTopic::Support, ContactInquiry::query()->sole()->topic);

        $this->post('/contact', $this->withSpamProtection([
            'type' => 'project',
            'topic' => 'careers',
            'name' => 'Grace Hopper',
            'email' => 'grace@example.test',
            'subject' => 'Compiler platform',
            'message' => 'We need a team to modernise our internal compiler tooling and pipelines.',
            'privacy_consent' => '1',
        ]))->assertSessionHasNoErrors();

        $this->assertSame(InquiryTopic::Sales, ContactInquiry::query()->where('email', 'grace@example.test')->sole()->topic);
    }

    public function test_honeypot_and_instant_submissions_are_rejected(): void
    {
        $this->post('/contact', $this->validQuick([config('softec.spam.honeypot_field') => 'http://spam.example']))->assertSessionHasErrors('form');
        $this->post('/contact', $this->withSpamProtection($this->validQuick(['_form_token' => null]), 0))->assertSessionHasErrors('form');
        $this->post('/contact', array_merge($this->validQuick(), ['_form_token' => 'tampered']))->assertSessionHasErrors('form');

        $this->assertDatabaseCount('contact_inquiries', 0);
    }

    public function test_duplicate_submissions_are_collapsed(): void
    {
        $this->post('/contact', $this->validQuick());
        $this->post('/contact', $this->validQuick());

        $this->assertDatabaseCount('contact_inquiries', 1);
        Mail::assertQueuedCount(2);
    }

    public function test_project_inquiry_requires_subject_and_stores_attachment_privately(): void
    {
        $this->post('/contact', $this->validQuick(['type' => 'project', 'subject' => '']))->assertSessionHasErrors('subject');

        $file = UploadedFile::fake()->create('brief.pdf', 120, 'application/pdf');
        $this->post('/contact', $this->validQuick(['type' => 'project', 'budget_range' => '$10k – $25k', 'attachment' => $file]))->assertSessionHasNoErrors();

        $inquiry = ContactInquiry::query()->sole();
        $this->assertSame('brief.pdf', $inquiry->attachment_name);
        Storage::disk('local')->assertExists($inquiry->attachment_path);
        Storage::disk('public')->assertMissing($inquiry->attachment_path);
    }

    public function test_dangerous_attachments_and_unknown_budget_values_are_rejected(): void
    {
        $this->post('/contact', $this->validQuick(['type' => 'project', 'attachment' => UploadedFile::fake()->create('shell.php', 1, 'application/x-php')]))->assertSessionHasErrors('attachment');
        $this->post('/contact', $this->validQuick(['type' => 'project', 'budget_range' => 'one million dollars']))->assertSessionHasErrors('budget_range');
        $this->post('/contact', $this->validQuick(['attachment' => UploadedFile::fake()->create('brief.pdf', 10, 'application/pdf')]))->assertSessionHasErrors('attachment');
    }

    public function test_attachment_download_is_authorised(): void
    {
        $this->post('/contact', $this->validQuick(['type' => 'project', 'attachment' => UploadedFile::fake()->create('brief.pdf', 12, 'application/pdf')]));
        $inquiry = ContactInquiry::query()->sole();

        $this->get(route('files.inquiry-attachment', $inquiry))->assertRedirect(route('filament.admin.auth.login'));
        $this->actingAs(User::factory()->withRole('Support Agent')->create())->get(route('files.inquiry-attachment', $inquiry))->assertForbidden();
        $this->actingAs(User::factory()->withRole('Sales Manager')->create())->get(route('files.inquiry-attachment', $inquiry))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertDatabaseHas('audit_logs', ['event' => 'inquiry.attachment_downloaded']);
    }

    public function test_public_form_is_rate_limited(): void
    {
        foreach (range(1, 5) as $i) {
            $this->post('/contact', $this->validQuick(['email' => "person{$i}@example.test"]))->assertRedirect();
        }

        $this->post('/contact', $this->validQuick(['email' => 'person6@example.test']))->assertStatus(429);
    }
}
