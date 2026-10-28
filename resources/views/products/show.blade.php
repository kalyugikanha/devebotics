@php
    $quoteUrl = route('project.create');
@endphp
<x-layout :seo="$seo" :is-preview="$isPreview">
    <x-site.page-hero :seo="$seo" :eyebrow="$product->category?->name ?? 'Product'" :title="$product->name" :intro="$product->tagline ?: $product->summary"
        :media="$product->heroMedia" :illustration="\App\Support\Art\IllustrationRegistry::resolve($product->illustration, $product->slug, $product->features[0]['icon'] ?? null, 'saas')" :demo="$product->is_demo">
        @if ($product->has_demo)
            <a href="{{ $quoteUrl }}?product={{ $product->id }}" class="btn btn-primary btn-lg">Request a demo <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
        @else
            <a href="{{ $quoteUrl }}" class="btn btn-primary btn-lg">{{ $product->pricing_model === \App\Enums\PricingModel::QuoteOnly ? 'Request a quote' : 'Contact sales' }} <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
        @endif
        @if ($product->website_url)
            <a href="{{ \App\Support\SafeUrl::make($product->website_url) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-lg">Visit product site</a>
        @endif
    </x-site.page-hero>

    <section class="border-b border-border bg-surface">
        <dl class="container-site grid gap-6 py-8 text-sm sm:grid-cols-2 lg:grid-cols-4">
            <div><dt class="text-muted">Availability</dt><dd class="mt-1 font-semibold text-foreground">{{ $product->availability->getLabel() }}</dd></div>
            <div><dt class="text-muted">Pricing</dt><dd class="mt-1 font-semibold text-foreground">{{ $product->showsPlans() && $product->plans->isNotEmpty() ? 'See plans below' : $product->pricing_model->getLabel() }}</dd></div>
            @if ($product->has_free_trial)
                <div><dt class="text-muted">Free trial</dt><dd class="mt-1 font-semibold text-foreground">{{ $product->trial_days ? $product->trial_days.' days' : 'Available' }}</dd></div>
            @endif
            @if ($product->industries->isNotEmpty())
                <div><dt class="text-muted">Industries</dt><dd class="mt-1 font-semibold text-foreground">{{ $product->industries->pluck('name')->take(3)->implode(', ') }}</dd></div>
            @endif
        </dl>
    </section>

    <section class="section">
        <div class="container-site grid gap-14 lg:grid-cols-[1.5fr_1fr]">
            <div>
                <p class="font-display text-2xl leading-snug font-semibold text-foreground" data-reveal>{{ $product->summary }}</p>
                @if ($description)
                    <div class="prose-brand mt-8" data-reveal>{!! $description !!}</div>
                @endif
                @if ($product->demo_video_url)
                    <x-site.video :url="$product->demo_video_url" :title="$product->name.' demo video'" class="mt-10" />
                @endif
            </div>
            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                @if ($product->target_audience)
                    <div class="card card-muted" data-reveal>
                        <h2 class="text-sm font-semibold tracking-[0.14em] text-muted uppercase">Who it is for</h2>
                        <p class="mt-3 text-[0.9375rem]">{{ $product->target_audience }}</p>
                    </div>
                @endif
                @if ($product->technologies->isNotEmpty())
                    <div class="card card-muted" data-reveal>
                        <h2 class="text-sm font-semibold tracking-[0.14em] text-muted uppercase">Built with</h2>
                        <ul class="mt-4 flex flex-wrap gap-2">
                            @foreach ($product->technologies as $technology)<x-ui.tech-chip :technology="$technology" />@endforeach
                        </ul>
                    </div>
                @endif
                @if ($product->documentation_url || $product->support_url)
                    <div class="card card-muted space-y-2" data-reveal>
                        <h2 class="text-sm font-semibold tracking-[0.14em] text-muted uppercase">Resources</h2>
                        @if ($product->documentation_url)<a href="{{ \App\Support\SafeUrl::make($product->documentation_url) }}" target="_blank" rel="noopener" class="link-arrow">Documentation <x-heroicon-m-arrow-top-right-on-square aria-hidden="true" /></a>@endif
                        @if ($product->support_url)<a href="{{ \App\Support\SafeUrl::make($product->support_url) }}" target="_blank" rel="noopener" class="link-arrow block">Support <x-heroicon-m-arrow-top-right-on-square aria-hidden="true" /></a>@endif
                    </div>
                @endif
            </aside>
        </div>
    </section>

    @if ($product->features)
        <section class="section section-muted" aria-labelledby="features-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Features" title="Everything you need, [nothing you don’t]" id="features-heading" />
                <x-site.item-grid :items="$product->features" class="mt-12" icon="check-badge" visual />
            </div>
        </section>
    @endif

    @if ($product->galleryItems->isNotEmpty())
        <section class="section" aria-labelledby="gallery-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Screenshots" title="See it [in action]" id="gallery-heading" />
                <ul class="mt-12 grid gap-6 md:grid-cols-2">
                    @foreach ($product->galleryItems as $item)
                        <li data-reveal style="--i:{{ $loop->index }}">
                            <figure class="overflow-hidden rounded-panel border border-border bg-surface shadow-lg">
                                <x-media.image :media="$item->media" sizes="(min-width: 768px) 50vw, 100vw" class="w-full" />
                                @if ($item->caption)<figcaption class="px-5 py-3 text-sm text-muted">{{ $item->caption }}</figcaption>@endif
                            </figure>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($product->benefits)
        <section class="section {{ $product->galleryItems->isNotEmpty() ? 'section-muted' : '' }}" aria-labelledby="benefits-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Benefits" title="Why teams [choose] {{ $product->name }}" id="benefits-heading" />
                <x-site.item-grid :items="$product->benefits" class="mt-12" />
            </div>
        </section>
    @endif

    @if ($product->showsPlans() && $product->plans->isNotEmpty())
        <section class="section" aria-labelledby="pricing-heading" id="pricing">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Pricing" title="Simple, [transparent] plans" id="pricing-heading" />
                <ul class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-{{ min(4, max(2, $product->plans->count())) }}">
                    @foreach ($product->plans as $plan)
                        <li class="flex h-full flex-col rounded-panel border p-7 {{ $plan->is_highlighted ? 'border-primary/40 bg-primary/5 shadow-xl' : 'border-border bg-background' }}" data-reveal style="--i:{{ $loop->index }}">
                            <h3 class="font-display text-lg font-bold">{{ $plan->name }}</h3>
                            @if ($plan->formattedPrice())
                                <p class="mt-4"><span class="font-display text-4xl font-extrabold">{{ $plan->formattedPrice() }}</span><span class="text-muted">{{ $plan->billing_period->suffix() }}</span></p>
                            @else
                                <p class="mt-4 font-display text-2xl font-extrabold">Custom</p>
                            @endif
                            @if ($plan->description)<p class="mt-3 text-sm text-muted">{{ $plan->description }}</p>@endif
                            <ul class="mt-6 flex-1 space-y-2.5 text-[0.9375rem]">
                                @foreach ((array) $plan->features as $feature)
                                    <li class="flex gap-2.5"><x-heroicon-s-check-circle class="mt-0.5 size-5 shrink-0 text-primary" aria-hidden="true" />{{ is_array($feature) ? ($feature['title'] ?? '') : $feature }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ \App\Support\SafeUrl::make($plan->cta_url) ?? $quoteUrl }}" class="btn mt-8 w-full {{ $plan->is_highlighted ? 'btn-primary' : 'btn-secondary' }}">{{ $plan->cta_label ?: 'Get started' }}</a>
                        </li>
                    @endforeach
                </ul>
                @if ($product->pricing_note)<p class="mt-6 text-center text-sm text-muted">{{ $product->pricing_note }}</p>@endif
            </div>
        </section>
    @elseif ($product->pricing_note)
        <section class="section pt-0"><div class="container-site"><p class="alert alert-info"><x-heroicon-o-information-circle class="size-5 shrink-0" aria-hidden="true" />{{ $product->pricing_note }}</p></div></section>
    @endif

    <x-site.faq-list :faqs="$product->faqs" :title="'Questions about '.$product->name" />

    @if ($product->relatedProducts->isNotEmpty())
        <section class="section section-muted">
            <div class="container-site">
                <x-ui.section-heading eyebrow="More products" title="Related [solutions]" />
                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($product->relatedProducts as $related)<x-cards.product :product="$related" :index="$loop->index" />@endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="pt-20 md:pt-28">
        <x-site.cta-band :title="'See [' . $product->name . '] in action'" text="Book a walkthrough with our team and discuss how it can be adapted to your workflows." primary-label="Request a demo" :primary-url="$quoteUrl" />
    </div>
</x-layout>
