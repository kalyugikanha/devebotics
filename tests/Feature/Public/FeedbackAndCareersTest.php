<?php

namespace Tests\Feature\Public;

use App\Enums\TestimonialStatus;
use App\Models\ConsentRecord;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\SubmitsPublicForms;
use Tests\TestCase;

class FeedbackAndCareersTest extends TestCase
{
    use RefreshDatabase, SubmitsPublicForms;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
    }

    /**
     * @return array<string, mixed>
     */
    private function feedback(array $overrides = []): array
    {
        return $this->withSpamProtection(array_merge([
            'client_name' => 'Grace Hopper',
            'email' => 'grace@example.test',
            'company_name' => 'Navy Systems',
            'rating' => 5,
            'rating_quality' => 4,
            'feedback' => 'Outstanding collaboration from discovery to launch. Highly recommended.',
            'publish_permission' => '1',
            'display_identity_consent' => '1',
            'privacy_consent' => '1',
        ], $overrides));
    }

    public function test_feedback_is_stored_as_pending_and_hidden_until_approved(): void
    {
        $this->post('/feedback', $this->feedback())->assertRedirect(route('feedback.create'))->assertSessionHas('feedback_submitted');

        $testimonial = Testimonial::query()->sole();
        $this->assertSame(TestimonialStatus::Pending, $testimonial->status);
        $this->assertSame('grace@example.test', $testimonial->email);
        $this->assertNotSame('grace@example.test', $testimonial->getRawOriginal('email'), 'Email must be encrypted at rest.');
        $this->assertSame(3, ConsentRecord::query()->where('consentable_type', 'testimonial')->count());

        $this->get('/testimonials')->assertDontSee('Outstanding collaboration');

        $testimonial->update(['status' => TestimonialStatus::Approved]);
        $this->get('/testimonials')->assertSee('Outstanding collaboration')->assertSee('Grace Hopper')->assertDontSee('grace@example.test');
    }

    public function test_feedback_without_publish_permission_never_appears(): void
    {
        $this->post('/feedback', $this->feedback(['publish_permission' => null, 'display_identity_consent' => null]));
        Testimonial::query()->sole()->update(['status' => TestimonialStatus::Approved]);

        $this->get('/testimonials')->assertDontSee('Outstanding collaboration');
    }

    public function test_identity_is_hidden_without_display_consent(): void
    {
        $this->post('/feedback', $this->feedback(['display_identity_consent' => null]));
        Testimonial::query()->sole()->update(['status' => TestimonialStatus::Approved]);

        $this->get('/testimonials')->assertSee('Outstanding collaboration')->assertSee('Verified client')->assertDontSee('Grace Hopper')->assertDontSee('Navy Systems');
    }

    public function test_feedback_rating_must_be_between_one_and_five(): void
    {
        $this->post('/feedback', $this->feedback(['rating' => 6]))->assertSessionHasErrors('rating');
        $this->post('/feedback', $this->feedback(['rating' => null]))->assertSessionHasErrors('rating');
    }

    public function test_job_application_stores_resume_privately_and_notifies(): void
    {
        $job = JobOpening::factory()->create(['slug' => 'backend-engineer']);
        $resume = UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf');

        $this->post(route('careers.apply', $job->slug), $this->withSpamProtection([
            'name' => 'Linus',
            'email' => 'linus@example.test',
            'resume' => $resume,
            'privacy_consent' => '1',
        ]))->assertRedirect($job->publicUrl().'#apply')->assertSessionHas('application_reference');

        $application = JobApplication::query()->sole();
        Storage::disk('local')->assertExists($application->resume_path);
        $this->assertStringStartsWith('JOB-', $application->reference);

        $this->actingAs(User::factory()->withRole('Sales Manager')->create())->get(route('files.resume', $application))->assertForbidden();
        $this->actingAs(User::factory()->withRole('HR Manager')->create())->get(route('files.resume', $application))->assertOk();
    }

    public function test_closed_jobs_and_invalid_resumes_are_rejected(): void
    {
        $closed = JobOpening::factory()->closed()->create(['slug' => 'closed']);

        $this->post(route('careers.apply', $closed->slug), $this->withSpamProtection([
            'name' => 'Late', 'email' => 'late@example.test', 'resume' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf'), 'privacy_consent' => '1',
        ]))->assertStatus(410);

        $open = JobOpening::factory()->create(['slug' => 'open']);
        $this->post(route('careers.apply', $open->slug), $this->withSpamProtection([
            'name' => 'Bad', 'email' => 'bad@example.test', 'resume' => UploadedFile::fake()->create('cv.exe', 10, 'application/octet-stream'), 'privacy_consent' => '1',
        ]))->assertSessionHasErrors('resume');

        $this->assertDatabaseCount('job_applications', 0);
    }
}
