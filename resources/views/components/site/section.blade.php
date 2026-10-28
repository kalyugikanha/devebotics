@props(['section'])
@php
    $background = $section->background?->value ?? 'default';
    $classes = match ($background) {
        'muted' => 'section-muted',
        'gradient' => 'section-gradient',
        'contrast' => 'dark bg-background text-foreground',
        default => 'section-plain',
    };
@endphp
<section id="{{ $section->anchor ?: 'section-'.$section->id }}" @if ($section->title) aria-labelledby="section-heading-{{ $section->id }}" @endif
         {{ $attributes->merge(['class' => 'section overflow-hidden '.$classes]) }}>
    @if ($background === 'contrast')
        <div class="orb ambient-optional -top-40 left-1/3 size-[30rem] bg-brand-violet/40" aria-hidden="true"></div>
        <div class="orb ambient-optional -bottom-40 right-0 size-[26rem] bg-brand-cyan/30" aria-hidden="true"></div>
    @endif
    <div class="container-site relative">
        {{ $slot }}
    </div>
</section>
