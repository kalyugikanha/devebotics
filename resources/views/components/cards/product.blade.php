@props(['product', 'index' => 0, 'headingLevel' => 'h3'])
@php $illustration = \App\Support\Art\IllustrationRegistry::resolve($product->illustration, $product->slug, $product->features[0]['icon'] ?? null, 'saas'); @endphp
<article class="card card-interactive group flex h-full flex-col overflow-hidden p-0 sm:p-0" data-reveal style="--i:{{ $index }}">
    <div class="relative aspect-[16/10] overflow-hidden">
        @if ($product->heroMedia)
            <x-media.image :media="$product->heroMedia" sizes="(min-width: 1024px) 33vw, 100vw" class="h-full w-full object-cover transition duration-700 ease-out-expo group-hover:scale-105" />
        @else
            <x-art.frame :name="$illustration" class="h-full" />
        @endif
        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
            <span class="badge border-transparent bg-foreground/85 text-background backdrop-blur">{{ $product->availability->getLabel() }}</span>
            <x-ui.demo-badge :show="$product->is_demo" />
        </div>
    </div>
    <div class="flex flex-1 flex-col p-6 sm:p-7">
        <div class="flex items-center gap-3">
            @if ($product->logo)
                <x-media.image :media="$product->logo" sizes="40px" class="size-10 rounded-xl object-contain" />
            @endif
            <div>
                @if ($product->category)
                    <p class="text-xs font-semibold tracking-[0.14em] text-primary uppercase">{{ $product->category->name }}</p>
                @endif
                <{{ $headingLevel }} class="heading-card text-xl">
                    <a href="{{ $product->publicUrl() }}" class="card-link focus-visible:outline-none">{{ $product->name }}</a>
                </{{ $headingLevel }}>
            </div>
        </div>
        <p class="mt-3 flex-1 text-[0.9375rem] text-muted">{{ $product->summary }}</p>
        <span class="link-arrow mt-6">View product <x-heroicon-m-arrow-right aria-hidden="true" /></span>
    </div>
</article>
