{{-- Crawlable link-based filter pills (work without JavaScript). --}}
@props(['label', 'param', 'options', 'active' => '', 'route'])
@if (count($options))
    <div {{ $attributes }}>
        <p class="sr-only">{{ $label }}</p>
        <ul class="flex flex-wrap gap-2">
            <li><a href="{{ request()->fullUrlWithQuery([$param => null, 'page' => null]) }}" class="chip {{ $active === '' ? 'border-primary/40 bg-primary/10 text-primary' : 'hover:border-primary/30' }}" @if ($active === '') aria-current="true" @endif>All</a></li>
            @foreach ($options as $value => $text)
                <li><a href="{{ request()->fullUrlWithQuery([$param => $value, 'page' => null]) }}" class="chip {{ $active === (string) $value ? 'border-primary/40 bg-primary/10 text-primary' : 'hover:border-primary/30' }}" @if ($active === (string) $value) aria-current="true" @endif>{{ $text }}</a></li>
            @endforeach
        </ul>
    </div>
@endif
