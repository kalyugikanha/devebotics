<?php

namespace App\Support\Seo;

use App\Models\BlogPost;
use App\Models\JobOpening;
use App\Models\Product;
use App\Models\Service;
use App\Support\Branding;
use Illuminate\Support\Str;

/**
 * schema.org JSON-LD builders. Only facts stored by administrators are used —
 * no ratings, reviews, prices or addresses are ever invented.
 */
final class SchemaFactory
{
    /**
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        $sameAs = array_values(array_filter(settings()->group('social')));
        $contact = settings()->group('contact');

        $organization = array_filter([
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => settings('company.name'),
            'legalName' => settings('company.legal_name'),
            'url' => url('/'),
            'logo' => Branding::organizationLogoUrl(),
            'description' => settings('company.short_description'),
            'foundingDate' => settings('company.founded_year') ? (string) settings('company.founded_year') : null,
            'email' => $contact['email'] ?: null,
            'telephone' => $contact['phone'] ?: null,
            'sameAs' => $sameAs ?: null,
        ]);

        if ($contact['show_address'] && filled($contact['address_line1'])) {
            $organization['address'] = self::postalAddress($contact);
        }

        return ['@context' => 'https://schema.org'] + $organization;
    }

    /**
     * @return array<string, mixed>
     */
    public static function website(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'name' => settings('company.name'),
            'url' => url('/'),
            'publisher' => ['@id' => url('/').'#organization'],
        ];
    }

    /**
     * LocalBusiness only when explicitly enabled and a full address is configured.
     *
     * @return array<string, mixed>|null
     */
    public static function localBusiness(): ?array
    {
        $contact = settings()->group('contact');

        if (! settings('seo.local_business_enabled') || ! $contact['show_address'] || blank($contact['address_line1']) || blank($contact['city'])) {
            return null;
        }

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            '@id' => url('/').'#localbusiness',
            'name' => settings('company.name'),
            'url' => url('/'),
            'image' => Branding::organizationLogoUrl(),
            'telephone' => $contact['phone'] ?: null,
            'email' => $contact['email'] ?: null,
            'address' => self::postalAddress($contact),
            'parentOrganization' => ['@id' => url('/').'#organization'],
        ]);
    }

    /**
     * @param  array<int, array{label: string, url: ?string}>  $crumbs
     * @return array<string, mixed>
     */
    public static function breadcrumbList(array $crumbs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($crumbs)->values()->map(fn (array $crumb, int $index) => array_filter([
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['label'],
                'item' => $crumb['url'] ?? null,
            ]))->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function article(BlogPost $post, ?string $image): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => Str::limit($post->title, 110, ''),
            'description' => $post->excerpt,
            'image' => $image ? [$image] : null,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => $post->author ? array_filter([
                '@type' => $post->author->user_id ? 'Person' : 'Organization',
                'name' => $post->author->name,
                'url' => $post->author->publicUrl(),
            ]) : ['@id' => url('/').'#organization'],
            'publisher' => ['@id' => url('/').'#organization'],
            'mainEntityOfPage' => $post->publicUrl(),
            'articleSection' => $post->category?->name,
            'keywords' => $post->tags->pluck('name')->implode(', ') ?: null,
            'wordCount' => str_word_count(strip_tags((string) $post->body)),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function service(Service $service): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->title,
            'description' => $service->excerpt,
            'serviceType' => $service->category?->name,
            'provider' => ['@id' => url('/').'#organization'],
            'url' => $service->publicUrl(),
            'areaServed' => 'Worldwide',
        ]);
    }

    /**
     * Product markup; offers are included only for real, published plan prices.
     *
     * @return array<string, mixed>
     */
    public static function product(Product $product, ?string $image): array
    {
        $offers = $product->showsPlans()
            ? $product->plans->filter(fn ($plan) => $plan->is_active && $plan->price !== null)->map(fn ($plan) => array_filter([
                '@type' => 'Offer',
                'name' => $plan->name,
                'price' => number_format((float) $plan->price, 2, '.', ''),
                'priceCurrency' => $plan->currency,
                'availability' => $product->availability->schemaOrgAvailability(),
                'url' => $product->publicUrl(),
            ]))->values()->all()
            : [];

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $product->name,
            'description' => $product->summary,
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'image' => $image,
            'url' => $product->publicUrl(),
            'publisher' => ['@id' => url('/').'#organization'],
            'offers' => $offers ?: null,
        ]);
    }

    /**
     * JobPosting for genuine, open vacancies only.
     *
     * @return array<string, mixed>|null
     */
    public static function jobPosting(JobOpening $job): ?array
    {
        if (! $job->isOpen() || $job->is_demo) {
            return null;
        }

        $contact = settings()->group('contact');

        $posting = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->title,
            'description' => (string) ($job->description ?: e((string) $job->summary)),
            'datePosted' => ($job->published_at ?? $job->created_at)?->toDateString(),
            'validThrough' => $job->valid_through?->endOfDay()->toIso8601String(),
            'employmentType' => $job->employment_type->schemaOrgValue(),
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => settings('company.name'),
                'sameAs' => url('/'),
                'logo' => Branding::organizationLogoUrl(),
            ],
            'directApply' => true,
        ];

        if ($job->workplace_type->value === 'remote') {
            $posting['jobLocationType'] = 'TELECOMMUTE';
            $posting['applicantLocationRequirements'] = ['@type' => 'Country', 'name' => $job->country_code ?: ($contact['country'] ?: 'Worldwide')];
        } elseif (filled($job->location)) {
            $posting['jobLocation'] = [
                '@type' => 'Place',
                'address' => array_filter([
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job->location,
                    'addressCountry' => $job->country_code ?: ($contact['country'] ?: null),
                ]),
            ];
        }

        if ($job->show_salary && $job->salary_min !== null) {
            $posting['baseSalary'] = [
                '@type' => 'MonetaryAmount',
                'currency' => $job->salary_currency ?: 'USD',
                'value' => array_filter([
                    '@type' => 'QuantitativeValue',
                    'minValue' => (float) $job->salary_min,
                    'maxValue' => $job->salary_max !== null ? (float) $job->salary_max : null,
                    'unitText' => match ($job->salary_period) {
                        'hour' => 'HOUR', 'month' => 'MONTH', 'week' => 'WEEK', default => 'YEAR',
                    },
                ]),
            ];
        }

        return array_filter($posting);
    }

    /**
     * @param  array<string, mixed>  $contact
     * @return array<string, mixed>
     */
    private static function postalAddress(array $contact): array
    {
        return array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => trim($contact['address_line1'].' '.($contact['address_line2'] ?? '')),
            'addressLocality' => $contact['city'] ?? null,
            'addressRegion' => $contact['region'] ?? null,
            'postalCode' => $contact['postal_code'] ?? null,
            'addressCountry' => $contact['country'] ?? null,
        ]);
    }
}
