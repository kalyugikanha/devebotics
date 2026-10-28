<?php

namespace App\Filament;

use Filament\Support\Contracts\HasLabel;

/**
 * Admin sidebar groups, in display order.
 */
enum AdminNavigationGroup implements HasLabel
{
    case Leads;
    case Website;
    case Catalog;
    case Portfolio;
    case Blog;
    case Company;
    case Reviews;
    case Careers;
    case Seo;
    case Appearance;
    case Settings;
    case Security;
    case System;

    public function getLabel(): string
    {
        return match ($this) {
            self::Leads => 'Query desk',
            self::Website => 'Website content',
            self::Catalog => 'Services & products',
            self::Portfolio => 'Portfolio & clients',
            self::Blog => 'Blog',
            self::Company => 'Company',
            self::Reviews => 'Reviews',
            self::Careers => 'Careers',
            self::Seo => 'SEO',
            self::Appearance => 'Appearance',
            self::Settings => 'Settings',
            self::Security => 'Users & security',
            self::System => 'System',
        };
    }
}
