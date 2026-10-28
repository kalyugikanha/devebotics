<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use App\Support\Html\ContentProcessor;
use App\Support\Seo\SeoData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'category' => (string) $request->string('category'),
            'industry' => (string) $request->string('industry'),
        ];

        $portfolios = Portfolio::query()->published()
            ->with(['industry', 'category', 'heroMedia', 'client'])
            ->when($filters['category'] !== '', fn (Builder $q) => $q->whereHas('category', fn (Builder $c) => $c->where('slug', $filters['category'])))
            ->when($filters['industry'] !== '', fn (Builder $q) => $q->whereHas('industry', fn (Builder $i) => $i->where('slug', $filters['industry'])))
            ->orderByDesc('is_featured')->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $seo = SeoData::make('Case Studies & Portfolio', 'Explore Softec Ai Solution case studies: how we design and deliver software, AI and cloud solutions across industries.')
            ->canonical(route('portfolio.index'))
            ->breadcrumbs([['Case studies', null]]);

        if (array_filter($filters) !== []) {
            $seo->noindex();
        }

        return view('portfolio.index', [
            'portfolios' => $portfolios,
            'filters' => $filters,
            'categories' => PortfolioCategory::query()->active()->ordered()->whereHas('portfolios', fn (Builder $q) => $q->published())->get(),
            'industries' => Industry::query()->published()->ordered()->whereHas('portfolios', fn (Builder $q) => $q->published())->get(),
            'seo' => $seo,
        ]);
    }

    public function show(string $slug): View
    {
        return $this->render(Portfolio::query()->published()->where('slug', $slug)->firstOrFail());
    }

    public function render(Portfolio $portfolio, bool $isPreview = false): View
    {
        $portfolio->load([
            'industry', 'category', 'heroMedia', 'client', 'seo.ogImage', 'technologies.logo', 'galleryItems.media',
            'services' => fn ($q) => $q->published()->ordered(),
            'products' => fn ($q) => $q->published(),
        ]);

        $more = Portfolio::query()->published()->whereKeyNot($portfolio->getKey())
            ->with(['heroMedia', 'industry'])
            ->orderByRaw('CASE WHEN industry_id = ? THEN 0 ELSE 1 END', [$portfolio->industry_id ?? 0])
            ->latest('published_at')->limit(2)->get();

        $seo = SeoData::forModel($portfolio, $portfolio->title.' — Case Study', $portfolio->summary, $portfolio->heroMedia)
            ->type('article')
            ->canonical($portfolio->publicUrl())
            ->breadcrumbs([['Case studies', route('portfolio.index')], [$portfolio->title, null]]);

        if ($isPreview) {
            $seo->noindex();
        }

        $processor = app(ContentProcessor::class);

        return view('portfolio.show', [
            'portfolio' => $portfolio,
            'challenge' => $processor->process($portfolio->challenge, false)['html'],
            'solution' => $processor->process($portfolio->solution, false)['html'],
            'outcome' => $processor->process($portfolio->outcome, false)['html'],
            'more' => $more,
            'seo' => $seo,
            'isPreview' => $isPreview,
        ]);
    }
}
