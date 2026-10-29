<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Support\Html\ContentProcessor;
use App\Support\Seo\SchemaFactory;
use App\Support\Seo\SeoData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ServiceCategory::query()->active()->ordered()
            ->with(['services' => fn ($query) => $query->published()->with('heroMedia')->ordered()])
            ->get()
            ->filter(fn (ServiceCategory $category) => $category->services->isNotEmpty())
            ->values();

        $uncategorised = Service::query()->published()->whereNull('service_category_id')->with('heroMedia')->ordered()->get();

        $seo = SeoData::make('IT Services', 'Explore Softec Ai Solution IT services: custom software, web and mobile apps, AI and machine learning, cloud, DevOps, cybersecurity, data analytics and digital transformation.')
            ->canonical(route('services.index'))
            ->breadcrumbs([['Services', null]]);

        return view('services.index', compact('categories', 'uncategorised', 'seo'));
    }

    public function show(string $slug): View
    {
        $service = Service::query()->published()->where('slug', $slug)->firstOrFail();

        return $this->render($service);
    }

    public function render(Service $service, bool $isPreview = false): View
    {
        $service->load([
            'category', 'heroMedia', 'seo.ogImage', 'technologies.logo',
            'industries' => fn ($q) => $q->published()->ordered(),
            'relatedServices' => fn ($q) => $q->published()->with('heroMedia')->ordered()->limit(3),
            'portfolios' => fn ($q) => $q->published()->with(['heroMedia', 'industry'])->latest('published_at')->limit(3),
            'faqs' => fn ($q) => $q->where('is_active', true),
        ]);

        $seo = SeoData::forModel($service, $service->title, $service->excerpt, $service->heroMedia)
            ->canonical($service->publicUrl())
            ->breadcrumbs([['Services', route('services.index')], [$service->title, null]])
            ->withSchema(SchemaFactory::service($service));

        if ($service->faqs && $service->faqs->isNotEmpty()) {
            $seo->withSchema(SchemaFactory::faqPage($service->faqs));
        }

        if ($isPreview) {
            $seo->noindex();
        }

        $serviceBlogMap = [
            'generative-ai-ai-agent-development' => [
                'what-are-ai-agents-a-practical-guide-for-business-leaders',
                'retrieval-augmented-generation-explained-for-business-leaders',
                'generative-ai-use-cases-for-small-and-mid-sized-businesses',
            ],
            'ai-machine-learning-solutions' => [
                'how-to-identify-high-value-ai-use-cases-in-your-business',
                'what-are-ai-agents-a-practical-guide-for-business-leaders',
            ],
            'custom-software-development' => [
                'how-much-does-custom-software-development-cost',
                'how-to-choose-a-software-development-company',
                'custom-software-vs-off-the-shelf-how-to-choose',
            ],
            'web-application-development' => [
                'web-app-vs-mobile-app-vs-pwa',
                'laravel-for-business-web-applications',
                'how-much-does-custom-software-development-cost',
            ],
            'mobile-application-development' => [
                'web-app-vs-mobile-app-vs-pwa',
                'how-to-choose-a-software-development-company',
            ],
            'saas-product-development' => [
                'saas-product-development-from-mvp-to-scale',
                'how-much-does-custom-software-development-cost',
            ],
            'business-process-automation' => [
                'business-process-automation-where-to-start',
                'how-to-identify-high-value-ai-use-cases-in-your-business',
            ],
            'cloud-computing-cloud-migration' => [
                'a-practical-checklist-for-migrating-to-the-cloud',
            ],
            'devops-cicd' => [
                'a-practical-checklist-for-migrating-to-the-cloud',
            ],
            'cybersecurity-consulting' => [
                'cybersecurity-checklist-for-growing-businesses',
            ],
            'uiux-design' => [
                'why-design-systems-speed-up-product-development',
            ],
        ];

        $postSlugs = $serviceBlogMap[$service->slug] ?? [];
        $relatedPosts = $postSlugs !== []
            ? BlogPost::query()->published()->whereIn('slug', $postSlugs)->with(['author', 'category', 'featuredMedia'])->get()
            : collect();

        return view('services.show', [
            'service' => $service,
            'description' => app(ContentProcessor::class)->process($service->description, false)['html'],
            'relatedPosts' => $relatedPosts,
            'seo' => $seo,
            'isPreview' => $isPreview,
        ]);
    }
}
