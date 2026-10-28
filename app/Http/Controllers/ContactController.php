<?php

namespace App\Http\Controllers;

use App\Enums\InquiryType;
use App\Http\Requests\ContactInquiryRequest;
use App\Models\Faq;
use App\Models\Service;
use App\Services\InquiryService;
use App\Support\Seo\SeoData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        $seo = SeoData::make('Contact Us', 'Get in touch with Softec Ai Solution about software development, AI, cloud and IT services. We usually reply within one business day.')
            ->canonical(route('contact.create'))
            ->breadcrumbs([['Contact', null]]);

        return view('contact.create', [
            'services' => $this->services(),
            'faqs' => Faq::query()->active()->whereHas('category', fn (Builder $q) => $q->whereIn('slug', ['general', 'projects-engagement']))->ordered()->limit(5)->get(),
            'seo' => $seo,
        ]);
    }

    public function project(): View
    {
        $seo = SeoData::make('Start a Project', 'Tell Softec Ai Solution about your project — goals, scope, budget and timeline — and receive a tailored proposal.')
            ->canonical(route('project.create'))
            ->breadcrumbs([['Contact', route('contact.create')], ['Start a project', null]]);

        return view('contact.project', [
            'services' => $this->services(),
            'budgets' => (array) settings('forms.budget_ranges', []),
            'timelines' => (array) settings('forms.timelines', []),
            'attachmentsEnabled' => (bool) settings('forms.attachments_enabled', true),
            'selectedService' => (int) request()->integer('service') ?: null,
            'seo' => $seo,
        ]);
    }

    public function store(ContactInquiryRequest $request, InquiryService $inquiries): RedirectResponse
    {
        $inquiry = $inquiries->submit($request->safe()->except(['privacy_consent', 'attachment']), $request, $request->file('attachment'));

        $route = $inquiry->type === InquiryType::Project ? 'project.create' : 'contact.create';

        return redirect()->route($route)->with('inquiry_reference', $inquiry->reference);
    }

    /**
     * @return Collection<int|string, string>
     */
    private function services(): Collection
    {
        return Service::query()->published()->ordered()->pluck('title', 'id');
    }
}
