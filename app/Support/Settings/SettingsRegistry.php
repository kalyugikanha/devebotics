<?php

namespace App\Support\Settings;

/**
 * Default values for every admin-editable setting, grouped by module.
 *
 * Only presentation and content settings live here. Secrets (SMTP, API keys,
 * CAPTCHA secret) are environment-only and never stored in the database.
 */
final class SettingsRegistry
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function defaults(): array
    {
        return [
            'company' => [
                'name' => 'Softec Ai Solution',
                'legal_name' => 'Softec Ai Solution',
                'tagline' => 'IT Services | Products | Innovation',
                'short_description' => 'Softec Ai Solution designs, builds and runs software, AI and cloud solutions that help organisations work smarter and grow faster.',
                'founded_year' => null,
                'logo_light_media_id' => null,
                'logo_dark_media_id' => null,
                'logo_mark_media_id' => null,
                'favicon_media_id' => null,
                'show_wordmark' => true,
                'brochure_media_id' => null,
                'header_cta_label' => 'Start a project',
                'header_cta_url' => '/start-a-project',
            ],
            'contact' => [
                'email' => 'contact@softecai.com',
                'sales_email' => 'sales@softecai.com',
                'support_email' => 'support@softecai.com',
                'phone' => '+91-6397013739',
                'whatsapp_number' => '+91-6397013739',
                'whatsapp_message' => 'Hello Softec AI, I would like to discuss a project.',
                'whatsapp_widget_enabled' => true,
                'whatsapp_greeting' => 'Hi there! Looking for custom software, AI solutions, or digital growth? Chat with our team on WhatsApp.',
                'whatsapp_quick_replies' => [
                    'I would like a quote for a custom software project.',
                    'I want to explore AI agents or automation for my business.',
                    'I need a website or mobile application.',
                    'I have a question about your tech & marketing services.',
                ],
                'address_line1' => 'Jaipur, Rajasthan',
                'address_line2' => null,
                'city' => 'Jaipur',
                'region' => 'Rajasthan',
                'postal_code' => '302001',
                'country' => 'India',
                'show_address' => true,
                'business_hours' => [
                    'Monday - Friday: 9:00 AM - 7:00 PM IST',
                    'Saturday: 10:00 AM - 4:00 PM IST',
                ],
                'map_embed_url' => null,
                'booking_url' => null,
            ],
            'social' => [
                'linkedin' => 'https://www.linkedin.com/in/softecai-undefined-765923447/',
                'x' => null,
                'facebook' => 'https://www.facebook.com/softecaisolution/photos',
                'instagram' => 'https://www.instagram.com/softec_ai/',
                'youtube' => 'https://www.youtube.com/@SoftecAI',
                'github' => 'https://github.com/kalyugikanha',
            ],
            'theme' => [
                'draft' => self::defaultTheme(),
                'published' => self::defaultTheme(),
            ],
            'animation' => [
                'enabled' => true,
                'intensity' => 'standard',
                'duration_scale' => 1.0,
                'hero_preset' => 'rise',
                'reveal_preset' => 'fade-up',
                'stagger' => true,
                'parallax' => true,
                'counters' => true,
                'ambient' => true,
                'marquee' => true,
                'decorative_on_mobile' => false,
            ],
            'seo' => [
                'title_suffix' => ' | Softec AI Solution',
                'default_description' => 'Softec AI Solution is a premier global software engineering & AI development company based in Jaipur, India. Specializing in Custom Development, AI Agents, App Development, Cloud DevOps, Cybersecurity, and Digital Growth.',
                'default_og_image_media_id' => null,
                'google_site_verification' => null,
                'bing_site_verification' => null,
                'allow_indexing' => true,
                'robots_disallow' => [],
                'local_business_enabled' => true,
                'x_handle' => null,
            ],
            'integrations' => [
                'ga4_measurement_id' => 'G-B39FWJXTMJ',
                'gtm_container_id' => null,
                'turnstile_enabled' => false,
                'ai_drafting_enabled' => false,
            ],
            'privacy' => [
                'cookie_banner_enabled' => true,
                'consent_version' => '2026-10',
                'form_privacy_notice' => 'We use the details you provide only to respond to your request. See our Privacy Policy for how we handle personal data.',
                'inquiry_retention_days' => 730,
                'application_retention_days' => 365,
            ],
            'forms' => [
                'inquiry_recipients' => [],
                'feedback_recipients' => [],
                'careers_recipients' => [],
                'send_acknowledgements' => true,
                'feedback_email_verification' => false,
                'attachments_enabled' => true,
                'response_target_hours' => 24,
                'budget_ranges' => ['Under $10k', '$10k – $25k', '$25k – $50k', '$50k – $100k', '$100k+', 'Not sure yet'],
                'timelines' => ['ASAP', '1 – 3 months', '3 – 6 months', '6+ months', 'Flexible'],
            ],
            'features' => [
                'blog_comments_enabled' => false,
                'newsletter_enabled' => true,
                'careers_enabled' => true,
                'open_applications_enabled' => true,
                'feedback_form_enabled' => true,
                'search_enabled' => true,
                'show_demo_badges' => true,
            ],
        ];
    }

    /**
     * Brand palette derived from the Softec Ai Solution logo (violet → fuchsia → orange, cyan → mint). All text pairs meet WCAG AA.
     *
     * @return array<string, mixed>
     */
    public static function defaultTheme(): array
    {
        return [
            'default_mode' => 'system',
            'allow_switcher' => true,
            'radius' => 'rounded',
            'light' => [
                'primary' => '#4F46E5',
                'secondary' => '#0E7490',
                'accent' => '#B45309',
                'highlight' => '#7C3AED',
                'background' => '#FFFFFF',
                'surface' => '#F5F7FD',
                'foreground' => '#0A1128',
                'muted' => '#4A5672',
                'border' => '#E2E7F2',
            ],
            'dark' => [
                'primary' => '#818CF8',
                'secondary' => '#22D3EE',
                'accent' => '#FBBF24',
                'highlight' => '#C4B5FD',
                'background' => '#060B1A',
                'surface' => '#0C1428',
                'foreground' => '#E9EEF9',
                'muted' => '#9BA7C2',
                'border' => '#1B2642',
            ],
        ];
    }

    public static function default(string $key): mixed
    {
        return data_get(self::defaults(), $key);
    }
}
