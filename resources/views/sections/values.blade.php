<x-site.section :section="$section">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />
    @if ($section->setting('mission') || $section->setting('vision'))
        <div class="mt-14 grid gap-6 md:grid-cols-2">
            @foreach (['Mission' => [$section->setting('mission'), 'rocket-launch'], 'Vision' => [$section->setting('vision'), 'light-bulb']] as $label => [$text, $icon])
                @if ($text)
                    <div class="relative overflow-hidden rounded-panel border border-border bg-background p-8 sm:p-10" data-reveal style="--i:{{ $loop->index }}">
                        <div class="orb -top-16 -right-16 size-48 {{ $loop->first ? 'bg-brand-violet/30' : 'bg-brand-cyan/30' }}" aria-hidden="true"></div>
                        <span class="icon-tile relative"><x-ui.icon :name="$icon" /></span>
                        <h3 class="relative mt-6 text-sm font-semibold tracking-[0.16em] text-muted uppercase">Our {{ strtolower($label) }}</h3>
                        <p class="relative mt-3 font-display text-2xl leading-snug font-bold">{{ $text }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
    @if ($records->isNotEmpty())
        <ul class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($records as $value)
                <li class="card h-full" data-reveal style="--i:{{ $loop->index }}">
                    <span class="icon-tile-soft"><x-ui.icon :name="$value['icon'] ?? null" /></span>
                    <h3 class="heading-card mt-5">{{ $value['title'] }}</h3>
                    @if (filled($value['description'] ?? null))<p class="mt-2 text-[0.9375rem] text-muted">{{ $value['description'] }}</p>@endif
                </li>
            @endforeach
        </ul>
    @endif
</x-site.section>
