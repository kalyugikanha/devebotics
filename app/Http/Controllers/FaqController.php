<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $categories = FaqCategory::query()->active()->ordered()
            ->with(['faqs' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->filter(fn (FaqCategory $category) => $category->faqs->isNotEmpty())
            ->values();

        $seo = SeoData::make('Frequently Asked Questions', 'Answers to common questions about working with Softec Ai Solution: services, projects, pricing, support and AI.')
            ->canonical(route('faq.index'))
            ->breadcrumbs([['FAQ', null]]);

        return view('faq.index', compact('categories', 'seo'));
    }
}
