<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="IT services" title="End-to-end IT services for [ambitious organisations]"
        intro="From strategy and design to engineering, AI, cloud and long-term support — one partner for the full lifecycle of your digital products." illustration="software">
        <a href="{{ route('project.create') }}" class="btn btn-primary btn-lg">Start a project <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
        <a href="{{ route('contact.create') }}" class="btn btn-secondary btn-lg">Talk to us</a>
    </x-site.page-hero>

    @if ($categories->count() > 1)
        <nav aria-label="Service categories" class="sticky top-[var(--header-height)] z-30 border-b border-border bg-background/85 backdrop-blur-xl">
            <ul class="container-site flex gap-2 overflow-x-auto py-3 [scrollbar-width:none]">
                @foreach ($categories as $category)
                    <li><a href="#{{ $category->slug }}" class="chip shrink-0 whitespace-nowrap hover:border-primary/40 hover:text-primary"><x-ui.icon :name="$category->icon" class="size-4 text-primary" />{{ $category->name }}</a></li>
                @endforeach
            </ul>
        </nav>
    @endif

    @foreach ($categories as $category)
        <section id="{{ $category->slug }}" class="section {{ $loop->even ? 'section-muted' : '' }}" aria-labelledby="cat-{{ $category->id }}">
            <div class="container-site">
                <div class="grid gap-6 md:grid-cols-[1fr_auto] md:items-end">
                    <div data-reveal>
                        <span class="icon-tile"><x-ui.icon :name="$category->icon" /></span>
                        <h2 id="cat-{{ $category->id }}" class="heading-section mt-5">{{ $category->name }}</h2>
                        @if ($category->description)<p class="lead mt-3 max-w-2xl">{{ $category->description }}</p>@endif
                    </div>
                </div>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($category->services as $service)
                        <x-cards.service :service="$service" :index="$loop->index" />
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    @if ($uncategorised->isNotEmpty())
        <section class="section">
            <div class="container-site grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($uncategorised as $service)
                    <x-cards.service :service="$service" :index="$loop->index" />
                @endforeach
            </div>
        </section>
    @endif

    <x-site.cta-band title="Not sure which service you need?" text="Describe your challenge and our architects will recommend the right combination of services." />
</x-layout>
