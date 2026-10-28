@props(['eyebrow' => null, 'title' => null, 'subtitle' => null, 'align' => 'center', 'level' => 'h2', 'id' => null])
<div {{ $attributes->class(['max-w-3xl', 'mx-auto text-center' => $align === 'center']) }} data-reveal>
    @if ($eyebrow)
        <p class="eyebrow">{{ $eyebrow }}</p>
    @endif
    @if ($title)
        <{{ $level }} @if ($id) id="{{ $id }}" @endif class="heading-section mt-4">{!! \App\Support\Typography::highlight($title) !!}</{{ $level }}>
    @endif
    @if ($subtitle)
        <p class="lead mt-5">{{ $subtitle }}</p>
    @endif
    {{ $slot }}
</div>
