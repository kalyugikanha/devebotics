<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\EmploymentType;
use App\Enums\PartnerType;
use App\Enums\PricingModel;
use App\Enums\ProductAvailability;
use App\Enums\TestimonialStatus;
use App\Enums\WorkplaceType;
use App\Models\Client;
use App\Models\Industry;
use App\Models\JobOpening;
use App\Models\Media;
use App\Models\Milestone;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\Statistic;
use App\Models\TeamMember;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Support\Media\MediaLibrary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * DEMONSTRATION content so the design can be reviewed before real content is
 * entered. Every record is flagged `is_demo`, labelled "Demo" on the public
 * site, and removable with `php artisan softec:purge-demo`.
 *
 * Deliberately excluded: certifications/awards, published prices and
 * verified outcome metrics — those must only ever come from real data.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Service::query()->exists() === false) {
            $this->command?->warn('Run CoreContentSeeder first.');

            return;
        }

        if (Product::withTrashed()->where('is_demo', true)->exists()) {
            $this->command?->info('Demo content already present — skipped.');

            return;
        }

        config(['queue.default' => 'sync']);

        $this->seedClientsAndPartners();
        $this->seedCompany();
        $this->seedProducts();
        $this->seedPortfolio();
        $this->seedTestimonials();
        $this->seedJobs();
    }

    private function seedClientsAndPartners(): void
    {
        $clients = ['Northwind', 'Lumenfield', 'Atlas Grid', 'Vertexa', 'Harborline', 'Quantia', 'Brightpath', 'Cobalt Labs'];

        foreach ($clients as $order => $name) {
            Client::query()->create([
                'name' => $name.' (demo)',
                'logo_media_id' => $this->logo($name, $order)->getKey(),
                'description' => 'Placeholder client used to preview the logo showcase.',
                'logo_usage_approved' => true,
                'logo_approved_at' => now(),
                'approval_reference' => 'Demo data — replace before launch',
                'show_on_home' => true,
                'sort_order' => $order,
                'is_active' => true,
                'is_demo' => true,
            ]);
        }

        $partners = [['Nimbus Cloud', PartnerType::Cloud], ['Stackwise', PartnerType::Technology], ['Orbital Data', PartnerType::Strategic], ['Keystone Security', PartnerType::Consulting]];

        foreach ($partners as $order => [$name, $type]) {
            Partner::query()->create([
                'name' => $name.' (demo)',
                'type' => $type,
                'logo_media_id' => $this->logo($name, $order + 3)->getKey(),
                'description' => 'Placeholder partner used to preview the alliances section.',
                'logo_usage_approved' => true,
                'logo_approved_at' => now(),
                'approval_reference' => 'Demo data — replace before launch',
                'sort_order' => $order,
                'is_active' => true,
                'is_demo' => true,
            ]);
        }
    }

    private function seedCompany(): void
    {
        $stats = [['Projects delivered', 120, '+'], ['Client satisfaction', 98, '%'], ['Engineers & designers', 45, '+'], ['Countries served', 12, '']];

        foreach ($stats as $order => [$label, $value, $suffix]) {
            Statistic::query()->create(['label' => $label, 'value' => $value, 'suffix' => $suffix, 'description' => 'Demo figure — replace with verified data', 'is_verified' => true, 'sort_order' => $order, 'is_active' => true, 'is_demo' => true]);
        }

        $team = [['Aarav Mehta', 'Chief Executive Officer'], ['Sofia Laurent', 'Chief Technology Officer'], ['Daniel Okafor', 'Head of AI & Data'], ['Mei Tanaka', 'Head of Product Design']];

        foreach ($team as $order => [$name, $title]) {
            TeamMember::query()->create(['name' => $name, 'job_title' => $title, 'department' => 'Leadership', 'bio' => 'Demo profile used to preview the team section. Replace with a real team member.', 'is_leadership' => true, 'sort_order' => $order, 'is_active' => true, 'is_demo' => true]);
        }

        $milestones = [['Founded', 'Softec Ai Solution starts with a small team of engineers.'], ['First enterprise platform', 'Delivered a large-scale platform for an enterprise client.'], ['AI practice launched', 'Dedicated AI & data team formed.'], ['Product portfolio', 'Launched our first in-house SaaS products.']];

        foreach ($milestones as $order => [$title, $description]) {
            Milestone::query()->create(['year' => 'Year '.($order + 1), 'title' => $title, 'description' => $description.' (demo)', 'sort_order' => $order, 'is_active' => true, 'is_demo' => true]);
        }
    }

    private function seedProducts(): void
    {
        $categories = ProductCategory::query()->pluck('id', 'slug');
        $industries = Industry::query()->pluck('id', 'slug');
        $technologies = Technology::query()->pluck('id', 'slug');

        $products = [
            ['Orbit CRM', 'crm-erp-platforms', 'A modern CRM with AI lead scoring and pipeline automation.', 'users', ['Pipeline management', 'AI lead scoring', 'Email & calendar sync', 'Custom dashboards', 'Role-based access', 'Open API'], ['professional-services', 'real-estate'], ProductAvailability::Available, ['laravel', 'vue-js', 'mysql']],
            ['Nimbus Assist', 'ai-powered-applications', 'An AI knowledge assistant grounded in your company documents.', 'sparkles', ['Retrieval-augmented answers', 'Source citations', 'Access controls', 'Slack & Teams integration', 'Usage analytics', 'Admin guardrails'], ['professional-services', 'education'], ProductAvailability::Beta, ['python', 'large-language-models', 'vector-databases']],
            ['Pulse Analytics', 'saas-products', 'Self-service BI dashboards that unify data from your tools.', 'presentation-chart-line', ['Connectors for popular tools', 'Drag-and-drop dashboards', 'Scheduled reports', 'KPI alerts', 'Row-level security', 'Embeddable charts'], ['retail-e-commerce', 'manufacturing'], ProductAvailability::ComingSoon, ['python', 'postgresql', 'react']],
            ['FlowDesk', 'automation-tools', 'Workflow automation for approvals, documents and back-office tasks.', 'cog-6-tooth', ['Visual workflow builder', 'Document extraction', 'Approval routing', 'Audit trail', 'Integrations', 'SLA tracking'], ['finance-fintech', 'logistics'], ProductAvailability::CustomBuild, ['laravel', 'large-language-models', 'redis']],
        ];

        foreach ($products as $order => [$name, $category, $summary, $icon, $features, $productIndustries, $availability, $tech]) {
            $product = Product::query()->create([
                'product_category_id' => $categories[$category] ?? null,
                'name' => $name,
                'tagline' => $summary,
                'summary' => $summary.' Demo product for previewing the catalogue.',
                'description' => '<h2>Overview</h2><p>'.e($summary).' This is placeholder copy demonstrating how product pages present features, audiences and next steps.</p><h2>Who it is for</h2><p>Teams that want to replace manual work with a focused, well-designed tool.</p>',
                'features' => array_map(fn (string $title) => ['title' => $title, 'description' => null, 'icon' => $icon], $features),
                'benefits' => [
                    ['title' => 'Faster onboarding', 'description' => 'Get started quickly with guided setup.', 'icon' => 'rocket-launch'],
                    ['title' => 'Secure by default', 'description' => 'Role-based access and audit logging.', 'icon' => 'shield-check'],
                    ['title' => 'Customisable', 'description' => 'Adapted to your workflows by our team.', 'icon' => 'adjustments-horizontal'],
                ],
                'target_audience' => 'Small and mid-sized teams, and enterprises looking for a configurable solution.',
                'pricing_model' => PricingModel::QuoteOnly,
                'pricing_note' => 'Pricing depends on users, modules and deployment model.',
                'has_demo' => true,
                'availability' => $availability,
                'status' => ContentStatus::Published,
                'published_at' => now(),
                'is_featured' => true,
                'sort_order' => $order,
                'is_demo' => true,
            ]);

            $product->industries()->sync($industries->only($productIndustries)->values()->all());
            $product->technologies()->sync($technologies->only($tech)->values()->all());
        }
    }

    private function seedPortfolio(): void
    {
        $categories = PortfolioCategory::query()->pluck('id', 'slug');
        $industries = Industry::query()->pluck('id', 'slug');
        $services = Service::query()->pluck('id', 'slug');
        $technologies = Technology::query()->pluck('id', 'slug');

        $projects = [
            ['Patient engagement platform', 'A regional healthcare provider', 'healthcare', 'web-platforms', ['custom-software-development', 'mobile-application-development'], ['laravel', 'flutter', 'aws']],
            ['AI document processing for loan operations', 'A financial services company', 'finance-fintech', 'ai-solutions', ['business-process-automation', 'ai-machine-learning-solutions'], ['python', 'large-language-models', 'microsoft-azure']],
            ['Omnichannel commerce replatforming', 'A multi-brand retailer', 'retail-e-commerce', 'e-commerce', ['e-commerce-development', 'cloud-computing-cloud-migration'], ['next-js', 'stripe', 'google-cloud']],
        ];

        foreach ($projects as $order => [$title, $label, $industry, $category, $projectServices, $tech]) {
            $portfolio = Portfolio::query()->create([
                'title' => $title,
                'client_label' => $label.' (demo)',
                'is_client_anonymized' => true,
                'industry_id' => $industries[$industry] ?? null,
                'portfolio_category_id' => $categories[$category] ?? null,
                'summary' => 'Demo case study showing how projects are presented: the challenge, our approach and the outcome.',
                'challenge' => '<p>The organisation relied on manual, disconnected processes that slowed teams down and made it hard to give customers a consistent experience.</p>',
                'solution' => '<p>We ran a short discovery, designed a phased roadmap and delivered a secure, scalable solution in iterative releases with regular stakeholder demos.</p>',
                'outcome' => '<p>Teams now work from a single platform with automated workflows and clear reporting. (Demo narrative — replace with a verified case study.)</p>',
                'metrics' => [
                    ['label' => 'Faster processing time', 'value' => '—', 'is_verified' => false],
                    ['label' => 'Manual steps removed', 'value' => '—', 'is_verified' => false],
                ],
                'duration' => (4 + $order * 2).' months',
                'status' => ContentStatus::Published,
                'published_at' => now()->subDays(10 * ($order + 1)),
                'is_featured' => true,
                'sort_order' => $order,
                'is_demo' => true,
            ]);

            $portfolio->services()->sync($services->only($projectServices)->values()->all());
            $portfolio->technologies()->sync($technologies->only($tech)->values()->all());
        }
    }

    private function seedTestimonials(): void
    {
        $services = Service::query()->pluck('id', 'slug');

        $items = [
            ['Demo Client — Operations Director', 'Example Logistics Co.', 5, 'custom-software-development', 'Placeholder testimonial: the team understood our processes quickly and delivered a platform our staff actually enjoy using. Communication was clear throughout.'],
            ['Demo Client — Product Lead', 'Example Health Ltd.', 5, 'mobile-application-development', 'Placeholder testimonial: thoughtful design, reliable delivery and great support after launch.'],
            ['Demo Client — CTO', 'Example Fintech Inc.', 4, 'generative-ai-ai-agent-development', 'Placeholder testimonial: a pragmatic approach to AI — they focused on the use cases that mattered and built in the right safeguards.'],
        ];

        foreach ($items as $order => [$name, $company, $rating, $service, $feedback]) {
            Testimonial::query()->create([
                'source' => 'admin',
                'client_name' => $name,
                'company_name' => $company,
                'service_id' => $services[$service] ?? null,
                'rating' => $rating,
                'rating_communication' => 5,
                'rating_quality' => $rating,
                'rating_delivery' => 5,
                'feedback' => $feedback,
                'publish_permission' => true,
                'display_identity_consent' => true,
                'status' => TestimonialStatus::Approved,
                'is_featured' => true,
                'sort_order' => $order,
                'is_demo' => true,
            ]);
        }
    }

    private function seedJobs(): void
    {
        $jobs = [
            ['Senior Laravel Developer', 'Engineering', 'Build secure, scalable web platforms with Laravel and modern front-end tooling.'],
            ['Machine Learning Engineer', 'AI & Data', 'Design, evaluate and deploy machine learning and generative AI solutions.'],
        ];

        foreach ($jobs as $order => [$title, $department, $summary]) {
            JobOpening::query()->create([
                'title' => $title,
                'department' => $department,
                'location' => 'Remote / Hybrid',
                'employment_type' => EmploymentType::FullTime,
                'workplace_type' => WorkplaceType::Hybrid,
                'experience_level' => 'Senior',
                'summary' => $summary.' (Demo vacancy — not a real opening.)',
                'description' => '<p>'.e($summary).'</p><p>This is a demonstration job posting used to preview the careers section. It is not an active vacancy.</p>',
                'responsibilities' => [['title' => 'Deliver high-quality features end to end', 'description' => null], ['title' => 'Collaborate with designers, product and clients', 'description' => null], ['title' => 'Review code and mentor colleagues', 'description' => null]],
                'requirements' => [['title' => 'Relevant professional experience', 'description' => null], ['title' => 'Strong communication skills', 'description' => null], ['title' => 'A passion for well-crafted software', 'description' => null]],
                'benefits' => [['title' => 'Flexible working', 'description' => null], ['title' => 'Learning budget', 'description' => null]],
                'status' => ContentStatus::Published,
                'published_at' => now(),
                'valid_through' => now()->addMonths(2),
                'sort_order' => $order,
                'is_demo' => true,
            ]);
        }
    }

    /**
     * Generates a simple placeholder wordmark SVG and stores it in the media library.
     */
    private function logo(string $name, int $variant): Media
    {
        $palettes = [['#7C3AED', '#EC4899'], ['#0EA5E9', '#22D3EE'], ['#F59E0B', '#F97316'], ['#10B981', '#34D399'], ['#6366F1', '#8B5CF6'], ['#EF4444', '#F59E0B'], ['#14B8A6', '#0EA5E9'], ['#8B5CF6', '#0EA5E9']];
        [$from, $to] = $palettes[$variant % count($palettes)];
        $id = 'g'.$variant;
        $text = htmlspecialchars($name, ENT_XML1);
        $width = 70 + (int) (Str::length($name) * 13.5);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$width} 48" width="{$width}" height="48" role="img"><title>{$text}</title><defs><linearGradient id="{$id}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$from}"/><stop offset="1" stop-color="{$to}"/></linearGradient></defs><rect x="4" y="8" width="32" height="32" rx="9" fill="url(#{$id})"/><circle cx="20" cy="24" r="7" fill="#fff" fill-opacity=".9"/><text x="46" y="31" font-family="Inter, Arial, sans-serif" font-size="20" font-weight="700" fill="#475569">{$text}</text></svg>
SVG;

        $path = tempnam(sys_get_temp_dir(), 'logo').'.svg';
        file_put_contents($path, $svg);

        try {
            return app(MediaLibrary::class)->store($path, Str::slug($name).'-logo.svg', [
                'title' => $name.' logo (demo)',
                'alt_text' => $name.' logo',
                'category' => 'logos',
                'is_demo' => true,
            ]);
        } finally {
            @unlink($path);
        }
    }
}
