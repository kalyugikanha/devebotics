<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Support\Html\ContentProcessor;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class IndustryController extends Controller
{
    public function index(): View
    {
        $industries = Industry::query()->published()->with('heroMedia')->ordered()->get();

        $seo = SeoData::make('Industries We Serve', 'Softec Ai Solution builds secure, scalable software and AI solutions for healthcare, education, finance, retail, manufacturing, logistics, real estate, travel and professional services.')
            ->canonical(route('industries.index'))
            ->breadcrumbs([['Industries', null]]);

        return view('industries.index', compact('industries', 'seo'));
    }

    public function show(string $slug): View
    {
        return $this->render(Industry::query()->published()->where('slug', $slug)->firstOrFail());
    }

    public function render(Industry $industry, bool $isPreview = false): View
    {
        $industry->load([
            'heroMedia', 'seo.ogImage',
            'services' => fn ($q) => $q->published()->with('heroMedia')->ordered(),
            'products' => fn ($q) => $q->published()->with(['logo', 'heroMedia', 'category'])->limit(3),
            'portfolios' => fn ($q) => $q->published()->with(['heroMedia', 'category'])->latest('published_at')->limit(3),
            'faqs' => fn ($q) => $q->where('is_active', true),
        ]);

        $seo = SeoData::forModel($industry, 'Software & AI Solutions for '.$industry->name, $industry->excerpt, $industry->heroMedia)
            ->canonical($industry->publicUrl())
            ->breadcrumbs([['Industries', route('industries.index')], [$industry->name, null]]);

        if ($isPreview) {
            $seo->noindex();
        }

        return view('industries.show', [
            'industry' => $industry,
            'description' => app(ContentProcessor::class)->process($industry->description, false)['html'],
            'seo' => $seo,
            'isPreview' => $isPreview,
        ]);
    }
}
