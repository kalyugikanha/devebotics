<x-layout :seo="$seo">
    <section class="relative overflow-hidden border-b border-border">
        <div class="bg-grid absolute inset-0 opacity-60" aria-hidden="true"></div>
        <div class="container-site relative max-w-3xl py-16 text-center lg:py-20">
            <p class="eyebrow justify-center">Search</p>
            <h1 class="heading-display mt-4 text-4xl sm:text-5xl">Search the site</h1>
            <form method="GET" action="{{ route('search') }}" role="search" class="relative mt-8">
                <label for="site-search" class="sr-only">Search</label>
                <x-heroicon-o-magnifying-glass class="pointer-events-none absolute top-1/2 left-5 size-6 -translate-y-1/2 text-muted" aria-hidden="true" />
                <input id="site-search" type="search" name="q" value="{{ $term }}" placeholder="Services, products, articles…" autofocus minlength="2" maxlength="80" class="form-control h-16 rounded-full pr-32 pl-14 text-lg">
                <button type="submit" class="btn btn-primary absolute top-1/2 right-2 -translate-y-1/2">Search</button>
            </form>
        </div>
    </section>
    <section class="section pt-12">
        <div class="container-site max-w-4xl">
            @if ($term !== '')
                <p class="text-muted" role="status">{{ $total }} {{ \Illuminate\Support\Str::plural('result', $total) }} for “{{ $term }}”</p>
                @forelse ($groups as $group => $items)
                    <div class="mt-10">
                        <h2 class="text-sm font-semibold tracking-[0.16em] text-muted uppercase">{{ $group }}</h2>
                        <ul class="mt-4 divide-y divide-border rounded-panel border border-border">
                            @foreach ($items as $item)
                                <li><a href="{{ $item['url'] }}" class="group block p-5 transition hover:bg-surface"><span class="font-semibold text-foreground group-hover:text-primary">{{ $item['title'] }}</span>@if ($item['excerpt'])<span class="mt-1 block text-sm text-muted">{{ $item['excerpt'] }}</span>@endif</a></li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="mt-10 rounded-panel border border-dashed border-border p-12 text-center">
                        <h2 class="heading-card">No results found</h2>
                        <p class="mt-2 text-muted">Try different keywords, or browse our <a href="{{ route('services.index') }}" class="font-semibold text-primary">services</a> and <a href="{{ route('blog.index') }}" class="font-semibold text-primary">insights</a>.</p>
                    </div>
                @endforelse
            @else
                <p class="text-center text-muted">Enter at least two characters to search services, products, case studies, articles and pages.</p>
            @endif
        </div>
    </section>
</x-layout>
