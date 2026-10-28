@props(['industry', 'index' => 0, 'headingLevel' => 'h3'])
@php $illustration = \App\Support\Art\IllustrationRegistry::resolve($industry->illustration, $industry->slug, $industry->icon, 'professional'); @endphp
<article class="card card-interactive group flex h-full flex-col overflow-hidden p-0 sm:p-0" data-reveal style="--i:{{ $index }}">
    @if ($industry->heroMedia)
        <x-media.image :media="$industry->heroMedia" sizes="(min-width: 1024px) 33vw, 100vw" class="aspect-[16/10] w-full object-cover" />
    @else
        <x-art.frame :name="$illustration" class="aspect-[16/10]" />
    @endif
    <div class="flex flex-1 flex-col p-6 sm:p-7">
        <div class="flex items-center gap-3">
            <span class="icon-tile-soft"><x-ui.icon :name="$industry->icon" fallback="building-office-2" /></span>
            <{{ $headingLevel }} class="heading-card text-xl">
                <a href="{{ $industry->publicUrl() }}" class="card-link focus-visible:outline-none">{{ $industry->name }}</a>
            </{{ $headingLevel }}>
        </div>
        <p class="mt-3 flex-1 text-[0.9375rem] text-muted">{{ $industry->excerpt }}</p>
        <span class="link-arrow mt-5">Explore solutions <x-heroicon-m-arrow-right aria-hidden="true" /></span>
    </div>
</article>
