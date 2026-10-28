<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\Pages\SectionDataResolver;
use App\Support\Seo\SchemaFactory;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(SectionDataResolver $resolver): View
    {
        $page = Page::query()->where('slug', 'home')->with(['sections.media', 'seo.ogImage'])->first();

        $seo = $page
            ? SeoData::forModel($page, settings('company.name').' — '.settings('company.tagline'), $page->excerpt ?: settings('seo.default_description'))
            : SeoData::make((string) settings('company.name'), (string) settings('seo.default_description'));

        $seo->withoutSuffix()
            ->canonical(route('home'))
            ->withSchema(SchemaFactory::organization())
            ->withSchema(SchemaFactory::website())
            ->withSchema(SchemaFactory::localBusiness());

        return view('pages.home', [
            'page' => $page,
            'sections' => $resolver->resolve($page?->sections ?? collect()),
            'seo' => $seo,
        ]);
    }
}
