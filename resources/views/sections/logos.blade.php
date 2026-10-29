@php
    $style = $section->setting('style', 'marquee');
    $hasDemo = $records->contains(fn ($org) => $org->is_demo);
@endphp
<section class="relative border-y border-border py-12" aria-labelledby="section-heading-{{ $section->id }}">
    <div class="container-site">
        <h2 id="section-heading-{{ $section->id }}" class="text-center text-sm font-semibold tracking-[0.16em] text-muted uppercase">
            {{ $section->title ?: 'Organisations we work with' }}
            @if ($hasDemo)<x-ui.demo-badge :show="true" class="ml-2 align-middle" />@endif
        </h2>
        @if ($style === 'marquee' && $records->count() >= 5)
            <div class="marquee mt-8 overflow-hidden" tabindex="0" aria-label="Logos">
                <ul class="marquee-track items-center">
                    @foreach ([$records, $records] as $pass => $set)
                        @foreach ($set as $org)
                            <li @if ($pass === 1) aria-hidden="true" @endif class="flex h-12 shrink-0 items-center">
                                <x-media.image :media="$org->logo" sizes="180px" :alt="$org->name . ($pass === 1 ? ' Partner' : '')" class="logo-mono h-10 w-auto max-w-[11rem] object-contain" />
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @else
            <ul class="mt-8 grid grid-cols-2 items-center gap-8 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($records as $org)
                    <li class="flex justify-center" data-reveal style="--i:{{ $loop->index }}">
                        <x-media.image :media="$org->logo" sizes="180px" :alt="$org->name" class="logo-mono h-10 w-auto max-w-[10rem] object-contain" />
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
