<x-site.section :section="$section">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />
    <ol class="relative mx-auto mt-14 max-w-3xl border-l border-border pl-8">
        @foreach ($records as $milestone)
            <li class="relative pb-10 last:pb-0" data-reveal style="--i:{{ $loop->index }}">
                <span class="absolute top-1 -left-[2.55rem] grid size-5 place-items-center rounded-full border-4 border-background bg-gradient-to-br from-brand-purple to-brand-blue" aria-hidden="true"></span>
                <p class="text-sm font-bold tracking-wide text-primary uppercase">{{ $milestone->year }} <x-ui.demo-badge :show="$milestone->is_demo" class="ml-2" /></p>
                <h3 class="heading-card mt-1">{{ $milestone->title }}</h3>
                @if ($milestone->description)<p class="mt-2 text-muted">{{ $milestone->description }}</p>@endif
            </li>
        @endforeach
    </ol>
</x-site.section>
