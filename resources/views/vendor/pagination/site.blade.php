@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="mt-16 flex items-center justify-between gap-4">
        @if ($paginator->onFirstPage())
            <span class="btn btn-secondary btn-sm pointer-events-none opacity-50" aria-disabled="true"><x-heroicon-m-arrow-left class="size-4" aria-hidden="true" />Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-secondary btn-sm"><x-heroicon-m-arrow-left class="size-4" aria-hidden="true" />Previous</a>
        @endif

        <ul class="hidden items-center gap-1 sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="px-2 text-muted">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="grid size-10 place-items-center rounded-full bg-foreground text-sm font-semibold text-background">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="grid size-10 place-items-center rounded-full text-sm font-medium text-muted hover:bg-foreground/5 hover:text-foreground" aria-label="Page {{ $page }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-secondary btn-sm">Next<x-heroicon-m-arrow-right class="size-4" aria-hidden="true" /></a>
        @else
            <span class="btn btn-secondary btn-sm pointer-events-none opacity-50" aria-disabled="true">Next<x-heroicon-m-arrow-right class="size-4" aria-hidden="true" /></span>
        @endif
    </nav>
@endif
