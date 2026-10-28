<x-site.section :section="$section">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />

    @if ($section->setting('layout', 'tabs') === 'tabs')
        <div x-data="tabs" class="mt-14" data-reveal>
            <div role="tablist" aria-label="Industries" x-on:keydown="onKeydown" class="-mx-5 flex gap-2 overflow-x-auto px-5 pb-3 [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:justify-center sm:px-0">
                @foreach ($records as $industry)
                    <button type="button" role="tab" id="industry-tab-{{ $industry->id }}" aria-controls="industry-panel-{{ $industry->id }}"
                            x-on:click="select({{ $loop->index }})" :aria-selected="isActive({{ $loop->index }})" :tabindex="tabIndex({{ $loop->index }})"
                            class="flex shrink-0 items-center gap-2 rounded-full border px-4 py-2.5 text-sm font-semibold transition duration-300"
                            :class="{ 'border-transparent bg-foreground text-background shadow-lg': isActive({{ $loop->index }}), 'border-border bg-background text-muted hover:border-primary/40 hover:text-foreground': !isActive({{ $loop->index }}) }">
                        <x-ui.icon :name="$industry->icon" fallback="building-office-2" class="size-4" />{{ $industry->name }}
                    </button>
                @endforeach
            </div>
            @foreach ($records as $industry)
                <div role="tabpanel" id="industry-panel-{{ $industry->id }}" aria-labelledby="industry-tab-{{ $industry->id }}" tabindex="0"
                     x-show="isActive({{ $loop->index }})" @if (! $loop->first) x-cloak @endif
                     x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-8 grid overflow-hidden rounded-[2rem] border border-border bg-background shadow-[0_30px_80px_-50px_rgba(30,20,80,0.5)] md:grid-cols-[1fr_1.05fr]">
                    <div class="p-7 sm:p-10">
                        <span class="icon-tile"><x-ui.icon :name="$industry->icon" fallback="building-office-2" /></span>
                        <h3 class="mt-6 font-display text-3xl font-bold tracking-tight">{{ $industry->name }}</h3>
                        <p class="mt-3 text-lg text-muted">{{ $industry->excerpt }}</p>
                        @if ($industry->solutions)
                            <ul class="mt-7 space-y-3.5">
                                @foreach (array_slice($industry->solutions, 0, 4) as $solution)
                                    <li class="flex gap-3"><x-heroicon-s-check-circle class="mt-0.5 size-5 shrink-0 text-emerald-500" aria-hidden="true" />{{ $solution['title'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if ($industry->services->isNotEmpty())
                            <div class="mt-7 flex flex-wrap gap-2">
                                @foreach ($industry->services as $service)
                                    <a href="{{ $service->publicUrl() }}" class="badge transition hover:border-primary/40 hover:text-primary">{{ $service->title }}</a>
                                @endforeach
                            </div>
                        @endif
                        <a href="{{ $industry->publicUrl() }}" class="btn btn-primary mt-8">Explore {{ $industry->name }} <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                    </div>
                    <div class="relative min-h-72">
                        {{-- Artwork of hidden panels is only added to the page when its tab is opened. --}}
                        @unless ($loop->first)<template x-if="isActive({{ $loop->index }})">@endunless
                        @if ($industry->heroMedia)
                            <x-media.image :media="$industry->heroMedia" sizes="(min-width: 1024px) 45vw, 100vw" class="absolute inset-0 h-full w-full object-cover" />
                        @else
                            <x-art.frame :name="\App\Support\Art\IllustrationRegistry::resolve($industry->illustration, $industry->slug, $industry->icon, 'professional')" class="absolute inset-0 flex items-center" />
                        @endif
                        @unless ($loop->first)</template>@endunless
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($records as $industry)
                <x-cards.industry :industry="$industry" :index="$loop->index" />
            @endforeach
        </div>
    @endif
</x-site.section>
