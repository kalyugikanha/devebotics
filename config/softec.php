<?php

/*
|--------------------------------------------------------------------------
| Softec Ai Solution — application configuration
|--------------------------------------------------------------------------
|
| Infrastructure-level settings that must come from the environment. Content
| and presentation settings (company details, theme colours, animation
| presets, SEO defaults, ...) are editable in the admin panel and stored in
| the `settings` table instead — see App\Support\Settings\SettingsRegistry.
|
*/

return [

    /*
     * Search engines may only index the site when the application runs in
     * production AND this flag is enabled. Staging/local are always noindex.
     */
    'allow_indexing' => (bool) env('APP_ALLOW_INDEXING', false),

    'force_https' => (bool) env('APP_FORCE_HTTPS', false),

    /*
     * Reverse proxies / load balancers allowed to set X-Forwarded-* headers
     * (comma-separated IPs/CIDRs, or "*" when the app is only reachable via the proxy).
     */
    'trusted_proxies' => env('TRUSTED_PROXIES'),

    'media' => [
        'disk' => env('MEDIA_DISK', 'public'),
        'directory' => 'media',
        // Responsive WebP widths generated for raster images.
        'variant_widths' => [480, 960, 1600],
        'max_upload_kb' => 8192,
        'max_dimension' => 6000,
        'image_mimes' => ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'svg'],
        'document_mimes' => ['pdf'],
    ],

    'uploads' => [
        // Private, authorization-protected attachments (inquiries, résumés).
        'private_disk' => 'local',
        'inquiry_attachment_mimes' => ['pdf', 'doc', 'docx', 'txt', 'png', 'jpg', 'jpeg', 'zip'],
        'resume_mimes' => ['pdf', 'doc', 'docx'],
        'max_attachment_kb' => 10240,
    ],

    'spam' => [
        // Honeypot input name rendered (hidden) on every public form.
        'honeypot_field' => 'website_url_confirm',
        // Forms submitted faster than this many seconds after rendering are rejected.
        'min_submit_seconds' => 3,
        // Duplicate submissions (same email + message) inside this window are collapsed.
        'duplicate_window_minutes' => 60 * 24,
    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
        'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
    ],

    'ai' => [
        'anthropic_api_key' => env('ANTHROPIC_API_KEY'),
        'anthropic_model' => env('ANTHROPIC_MODEL', 'claude-sonnet-5-5'),
    ],

    'seed_admin' => [
        'name' => env('SEED_ADMIN_NAME', 'Super Admin'),
        'email' => env('SEED_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('SEED_ADMIN_PASSWORD'),
    ],

    // Retention (days) used by the scheduled `softec:prune-data` command.
    'retention' => [
        'audit_logs' => 365,
        'security_logs' => 180,
        'email_logs' => 180,
        'spam_inquiries' => 30,
    ],

    // Slugs that CMS pages may not use because public routes own them.
    'reserved_slugs' => [
        'admin', 'api', 'about', 'services', 'products', 'industries', 'case-studies', 'blog',
        'careers', 'contact', 'start-a-project', 'feedback', 'testimonials', 'faq', 'team',
        'search', 'sitemap.xml', 'sitemaps', 'robots.txt', 'storage', 'livewire', 'filament',
        'newsletter', 'cookie-consent', 'company-profile', 'up', 'theme-preview', 'build', 'og',
    ],
];
