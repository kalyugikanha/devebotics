<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\Html\ContentProcessor;
use App\Support\Pages\SectionDataResolver;
use App\Support\Seo\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private SectionDataResolver $resolver, private ContentProcessor $content) {}

    public function about(): View
    {
        $page = Page::query()->published()->where('slug', 'about')->with(['sections.media', 'seo.ogImage', 'heroMedia'])->firstOrFail();

        return $this->render($page);
    }

    public function show(string $slug): View|RedirectResponse
    {
        if ($slug === 'home') {
            return redirect()->route('home', status: 301);
        }

        if ($slug === 'about') {
            return redirect()->route('about', status: 301);
        }

        $page = Page::query()->published()->where('slug', $slug)->with(['sections.media', 'seo.ogImage', 'heroMedia'])->firstOrFail();

        return $this->render($page);
    }

    public function render(Page $page, bool $isPreview = false): View
    {
        $seo = SeoData::forModel($page, $page->title, $page->excerpt, $page->heroMedia)
            ->canonical($page->publicUrl())
            ->breadcrumbs([[$page->title, null]]);

        if ($isPreview) {
            $seo->noindex();
        }

        $body = $this->content->process($page->body, buildToc: $page->template === 'legal');

        return view($page->template === 'legal' ? 'pages.legal' : 'pages.page', [
            'page' => $page,
            'sections' => $this->resolver->resolve($page->sections),
            'body' => $body,
            'seo' => $seo,
            'isPreview' => $isPreview,
        ]);
    }
}
