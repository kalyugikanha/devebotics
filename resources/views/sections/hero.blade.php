@php
    $primary = $section->setting('primary_cta');
    $secondary = $section->setting('secondary_cta');
    $highlights = array_values(array_filter((array) $section->setting('highlights', [])));
    $technologies = $records instanceof \Illuminate\Support\Collection ? $records : collect();
@endphp
<section id="{{ $section->anchor ?: 'top' }}" class="dark stage -mt-[var(--header-height)] pt-[var(--header-height)]">
    {{-- Ambient layers --}}
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(120%_80%_at_50%_-10%,#1b1446_0%,#050816_55%)]" aria-hidden="true"></div>
    <div class="aurora ambient animate-aurora top-[-18rem] left-[-12rem] size-[42rem] bg-[#4f46e5]/45" aria-hidden="true"></div>
    <div class="aurora ambient animate-aurora top-[-6rem] right-[-14rem] size-[38rem] bg-[#0891b2]/30 [animation-delay:-8s]" aria-hidden="true"></div>
    <div class="aurora ambient animate-aurora bottom-[-20rem] left-1/3 size-[36rem] bg-[#06b6d4]/25 [animation-delay:-14s]" aria-hidden="true"></div>
    <div class="bg-grid-lines absolute inset-0 -z-10 opacity-60" aria-hidden="true"></div>
    <canvas class="absolute inset-0 -z-10 h-full w-full" data-constellation aria-hidden="true"></canvas>
    <div class="bg-noise pointer-events-none absolute inset-0 -z-10" aria-hidden="true"></div>

    <div class="container-site grid items-center gap-14 pt-12 pb-20 sm:pt-16 lg:grid-cols-[1.08fr_1fr] lg:gap-8 lg:pt-20 lg:pb-28">
        <div class="relative">
            @if ($section->eyebrow)
                <p class="hero-fade inline-flex items-center gap-2.5 rounded-full border border-white/12 bg-white/5 py-1.5 pr-4 pl-1.5 text-sm font-medium text-white/85 backdrop-blur">
                    <span class="rounded-full bg-[linear-gradient(120deg,#6d28d9,#4f46e5_55%,#2563eb)] px-2.5 py-0.5 text-xs font-semibold text-white">New</span>
                    {{ $section->eyebrow }}
                </p>
            @endif

            <h1 class="heading-display mt-7 text-white">{{ \App\Support\Typography::heroWords($section->title ?: settings('company.name')) }}</h1>

            @if (count($highlights) > 1)
                <p class="hero-fade mt-6 flex flex-wrap items-baseline gap-x-2.5 text-xl font-semibold text-white/80 sm:text-2xl" style="--i:1">
                    <span>Specialists in</span>
                    <span class="rotator" data-rotator aria-hidden="true">
                        @foreach ($highlights as $highlight)
                            <span data-rotator-word class="{{ $loop->first ? 'is-active' : '' }} text-gradient whitespace-nowrap">{{ $highlight }}</span>
                        @endforeach
                    </span>
                    <span class="sr-only">{{ implode(', ', $highlights) }}</span>
                </p>
            @endif

            @if ($section->subtitle)
                <p class="hero-fade mt-6 max-w-xl text-lg leading-relaxed text-white/70 sm:text-xl" style="--i:2">{{ $section->subtitle }}</p>
            @endif

            @php
                $primaryUrl = \App\Support\SafeUrl::make($primary['url'] ?? null);
                $secondaryUrl = \App\Support\SafeUrl::make($secondary['url'] ?? null);
            @endphp
            <div class="hero-fade mt-10 flex flex-wrap items-center gap-3" style="--i:3">
                @if ($primaryUrl && filled($primary['label'] ?? null))
                    <a href="{{ $primaryUrl }}" class="btn btn-primary btn-lg" data-magnetic="0.25">{{ $primary['label'] }} <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                @endif
                @if ($secondaryUrl && filled($secondary['label'] ?? null))
                    <a href="{{ $secondaryUrl }}" class="btn btn-glass btn-lg">
                        <span class="grid size-7 place-items-center rounded-full bg-white/10"><x-heroicon-s-squares-2x2 class="size-3.5" aria-hidden="true" /></span>
                        {{ $secondary['label'] }}
                    </a>
                @endif
            </div>

            @if ($technologies->isNotEmpty())
                <div class="hero-fade mt-12 border-t border-white/10 pt-6" style="--i:4">
                    <p class="text-xs font-semibold tracking-[0.2em] text-white/50 uppercase">Built with modern, proven technology</p>
                    <ul class="mt-4 flex flex-wrap gap-x-6 gap-y-3 text-sm font-semibold text-white/75">
                        @foreach ($technologies as $technology)
                            <li class="flex items-center gap-2"><x-ui.tech-logo :technology="$technology" class="size-7 rounded-lg text-[0.625rem]" />{{ $technology->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="hero-fade relative" style="--i:2">
            @if ($section->media)
                <div class="relative mx-auto max-w-xl overflow-hidden rounded-panel border border-white/10 shadow-2xl" data-tilt="6">
                    <x-media.image :media="$section->media" sizes="(min-width: 1024px) 45vw, 100vw" class="h-full w-full object-cover" :eager="true" />
                </div>
            @else
                <x-art.hero-scene />
            @endif
        </div>
    </div>

    <div class="absolute bottom-6 left-1/2 hidden -translate-x-1/2 lg:block" aria-hidden="true">
        <span class="grid h-9 w-6 place-items-start justify-center rounded-full border border-white/25 p-1.5"><span class="h-2 w-1 animate-bounce-soft rounded-full bg-white/70"></span></span>
    </div>
</section>
