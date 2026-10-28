{{-- Grid of {title, description, icon} items (benefits, features, challenges, ...). --}}
@props(['items' => [], 'columns' => 3, 'variant' => 'card', 'icon' => null, 'visual' => false])
@php
    $items = collect($items)->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null))->values();
    // When every item repeats the same icon (or none), pick a relevant icon per item from its title.
    $sharedIcon = $items->pluck('icon')->filter()->unique()->count() <= 1;
    $iconFor = fn (array $item) => $sharedIcon
        ? \App\Support\Art\SpotRegistry::iconFor($item['title'], $item['icon'] ?? $icon ?? 'sparkles')
        : ($item['icon'] ?? $icon);
    $spots = $visual ? \App\Support\Art\SpotRegistry::distinct($items->map(fn (array $item) => $item['title'].' '.($item['description'] ?? ''))->all()) : [];
@endphp
@if ($items->isNotEmpty())
    <ul {{ $attributes->merge(['class' => 'grid gap-5 sm:grid-cols-2 '.($columns >= 3 ? 'lg:grid-cols-3' : '')]) }}>
        @foreach ($items as $item)
            @if ($variant === 'check')
                <li class="flex gap-3 rounded-2xl border border-border bg-background p-4" data-reveal style="--i:{{ $loop->index }}">
                    <x-heroicon-s-check-circle class="mt-0.5 size-5 shrink-0 text-brand-mint" aria-hidden="true" />
                    <span>
                        <span class="block font-semibold text-foreground">{{ $item['title'] }}</span>
                        @if (filled($item['description'] ?? null))<span class="mt-1 block text-sm text-muted">{{ $item['description'] }}</span>@endif
                    </span>
                </li>
            @elseif ($visual)
                <li class="card card-interactive group h-full overflow-hidden !p-0" data-reveal style="--i:{{ $loop->index }}">
                    <x-spot :name="$spots[$loop->index]"
                            class="aspect-[20/11] w-full border-b border-border/70 bg-surface/60 p-3 transition duration-700 ease-out-expo group-hover:scale-[1.03]" />
                    <div class="flex items-start gap-3 p-5">
                        <span class="icon-tile-soft size-9 rounded-lg"><x-ui.icon :name="$iconFor($item)" class="size-[1.1rem]" /></span>
                        <div>
                            <h3 class="heading-card text-base">{{ $item['title'] }}</h3>
                            @if (filled($item['description'] ?? null))<p class="mt-1.5 text-sm text-muted">{{ $item['description'] }}</p>@endif
                        </div>
                    </div>
                </li>
            @else
                <li class="card h-full" data-reveal style="--i:{{ $loop->index }}">
                    <span class="icon-tile-soft"><x-ui.icon :name="$iconFor($item)" /></span>
                    <h3 class="heading-card mt-4 text-base">{{ $item['title'] }}</h3>
                    @if (filled($item['description'] ?? null))<p class="mt-2 text-sm text-muted">{{ $item['description'] }}</p>@endif
                </li>
            @endif
        @endforeach
    </ul>
@endif
