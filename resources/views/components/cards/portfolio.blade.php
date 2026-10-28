@props(['portfolio', 'index' => 0, 'headingLevel' => 'h3'])
@php $illustration = \App\Support\Art\IllustrationRegistry::resolve($portfolio->illustration, $portfolio->industry?->slug, $portfolio->industry?->icon, 'analytics'); @endphp
<article class="group relative flex h-full flex-col" data-reveal style="--i:{{ $index }}">
    <div class="relative aspect-[4/3] overflow-hidden rounded-panel border border-border shadow-sm transition duration-700 ease-out-expo group-hover:-translate-y-1.5 group-hover:shadow-[0_30px_70px_-36px_rgba(79,70,229,0.55)]">
        @if ($portfolio->heroMedia)
            <x-media.image :media="$portfolio->heroMedia" sizes="(min-width: 1024px) 33vw, 100vw" class="h-full w-full object-cover transition duration-700 ease-out-expo group-hover:scale-105" />
        @else
            <x-art.frame :name="$illustration" class="h-full" />
        @endif
        <div class="absolute inset-x-4 bottom-4 flex flex-wrap gap-2">
            @if ($portfolio->industry)
                <span class="badge border-transparent bg-foreground/85 text-background backdrop-blur">{{ $portfolio->industry->name }}</span>
            @endif
            @if ($portfolio->category)
                <span class="badge bg-background/85 backdrop-blur">{{ $portfolio->category->name }}</span>
            @endif
            <x-ui.demo-badge :show="$portfolio->is_demo" />
        </div>
    </div>
    <div class="mt-5">
        @if ($label = $portfolio->clientDisplayName())
            <p class="text-sm font-medium text-muted">{{ $label }}</p>
        @endif
        <{{ $headingLevel }} class="heading-card mt-1 text-xl">
            <a href="{{ $portfolio->publicUrl() }}" class="card-link transition group-hover:text-primary focus-visible:outline-none">{{ $portfolio->title }}</a>
        </{{ $headingLevel }}>
        <p class="mt-2 line-clamp-2 text-[0.9375rem] text-muted">{{ $portfolio->summary }}</p>
        <span class="link-arrow mt-4">Read case study <x-heroicon-m-arrow-right aria-hidden="true" /></span>
    </div>
</article>
