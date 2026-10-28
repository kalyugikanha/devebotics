<?php

namespace App\Http\Controllers;

use App\Enums\ConsentType;
use App\Support\Forms\ConsentRecorder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * Stores the visitor's cookie choices in a first-party cookie and keeps an
 * anonymous consent record (no personal data; IP is hashed).
 */
class CookieConsentController extends Controller
{
    public const COOKIE = 'softec_consent';

    public function __invoke(Request $request, ConsentRecorder $consents): JsonResponse
    {
        $validated = $request->validate([
            'analytics' => ['required', 'boolean'],
            'marketing' => ['required', 'boolean'],
        ]);

        $choice = [
            'v' => (string) settings('privacy.consent_version'),
            'necessary' => true,
            'analytics' => (bool) $validated['analytics'],
            'marketing' => (bool) $validated['marketing'],
            'at' => now()->toIso8601String(),
        ];

        $consents->record(ConsentType::Cookies, $choice['analytics'] || $choice['marketing'], null, $request, $choice);

        $cookie = Cookie::make(self::COOKIE, (string) json_encode($choice), 60 * 24 * 180, '/', null, $request->isSecure(), false, false, 'lax');

        return response()->json(['ok' => true, 'consent' => $choice])->withCookie($cookie);
    }

    /**
     * @return array{analytics: bool, marketing: bool}|null
     */
    public static function current(Request $request): ?array
    {
        $raw = $request->cookie(self::COOKIE);
        $data = is_string($raw) ? json_decode($raw, true) : null;

        if (! is_array($data) || ($data['v'] ?? null) !== (string) settings('privacy.consent_version')) {
            return null;
        }

        return ['analytics' => (bool) ($data['analytics'] ?? false), 'marketing' => (bool) ($data['marketing'] ?? false)];
    }
}
