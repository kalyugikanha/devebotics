@props(['seo' => null, 'eyebrow' => null, 'title', 'intro' => null, 'art' => null, 'artIcon' => null, 'media' => null, 'demo' => false, 'illustration' => null])
@php $hasVisual = $media || $illustration || $art; @endphp
<section class="relative isolate overflow-hidden border-b border-border">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(70%_90%_at_0%_0%,rgba(139,92,246,0.16),transparent_60%),radial-gradient(60%_80%_at_100%_10%,rgba(34,211,238,0.14),transparent_60%),radial-gradient(50%_60%_at_60%_100%,rgba(236,72,153,0.10),transparent_60%)] dark:bg-[radial-gradient(70%_90%_at_0%_0%,rgba(139,92,246,0.28),transparent_60%),radial-gradient(60%_80%_at_100%_10%,rgba(34,211,238,0.16),transparent_60%),radial-gradient(50%_60%_at_60%_100%,rgba(236,72,153,0.14),transparent_60%)]" aria-hidden="true"></div>
    <div class="bg-grid-lines absolute inset-0 -z-10 opacity-70" aria-hidden="true"></div>
    <div class="orb ambient ambient-optional -top-32 -left-24 -z-10 size-96 animate-drift bg-brand-violet/25" aria-hidden="true"></div>

    <div class="container-site relative grid items-center gap-12 py-14 sm:py-16 lg:py-24 {{ $hasVisual ? 'lg:grid-cols-[1.1fr_1fr]' : '' }}">
        <div class="{{ $hasVisual ? '' : 'mx-auto max-w-4xl text-center' }}">
            @if ($seo)
                <x-site.breadcrumbs :items="$seo->breadcrumbs" class="mb-7 {{ $hasVisual ? '' : 'flex justify-center' }}" />
            @endif
            <div class="flex flex-wrap items-center gap-3 {{ $hasVisual ? '' : 'justify-center' }}">
                @if ($eyebrow)
                    <p class="eyebrow hero-fade">{{ $eyebrow }}</p>
                @endif
                <x-ui.demo-badge :show="$demo" class="hero-fade" />
            </div>
            <h1 class="heading-display mt-5 text-[2.5rem] sm:text-5xl lg:text-[3.6rem]">{{ \App\Support\Typography::heroWords($title) }}</h1>
            @if ($intro)
                <p class="lead hero-fade mt-6 max-w-2xl {{ $hasVisual ? '' : 'mx-auto' }}" style="--i:1">{{ $intro }}</p>
            @endif
            @if (trim($slot))
                <div class="hero-fade mt-9 flex flex-wrap gap-3 {{ $hasVisual ? '' : 'justify-center' }}" style="--i:2">{{ $slot }}</div>
            @endif
        </div>

        @if ($hasVisual)
            <div class="hero-fade relative" style="--i:2">
                <div class="absolute -inset-4 -z-10 rounded-[2.5rem] bg-[linear-gradient(135deg,rgba(139,92,246,0.25),rgba(236,72,153,0.15),rgba(34,211,238,0.2))] blur-2xl" aria-hidden="true"></div>
                <div class="relative overflow-hidden rounded-panel border border-border bg-background shadow-[0_40px_90px_-40px_rgba(30,20,80,0.55)]" data-tilt="5">
                    @if ($media)
                        <x-media.image :media="$media" sizes="(min-width: 1024px) 42vw, 100vw" class="aspect-[16/11] h-full w-full object-cover" :eager="true" />
                    @elseif ($illustration)
                        <x-art.frame :name="$illustration" class="aspect-[16/11]" />
                    @else
                        <div class="aspect-[16/11]"><x-art.cover :seed="$art" :icon="$artIcon" :eager="true" /></div>
                    @endif
                </div>
                <div class="ambient absolute -bottom-5 -left-3 hidden animate-float items-center gap-2.5 rounded-2xl border border-border bg-background/90 px-4 py-3 text-sm font-semibold shadow-xl backdrop-blur sm:flex" aria-hidden="true">
                    <span class="grid size-8 place-items-center rounded-lg bg-[linear-gradient(135deg,#6d28d9,#4f46e5_55%,#2563eb)] text-white"><x-heroicon-s-sparkles class="size-4" /></span>
                    {{ settings('company.name') }}
                </div>
            </div>
        @endif
    </div>
</section>
