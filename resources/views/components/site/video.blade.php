{{-- Privacy-friendly YouTube / Vimeo embed (youtube-nocookie, lazy iframe). --}}
@props(['url', 'title' => 'Video'])
@php
    $embed = null;
    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([\w-]{11})~', (string) $url, $m)) {
        $embed = 'https://www.youtube-nocookie.com/embed/'.$m[1].'?rel=0';
    } elseif (preg_match('~vimeo\.com/(\d+)~', (string) $url, $m)) {
        $embed = 'https://player.vimeo.com/video/'.$m[1].'?dnt=1';
    }
@endphp
@if ($embed)
    <div {{ $attributes->merge(['class' => 'relative aspect-video overflow-hidden rounded-panel border border-border bg-ink shadow-xl']) }}>
        <iframe src="{{ $embed }}" title="{{ $title }}" loading="lazy" class="absolute inset-0 h-full w-full" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture; fullscreen" referrerpolicy="strict-origin-when-cross-origin"></iframe>
    </div>
@endif
