<x-layout :seo="$seo">
    <section class="relative overflow-hidden border-b border-border">
        <div class="bg-grid absolute inset-0 opacity-60" aria-hidden="true"></div>
        <div class="orb ambient ambient-optional -top-24 right-0 size-96 animate-drift bg-brand-sky/20" aria-hidden="true"></div>
        <div class="container-site relative py-14 lg:py-20">
            <x-site.breadcrumbs :items="$seo->breadcrumbs" class="mb-6" />
            @isset($author)
                <div class="flex items-center gap-4">
                    <span class="grid size-16 place-items-center rounded-full bg-gradient-to-br from-brand-purple to-brand-blue text-xl font-bold text-white">{{ $author->initials() }}</span>
                    <div><p class="eyebrow">Author</p><p class="text-sm text-muted">{{ $author->job_title }}</p></div>
                </div>
            @else
                <p class="eyebrow">Insights</p>
            @endisset
            <h1 class="heading-display mt-4 text-4xl sm:text-5xl">{{ \App\Support\Typography::heroWords($heading) }}</h1>
            @if ($intro)<p class="lead hero-fade mt-5 max-w-2xl">{{ $intro }}</p>@endif

            <div class="mt-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <ul class="flex flex-wrap gap-2" aria-label="Categories">
                    <li><a href="{{ route('blog.index') }}" class="chip {{ empty($activeCategory) && ! isset($author) && request()->routeIs('blog.index') ? 'border-primary/40 bg-primary/10 text-primary' : '' }}">All</a></li>
                    @foreach ($categories as $category)
                        <li><a href="{{ $category->publicUrl() }}" class="chip {{ ($activeCategory ?? null) === $category->id ? 'border-primary/40 bg-primary/10 text-primary' : 'hover:border-primary/30' }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
                <form method="GET" action="{{ route('blog.index') }}" role="search" class="relative w-full lg:w-80">
                    <label for="blog-search" class="sr-only">Search articles</label>
                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-muted" aria-hidden="true" />
                    <input id="blog-search" type="search" name="q" value="{{ $search }}" placeholder="Search articles" class="form-control rounded-full pl-11">
                </form>
            </div>
        </div>
    </section>

    <section class="section pt-14">
        <div class="container-site">
            @if ($search !== '')
                <p class="mb-8 text-muted" role="status">{{ $posts->total() }} {{ \Illuminate\Support\Str::plural('result', $posts->total()) }} for “{{ $search }}”</p>
            @endif

            @if ($featured)
                <div class="mb-16 border-b border-border pb-16"><x-cards.post :post="$featured" featured heading-level="h2" /></div>
            @endif

            @if ($posts->isEmpty())
                <div class="rounded-panel border border-dashed border-border p-12 text-center">
                    <x-heroicon-o-newspaper class="mx-auto size-10 text-muted" aria-hidden="true" />
                    <h2 class="heading-card mt-4">No articles found</h2>
                    <p class="mt-2 text-muted">Try a different search or browse all insights.</p>
                    <a href="{{ route('blog.index') }}" class="btn btn-secondary mt-6">All insights</a>
                </div>
            @else
                <div class="grid gap-x-8 gap-y-14 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)<x-cards.post :post="$post" :index="$loop->index % 3" heading-level="h2" />@endforeach
                </div>
                {{ $posts->links('vendor.pagination.site') }}
            @endif
        </div>
    </section>
</x-layout>
