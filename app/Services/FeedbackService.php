<?php

namespace App\Services;

use App\Enums\ConsentType;
use App\Enums\TestimonialStatus;
use App\Mail\TemplatedMail;
use App\Models\Testimonial;
use App\Support\Forms\ConsentRecorder;
use App\Support\Forms\SpamGuard;
use App\Support\Mail\Outbox;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Stores client feedback from the public form. Every submission starts as
 * "pending" and is never shown publicly until a moderator approves it AND the
 * client granted permission to publish.
 */
class FeedbackService
{
    public function __construct(
        private ConsentRecorder $consents,
        private Outbox $outbox,
        private StaffNotifier $notifier,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function submit(array $data, Request $request, ?UploadedFile $photo = null): Testimonial
    {
        $emailHash = Testimonial::hashEmail((string) $data['email']);
        $contentHash = hash('sha256', $emailHash.'|'.Str::squish(Str::lower((string) $data['feedback'])));

        $existing = Testimonial::query()->where('content_hash', $contentHash)->where('created_at', '>=', now()->subDay())->first();

        if ($existing !== null) {
            return $existing;
        }

        $testimonial = DB::transaction(function () use ($data, $request, $photo, $emailHash, $contentHash): Testimonial {
            $publish = (bool) ($data['publish_permission'] ?? false);
            $identity = $publish && (bool) ($data['display_identity_consent'] ?? false);

            $testimonial = Testimonial::query()->create([
                'source' => 'client_form',
                'client_name' => $data['client_name'],
                'company_name' => $data['company_name'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'email' => Str::lower((string) $data['email']),
                'email_hash' => $emailHash,
                'service_id' => $data['service_id'] ?? null,
                'project_name' => $data['project_name'] ?? null,
                'rating' => (int) $data['rating'],
                'rating_communication' => $data['rating_communication'] ?? null,
                'rating_quality' => $data['rating_quality'] ?? null,
                'rating_delivery' => $data['rating_delivery'] ?? null,
                'feedback' => trim((string) $data['feedback']),
                'publish_permission' => $publish,
                'display_identity_consent' => $identity,
                'status' => TestimonialStatus::Pending,
                'ip_hash' => SpamGuard::hashIp($request->ip()),
                'content_hash' => $contentHash,
            ]);

            if ($photo !== null && $identity) {
                $testimonial->forceFill([
                    'submitted_photo_path' => $photo->store('testimonials', config('softec.uploads.private_disk')),
                ])->save();
            }

            $this->consents->record(ConsentType::PrivacyPolicy, true, $testimonial, $request);
            $this->consents->record(ConsentType::PublishTestimonial, $publish, $testimonial, $request);
            $this->consents->record(ConsentType::DisplayIdentity, $identity, $testimonial, $request);

            return $testimonial;
        });

        $this->notifyStaff($testimonial);

        if (settings('forms.feedback_email_verification')) {
            $this->outbox->send($testimonial->email, new TemplatedMail('feedback_verification', [
                'client_name' => $testimonial->client_name,
            ], URL::temporarySignedRoute('feedback.verify', now()->addDays(7), ['testimonial' => $testimonial->getKey()])), $testimonial);
        }

        return $testimonial;
    }

    public function markEmailVerified(Testimonial $testimonial): void
    {
        if ($testimonial->email_verified_at === null) {
            $testimonial->forceFill(['email_verified_at' => now()])->save();
        }
    }

    private function notifyStaff(Testimonial $testimonial): void
    {
        $adminUrl = rescue(fn () => route('filament.admin.resources.testimonials.edit', $testimonial), null, false);

        $this->notifier->notify(
            permission: 'testimonials.moderate',
            title: 'New client feedback ('.$testimonial->rating.'/5)',
            body: $testimonial->client_name.($testimonial->company_name ? ' · '.$testimonial->company_name : '').' — awaiting moderation',
            adminUrl: $adminUrl,
            configuredRecipients: (array) settings('forms.feedback_recipients', []),
            mail: new TemplatedMail('feedback_notification', [
                'client_name' => $testimonial->client_name,
                'company' => $testimonial->company_name,
                'rating' => $testimonial->rating,
                'publish_permission' => $testimonial->publish_permission,
                'feedback_excerpt' => Str::limit($testimonial->feedback, 400),
            ], $adminUrl),
            related: $testimonial,
        );
    }
}
