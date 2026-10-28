<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="Case studies" title="Real challenges, [thoughtful solutions]"
        intro="How we help organisations design, build and scale digital products — from discovery to launch and beyond." illustration="analytics" />
    <section class="section pt-12">
        <div class="container-site">
            <div class="flex flex-col gap-4">
                <x-site.filter-pills label="Filter by category" param="category" :options="$categories->pluck('name', 'slug')->all()" :active="$filters['category']" route="portfolio.index" />
                @if ($industries->isNotEmpty())
                    <x-site.filter-pills label="Filter by industry" param="industry" :options="$industries->pluck('name', 'slug')->all()" :active="$filters['industry']" route="portfolio.index" />
                @endif
            </div>
            @if ($portfolios->isEmpty())
                <div class="mt-12 rounded-panel border border-dashed border-border p-12 text-center">
                    <h2 class="heading-card">No case studies match this filter yet</h2>
                    <a href="{{ route('portfolio.index') }}" class="btn btn-secondary mt-6">Show all</a>
                </div>
            @else
                <div class="mt-12 grid gap-x-8 gap-y-14 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($portfolios as $portfolio)<x-cards.portfolio :portfolio="$portfolio" :index="$loop->index % 3" heading-level="h2" />@endforeach
                </div>
                {{ $portfolios->links('vendor.pagination.site') }}
            @endif
        </div>
    </section>
    <x-site.cta-band title="Your project could be [next]" />
</x-layout>
