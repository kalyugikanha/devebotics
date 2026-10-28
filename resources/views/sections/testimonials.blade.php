<x-site.section :section="$section">
    <div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
        <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" align="left" id="section-heading-{{ $section->id }}" />
        <div class="flex shrink-0 gap-3" data-reveal>
            <a href="{{ route('testimonials.index') }}" class="btn btn-secondary">All reviews</a>
            @if (settings('features.feedback_form_enabled'))
                <a href="{{ route('feedback.create') }}" class="btn btn-ghost">Leave feedback</a>
            @endif
        </div>
    </div>
    <div x-data="carousel" class="relative mt-14" role="region" aria-roledescription="carousel" aria-label="Client testimonials">
        <ul x-ref="track" class="-mx-4 flex snap-x snap-mandatory scroll-px-4 gap-6 overflow-x-auto scroll-smooth px-4 pb-4 [scrollbar-width:none] sm:-mx-6 sm:scroll-px-6 sm:px-6 lg:mx-0 lg:scroll-px-0 lg:px-0">
            @foreach ($records as $testimonial)
                <li data-carousel-item class="w-[85%] shrink-0 snap-start sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)]" aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $loop->count }}">
                    <x-cards.testimonial :testimonial="$testimonial" :index="$loop->index" />
                </li>
            @endforeach
        </ul>
        @if ($records->count() > 3)
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" x-on:click="prev" :disabled="atStart" class="grid size-11 place-items-center rounded-full border border-border bg-background transition hover:border-primary/40 disabled:opacity-40" aria-label="Previous testimonials"><x-heroicon-m-arrow-left class="size-5" aria-hidden="true" /></button>
                <button type="button" x-on:click="next" :disabled="atEnd" class="grid size-11 place-items-center rounded-full border border-border bg-background transition hover:border-primary/40 disabled:opacity-40" aria-label="Next testimonials"><x-heroicon-m-arrow-right class="size-5" aria-hidden="true" /></button>
            </div>
        @endif
    </div>
</x-site.section>
