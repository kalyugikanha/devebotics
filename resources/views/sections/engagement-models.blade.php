<x-site.section :section="$section">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />
    <ul class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-{{ min(4, max(2, $records->count())) }}">
        @foreach ($records as $model)
            @php $highlighted = (bool) ($model['highlighted'] ?? false); @endphp
            <li class="relative flex h-full flex-col rounded-panel border p-7 {{ $highlighted ? 'border-transparent bg-ink text-white shadow-2xl lg:-translate-y-3' : 'border-border bg-background' }}" data-reveal style="--i:{{ $loop->index }}">
                @if ($highlighted)
                    <div class="absolute inset-0 -z-10 rounded-[inherit] bg-[linear-gradient(135deg,#7c3aed,#4f46e5,#06b6d4)] p-px" aria-hidden="true"></div>
                    <span class="badge absolute -top-3 left-7 border-transparent bg-white text-ink">Most popular</span>
                @endif
                <h3 class="font-display text-xl font-bold {{ $highlighted ? 'text-white' : '' }}">{{ $model['title'] }}</h3>
                @if (filled($model['best_for'] ?? null))
                    <p class="mt-1 text-sm font-medium {{ $highlighted ? 'text-white/70' : 'text-primary' }}">Best for: {{ $model['best_for'] }}</p>
                @endif
                @if (filled($model['description'] ?? null))
                    <p class="mt-4 text-[0.9375rem] {{ $highlighted ? 'text-white/80' : 'text-muted' }}">{{ $model['description'] }}</p>
                @endif
                <ul class="mt-6 flex-1 space-y-3 text-[0.9375rem]">
                    @foreach ((array) ($model['features'] ?? []) as $feature)
                        <li class="flex gap-2.5"><x-heroicon-s-check-circle class="mt-0.5 size-5 shrink-0 {{ $highlighted ? 'text-brand-mint' : 'text-primary' }}" aria-hidden="true" />{{ $feature }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('project.create') }}" class="btn mt-8 w-full {{ $highlighted ? 'btn-inverse' : 'btn-secondary' }}">Discuss this model</a>
            </li>
        @endforeach
    </ul>
</x-site.section>
