@props(['seed', 'icon' => null, 'label' => '', 'eager' => false])
@php
    $params = array_filter([
        'seed' => \Illuminate\Support\Str::slug((string) $seed) ?: 'art',
        'i' => \App\Support\IconRegistry::isValid($icon) ? $icon : null,
        'l' => $label ?: null,
        'v' => \App\Support\Art\CoverArt::VERSION,
    ]);
@endphp
<img src="{{ route('art', $params) }}" alt="" width="640" height="400" decoding="async"
     loading="{{ $eager ? 'eager' : 'lazy' }}" @if ($eager) fetchpriority="high" @endif
     {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}>
