@php
    $words = preg_split('/\s+/u', trim(\App\Support\Typography::plain($section->title)), -1, PREG_SPLIT_NO_EMPTY) ?: [];
@endphp
<section id="{{ $section->anchor ?: 'section-'.$section->id }}" class="section section-plain overflow-hidden" aria-labelledby="section-heading-{{ $section->id }}">
    <div class="orb ambient-optional top-1/4 -left-40 size-96 bg-brand-violet/25" aria-hidden="true"></div>
    <div class="orb ambient-optional -right-32 bottom-0 size-80 bg-brand-cyan/20" aria-hidden="true"></div>
    <div class="container-site relative">
        <div class="mx-auto max-w-5xl">
            @if ($section->eyebrow)
                <p class="eyebrow" data-reveal>{{ $section->eyebrow }}</p>
            @endif
            <h2 id="section-heading-{{ $section->id }}" class="mt-6 font-display text-3xl leading-[1.18] font-bold tracking-[-0.025em] sm:text-4xl lg:text-[3.25rem]" data-scrub-words>
                @foreach ($words as $word)
                    <span class="scrub-word">{{ $word }}</span>{{ $loop->last ? '' : ' ' }}
                @endforeach
            </h2>
            @if ($section->subtitle)
                <p class="lead mt-8 max-w-2xl" data-reveal>{{ $section->subtitle }}</p>
            @endif
        </div>
    </div>
</section>
