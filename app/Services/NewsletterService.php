<?php

namespace App\Services;

use App\Enums\ConsentType;
use App\Enums\NewsletterStatus;
use App\Mail\TemplatedMail;
use App\Models\NewsletterSubscriber;
use App\Support\Forms\ConsentRecorder;
use App\Support\Forms\SpamGuard;
use App\Support\Mail\Outbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Double opt-in newsletter subscriptions. Responses never reveal whether an
 * address was already subscribed.
 */
class NewsletterService
{
    public function __construct(private ConsentRecorder $consents, private Outbox $outbox) {}

    public function subscribe(string $email, Request $request, ?string $source = null): void
    {
        $subscriber = NewsletterSubscriber::query()->firstOrNew(['email' => Str::lower(trim($email))]);

        if ($subscriber->exists && $subscriber->status === NewsletterStatus::Confirmed) {
            return;
        }

        $subscriber->fill([
            'status' => NewsletterStatus::Pending,
            'ip_hash' => SpamGuard::hashIp($request->ip()),
            'source' => Str::limit((string) $source, 60, ''),
            'unsubscribed_at' => null,
        ])->save();

        $this->consents->record(ConsentType::Marketing, true, $subscriber, $request, ['stage' => 'requested']);

        $this->outbox->send($subscriber->email, new TemplatedMail(
            'newsletter_confirmation',
            [],
            URL::temporarySignedRoute('newsletter.confirm', now()->addDays(3), ['subscriber' => $subscriber->getKey()]),
        ), $subscriber);
    }

    public function confirm(NewsletterSubscriber $subscriber, Request $request): void
    {
        if ($subscriber->status !== NewsletterStatus::Confirmed) {
            $subscriber->update(['status' => NewsletterStatus::Confirmed, 'confirmed_at' => now(), 'unsubscribed_at' => null]);
            $this->consents->record(ConsentType::Marketing, true, $subscriber, $request, ['stage' => 'confirmed']);
        }
    }

    public function unsubscribe(NewsletterSubscriber $subscriber, Request $request): void
    {
        $subscriber->update(['status' => NewsletterStatus::Unsubscribed, 'unsubscribed_at' => now()]);
        $this->consents->record(ConsentType::Marketing, false, $subscriber, $request, ['stage' => 'unsubscribed']);
    }

    public static function unsubscribeUrl(NewsletterSubscriber $subscriber): string
    {
        return URL::signedRoute('newsletter.unsubscribe', ['subscriber' => $subscriber->getKey()]);
    }
}
