<x-site.section :section="$section">
    <div class="grid gap-14 lg:grid-cols-[0.85fr_1.4fr] lg:gap-16">
        <div class="lg:sticky lg:top-28 lg:self-start">
            <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" align="left" id="section-heading-{{ $section->id }}" />
            <div class="mt-10 hidden overflow-hidden rounded-panel border border-border lg:block" data-reveal>
                <x-art.frame name="strategy" tone="violet" class="aspect-[16/11]" />
            </div>
            <div class="mt-8 flex flex-wrap gap-3" data-reveal>
                <a href="{{ route('about') }}" class="btn btn-secondary">About us <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                <a href="{{ route('project.create') }}" class="btn btn-ghost">Start a project</a>
            </div>
        </div>
        <ul class="grid gap-5 sm:grid-cols-2">
            @foreach ($records as $item)
                <li class="card card-interactive group" data-reveal style="--i:{{ $loop->index }}">
                    <div class="flex items-center justify-between">
                        <span class="icon-tile transition duration-500 ease-out-expo group-hover:scale-110 group-hover:rotate-6"><x-ui.icon :name="$item['icon'] ?? null" /></span>
                        <span class="font-display text-4xl font-extrabold text-foreground/[0.07] transition group-hover:text-primary/20">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="heading-card mt-6">{{ $item['title'] }}</h3>
                    @if (filled($item['description'] ?? null))
                        <p class="mt-2 text-[0.9375rem] text-muted">{{ $item['description'] }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</x-site.section>
