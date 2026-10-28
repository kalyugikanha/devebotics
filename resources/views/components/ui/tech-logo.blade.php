{{-- Technology mark: uploaded logo, bundled logo (resources/svg/tech), capability icon or initials tile. --}}
@props(['technology'])
@php $mark = \App\Support\Art\TechLogoRegistry::resolve($technology->slug, $technology->name, $technology->category); @endphp
<span {{ $attributes->merge(['class' => 'tech-logo'.($mark['type'] === 'initials' && ! $technology->logo ? ' is-initials' : '')]) }} style="--logo: {{ $mark['color'] }}; --logo-dark: {{ $mark['dark'] }}" aria-hidden="true">
    @if ($technology->logo)
        <x-media.image :media="$technology->logo" sizes="40px" class="size-[62%] object-contain" alt="" />
    @elseif ($mark['type'] === 'logo')
        {{ app(\App\Support\Art\TechLogoSprite::class)->render($mark['name'], 'size-[56%]') }}
    @elseif ($mark['type'] === 'icon')
        <x-ui.icon :name="$mark['name']" class="size-[54%]" />
    @else
        <span class="tech-logo-initials">{{ $mark['name'] }}</span>
    @endif
</span>
