@props(['announcement' => null])
@if ($announcement)
    @php
        $styles = [
            'brand' => 'text-white bg-[linear-gradient(90deg,#3730a3,#4f46e5_50%,#0e7490)]',
            'neutral' => 'bg-ink text-white',
            'success' => 'bg-emerald-600 text-white',
            'warning' => 'bg-amber-400 text-ink',
        ];
    @endphp
    <div x-data="announcement" x-show="visible" data-dismiss-key="{{ $announcement->dismissKey() }}"
         class="relative z-50 {{ $styles[$announcement->style->value] ?? $styles['brand'] }} no-print" role="region" aria-label="Announcement">
        <div class="container-site flex min-h-10 items-center justify-center gap-3 py-2 pr-10 text-center text-sm font-medium">
            <x-heroicon-s-sparkles class="hidden size-4 shrink-0 opacity-80 sm:block" aria-hidden="true" />
            <p>
                {{ $announcement->message }}
                @if ($announcement->link_url && $announcement->link_label)
                    <a href="{{ $announcement->link_url }}" class="ml-1.5 inline-flex items-center gap-1 font-semibold underline decoration-white/40 underline-offset-4 hover:decoration-current">
                        {{ $announcement->link_label }}<span aria-hidden="true">→</span>
                    </a>
                @endif
            </p>
        </div>
        @if ($announcement->is_dismissible)
            <button type="button" x-on:click="dismiss" class="absolute top-1/2 right-2 grid size-8 -translate-y-1/2 place-items-center rounded-full hover:bg-white/15" aria-label="Dismiss announcement">
                <x-heroicon-m-x-mark class="size-4" aria-hidden="true" />
            </button>
        @endif
    </div>
@endif
