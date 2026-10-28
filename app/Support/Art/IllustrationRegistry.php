<?php

namespace App\Support\Art;

/**
 * Whitelist of the hand-built, theme-aware SVG illustrations in
 * resources/views/components/illustrations. Administrators pick one per
 * service, industry, product or case study; when none is chosen a sensible
 * default is derived from the record's slug.
 */
final class IllustrationRegistry
{
    /**
     * @return array<string, string> name => label
     */
    public static function all(): array
    {
        return [
            // Services
            'software' => 'Custom software (code + modules)',
            'web' => 'Web application (browser dashboard)',
            'mobile' => 'Mobile apps (phones)',
            'saas' => 'SaaS platform (multi-tenant dashboard)',
            'ecommerce' => 'E-commerce (storefront + cart)',
            'api' => 'APIs & integrations (connected systems)',
            'ai' => 'AI & machine learning (neural network)',
            'chat' => 'Generative AI assistant (chat)',
            'automation' => 'Automation (workflow)',
            'analytics' => 'Analytics & BI (charts)',
            'cloud' => 'Cloud (architecture)',
            'devops' => 'DevOps & CI/CD (pipeline)',
            'infrastructure' => 'Managed IT / infrastructure (servers)',
            'security' => 'Cybersecurity (shield)',
            'crm' => 'CRM & ERP (pipeline board)',
            'strategy' => 'Digital transformation (roadmap)',
            'design' => 'UI/UX design (design canvas)',
            'support' => 'Maintenance & support (tickets)',
            // Industries
            'health' => 'Healthcare',
            'education' => 'Education',
            'finance' => 'Finance & fintech',
            'retail' => 'Retail',
            'manufacturing' => 'Manufacturing',
            'logistics' => 'Logistics',
            'realestate' => 'Real estate',
            'travel' => 'Travel & hospitality',
            'professional' => 'Professional services',
            'startup' => 'Startups & enterprises',
        ];
    }

    /**
     * Default illustration per seeded slug (services, industries, products, case studies).
     *
     * @var array<string, string>
     */
    private const DEFAULTS = [
        'custom-software-development' => 'software',
        'web-application-development' => 'web',
        'mobile-application-development' => 'mobile',
        'saas-product-development' => 'saas',
        'e-commerce-development' => 'ecommerce',
        'api-development-system-integration' => 'api',
        'ai-machine-learning-solutions' => 'ai',
        'generative-ai-ai-agent-development' => 'chat',
        'business-process-automation' => 'automation',
        'data-analytics-business-intelligence' => 'analytics',
        'cloud-computing-cloud-migration' => 'cloud',
        'devops-cicd' => 'devops',
        'it-infrastructure-managed-it-services' => 'infrastructure',
        'cybersecurity-consulting' => 'security',
        'crm-erp-solutions' => 'crm',
        'digital-transformation-consulting' => 'strategy',
        'uiux-design' => 'design',
        'maintenance-technical-support' => 'support',
        'healthcare' => 'health',
        'education' => 'education',
        'finance-fintech' => 'finance',
        'retail-e-commerce' => 'retail',
        'manufacturing' => 'manufacturing',
        'logistics' => 'logistics',
        'real-estate' => 'realestate',
        'travel-hospitality' => 'travel',
        'professional-services' => 'professional',
        'startups-enterprises' => 'startup',
        'orbit-crm' => 'crm',
        'nimbus-assist' => 'chat',
        'pulse-analytics' => 'analytics',
        'flowdesk' => 'automation',
    ];

    /**
     * Fallback by Heroicon name when the slug is unknown.
     *
     * @var array<string, string>
     */
    private const BY_ICON = [
        'cpu-chip' => 'ai', 'sparkles' => 'chat', 'code-bracket' => 'software', 'window' => 'web',
        'device-phone-mobile' => 'mobile', 'cloud' => 'cloud', 'cloud-arrow-up' => 'cloud', 'server-stack' => 'infrastructure',
        'server' => 'infrastructure', 'shield-check' => 'security', 'chart-bar' => 'analytics', 'presentation-chart-line' => 'analytics',
        'cog-6-tooth' => 'automation', 'arrow-path-rounded-square' => 'devops', 'arrows-right-left' => 'api', 'shopping-cart' => 'ecommerce',
        'building-storefront' => 'retail', 'users' => 'crm', 'briefcase' => 'crm', 'paint-brush' => 'design', 'lifebuoy' => 'support',
        'globe-alt' => 'strategy', 'cube-transparent' => 'saas', 'heart' => 'health', 'academic-cap' => 'education',
        'banknotes' => 'finance', 'truck' => 'logistics', 'home-modern' => 'realestate', 'paper-airplane' => 'travel',
        'scale' => 'professional', 'rocket-launch' => 'startup', 'newspaper' => 'strategy',
    ];

    /**
     * Illustration for an article without a featured image, varied by slug within its category.
     */
    public static function forPost(?string $categorySlug, string $seed): string
    {
        $sets = [
            'artificial-intelligence' => ['ai', 'chat', 'automation', 'analytics'],
            'software-engineering' => ['software', 'api', 'web', 'saas'],
            'cloud-devops' => ['cloud', 'devops', 'infrastructure', 'security'],
            'product-design' => ['design', 'mobile', 'web'],
            'business-strategy' => ['strategy', 'analytics', 'startup', 'crm'],
            'company-news' => ['startup', 'professional'],
        ];

        $set = $sets[$categorySlug] ?? ['software', 'ai', 'cloud', 'strategy'];

        return $set[crc32($seed) % count($set)];
    }

    public static function isValid(?string $name): bool
    {
        return $name !== null && array_key_exists($name, self::all());
    }

    public static function resolve(?string $chosen, ?string $slug = null, ?string $icon = null, string $fallback = 'software'): string
    {
        if (self::isValid($chosen)) {
            return $chosen;
        }

        if ($slug !== null && isset(self::DEFAULTS[$slug])) {
            return self::DEFAULTS[$slug];
        }

        if ($icon !== null && isset(self::BY_ICON[$icon])) {
            return self::BY_ICON[$icon];
        }

        return $fallback;
    }
}
