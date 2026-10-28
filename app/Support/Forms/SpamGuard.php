<?php

namespace App\Support\Forms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Layered bot protection for public forms:
 * 1. Honeypot field that humans never see.
 * 2. Encrypted render timestamp — submissions faster than a human could type are rejected.
 * 3. Optional Cloudflare Turnstile verification (server-side) when configured.
 * Rate limiting is applied separately at the route level.
 */
class SpamGuard
{
    public function honeypotField(): string
    {
        return (string) config('softec.spam.honeypot_field');
    }

    /**
     * Encrypted timestamp embedded in each rendered form.
     */
    public function token(): string
    {
        return Crypt::encryptString((string) now()->timestamp);
    }

    public function turnstileEnabled(): bool
    {
        return (bool) settings('integrations.turnstile_enabled')
            && filled(config('softec.turnstile.site_key'))
            && filled(config('softec.turnstile.secret_key'));
    }

    /**
     * @throws ValidationException
     */
    public function validate(Request $request): void
    {
        // Honeypot filled → silently treat as spam (generic error, no hint for bots).
        if (filled($request->input($this->honeypotField()))) {
            throw ValidationException::withMessages(['form' => 'Your submission could not be processed. Please try again.']);
        }

        try {
            $renderedAt = (int) Crypt::decryptString((string) $request->input('_form_token'));
        } catch (Throwable) {
            throw ValidationException::withMessages(['form' => 'This form has expired. Please reload the page and try again.']);
        }

        $elapsed = now()->timestamp - $renderedAt;

        if ($elapsed < (int) config('softec.spam.min_submit_seconds', 3)) {
            throw ValidationException::withMessages(['form' => 'That was quick! Please take a moment to review your details and submit again.']);
        }

        if ($elapsed > 60 * 60 * 6) {
            throw ValidationException::withMessages(['form' => 'This form has expired. Please reload the page and try again.']);
        }

        if ($this->turnstileEnabled() && ! $this->verifyTurnstile((string) $request->input('cf-turnstile-response'), $request->ip())) {
            throw ValidationException::withMessages(['form' => 'Please complete the verification challenge.']);
        }
    }

    private function verifyTurnstile(string $token, ?string $ip): bool
    {
        if ($token === '') {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->retry(2, 200)->post((string) config('softec.turnstile.verify_url'), [
                'secret' => config('softec.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);

            return (bool) $response->json('success', false);
        } catch (Throwable $exception) {
            report($exception);

            // Fail closed: a verification outage should not open the form to bots.
            return false;
        }
    }

    public static function hashIp(?string $ip): ?string
    {
        return $ip ? hash_hmac('sha256', $ip, (string) config('app.key')) : null;
    }
}
