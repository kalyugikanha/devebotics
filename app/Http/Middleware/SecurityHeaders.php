<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers and Content Security Policy.
 *
 * Public pages use a strict nonce-based policy (no unsafe-inline/unsafe-eval
 * for scripts — the front-end uses Alpine's CSP build). The admin panel
 * (Filament/Livewire) requires inline scripts and eval, so it receives a
 * relaxed policy; it is only reachable by authenticated staff.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Str::random(32);
        Vite::useCspNonce($nonce);
        app()->instance('csp-nonce', $nonce);

        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $headers->remove('X-Powered-By');

        if ($request->isSecure() && app()->isProduction()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $contentType = (string) $headers->get('Content-Type');

        if ($contentType === '' || str_contains($contentType, 'text/html')) {
            $headers->set('Content-Security-Policy', $this->isAdminRequest($request) ? $this->adminPolicy() : $this->publicPolicy($nonce));
        }

        return $response;
    }

    private function isAdminRequest(Request $request): bool
    {
        return $request->is('admin', 'admin/*', 'livewire/*', 'filament/*');
    }

    private function publicPolicy(string $nonce): string
    {
        $hot = $this->viteDevServer();

        $frames = ['https://www.youtube-nocookie.com', 'https://www.youtube.com', 'https://player.vimeo.com', 'https://www.google.com', 'https://maps.google.com', 'https://challenges.cloudflare.com'];
        $connect = array_filter(["'self'", 'https://*.google-analytics.com', 'https://*.analytics.google.com', 'https://*.googletagmanager.com', 'https://challenges.cloudflare.com', $hot, $hot ? str_replace('http', 'ws', $hot) : null]);

        return implode('; ', array_filter([
            "default-src 'self'",
            // 'strict-dynamic' lets the nonced bundle load analytics/Turnstile after consent;
            // https: and 'unsafe-inline' are ignored by CSP3 browsers and only serve as legacy fallbacks.
            "script-src 'nonce-{$nonce}' 'strict-dynamic' https: 'unsafe-inline'".($hot ? " {$hot}" : ''),
            "style-src 'self' 'nonce-{$nonce}'".($hot ? " {$hot}" : ''),
            "style-src-attr 'unsafe-inline'",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data:",
            'connect-src '.implode(' ', $connect),
            'frame-src '.implode(' ', $frames),
            "media-src 'self' https:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            app()->isProduction() ? 'upgrade-insecure-requests' : null,
        ]));
    }

    private function adminPolicy(): string
    {
        $hot = $this->viteDevServer();
        $hotSources = $hot ? " {$hot} ".str_replace('http', 'ws', $hot) : '';

        return implode('; ', array_filter([
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'".$hotSources,
            "style-src 'self' 'unsafe-inline'".$hotSources,
            "img-src 'self' data: blob: https:",
            "font-src 'self' data:",
            "connect-src 'self'".$hotSources,
            "frame-src 'self' https://www.youtube-nocookie.com https://player.vimeo.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            app()->isProduction() ? 'upgrade-insecure-requests' : null,
        ]));
    }

    private function viteDevServer(): ?string
    {
        if (! Vite::isRunningHot()) {
            return null;
        }

        $url = trim((string) @file_get_contents(public_path('hot')));

        return $url !== '' ? rtrim($url, '/') : null;
    }
}
