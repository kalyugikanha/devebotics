<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Models\Service;
use App\Models\Testimonial;
use App\Services\FeedbackService;
use App\Support\Seo\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function create(): View
    {
        abort_unless(settings('features.feedback_form_enabled'), 404);

        $seo = SeoData::make('Share Your Feedback', 'Tell us about your experience working with Softec Ai Solution.')
            ->canonical(route('feedback.create'))
            ->breadcrumbs([['Client reviews', route('testimonials.index')], ['Leave feedback', null]]);

        return view('feedback.create', [
            'services' => Service::query()->published()->ordered()->pluck('title', 'id'),
            'seo' => $seo,
        ]);
    }

    public function store(FeedbackRequest $request, FeedbackService $feedback): RedirectResponse
    {
        abort_unless(settings('features.feedback_form_enabled'), 404);

        $feedback->submit($request->validated(), $request, $request->file('photo'));

        return redirect()->route('feedback.create')->with('feedback_submitted', true);
    }

    public function verify(Testimonial $testimonial, FeedbackService $feedback): View
    {
        $feedback->markEmailVerified($testimonial);

        return view('feedback.verified', [
            'seo' => SeoData::make('Feedback confirmed')->noindex(),
        ]);
    }
}
