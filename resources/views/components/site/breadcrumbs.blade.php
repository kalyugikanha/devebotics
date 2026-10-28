@props(['items' => []])
@if (count($items) > 1)
    <nav aria-label="Breadcrumb" {{ $attributes }}>
        <ol class="flex min-w-0 items-center gap-1.5 overflow-hidden text-sm whitespace-nowrap text-muted">
            @foreach ($items as $crumb)
                <li class="flex items-center gap-1.5 {{ $loop->last ? 'min-w-0' : 'shrink-0' }}">
                    @if (! $loop->first)
                        <x-heroicon-m-chevron-right class="size-4 opacity-60" aria-hidden="true" />
                    @endif
                    @if ($crumb['url'] && ! $loop->last)
                        <a href="{{ $crumb['url'] }}" class="transition hover:text-primary">{{ $crumb['label'] }}</a>
                    @else
                        <span aria-current="page" class="truncate font-medium text-foreground">{{ $crumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
