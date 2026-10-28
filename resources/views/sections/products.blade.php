<x-site.section :section="$section">
    <div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
        <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" align="left" id="section-heading-{{ $section->id }}" />
        <a href="{{ route('products.index') }}" class="btn btn-secondary shrink-0" data-reveal>All products <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
    </div>
    <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($records as $product)
            <x-cards.product :product="$product" :index="$loop->index" />
        @endforeach
    </div>
</x-site.section>
