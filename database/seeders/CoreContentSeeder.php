<?php

namespace Database\Seeders;

use App\Enums\AnnouncementStyle;
use App\Enums\ContentStatus;
use App\Enums\SectionBackground;
use App\Enums\SectionType;
use App\Enums\TechnologyCategory;
use App\Models\Announcement;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\EmailTemplate;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Industry;
use App\Models\Media;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\Page;
use App\Models\PortfolioCategory;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Technology;
use App\Support\Mail\EmailTemplateRegistry;
use App\Support\Media\MediaLibrary;
use Database\Seeders\Concerns\SeedsRichContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Core site structure and real company content (services offered, industries,
 * pages, navigation, email templates, Insights articles). Contains no
 * fictional clients, statistics, testimonials or credentials. Safe to run on
 * production; every step is idempotent. To apply updated copy to an existing
 * database, run ContentRefreshSeeder.
 */
class CoreContentSeeder extends Seeder
{
    use SeedsRichContent;

    public function run(): void
    {
        $this->seedBrandMedia();
        $technologies = $this->seedTechnologies();
        $this->seedServices($technologies);
        $this->seedIndustries();
        $this->seedTaxonomies();
        $this->seedFaqs();
        $this->seedPages();
        $this->seedRichContent(overwrite: false);
        $this->seedNavigation();
        $this->seedEmailTemplates();
        $this->seedAnnouncement();
    }

    private function seedBrandMedia(): void
    {
        if (Media::query()->where('category', 'brand')->exists()) {
            return;
        }

        config(['queue.default' => 'sync']);

        app(MediaLibrary::class)->store(
            resource_path('brand/softec-logo-original.png'),
            'softec-ai-solution-logo.png',
            ['title' => 'Softec Ai Solution logo (original)', 'alt_text' => 'Softec Ai Solution logo', 'category' => 'brand'],
        );
    }

    /**
     * @return array<string, int> technology name => id
     */
    private function seedTechnologies(): array
    {
        $catalogue = [
            TechnologyCategory::Backend->value => ['Laravel', 'PHP', 'Node.js', 'Python', 'RabbitMQ'],
            TechnologyCategory::Frontend->value => ['React', 'Next.js', 'Vue.js', 'Tailwind CSS', 'Storybook'],
            TechnologyCategory::Mobile->value => ['Flutter', 'React Native', 'Swift', 'Kotlin', 'Firebase'],
            TechnologyCategory::AiMl->value => ['Large language models', 'TensorFlow', 'PyTorch', 'scikit-learn', 'LangChain', 'Vector databases'],
            TechnologyCategory::Cloud->value => ['AWS', 'Microsoft Azure', 'Google Cloud', 'Cloudflare', 'Cloud platforms', 'AI services'],
            TechnologyCategory::Database->value => ['MySQL', 'PostgreSQL', 'Redis', 'BigQuery', 'dbt', 'Power BI', 'Data platforms'],
            TechnologyCategory::DevOps->value => ['Docker', 'Kubernetes', 'Terraform', 'GitHub Actions', 'GitLab CI', 'Grafana', 'Linux', 'Windows Server'],
            TechnologyCategory::Design->value => ['Figma'],
            TechnologyCategory::Other->value => ['Stripe', 'Shopify', 'WooCommerce', 'n8n', 'Power Automate', 'Low-code tools', 'Odoo', 'Salesforce', 'HubSpot', 'Microsoft Dynamics 365', 'Microsoft 365', 'OWASP'],
        ];

        $showOnHome = ['Laravel', 'React', 'Next.js', 'Vue.js', 'Flutter', 'Python', 'Large language models', 'TensorFlow', 'AWS', 'Microsoft Azure', 'Google Cloud', 'Docker', 'Kubernetes', 'PostgreSQL', 'MySQL', 'Figma'];

        $ids = [];
        $order = 0;

        foreach ($catalogue as $category => $names) {
            foreach ($names as $name) {
                $technology = Technology::query()->firstOrCreate(['slug' => Str::slug($name)], [
                    'name' => $name,
                    'category' => $category,
                    'show_on_home' => in_array($name, $showOnHome, true),
                    'sort_order' => $order++,
                    'is_active' => true,
                ]);

                $ids[$name] = $technology->getKey();
            }
        }

        return $ids;
    }

    /**
     * @param  array<string, int>  $technologies
     */
    private function seedServices(array $technologies): void
    {
        $catalogue = require __DIR__.'/data/services.php';
        $content = $this->serviceContent();
        $categoryOrder = 0;
        $serviceOrder = 0;

        foreach ($catalogue as $categoryData) {
            $category = ServiceCategory::query()->firstOrCreate(['slug' => Str::slug($categoryData['name'])], [
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'icon' => $categoryData['icon'],
                'sort_order' => $categoryOrder++,
                'is_active' => true,
            ]);

            foreach ($categoryData['services'] as $data) {
                if (Service::withTrashed()->where('slug', Str::slug($data['title']))->exists()) {
                    $serviceOrder++;

                    continue;
                }

                $toItems = fn (array $titles, string $icon): array => array_map(fn (string $title) => ['title' => $title, 'description' => null, 'icon' => $icon], $titles);

                $service = Service::query()->create([
                    'service_category_id' => $category->getKey(),
                    'title' => $data['title'],
                    'slug' => Str::slug($data['title']),
                    'tagline' => $data['tagline'],
                    'excerpt' => $data['excerpt'],
                    'overview' => $data['overview'],
                    'description' => $content[Str::slug($data['title'])]['description'] ?? '<h2>What you can expect</h2><ul>'
                        .implode('', array_map(fn (string $benefit) => '<li>'.e($benefit).'</li>', $data['benefits']))
                        .'</ul><p>Every engagement starts with a conversation about your goals, constraints and timeline, so the solution we propose fits your organisation — not the other way round.</p>',
                    'icon' => $data['icon'],
                    'challenges' => $toItems($data['challenges'], 'exclamation-triangle'),
                    'benefits' => $toItems($data['benefits'], 'check-badge'),
                    'features' => array_map(fn (string $title) => ['title' => $title, 'description' => null, 'icon' => $data['icon']], $data['features']),
                    'process_steps' => (require __DIR__.'/data/process.php'),
                    'cta_title' => 'Planning a '.Str::lower(Str::before($data['title'], ' &')).' project?',
                    'cta_text' => 'Tell us what you are trying to achieve and we will suggest the right approach, team and next steps.',
                    'status' => ContentStatus::Published,
                    'published_at' => now(),
                    'is_featured' => (bool) ($data['featured'] ?? false),
                    'sort_order' => $serviceOrder++,
                ]);

                $service->technologies()->sync(array_values(array_intersect_key($technologies, array_flip($data['technologies']))));
            }
        }
    }

    private function seedIndustries(): void
    {
        $services = Service::query()->pluck('id', 'title');
        $content = $this->industryContent();

        foreach (require __DIR__.'/data/industries.php' as $order => $data) {
            if (Industry::withTrashed()->where('slug', Str::slug($data['name']))->exists()) {
                continue;
            }

            $industry = Industry::query()->create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'icon' => $data['icon'],
                'excerpt' => $data['excerpt'],
                'description' => $content[Str::slug($data['name'])]['description'] ?? '<p>'.e($data['excerpt']).'</p><p>We combine domain understanding with modern engineering to deliver secure, scalable solutions that fit the way organisations in this sector operate.</p>',
                'challenges' => array_map(fn (string $title) => ['title' => $title, 'description' => null, 'icon' => 'exclamation-triangle'], $data['challenges']),
                'solutions' => array_map(fn (string $title) => ['title' => $title, 'description' => null, 'icon' => 'check-badge'], $data['solutions']),
                'status' => ContentStatus::Published,
                'published_at' => now(),
                'is_featured' => $order < 6,
                'sort_order' => $order,
            ]);

            $industry->services()->sync($services->only($data['services'])->values()->all());
        }
    }

    private function seedTaxonomies(): void
    {
        $productCategories = ['SaaS Products', 'AI-Powered Applications', 'CRM & ERP Platforms', 'Business Management Software', 'Automation Tools', 'Industry-Specific Applications', 'Mobile Applications', 'Custom Enterprise Software'];
        $portfolioCategories = ['Web Platforms', 'Mobile Apps', 'AI Solutions', 'Cloud & DevOps', 'Enterprise Systems', 'E-commerce'];
        $blogCategories = [
            'Artificial Intelligence' => 'Practical perspectives on machine learning, generative AI and AI agents.',
            'Software Engineering' => 'Architecture, quality and delivery practices for modern software.',
            'Cloud & DevOps' => 'Cloud strategy, infrastructure as code and reliable delivery pipelines.',
            'Product & Design' => 'Product thinking, UX research and design systems.',
            'Business & Strategy' => 'Digital transformation, automation and technology leadership.',
            'Company News' => 'Updates from the Softec Ai Solution team.',
        ];

        foreach ($productCategories as $order => $name) {
            ProductCategory::query()->firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'sort_order' => $order, 'is_active' => true]);
        }

        foreach ($portfolioCategories as $order => $name) {
            PortfolioCategory::query()->firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'sort_order' => $order, 'is_active' => true]);
        }

        $order = 0;

        foreach ($blogCategories as $name => $description) {
            BlogCategory::query()->firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => $description, 'sort_order' => $order++, 'is_active' => true]);
        }

        foreach (['Generative AI', 'Laravel', 'Cloud', 'Automation', 'Security', 'UX', 'Data', 'Startups'] as $tag) {
            BlogTag::query()->firstOrCreate(['slug' => Str::slug($tag)], ['name' => $tag]);
        }

        $this->editorialAuthor();
    }

    private function seedFaqs(): void
    {
        $faqs = [
            'General' => [
                ['What does Softec Ai Solution do?', 'We design, build and support custom software, web and mobile applications, AI solutions, cloud infrastructure and automation for organisations of all sizes.', true],
                ['Which industries do you work with?', 'We work across sectors including healthcare, education, finance, retail, manufacturing, logistics, real estate, travel and professional services. See our Industries page for details.', false],
            ],
            'Projects & engagement' => [
                ['How does a typical project start?', 'With a short discovery conversation about your goals, users and constraints. We then propose an approach, team and plan — usually with a phased roadmap so you see value early.', true],
                ['Can you work with our existing team or codebase?', 'Yes. We can extend your in-house team, take over an existing application after a code review, or deliver a complete solution end to end.', true],
                ['Who owns the code and intellectual property?', 'Ownership terms are agreed in the contract for each engagement. Our standard approach is that clients own the custom code and data created for them.', true],
            ],
            'Pricing' => [
                ['How do you estimate cost?', 'After discovery we provide a transparent estimate based on scope, complexity and team size. We offer fixed-scope, dedicated-team and time-and-materials engagement models.', true],
                ['Do you offer ongoing support after launch?', 'Yes. Maintenance and support plans cover security updates, monitoring, bug fixes and continuous improvements.', false],
            ],
            'AI & data' => [
                ['How do you keep our data safe when using AI?', 'We design AI solutions with data protection in mind — limiting what data models can access, using providers and configurations that meet your requirements, and adding human review where decisions matter.', true],
            ],
        ];

        $categoryOrder = 0;
        $faqOrder = 0;

        foreach ($faqs as $categoryName => $items) {
            $category = FaqCategory::query()->firstOrCreate(['slug' => Str::slug($categoryName)], ['name' => $categoryName, 'sort_order' => $categoryOrder++, 'is_active' => true]);

            foreach ($items as [$question, $answer, $home]) {
                Faq::query()->firstOrCreate(['question' => $question], [
                    'faq_category_id' => $category->getKey(),
                    'answer' => '<p>'.e($answer).'</p>',
                    'show_on_home' => $home,
                    'sort_order' => $faqOrder++,
                    'is_active' => true,
                ]);
            }
        }
    }

    private function seedPages(): void
    {
        $intro = $this->pageContent()['home_intro'];

        $home = $this->page('home', 'Home', 'home', 'AI-first software engineering, product development and cloud solutions.', [
            [SectionType::Hero, [
                'eyebrow' => 'AI-first software engineering',
                'title' => 'Build smarter software with [AI at the core]',
                'subtitle' => 'Softec Ai Solution designs, builds and scales custom software, AI and cloud solutions that help ambitious organisations work smarter and grow faster.',
                'data' => [
                    'primary_cta' => ['label' => 'Start a project', 'url' => '/start-a-project'],
                    'secondary_cta' => ['label' => 'Explore services', 'url' => '/services'],
                    'highlights' => ['Custom software', 'Generative AI', 'Cloud & DevOps', 'Product design'],
                ],
            ]],
            [SectionType::Logos, ['title' => 'Organisations we work with', 'data' => ['source' => 'clients', 'style' => 'marquee', 'limit' => 12]]],
            [SectionType::Services, ['eyebrow' => 'What we do', 'title' => 'IT services that [move your business forward]', 'subtitle' => 'From strategy and design to engineering, AI and cloud operations — one partner for the full lifecycle of your digital products.', 'data' => ['limit' => 6, 'mode' => 'featured']]],
            [SectionType::Statement, ['anchor' => self::HOME_STATEMENT_ANCHOR, 'eyebrow' => self::HOME_STATEMENT['eyebrow'], 'title' => self::HOME_STATEMENT['title'], 'subtitle' => self::HOME_STATEMENT['subtitle']]],
            [SectionType::Stats, ['eyebrow' => 'By the numbers', 'title' => 'Softec Ai Solution [at a glance]', 'background' => SectionBackground::Contrast]],
            [SectionType::WhyChoose, ['eyebrow' => 'Why Softec Ai Solution', 'title' => 'A partner focused on [outcomes], not just output', 'data' => ['items' => [
                ['title' => 'AI-first engineering', 'description' => 'We look for practical ways AI and automation can add value in every solution we build.', 'icon' => 'sparkles'],
                ['title' => 'Transparent delivery', 'description' => 'Clear estimates, regular demos and honest progress reporting throughout your project.', 'icon' => 'presentation-chart-line'],
                ['title' => 'Security by design', 'description' => 'Secure architecture, code review and least-privilege access are part of how we work.', 'icon' => 'shield-check'],
                ['title' => 'Full-stack capability', 'description' => 'Design, development, data, cloud and support from one coordinated team.', 'icon' => 'squares-2x2'],
                ['title' => 'Built to evolve', 'description' => 'Maintainable, documented code and architecture that grows with your business.', 'icon' => 'arrow-trending-up'],
                ['title' => 'Long-term partnership', 'description' => 'We stay with you after launch with support, optimisation and new features.', 'icon' => 'hand-raised'],
            ]]]],
            [SectionType::Products, ['eyebrow' => 'Products', 'title' => 'Software solutions [ready to adapt] to your business', 'background' => SectionBackground::Muted, 'data' => ['limit' => 3]]],
            [SectionType::Industries, ['eyebrow' => 'Industries', 'title' => 'Domain-aware solutions for [every sector]', 'data' => ['limit' => 6, 'layout' => 'tabs']]],
            [SectionType::TechStack, ['eyebrow' => 'Technology', 'title' => 'Modern, [proven] technology stack', 'subtitle' => 'We choose tools for reliability, security and long-term maintainability.']],
            [SectionType::CaseStudies, ['eyebrow' => 'Case studies', 'title' => 'Work we are [proud of]', 'background' => SectionBackground::Muted, 'data' => ['limit' => 3]]],
            [SectionType::Process, ['eyebrow' => 'How we work', 'title' => 'A clear path from [idea to impact]', 'data' => ['items' => require __DIR__.'/data/process.php']]],
            [SectionType::Testimonials, ['eyebrow' => 'Client feedback', 'title' => 'What our [clients say]', 'data' => ['limit' => 6]]],
            [SectionType::Blog, ['eyebrow' => 'Insights', 'title' => 'Ideas from our [engineers and consultants]', 'data' => ['limit' => 3]]],
            [SectionType::RichText, ['anchor' => self::HOME_INTRO_ANCHOR, 'eyebrow' => $intro['eyebrow'], 'title' => $intro['title'], 'content' => $intro['content'], 'background' => SectionBackground::Muted]],
            [SectionType::Faq, ['eyebrow' => 'FAQ', 'title' => 'Frequently asked questions', 'data' => ['limit' => 6]]],
            [SectionType::Cta, ['title' => 'Have a [project] in mind?', 'subtitle' => 'Tell us about your goals and we will propose the right team, approach and plan.', 'background' => SectionBackground::Gradient, 'data' => [
                'primary_cta' => ['label' => 'Start a project', 'url' => '/start-a-project'],
                'secondary_cta' => ['label' => 'Contact us', 'url' => '/contact'],
            ]]],
        ]);

        $this->page('about', 'About Softec Ai Solution', 'about', 'We are a software development and AI solutions company helping organisations design, build and scale digital products.', [
            [SectionType::RichText, ['eyebrow' => 'Who we are', 'title' => 'Engineering, AI and design [under one roof]', 'content' => '<p>Softec Ai Solution is an IT services and product company. We help startups, growing businesses and enterprises turn ideas into reliable software — combining product thinking, modern engineering, artificial intelligence and cloud expertise.</p><p>Our teams work closely with clients from discovery to launch and beyond, focusing on outcomes that matter: better customer experiences, more efficient operations and new sources of growth.</p>']],
            [SectionType::Values, ['eyebrow' => 'Purpose', 'title' => 'Mission, vision [and values]', 'background' => SectionBackground::Muted, 'data' => [
                'mission' => 'To help organisations work smarter by delivering secure, intelligent and well-crafted software.',
                'vision' => 'A world where every organisation can use technology and AI confidently to create value for people.',
                'items' => [
                    ['title' => 'Integrity', 'description' => 'Honest advice, transparent pricing and doing what we say.', 'icon' => 'check-badge'],
                    ['title' => 'Craftsmanship', 'description' => 'Thoughtful design and clean, tested, maintainable code.', 'icon' => 'code-bracket'],
                    ['title' => 'Curiosity', 'description' => 'Continuous learning and pragmatic innovation.', 'icon' => 'light-bulb'],
                    ['title' => 'Partnership', 'description' => 'Your success is the measure of ours.', 'icon' => 'hand-raised'],
                ],
            ]]],
            [SectionType::Stats, ['eyebrow' => 'At a glance', 'title' => 'Softec Ai Solution in numbers']],
            [SectionType::Milestones, ['eyebrow' => 'Our story', 'title' => 'Milestones along the way']],
            [SectionType::Features, ['eyebrow' => 'Methodology', 'title' => 'How we deliver', 'background' => SectionBackground::Muted, 'data' => ['items' => [
                ['title' => 'Agile, iterative delivery', 'description' => 'Short cycles, frequent demos and continuous feedback.', 'icon' => 'arrow-path-rounded-square'],
                ['title' => 'Quality engineering', 'description' => 'Code review, automated testing and CI/CD on every project.', 'icon' => 'clipboard-document-check'],
                ['title' => 'Security first', 'description' => 'Threat modelling, least-privilege access and secure defaults.', 'icon' => 'shield-check'],
                ['title' => 'Clear communication', 'description' => 'A dedicated point of contact and transparent reporting.', 'icon' => 'chat-bubble-left-right'],
            ]]]],
            [SectionType::TechStack, ['eyebrow' => 'Expertise', 'title' => 'Technical expertise']],
            [SectionType::Team, ['eyebrow' => 'Leadership', 'title' => 'The people behind Softec Ai Solution', 'data' => ['leadership_only' => true, 'limit' => 8]]],
            [SectionType::Certifications, ['eyebrow' => 'Recognition', 'title' => 'Certifications & achievements']],
            [SectionType::Partners, ['eyebrow' => 'Alliances', 'title' => 'Partners & strategic alliances', 'data' => ['limit' => 12]]],
            [SectionType::Cta, ['title' => 'Let’s build something [great] together', 'subtitle' => 'Talk to our team about your next product, platform or AI initiative.', 'background' => SectionBackground::Gradient, 'data' => [
                'primary_cta' => ['label' => 'Start a project', 'url' => '/start-a-project'],
                'secondary_cta' => ['label' => 'View careers', 'url' => '/careers'],
            ]]],
        ]);

        $this->page('engagement-models', 'Engagement Models', 'landing', 'Flexible ways to work with Softec Ai Solution — from fixed-scope projects to dedicated teams.', [
            [SectionType::EngagementModels, ['eyebrow' => 'Pricing & engagement', 'title' => 'Choose the model that [fits your project]', 'subtitle' => 'Every engagement starts with a free discovery call. Pricing is tailored to scope — we never publish one-size-fits-all prices.', 'data' => ['items' => [
                ['title' => 'Fixed-scope project', 'description' => 'Defined scope, timeline and budget agreed upfront.', 'best_for' => 'Well-defined projects and MVPs', 'features' => ['Detailed discovery and estimate', 'Milestone-based delivery', 'Predictable budget'], 'highlighted' => false],
                ['title' => 'Dedicated team', 'description' => 'A cross-functional team that works as an extension of yours.', 'best_for' => 'Long-term product development', 'features' => ['Flexible team composition', 'Your priorities, our people', 'Scale up or down monthly'], 'highlighted' => true],
                ['title' => 'Time & materials', 'description' => 'Pay for the effort used, with full transparency and control.', 'best_for' => 'Evolving scope and R&D', 'features' => ['Weekly reporting', 'Change priorities any time', 'No long-term lock-in'], 'highlighted' => false],
                ['title' => 'Support & maintenance', 'description' => 'Ongoing care for applications after launch.', 'best_for' => 'Live products and legacy systems', 'features' => ['Security updates', 'Monitoring and bug fixes', 'Agreed response times'], 'highlighted' => false],
            ]]]],
            [SectionType::Faq, ['eyebrow' => 'FAQ', 'title' => 'Questions about pricing', 'data' => ['faq_category' => 'pricing', 'limit' => 6]]],
            [SectionType::Cta, ['title' => 'Not sure which model fits?', 'subtitle' => 'Describe your project and we will recommend the most effective approach.', 'background' => SectionBackground::Gradient, 'data' => [
                'primary_cta' => ['label' => 'Request an estimate', 'url' => '/start-a-project'],
            ]]],
        ]);

        foreach ((require __DIR__.'/data/legal.php') as $slug => [$title, $body]) {
            $page = $this->page($slug, $title, 'legal', null, []);

            if (blank($page->body)) {
                $page->update(['body' => $body]);
            }
        }

        $home->forceFill(['is_system' => true])->saveQuietly();
    }

    /**
     * @param  array<int, array{0: SectionType, 1: array<string, mixed>}>  $sections
     */
    private function page(string $slug, string $title, string $template, ?string $excerpt, array $sections): Page
    {
        $page = Page::withTrashed()->where('slug', $slug)->first();

        if ($page !== null) {
            return $page;
        }

        $page = Page::query()->create([
            'title' => $title,
            'slug' => $slug,
            'template' => $template,
            'excerpt' => $excerpt,
            'status' => ContentStatus::Published,
            'published_at' => now(),
        ]);

        $page->forceFill(['is_system' => true])->saveQuietly();

        foreach ($sections as $order => [$type, $attributes]) {
            $page->sections()->create([
                'type' => $type,
                'anchor' => $attributes['anchor'] ?? null,
                'eyebrow' => $attributes['eyebrow'] ?? null,
                'title' => $attributes['title'] ?? null,
                'subtitle' => $attributes['subtitle'] ?? null,
                'content' => $attributes['content'] ?? null,
                'data' => $attributes['data'] ?? null,
                'background' => $attributes['background'] ?? SectionBackground::Default,
                'sort_order' => $order,
                'is_enabled' => true,
            ]);
        }

        return $page;
    }

    private function seedNavigation(): void
    {
        $menus = [
            'header' => ['Primary navigation', null, [
                ['Services', 'services_mega'],
                ['Products', 'route', 'products.index'],
                ['Industries', 'route', 'industries.index'],
                ['Case studies', 'route', 'portfolio.index'],
                ['Company', 'route', 'about', [
                    ['About us', 'route', 'about', 'Our story, mission and values'],
                    ['Our team', 'route', 'team.index', 'Meet the people behind the work'],
                    ['Engagement models', 'page', 'engagement-models', 'Flexible ways to work with us'],
                    ['Client reviews', 'route', 'testimonials.index', 'Feedback from our clients'],
                    ['Careers', 'route', 'careers.index', 'Join the Softec Ai Solution team'],
                    ['FAQ', 'route', 'faq.index', 'Answers to common questions'],
                ]],
                ['Insights', 'route', 'blog.index'],
                ['Contact', 'route', 'contact.create'],
            ]],
            'footer_1' => ['Footer — Services', 'Services', 'services'],
            'footer_2' => ['Footer — Company', 'Company', [
                ['About us', 'route', 'about'],
                ['Case studies', 'route', 'portfolio.index'],
                ['Our team', 'route', 'team.index'],
                ['Careers', 'route', 'careers.index'],
                ['Client reviews', 'route', 'testimonials.index'],
                ['Contact', 'route', 'contact.create'],
            ]],
            'footer_3' => ['Footer — Resources', 'Resources', [
                ['Insights & blog', 'route', 'blog.index'],
                ['Products', 'route', 'products.index'],
                ['Industries', 'route', 'industries.index'],
                ['FAQ', 'route', 'faq.index'],
                ['Leave feedback', 'route', 'feedback.create'],
                ['Start a project', 'route', 'project.create'],
            ]],
            'footer_legal' => ['Footer — Legal', null, [
                ['Privacy policy', 'page', 'privacy-policy'],
                ['Terms & conditions', 'page', 'terms-and-conditions'],
                ['Cookie policy', 'page', 'cookie-policy'],
                ['Accessibility', 'page', 'accessibility-statement'],
            ]],
        ];

        foreach ($menus as $location => [$name, $title, $items]) {
            if (NavigationMenu::query()->where('location', $location)->exists()) {
                continue;
            }

            $menu = NavigationMenu::query()->create(['name' => $name, 'location' => $location, 'title' => $title, 'is_active' => true]);

            if ($items === 'services') {
                foreach (Service::query()->where('is_featured', true)->orderBy('sort_order')->limit(6)->get() as $order => $service) {
                    $menu->allItems()->create(['label' => $service->title, 'link_type' => 'linkable', 'linkable_type' => 'service', 'linkable_id' => $service->getKey(), 'sort_order' => $order]);
                }

                $menu->allItems()->create(['label' => 'All services', 'link_type' => 'route', 'route_name' => 'services.index', 'sort_order' => 99]);

                continue;
            }

            $this->createItems($menu, $items);
        }
    }

    /**
     * @param  array<int, array<int, mixed>>  $items
     */
    private function createItems(NavigationMenu $menu, array $items, ?NavigationItem $parent = null): void
    {
        foreach ($items as $order => $item) {
            [$label, $type] = $item;
            $attributes = ['label' => $label, 'sort_order' => $order, 'parent_id' => $parent?->getKey(), 'link_type' => $type === 'page' ? 'linkable' : $type];

            if ($type === 'route') {
                $attributes['route_name'] = $item[2];
            }

            if ($type === 'page') {
                $page = Page::query()->where('slug', $item[2])->first();
                $attributes += ['linkable_type' => 'page', 'linkable_id' => $page?->getKey()];
            }

            $children = $item[3] ?? null;

            if (is_string($children)) {
                $attributes['description'] = $children;
                $children = null;
            }

            $created = $menu->allItems()->create($attributes);

            if (is_array($children)) {
                $this->createItems($menu, $children, $created);
            }
        }
    }

    private function seedEmailTemplates(): void
    {
        foreach (EmailTemplateRegistry::all() as $key => $template) {
            EmailTemplate::query()->firstOrCreate(['key' => $key], [
                'name' => $template['name'],
                'subject' => $template['subject'],
                'heading' => $template['heading'],
                'body' => $template['body'],
                'action_label' => $template['action_label'],
                'is_active' => true,
            ]);
        }
    }

    private function seedAnnouncement(): void
    {
        if (Announcement::query()->exists()) {
            return;
        }

        Announcement::query()->create([
            'message' => 'New: Generative AI & AI agent development — build assistants grounded in your own knowledge.',
            'link_label' => 'Learn more',
            'link_url' => '/services/generative-ai-ai-agent-development',
            'style' => AnnouncementStyle::Brand,
            'is_active' => true,
            'is_dismissible' => true,
        ]);
    }
}
