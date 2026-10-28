{{-- Uploaded image when available, otherwise generated brand artwork. --}}
@props(['media' => null, 'seed', 'icon' => null, 'sizes' => '(min-width: 1024px) 33vw, 100vw', 'eager' => false, 'alt' => null])
@if ($media)
    <x-media.image :media="$media" :sizes="$sizes" :eager="$eager" :alt="$alt" {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }} />
@else
    <x-art.cover :seed="$seed" :icon="$icon" :eager="$eager" {{ $attributes }} />
@endif
