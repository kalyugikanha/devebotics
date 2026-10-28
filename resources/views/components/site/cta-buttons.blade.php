@props(['primary' => null, 'secondary' => null, 'inverse' => false])
@php
    $primaryUrl = \App\Support\SafeUrl::make($primary['url'] ?? null);
    $secondaryUrl = \App\Support\SafeUrl::make($secondary['url'] ?? null);
@endphp
@if (($primaryUrl && filled($primary['label'] ?? null)) || ($secondaryUrl && filled($secondary['label'] ?? null)))
    <div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-3']) }}>
        @if ($primaryUrl && filled($primary['label'] ?? null))
            <a href="{{ $primaryUrl }}" class="btn btn-lg {{ $inverse ? 'btn-inverse' : 'btn-primary' }}" @if (\App\Support\SafeUrl::isExternal($primaryUrl)) target="_blank" rel="noopener" @endif>
                {{ $primary['label'] }} <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" />
            </a>
        @endif
        @if ($secondaryUrl && filled($secondary['label'] ?? null))
            <a href="{{ $secondaryUrl }}" class="btn btn-lg {{ $inverse ? 'border border-white/30 text-white hover:bg-white/10' : 'btn-secondary' }}" @if (\App\Support\SafeUrl::isExternal($secondaryUrl)) target="_blank" rel="noopener" @endif>
                {{ $secondary['label'] }}
            </a>
        @endif
    </div>
@endif
