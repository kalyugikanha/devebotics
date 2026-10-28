@php
    $all = $records->flatten(1)->values();
    $half = (int) ceil($all->count() / 2);
    $rows = $all->count() >= 10 ? [$all->take($half)->values(), $all->slice($half)->values()] : [$all, $all->reverse()->values()];
@endphp
<x-site.section :section="$section" class="overflow-hidden">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />

    <div class="mt-14 space-y-4" data-reveal>
        @foreach ($rows as $rowIndex => $row)
            <div class="marquee overflow-hidden">
                <ul class="{{ $rowIndex === 0 ? 'marquee-track' : 'marquee-track-reverse' }} !gap-4 py-2" @if ($rowIndex === 1 && $all->count() < 10) aria-hidden="true" @endif>
                    @foreach ([$row, $row] as $pass => $set)
                        @foreach ($set as $technology)
                            <li @if ($pass === 1) aria-hidden="true" @endif class="tech-card">
                                <x-ui.tech-logo :technology="$technology" class="size-11 rounded-xl text-xs" />
                                <span>
                                    <span class="block text-[0.9375rem] font-semibold text-foreground">{{ $technology->name }}</span>
                                    <span class="block text-xs text-muted">{{ $technology->category->getLabel() }}</span>
                                </span>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <ul class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Technologies by discipline">
        @foreach ($records as $label => $technologies)
            <li class="card card-interactive h-full p-5" data-reveal style="--i:{{ $loop->index % 4 }}">
                <div class="flex items-center justify-between gap-3">
                    <span class="icon-tile-soft size-10"><x-ui.icon :name="$technologies->first()->category->getIcon()" /></span>
                    <span class="text-xs font-medium text-muted">{{ $technologies->count() }} {{ \Illuminate\Support\Str::plural('tool', $technologies->count()) }}</span>
                </div>
                <h3 class="heading-card mt-4 text-base">{{ $label }}</h3>
                <div class="mt-4 flex -space-x-2">
                    @foreach ($technologies->take(5) as $technology)
                        <x-ui.tech-logo :technology="$technology" class="size-9 rounded-full text-[0.625rem] ring-[3px] ring-background" />
                    @endforeach
                </div>
                <p class="mt-3 text-sm leading-relaxed text-muted">{{ $technologies->pluck('name')->join(', ') }}</p>
            </li>
        @endforeach
    </ul>
</x-site.section>
