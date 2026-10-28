<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterService;
use App\Support\Seo\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function store(NewsletterRequest $request, NewsletterService $newsletter): RedirectResponse
    {
        abort_unless(settings('features.newsletter_enabled'), 404);

        $newsletter->subscribe((string) $request->validated('email'), $request, 'footer');

        // Same response whether or not the address already existed.
        return back()->with('newsletter_status', 'Thanks! Please check your inbox to confirm your subscription.')->withFragment('newsletter');
    }

    public function confirm(Request $request, NewsletterSubscriber $subscriber, NewsletterService $newsletter): View
    {
        $newsletter->confirm($subscriber, $request);

        return view('newsletter.status', [
            'title' => 'Subscription confirmed',
            'message' => 'Thank you — you will now receive occasional insights from '.settings('company.name').'. You can unsubscribe at any time.',
            'seo' => SeoData::make('Subscription confirmed')->noindex(),
        ]);
    }

    public function unsubscribe(Request $request, NewsletterSubscriber $subscriber, NewsletterService $newsletter): View
    {
        $newsletter->unsubscribe($subscriber, $request);

        return view('newsletter.status', [
            'title' => 'You have been unsubscribed',
            'message' => 'You will no longer receive newsletter emails from us.',
            'seo' => SeoData::make('Unsubscribed')->noindex(),
        ]);
    }
}
