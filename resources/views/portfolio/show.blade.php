@php $metrics = $portfolio->verifiedMetrics(); @endphp
<x-layout :seo="$seo" :is-preview="$isPreview">
    <x-site.page-hero :seo="$seo" :eyebrow="$portfolio->category?->name ?? 'Case study'" :title="$portfolio->title" :intro="$portfolio->summary"
        :media="$portfolio->heroMedia" :illustration="\App\Support\Art\IllustrationRegistry::resolve($portfolio->illustration, $portfolio->industry?->slug, $portfolio->industry?->icon, 'analytics')" :demo="$portfolio->is_demo" />

    <section class="border-b border-border bg-surface">
        <dl class="container-site grid gap-6 py-8 text-sm sm:grid-cols-2 lg:grid-cols-4">
            @if ($client = $portfolio->clientDisplayName())<div><dt class="text-muted">Client</dt><dd class="mt-1 font-semibold">{{ $client }}</dd></div>@endif
            @if ($portfolio->industry)<div><dt class="text-muted">Industry</dt><dd class="mt-1 font-semibold"><a href="{{ $portfolio->industry->publicUrl() }}" class="hover:text-primary">{{ $portfolio->industry->name }}</a></dd></div>@endif
            @if ($portfolio->show_timeline && ($portfolio->duration || $portfolio->completed_on))
                <div><dt class="text-muted">Timeline</dt><dd class="mt-1 font-semibold">{{ $portfolio->duration }}{{ $portfolio->completed_on ? ' · '.$portfolio->completed_on->format('Y') : '' }}</dd></div>
            @endif
            @if ($portfolio->services->isNotEmpty())<div><dt class="text-muted">Services</dt><dd class="mt-1 font-semibold">{{ $portfolio->services->pluck('title')->take(2)->implode(', ') }}</dd></div>@endif
        </dl>
    </section>

    <section class="section">
        <div class="container-site grid gap-14 lg:grid-cols-[1.6fr_1fr]">
            <div class="space-y-14">
                @foreach (['The challenge' => $challenge, 'Our solution' => $solution, 'The outcome' => $outcome] as $heading => $html)
                    @if ($html)
                        <div data-reveal>
                            <p class="eyebrow">{{ str_replace('The ', '', $heading) }}</p>
                            <h2 class="heading-section mt-3 text-3xl">{{ $heading }}</h2>
                            <div class="prose-brand mt-5">{!! $html !!}</div>
                        </div>
                    @endif
                @endforeach
            </div>
            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                @if ($metrics)
                    <div class="rounded-panel bg-ink p-7 text-white" data-reveal>
                        <h2 class="text-sm font-semibold tracking-[0.14em] text-white/70 uppercase">Results</h2>
                        <dl class="mt-5 space-y-5">
                            @foreach ($metrics as $metric)
                                <div><dd class="font-display text-3xl font-extrabold text-gradient">{{ $metric['value'] }}</dd><dt class="text-sm text-white/75">{{ $metric['label'] }}</dt></div>
                            @endforeach
                        </dl>
                    </div>
                @endif
                @if ($portfolio->technologies->isNotEmpty())
                    <div class="card card-muted" data-reveal>
                        <h2 class="text-sm font-semibold tracking-[0.14em] text-muted uppercase">Technologies</h2>
                        <ul class="mt-4 flex flex-wrap gap-2">@foreach ($portfolio->technologies as $technology)<x-ui.tech-chip :technology="$technology" />@endforeach</ul>
                    </div>
                @endif
                @if ($portfolio->project_url)
                    <a href="{{ \App\Support\SafeUrl::make($portfolio->project_url) }}" target="_blank" rel="noopener" class="btn btn-secondary w-full">Visit project <x-heroicon-m-arrow-top-right-on-square class="size-4" aria-hidden="true" /></a>
                @endif
            </aside>
        </div>
    </section>

    @if ($portfolio->galleryItems->isNotEmpty())
        <section class="section section-muted">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Gallery" title="Project [highlights]" />
                <ul class="mt-12 grid gap-6 md:grid-cols-2">
                    @foreach ($portfolio->galleryItems as $item)
                        <li data-reveal data-image-reveal><figure class="overflow-hidden rounded-panel border border-border bg-background"><x-media.image :media="$item->media" sizes="(min-width: 768px) 50vw, 100vw" class="w-full" />@if ($item->caption)<figcaption class="px-5 py-3 text-sm text-muted">{{ $item->caption }}</figcaption>@endif</figure></li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($more->isNotEmpty())
        <section class="section">
            <div class="container-site">
                <x-ui.section-heading eyebrow="More work" title="Other [case studies]" />
                <div class="mt-12 grid gap-10 md:grid-cols-2">@foreach ($more as $item)<x-cards.portfolio :portfolio="$item" :index="$loop->index" />@endforeach</div>
            </div>
        </section>
    @endif

    <x-site.cta-band title="Facing a [similar challenge]?" />
</x-layout>
