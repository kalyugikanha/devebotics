<?php

namespace App\Support;

/**
 * Curated whitelist of outline Heroicons administrators can assign to
 * services, industries, features, ... Stored values are validated against
 * this list, so arbitrary markup can never be injected through "icons".
 */
final class IconRegistry
{
    /**
     * @return array<string, string> heroicon name => label
     */
    public static function icons(): array
    {
        return [
            'cpu-chip' => 'AI / processor',
            'sparkles' => 'Generative AI / magic',
            'beaker' => 'Research / experimentation',
            'variable' => 'Machine learning / data science',
            'code-bracket' => 'Code / software',
            'command-line' => 'Terminal / DevOps',
            'window' => 'Web application',
            'device-phone-mobile' => 'Mobile app',
            'cloud' => 'Cloud',
            'cloud-arrow-up' => 'Cloud migration',
            'server' => 'Server',
            'server-stack' => 'Infrastructure',
            'circle-stack' => 'Database',
            'shield-check' => 'Security',
            'lock-closed' => 'Privacy / lock',
            'finger-print' => 'Identity',
            'chart-bar' => 'Analytics',
            'presentation-chart-line' => 'Business intelligence',
            'document-chart-bar' => 'Reporting',
            'arrow-trending-up' => 'Growth',
            'cog-6-tooth' => 'Automation / settings',
            'arrow-path-rounded-square' => 'Workflow / CI-CD',
            'arrows-right-left' => 'Integration / APIs',
            'link' => 'Connections',
            'puzzle-piece' => 'Integration / modules',
            'cube' => 'Product',
            'cube-transparent' => 'Platform / SaaS',
            'squares-2x2' => 'Modules / dashboard',
            'paint-brush' => 'Design / UI-UX',
            'shopping-cart' => 'E-commerce',
            'building-storefront' => 'Retail',
            'users' => 'CRM / people',
            'user-group' => 'Team',
            'briefcase' => 'Business / ERP',
            'banknotes' => 'Finance / fintech',
            'currency-dollar' => 'Pricing',
            'heart' => 'Healthcare',
            'academic-cap' => 'Education',
            'truck' => 'Logistics',
            'home-modern' => 'Real estate',
            'paper-airplane' => 'Travel & hospitality',
            'building-office-2' => 'Enterprise',
            'scale' => 'Professional services / legal',
            'rocket-launch' => 'Startups / launch',
            'bolt' => 'Performance',
            'light-bulb' => 'Innovation / consulting',
            'globe-alt' => 'Global / digital transformation',
            'map' => 'Strategy / roadmap',
            'wrench-screwdriver' => 'Maintenance',
            'lifebuoy' => 'Support',
            'chat-bubble-left-right' => 'Communication',
            'check-badge' => 'Quality / certified',
            'exclamation-triangle' => 'Challenge / risk',
            'clipboard-document-check' => 'Delivery / QA',
            'calendar-days' => 'Planning',
            'adjustments-horizontal' => 'Customisation',
            'hand-raised' => 'Partnership',
            'magnifying-glass-circle' => 'Discovery',
            'newspaper' => 'Content',
            'film' => 'Media',
        ];
    }

    public static function isValid(?string $icon): bool
    {
        return $icon !== null && array_key_exists($icon, self::icons());
    }

    /**
     * Blade component name for a stored icon, with a safe fallback.
     */
    public static function component(?string $icon, string $fallback = 'sparkles'): string
    {
        return 'heroicon-o-'.(self::isValid($icon) ? $icon : $fallback);
    }
}
