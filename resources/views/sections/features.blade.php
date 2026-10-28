@php $spots = \App\Support\Art\SpotRegistry::distinct($records->map(fn ($item) => $item['title'].' '.($item['description'] ?? ''))->values()->all()); @endphp
<x-site.section :section="$section">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />
    <ul class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-{{ $records->count() % 3 === 0 ? 3 : 4 }}">
        @foreach ($records as $item)
            <li class="card card-interactive group h-full overflow-hidden !p-0" data-reveal style="--i:{{ $loop->index }}">
                <x-spot :name="$spots[$loop->index]"
                        class="aspect-[20/11] w-full border-b border-border/70 bg-surface/60 p-3 transition duration-700 ease-out-expo group-hover:scale-[1.03]" />
                <div class="p-6">
                    <span class="icon-tile-soft size-10 rounded-lg"><x-ui.icon :name="$item['icon'] ?? null" /></span>
                    <h3 class="heading-card mt-4">{{ $item['title'] }}</h3>
                    @if (filled($item['description'] ?? null))
                        <p class="mt-2 text-[0.9375rem] text-muted">{{ $item['description'] }}</p>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</x-site.section>
