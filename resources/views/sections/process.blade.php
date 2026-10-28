<x-site.section :section="$section">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />
    <div class="relative mt-16" data-progress-scope>
        {{-- Desktop: horizontal track; mobile: vertical track --}}
        <div class="absolute top-7 right-[10%] left-[10%] hidden h-0.5 rounded-full bg-border md:block" aria-hidden="true">
            <div class="h-full origin-left rounded-full bg-[linear-gradient(90deg,#8b5cf6,#6366f1,#3b82f6,#22d3ee)]" data-progress-line="x"></div>
        </div>
        <div class="absolute top-2 bottom-2 left-7 w-0.5 rounded-full bg-border md:hidden" aria-hidden="true">
            <div class="h-full w-full origin-top rounded-full bg-[linear-gradient(180deg,#8b5cf6,#6366f1,#3b82f6,#22d3ee)]" data-progress-line="y"></div>
        </div>
        <ol class="relative grid gap-10 md:grid-cols-{{ min(5, max(2, $records->count())) }} md:gap-6">
            @foreach ($records as $step)
                <li class="group relative flex gap-5 md:flex-col md:items-center md:text-center" data-step data-reveal style="--i:{{ $loop->index }}">
                    <span class="relative z-10 grid size-14 shrink-0 place-items-center rounded-2xl border border-border bg-background font-display text-lg font-extrabold text-primary shadow-sm transition duration-500 group-[.is-active]:border-transparent group-[.is-active]:bg-[linear-gradient(135deg,#6d28d9,#4f46e5_55%,#2563eb)] group-[.is-active]:text-white group-[.is-active]:shadow-[0_14px_30px_-12px_rgba(79,70,229,0.7)]">
                        <x-ui.icon :name="$step['icon'] ?? null" fallback="sparkles" class="size-6" />
                    </span>
                    <div>
                        <p class="text-xs font-bold tracking-[0.18em] text-primary uppercase">Step {{ $loop->iteration }}</p>
                        <h3 class="heading-card mt-1">{{ $step['title'] }}</h3>
                        @if (filled($step['description'] ?? null))
                            <p class="mt-2 text-[0.9375rem] text-muted md:mx-auto md:max-w-[15rem]">{{ $step['description'] }}</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</x-site.section>
