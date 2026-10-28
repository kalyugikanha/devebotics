<?php

namespace Tests\Feature\Public;

use App\Enums\NewsletterStatus;
use App\Mail\TemplatedMail;
use App\Models\ConsentRecord;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\Concerns\SubmitsPublicForms;
use Tests\TestCase;

class PrivacyAndNewsletterTest extends TestCase
{
    use RefreshDatabase, SubmitsPublicForms;

    public function test_cookie_consent_is_stored_and_recorded(): void
    {
        $response = $this->postJson('/cookie-consent', ['analytics' => true, 'marketing' => false]);

        $response->assertOk()->assertJsonPath('consent.analytics', true);
        $response->assertCookie('softec_consent');
        $this->assertSame(1, ConsentRecord::query()->where('consent_type', 'cookies')->count());
    }

    public function test_analytics_load_only_after_consent(): void
    {
        settings()->set('integrations', 'ga4_measurement_id', 'G-TEST12345');

        $this->get('/')->assertDontSee('googletagmanager.com/gtag/js', false)->assertSee('data-ga4="G-TEST12345"', false);

        $consent = json_encode(['v' => settings('privacy.consent_version'), 'analytics' => true, 'marketing' => false]);
        $this->withUnencryptedCookie('softec_consent', $consent)->get('/')->assertSee('googletagmanager.com/gtag/js?id=G-TEST12345', false);
    }

    public function test_newsletter_uses_double_opt_in_and_does_not_leak_existing_subscriptions(): void
    {
        Mail::fake();

        $this->post('/newsletter', $this->withSpamProtection(['email' => 'reader@example.test']))->assertSessionHas('newsletter_status');
        $subscriber = NewsletterSubscriber::query()->sole();
        $this->assertSame(NewsletterStatus::Pending, $subscriber->status);
        Mail::assertQueued(TemplatedMail::class, fn ($mail) => $mail->templateKey === 'newsletter_confirmation');

        $this->get(route('newsletter.confirm', $subscriber))->assertForbidden();
        $this->get(URL::temporarySignedRoute('newsletter.confirm', now()->addDay(), ['subscriber' => $subscriber->id]))->assertOk();
        $this->assertSame(NewsletterStatus::Confirmed, $subscriber->refresh()->status);

        $this->post('/newsletter', $this->withSpamProtection(['email' => 'reader@example.test']))->assertSessionHas('newsletter_status', 'Thanks! Please check your inbox to confirm your subscription.');
        Mail::assertQueuedCount(1);

        $this->get(URL::signedRoute('newsletter.unsubscribe', ['subscriber' => $subscriber->id]))->assertOk();
        $this->assertSame(NewsletterStatus::Unsubscribed, $subscriber->refresh()->status);
    }
}
