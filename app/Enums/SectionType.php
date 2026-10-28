<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/**
 * Whitelisted page-section building blocks. Each maps to a Blade view in
 * resources/views/sections. Administrators can only choose from this list,
 * so no arbitrary markup or script can be injected through sections.
 */
enum SectionType: string implements HasDescription, HasIcon, HasLabel
{
    case Hero = 'hero';
    case Stats = 'stats';
    case Logos = 'logos';
    case Services = 'services';
    case Products = 'products';
    case Industries = 'industries';
    case WhyChoose = 'why_choose';
    case TechStack = 'tech_stack';
    case CaseStudies = 'case_studies';
    case Testimonials = 'testimonials';
    case Process = 'process';
    case Blog = 'blog';
    case Faq = 'faq';
    case Cta = 'cta';
    case RichText = 'rich_text';
    case MediaText = 'media_text';
    case Features = 'features';
    case Values = 'values';
    case Team = 'team';
    case Milestones = 'milestones';
    case Certifications = 'certifications';
    case Partners = 'partners';
    case EngagementModels = 'engagement_models';
    case Statement = 'statement';

    public function getLabel(): string
    {
        return match ($this) {
            self::Hero => 'Hero banner',
            self::Stats => 'Company statistics',
            self::Logos => 'Client / partner logos',
            self::Services => 'Services overview',
            self::Products => 'Featured products',
            self::Industries => 'Industries served',
            self::WhyChoose => 'Why choose us',
            self::TechStack => 'Technology stack',
            self::CaseStudies => 'Case studies',
            self::Testimonials => 'Client testimonials',
            self::Process => 'Process / workflow',
            self::Blog => 'Blog & insights',
            self::Faq => 'FAQ',
            self::Cta => 'Call to action',
            self::RichText => 'Rich text',
            self::MediaText => 'Image + text',
            self::Features => 'Feature grid',
            self::Values => 'Mission, vision & values',
            self::Team => 'Team profiles',
            self::Milestones => 'Company milestones',
            self::Certifications => 'Certifications & achievements',
            self::Partners => 'Partners & alliances',
            self::EngagementModels => 'Engagement / pricing models',
            self::Statement => 'Scroll statement',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Hero => 'Headline, supporting copy, two calls to action and the animated hero artwork.',
            self::Stats => 'Animated counters from verified statistics (Company › Statistics).',
            self::Logos => 'Logos of clients/partners that have approved public logo usage.',
            self::Services => 'Cards for published services.',
            self::Products => 'Cards for published products.',
            self::Industries => 'Tabs or grid of published industries.',
            self::WhyChoose => 'Reasons to choose the company (items edited below).',
            self::TechStack => 'Technologies flagged "show on home", grouped by category.',
            self::CaseStudies => 'Published case studies.',
            self::Testimonials => 'Approved client reviews only.',
            self::Process => 'Numbered engagement steps (items edited below).',
            self::Blog => 'Latest published articles.',
            self::Faq => 'Accordion of FAQs flagged for the homepage or a chosen category.',
            self::Cta => 'Closing call-to-action band.',
            self::RichText => 'Free-form formatted text (sanitised).',
            self::MediaText => 'Image beside formatted text with an optional button.',
            self::Features => 'Icon grid (methodology, expertise, ...).',
            self::Values => 'Mission and vision statements plus value cards.',
            self::Team => 'Active team members (optionally leadership only).',
            self::Milestones => 'Timeline from Company › Milestones.',
            self::Certifications => 'Verified certifications, awards and memberships.',
            self::Partners => 'Partner organisations with approved logo usage.',
            self::EngagementModels => 'Engagement or pricing model cards (items edited below).',
            self::Statement => 'A large statement (the title) whose words light up as visitors scroll.',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Hero => 'heroicon-o-sparkles',
            self::Stats => 'heroicon-o-chart-bar',
            self::Logos, self::Partners => 'heroicon-o-building-office-2',
            self::Services => 'heroicon-o-cpu-chip',
            self::Products => 'heroicon-o-cube',
            self::Industries => 'heroicon-o-globe-alt',
            self::WhyChoose => 'heroicon-o-hand-thumb-up',
            self::TechStack => 'heroicon-o-code-bracket',
            self::CaseStudies => 'heroicon-o-briefcase',
            self::Testimonials => 'heroicon-o-chat-bubble-left-right',
            self::Process => 'heroicon-o-arrow-trending-up',
            self::Blog => 'heroicon-o-newspaper',
            self::Faq => 'heroicon-o-question-mark-circle',
            self::Cta => 'heroicon-o-megaphone',
            self::RichText => 'heroicon-o-document-text',
            self::MediaText => 'heroicon-o-photo',
            self::Features => 'heroicon-o-squares-2x2',
            self::Values => 'heroicon-o-heart',
            self::Team => 'heroicon-o-user-group',
            self::Milestones => 'heroicon-o-flag',
            self::Certifications => 'heroicon-o-check-badge',
            self::EngagementModels => 'heroicon-o-currency-dollar',
            self::Statement => 'heroicon-o-chat-bubble-bottom-center-text',
        };
    }

    public function view(): string
    {
        return 'sections.'.str_replace('_', '-', $this->value);
    }

    /**
     * Section types whose content is a repeatable list of items stored in `data.items`.
     */
    public function hasItems(): bool
    {
        return in_array($this, [self::WhyChoose, self::Process, self::Features, self::Values, self::EngagementModels], true);
    }

    /**
     * Section types that pull records from another module and accept a display limit.
     */
    public function hasLimit(): bool
    {
        return in_array($this, [
            self::Services, self::Products, self::Industries, self::CaseStudies, self::Testimonials,
            self::Blog, self::Faq, self::Team, self::Logos, self::Partners,
        ], true);
    }

    public function hasCallsToAction(): bool
    {
        return in_array($this, [self::Hero, self::Cta, self::MediaText], true);
    }
}
