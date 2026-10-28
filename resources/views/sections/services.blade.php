@php
    $items = $records->values();
    $layout = [0 => ['wide', 'lg:col-span-4'], 1 => ['feature', 'lg:col-span-2']];
@endphp
<x-site.section :section="$section">
    <div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
        <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" align="left" id="section-heading-{{ $section->id }}" />
        <a href="{{ route('services.index') }}" class="btn btn-secondary shrink-0" data-reveal>All services <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
    </div>
    <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-6 lg:gap-6">
        @foreach ($items as $service)
            @php [$variant, $span] = $layout[$loop->index] ?? (($loop->index === 5 && $loop->count === 6) ? ['wide', 'lg:col-span-6'] : ['default', 'lg:col-span-2']); @endphp
            <div class="{{ $span }} {{ $variant === 'wide' ? 'sm:col-span-2' : '' }}">
                <x-cards.service :service="$service" :index="$loop->index" :variant="$variant" />
            </div>
        @endforeach
    </div>
</x-site.section>
