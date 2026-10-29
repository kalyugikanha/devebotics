<x-layout :seo="$seo" :is-preview="$isPreview">
    <x-site.page-hero :seo="$seo" :eyebrow="$service->category?->name ?? 'Service'" :title="$service->title" :intro="$service->tagline ?: $service->excerpt"
        :media="$service->heroMedia" :illustration="\App\Support\Art\IllustrationRegistry::resolve($service->illustration, $service->slug, $service->icon)" :demo="$service->is_demo">
        <a href="{{ route('project.create', ['service' => $service->id]) }}" class="btn btn-primary btn-lg">Request a consultation <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
        <a href="#overview" class="btn btn-secondary btn-lg">Learn more</a>
    </x-site.page-hero>

    <section id="overview" class="section">
        <div class="container-site grid gap-14 lg:grid-cols-[1.5fr_1fr]">
            <div>
                @if ($service->overview)
                    <p class="font-display text-2xl leading-snug font-semibold text-foreground sm:text-[1.75rem]" data-reveal>{{ $service->overview }}</p>
                @endif
                @if ($description)
                    <div class="prose-brand mt-8" data-reveal>{!! $description !!}</div>
                @endif
                @if ($service->video_url)
                    <x-site.video :url="$service->video_url" :title="$service->title.' video'" class="mt-10" />
                @endif
            </div>
            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                @if ($service->technologies->isNotEmpty())
                    <div class="card card-muted" data-reveal>
                        <h2 class="text-sm font-semibold tracking-[0.14em] text-muted uppercase">Technologies</h2>
                        <ul class="mt-4 flex flex-wrap gap-2">
                            @foreach ($service->technologies as $technology)
                                <x-ui.tech-chip :technology="$technology" />
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if ($service->industries->isNotEmpty())
                    <div class="card card-muted" data-reveal>
                        <h2 class="text-sm font-semibold tracking-[0.14em] text-muted uppercase">Industries</h2>
                        <ul class="mt-4 space-y-2">
                            @foreach ($service->industries as $industry)
                                <li><a href="{{ $industry->publicUrl() }}" class="flex items-center gap-2 text-sm font-medium hover:text-primary"><x-ui.icon :name="$industry->icon" class="size-4 text-primary" />{{ $industry->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="relative overflow-hidden rounded-card bg-ink p-6 text-white" data-reveal>
                    <div class="orb -top-12 -right-12 size-40 bg-brand-violet" aria-hidden="true"></div>
                    <p class="relative font-display text-xl font-bold">Discuss your project</p>
                    <p class="relative mt-2 text-sm text-white/75">Get a tailored proposal for {{ $service->title }}.</p>
                    <a href="{{ route('project.create', ['service' => $service->id]) }}" class="btn btn-inverse btn-sm relative mt-5">Get in touch <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                </div>
            </aside>
        </div>
    </section>

    @if ($service->challenges)
        <section class="section section-muted" aria-labelledby="challenges-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Challenges" title="Business challenges [we solve]" id="challenges-heading" />
                <x-site.item-grid :items="$service->challenges" class="mt-12" icon="exclamation-triangle" />
            </div>
        </section>
    @endif

    @if ($service->features)
        <section class="section" aria-labelledby="features-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Capabilities" title="Key [features]" id="features-heading" />
                <x-site.item-grid :items="$service->features" class="mt-12" :icon="$service->icon" visual />
            </div>
        </section>
    @endif

    @if ($service->benefits)
        <section class="section {{ $service->features ? 'pt-0 sm:pt-0 lg:pt-0' : '' }}" aria-labelledby="benefits-heading">
            <div class="container-site grid items-center gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:gap-16">
                <div>
                    <x-ui.section-heading eyebrow="Benefits" title="What you [gain]" align="left" id="benefits-heading" />
                    <x-site.item-grid :items="$service->benefits" variant="check" :columns="1" class="mt-10 !grid-cols-1" />
                </div>
                <div class="hidden lg:block" data-reveal>
                    <x-art.frame :name="\App\Support\Art\IllustrationRegistry::resolve($service->illustration, $service->slug, $service->icon)" class="aspect-[4/3] rounded-[2rem] border border-border shadow-[0_30px_80px_-50px_rgba(10,17,40,0.5)]" />
                </div>
            </div>
        </section>
    @endif

    @if ($service->process_steps)
        <section class="section section-muted" aria-labelledby="process-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Delivery" title="Our [process]" id="process-heading" />
                <ol class="mt-14 grid gap-6 md:grid-cols-{{ min(5, max(2, count($service->process_steps))) }}">
                    @foreach ($service->process_steps as $step)
                        <li class="card h-full" data-reveal style="--i:{{ $loop->index }}">
                            <span class="font-display text-3xl font-extrabold text-gradient">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="heading-card mt-3 text-base">{{ $step['title'] ?? '' }}</h3>
                            @if (filled($step['description'] ?? null))<p class="mt-2 text-sm text-muted">{{ $step['description'] }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    @if ($service->portfolios->isNotEmpty())
        <section class="section" aria-labelledby="cases-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Case studies" title="Related [work]" id="cases-heading" />
                <div class="mt-12 grid gap-10 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($service->portfolios as $portfolio)
                        <x-cards.portfolio :portfolio="$portfolio" :index="$loop->index" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (isset($relatedPosts) && $relatedPosts->isNotEmpty())
        <section class="section section-muted" aria-labelledby="service-insights-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Engineering perspectives" title="Expert guides & [insights]" id="service-insights-heading" />
                <div class="mt-12 grid gap-10 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedPosts as $post)
                        <x-cards.post :post="$post" :index="$loop->index" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.faq-list :faqs="$service->faqs" :title="'Questions about '.$service->title" />

    @if ($service->relatedServices->isNotEmpty())
        <section class="section section-muted" aria-labelledby="related-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Explore more" title="Related [services]" id="related-heading" />
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($service->relatedServices as $related)
                        <x-cards.service :service="$related" :index="$loop->index" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="pt-20 md:pt-28">
        <x-site.cta-band :title="$service->cta_title" :text="$service->cta_text" primary-label="Request a consultation" :primary-url="route('project.create', ['service' => $service->id])" />
    </div>
</x-layout>
