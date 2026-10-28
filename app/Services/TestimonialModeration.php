<?php

namespace App\Services;

use App\Enums\TestimonialStatus;
use App\Mail\TemplatedMail;
use App\Models\Testimonial;
use App\Models\User;
use App\Support\Mail\Outbox;
use App\Support\Media\MediaLibrary;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Moderation transitions for client feedback. A review can only be approved
 * (and therefore shown publicly) when the client granted permission to
 * publish it; their name/company/photo are shown only with identity consent.
 */
class TestimonialModeration
{
    public function __construct(private Outbox $outbox, private MediaLibrary $media) {}

    public function approve(User $actor, Testimonial $testimonial, bool $notifyClient = true): void
    {
        Gate::forUser($actor)->authorize('moderate', $testimonial);

        if (! $testimonial->publish_permission) {
            throw ValidationException::withMessages([
                'status' => 'This client did not give permission to publish their feedback, so it cannot be approved for the website.',
            ]);
        }

        if ($testimonial->submitted_photo_path && $testimonial->display_identity_consent && $testimonial->photo_media_id === null) {
            $this->importPhoto($testimonial);
        }

        $testimonial->forceFill([
            'status' => TestimonialStatus::Approved,
            'moderated_by' => $actor->getKey(),
            'moderated_at' => now(),
        ])->save();

        if ($notifyClient && filled($testimonial->email)) {
            $this->outbox->send($testimonial->email, new TemplatedMail('testimonial_approved', [
                'client_name' => $testimonial->client_name,
            ], route('testimonials.index')), $testimonial);
        }
    }

    public function reject(User $actor, Testimonial $testimonial, ?string $note = null): void
    {
        $this->setStatus($actor, $testimonial, TestimonialStatus::Rejected, $note);
    }

    public function archive(User $actor, Testimonial $testimonial): void
    {
        $this->setStatus($actor, $testimonial, TestimonialStatus::Archived);
    }

    public function toggleFeatured(User $actor, Testimonial $testimonial): void
    {
        Gate::forUser($actor)->authorize('moderate', $testimonial);

        $testimonial->forceFill(['is_featured' => ! $testimonial->is_featured])->save();
    }

    public function replyPrivately(User $actor, Testimonial $testimonial, string $reply): void
    {
        Gate::forUser($actor)->authorize('moderate', $testimonial);

        if (blank($testimonial->email)) {
            throw ValidationException::withMessages(['reply' => 'There is no email address on file for this client.']);
        }

        $testimonial->forceFill(['admin_reply' => $reply, 'replied_at' => now()])->save();

        $this->outbox->send($testimonial->email, new TemplatedMail('testimonial_reply', [
            'client_name' => $testimonial->client_name,
            'reply' => $reply,
        ], null, replyToAddress: settings('contact.email')), $testimonial);
    }

    private function setStatus(User $actor, Testimonial $testimonial, TestimonialStatus $status, ?string $note = null): void
    {
        Gate::forUser($actor)->authorize('moderate', $testimonial);

        $testimonial->forceFill(array_filter([
            'status' => $status,
            'is_featured' => $status === TestimonialStatus::Approved ? $testimonial->is_featured : false,
            'moderated_by' => $actor->getKey(),
            'moderated_at' => now(),
            'moderation_note' => $note,
        ], fn ($value) => $value !== null))->save();
    }

    private function importPhoto(Testimonial $testimonial): void
    {
        try {
            $media = $this->media->importFromDisk(
                (string) config('softec.uploads.private_disk'),
                (string) $testimonial->submitted_photo_path,
                [
                    'title' => 'Testimonial photo — '.$testimonial->client_name,
                    'alt_text' => 'Photo of '.$testimonial->client_name,
                    'category' => 'testimonials',
                ],
            );

            $testimonial->forceFill(['photo_media_id' => $media->getKey(), 'submitted_photo_path' => null]);
        } catch (Throwable $exception) {
            // Approval proceeds without the photo; the moderator can attach one manually.
            report($exception);
        }
    }
}
