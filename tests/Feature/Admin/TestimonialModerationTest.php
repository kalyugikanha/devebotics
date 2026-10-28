<?php

namespace Tests\Feature\Admin;

use App\Enums\TestimonialStatus;
use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Resources\Testimonials\Pages\ViewTestimonial;
use App\Mail\TemplatedMail;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TestimonialModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_feedback_is_listed_first_and_is_not_public(): void
    {
        $moderator = User::factory()->withRole('Reviewer / Moderator')->create();
        $pending = Testimonial::factory()->create();
        $approved = Testimonial::factory()->approved()->create();

        $this->actingAs($moderator);

        Livewire::test(ListTestimonials::class)
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$approved]);

        $this->assertFalse(Testimonial::query()->publiclyVisible()->whereKey($pending->id)->exists());
    }

    public function test_moderator_approves_feedback_and_the_private_photo_is_imported(): void
    {
        Mail::fake();
        Storage::fake('local');
        Storage::fake('public');

        $path = UploadedFile::fake()->image('client.jpg', 400, 400)->store('testimonials', 'local');
        $moderator = User::factory()->withRole('Reviewer / Moderator')->create();
        $testimonial = Testimonial::factory()->create(['submitted_photo_path' => $path, 'display_identity_consent' => true]);

        $this->actingAs($moderator);

        Livewire::test(ViewTestimonial::class, ['record' => $testimonial->getRouteKey()])
            ->callAction('approve', data: ['notify_client' => true])
            ->assertHasNoActionErrors();

        $testimonial->refresh();
        $this->assertSame(TestimonialStatus::Approved, $testimonial->status);
        $this->assertSame($moderator->id, $testimonial->moderated_by);
        $this->assertNotNull($testimonial->photo_media_id);
        $this->assertNull($testimonial->submitted_photo_path);
        Storage::disk('local')->assertMissing($path);
        $this->assertTrue(Testimonial::query()->publiclyVisible()->whereKey($testimonial->id)->exists());

        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->templateKey === 'testimonial_approved');
    }

    public function test_feedback_without_publish_permission_cannot_be_approved(): void
    {
        $moderator = User::factory()->withRole('Reviewer / Moderator')->create();
        $testimonial = Testimonial::factory()->create(['publish_permission' => false, 'display_identity_consent' => false]);

        $this->actingAs($moderator);

        Livewire::test(ViewTestimonial::class, ['record' => $testimonial->getRouteKey()])
            ->callAction('approve');

        $this->assertSame(TestimonialStatus::Pending, $testimonial->refresh()->status);
    }

    public function test_private_reply_is_emailed_and_stored_but_never_public(): void
    {
        Mail::fake();
        $moderator = User::factory()->withRole('Reviewer / Moderator')->create();
        $testimonial = Testimonial::factory()->approved()->create();

        $this->actingAs($moderator);

        Livewire::test(ViewTestimonial::class, ['record' => $testimonial->getRouteKey()])
            ->callAction('replyPrivately', data: ['reply' => 'Thank you so much for the kind words!']);

        $testimonial->refresh();
        $this->assertSame('Thank you so much for the kind words!', $testimonial->admin_reply);
        $this->assertNotNull($testimonial->replied_at);
        $this->assertArrayNotHasKey('admin_reply', $testimonial->toArray());
        Mail::assertQueued(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->templateKey === 'testimonial_reply');
    }

    public function test_view_only_users_cannot_moderate_or_see_client_email(): void
    {
        $sales = User::factory()->withRole('Sales Manager')->create();
        $testimonial = Testimonial::factory()->create(['email' => 'private@client.test']);

        $this->actingAs($sales);

        Livewire::test(ViewTestimonial::class, ['record' => $testimonial->getRouteKey()])
            ->assertActionHidden('approve')
            ->assertActionHidden('replyPrivately')
            ->assertDontSee('private@client.test');

        $this->get(route('filament.admin.resources.testimonials.edit', $testimonial))->assertForbidden();
    }

    public function test_staff_created_testimonials_require_a_verified_source_and_start_pending(): void
    {
        $moderator = User::factory()->withRole('Reviewer / Moderator')->create();
        $this->actingAs($moderator);

        Livewire::test(CreateTestimonial::class)
            ->fillForm([
                'client_name' => 'Jordan Client',
                'rating' => 5,
                'feedback' => 'A wonderful team to work with on our platform rebuild.',
                'publish_permission' => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['moderation_note' => 'required']);

        Livewire::test(CreateTestimonial::class)
            ->fillForm([
                'client_name' => 'Jordan Client',
                'rating' => 5,
                'feedback' => 'A wonderful team to work with on our platform rebuild.',
                'publish_permission' => true,
                'moderation_note' => 'Email from client dated 3 March 2026 granting permission.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $testimonial = Testimonial::query()->where('client_name', 'Jordan Client')->firstOrFail();
        $this->assertSame('admin', $testimonial->source);
        $this->assertSame(TestimonialStatus::Pending, $testimonial->status);
    }
}
