<x-site.section :section="$section">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />
    <ul class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($records as $achievement)
            <li class="card flex items-start gap-4" data-reveal style="--i:{{ $loop->index }}">
                @if ($achievement->media)
                    <x-media.image :media="$achievement->media" sizes="64px" class="size-16 shrink-0 rounded-xl object-contain" />
                @else
                    <span class="icon-tile"><x-heroicon-o-check-badge aria-hidden="true" /></span>
                @endif
                <div>
                    <p class="text-xs font-semibold tracking-wide text-primary uppercase">{{ $achievement->type->getLabel() }}</p>
                    <h3 class="heading-card mt-1 text-base">{{ $achievement->title }}</h3>
                    <p class="mt-1 text-sm text-muted">{{ collect([$achievement->issuer, $achievement->issued_on?->format('Y')])->filter()->implode(' · ') }}</p>
                    @if ($achievement->verification_url)
                        <a href="{{ \App\Support\SafeUrl::make($achievement->verification_url) }}" target="_blank" rel="noopener" class="link-arrow mt-3">Verify <x-heroicon-m-arrow-top-right-on-square aria-hidden="true" /></a>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</x-site.section>
