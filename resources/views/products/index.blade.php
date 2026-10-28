<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="Products" title="Software products & [solutions]"
        intro="Ready-to-adapt platforms and applications, configured and extended by our team to fit the way your organisation works." illustration="saas" />

    <section class="section pt-12">
        <div class="container-site">
            <form method="GET" action="{{ route('products.index') }}" class="grid gap-3 rounded-panel border border-border bg-surface p-4 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr_auto]" role="search" aria-label="Filter products">
                <label class="sr-only" for="product-search">Search products</label>
                <div class="relative">
                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-muted" aria-hidden="true" />
                    <input id="product-search" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search products" class="form-control pl-11">
                </div>
                <div x-data="autoSubmit">
                    <label class="sr-only" for="filter-category">Category</label>
                    <select id="filter-category" name="category" class="form-control" x-on:change="submit">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected($filters['category'] === $category->slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-data="autoSubmit">
                    <label class="sr-only" for="filter-industry">Industry</label>
                    <select id="filter-industry" name="industry" class="form-control" x-on:change="submit">
                        <option value="">All industries</option>
                        @foreach ($industries as $industry)
                            <option value="{{ $industry->slug }}" @selected($filters['industry'] === $industry->slug)>{{ $industry->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-data="autoSubmit">
                    <label class="sr-only" for="filter-availability">Availability</label>
                    <select id="filter-availability" name="availability" class="form-control" x-on:change="submit">
                        <option value="">Any availability</option>
                        @foreach ($availabilities as $availability)
                            <option value="{{ $availability->value }}" @selected($filters['availability'] === $availability->value)>{{ $availability->getLabel() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1">Apply</button>
                    @if (array_filter($filters))
                        <a href="{{ route('products.index') }}" class="btn btn-ghost">Reset</a>
                    @endif
                </div>
            </form>

            <p class="mt-6 text-sm text-muted" role="status">{{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }}</p>

            @if ($products->isEmpty())
                <div class="mt-10 rounded-panel border border-dashed border-border p-12 text-center">
                    <x-heroicon-o-cube class="mx-auto size-10 text-muted" aria-hidden="true" />
                    <h2 class="heading-card mt-4">No products match these filters</h2>
                    <p class="mt-2 text-muted">Try another category, or tell us what you are looking for — we also build custom software.</p>
                    <div class="mt-6 flex justify-center gap-3"><a href="{{ route('products.index') }}" class="btn btn-secondary">Clear filters</a><a href="{{ route('project.create') }}" class="btn btn-primary">Request custom software</a></div>
                </div>
            @else
                <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <x-cards.product :product="$product" :index="$loop->index % 3" heading-level="h2" />
                    @endforeach
                </div>
                {{ $products->links('vendor.pagination.site') }}
            @endif
        </div>
    </section>

    <x-site.cta-band title="Need something [built for you]?" text="Our products are a starting point — we also design and build custom software around your exact requirements." primary-label="Discuss custom software" />
</x-layout>
