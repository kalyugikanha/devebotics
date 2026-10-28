<?php

namespace App\Services;

use App\Enums\ConsentType;
use App\Enums\InquiryActivityType;
use App\Enums\InquiryPriority;
use App\Enums\InquiryStatus;
use App\Enums\InquiryTopic;
use App\Enums\InquiryType;
use App\Mail\TemplatedMail;
use App\Models\ContactInquiry;
use App\Models\Service;
use App\Services\Crm\InquiryRouter;
use App\Support\Forms\ConsentRecorder;
use App\Support\Forms\SpamGuard;
use App\Support\Mail\Outbox;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Creates contact / project inquiries from validated public form data.
 */
class InquiryService
{
    public function __construct(
        private ConsentRecorder $consents,
        private Outbox $outbox,
        private StaffNotifier $notifier,
        private InquiryRouter $router,
    ) {}

    /**
     * @param  array<string, mixed>  $data  validated input
     */
    public function submit(array $data, Request $request, ?UploadedFile $attachment = null): ContactInquiry
    {
        $hash = hash('sha256', Str::lower(trim((string) $data['email'])).'|'.Str::squish(Str::lower((string) $data['message'])));

        $duplicate = ContactInquiry::query()
            ->where('content_hash', $hash)
            ->where('created_at', '>=', now()->subMinutes((int) config('softec.spam.duplicate_window_minutes')))
            ->whereNull('duplicate_of_id')
            ->latest('id')
            ->first();

        // Identical resubmission (double click, refresh): return the original reference, no new alerts.
        if ($duplicate !== null) {
            return $duplicate;
        }

        $inquiry = DB::transaction(function () use ($data, $request, $attachment, $hash): ContactInquiry {
            $type = InquiryType::tryFrom((string) ($data['type'] ?? '')) ?? InquiryType::Quick;

            $inquiry = ContactInquiry::query()->create([
                'type' => $type,
                'topic' => $type === InquiryType::Project ? InquiryTopic::Sales : (InquiryTopic::tryFrom((string) ($data['topic'] ?? '')) ?? InquiryTopic::Other),
                'name' => $data['name'],
                'email' => Str::lower($data['email']),
                'phone' => $data['phone'] ?? null,
                'company' => $data['company'] ?? null,
                'service_id' => $data['service_id'] ?? null,
                'budget_range' => $data['budget_range'] ?? null,
                'timeline' => $data['timeline'] ?? null,
                'subject' => $data['subject'] ?? null,
                'message' => $data['message'],
                'status' => InquiryStatus::New,
                'priority' => ($data['type'] ?? null) === InquiryType::Project->value ? InquiryPriority::High : InquiryPriority::Normal,
                'source_url' => Str::limit((string) $request->headers->get('referer'), 2000, ''),
                'utm' => $this->utm($request),
                'ip_hash' => SpamGuard::hashIp($request->ip()),
                'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
                'content_hash' => $hash,
            ]);

            if ($attachment !== null) {
                $inquiry->forceFill([
                    'attachment_path' => $attachment->store('inquiries/'.now()->format('Y/m'), config('softec.uploads.private_disk')),
                    'attachment_name' => Str::limit(basename($attachment->getClientOriginalName()), 200, ''),
                ])->save();
            }

            $inquiry->activities()->create([
                'type' => InquiryActivityType::Created,
                'body' => 'Submitted via the '.($inquiry->type === InquiryType::Project ? 'project inquiry' : 'contact').' form.',
                'created_at' => now(),
            ]);

            $this->consents->record(ConsentType::PrivacyPolicy, true, $inquiry, $request);

            return $inquiry;
        });

        // Routing must never break or block the public form; failures are reported.
        rescue(fn () => $this->router->route($inquiry), report: true);

        $this->sendNotifications($inquiry->load('service'));

        return $inquiry;
    }

    private function sendNotifications(ContactInquiry $inquiry): void
    {
        $service = $inquiry->service instanceof Service ? $inquiry->service->title : null;

        $this->notifier->notify(
            permission: 'inquiries.view_all',
            title: 'New inquiry '.$inquiry->reference,
            body: $inquiry->name.($inquiry->company ? ' · '.$inquiry->company : '').' — '.Str::limit((string) ($inquiry->subject ?: $inquiry->message), 80),
            adminUrl: $this->adminUrl($inquiry),
            configuredRecipients: (array) settings('forms.inquiry_recipients', []),
            mail: new TemplatedMail('inquiry_notification', [
                'reference' => $inquiry->reference,
                'type' => $inquiry->type->getLabel(),
                'name' => $inquiry->name,
                'company' => $inquiry->company,
                'service' => $service,
                'budget' => $inquiry->budget_range,
                'timeline' => $inquiry->timeline,
                'subject' => $inquiry->subject,
                'message_excerpt' => $inquiry->excerpt(400),
            ], $this->adminUrl($inquiry)),
            related: $inquiry,
        );

        if (settings('forms.send_acknowledgements', true)) {
            $this->outbox->send($inquiry->email, new TemplatedMail('inquiry_acknowledgement', [
                'name' => $inquiry->name,
                'reference' => $inquiry->reference,
                'subject' => $inquiry->subject ?: Str::limit($inquiry->message, 60),
            ], url('/'), replyToAddress: settings('contact.sales_email') ?: settings('contact.email')), $inquiry);
        }
    }

    private function adminUrl(ContactInquiry $inquiry): ?string
    {
        return rescue(fn () => route('filament.admin.resources.contact-inquiries.view', $inquiry), null, false);
    }

    /**
     * @return array<string, string>|null
     */
    private function utm(Request $request): ?array
    {
        $utm = collect(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'])
            ->mapWithKeys(fn (string $key) => [$key => Str::limit((string) $request->input($key, ''), 100, '')])
            ->filter()
            ->all();

        return $utm ?: null;
    }
}
