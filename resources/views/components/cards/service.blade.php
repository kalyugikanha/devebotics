@props(['service', 'index' => 0, 'headingLevel' => 'h3', 'variant' => 'default'])
@php $illustration = \App\Support\Art\IllustrationRegistry::resolve($service->illustration, $service->slug, $service->icon); @endphp
@if ($variant === 'wide')
    <article class="card card-interactive group grid h-full overflow-hidden p-0 sm:p-0 md:grid-cols-[1fr_1.1fr]" data-reveal style="--i:{{ $index }}">
        <div class="flex flex-col p-7 sm:p-9">
            <div class="flex items-center justify-between gap-4">
                <span class="icon-tile"><x-ui.icon :name="$service->icon" /></span>
                <x-ui.demo-badge :show="$service->is_demo" />
            </div>
            @if ($service->category)
                <p class="mt-7 text-xs font-semibold tracking-[0.16em] text-primary uppercase">{{ $service->category->name }}</p>
            @endif
            <{{ $headingLevel }} class="mt-2 font-display text-2xl font-bold tracking-tight sm:text-[1.75rem]">
                <a href="{{ $service->publicUrl() }}" class="card-link focus-visible:outline-none">{{ $service->title }}</a>
            </{{ $headingLevel }}>
            <p class="mt-3 flex-1 text-[0.98rem] leading-relaxed text-muted">{{ $service->excerpt }}</p>
            <span class="link-arrow mt-7">Explore service <x-heroicon-m-arrow-right aria-hidden="true" /></span>
        </div>
        <x-art.frame :name="$illustration" class="min-h-64 md:min-h-full" />
    </article>
@elseif ($variant === 'feature')
    <article class="card card-interactive group flex h-full flex-col overflow-hidden p-0 sm:p-0" data-reveal style="--i:{{ $index }}">
        <x-art.frame :name="$illustration" class="aspect-[16/10]" />
        <div class="flex flex-1 flex-col p-6 sm:p-7">
            <div class="flex items-start justify-between gap-3">
                <{{ $headingLevel }} class="heading-card text-xl">
                    <a href="{{ $service->publicUrl() }}" class="card-link focus-visible:outline-none">{{ $service->title }}</a>
                </{{ $headingLevel }}>
                <x-ui.demo-badge :show="$service->is_demo" />
            </div>
            <p class="mt-3 flex-1 text-[0.9375rem] text-muted">{{ $service->excerpt }}</p>
            <span class="link-arrow mt-6">Explore service <x-heroicon-m-arrow-right aria-hidden="true" /></span>
        </div>
    </article>
@else
    <article class="card card-interactive group flex h-full flex-col" data-reveal style="--i:{{ $index }}">
        <div class="flex items-start justify-between gap-4">
            <span class="icon-tile transition duration-500 ease-out-expo group-hover:scale-110 group-hover:-rotate-6"><x-ui.icon :name="$service->icon" /></span>
            <x-ui.demo-badge :show="$service->is_demo" />
        </div>
        <{{ $headingLevel }} class="heading-card mt-6">
            <a href="{{ $service->publicUrl() }}" class="card-link focus-visible:outline-none">{{ $service->title }}</a>
        </{{ $headingLevel }}>
        <p class="mt-3 flex-1 text-[0.9375rem] text-muted">{{ $service->excerpt }}</p>
        <span class="link-arrow mt-6">Explore service <x-heroicon-m-arrow-right aria-hidden="true" /></span>
    </article>
@endif
