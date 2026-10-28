@props(['testimonial', 'index' => 0])
<figure class="card card-interactive flex h-full flex-col" data-reveal style="--i:{{ $index }}">
    <div class="flex items-center justify-between gap-3">
        <svg class="size-10 text-primary/25" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><path d="M9.3 26c-3.3 0-5.8-2.6-5.8-6.4 0-5.6 4.3-10.7 9.9-12.6l1.2 2.1c-3.3 1.5-5.6 4.1-5.9 6.8.3-.1.8-.2 1.3-.2 2.9 0 5.1 2.2 5.1 5.1S12.6 26 9.3 26zm15.4 0c-3.3 0-5.8-2.6-5.8-6.4 0-5.6 4.3-10.7 9.9-12.6L30 9.1c-3.3 1.5-5.6 4.1-5.9 6.8.3-.1.8-.2 1.3-.2 2.9 0 5.1 2.2 5.1 5.1S28 26 24.7 26z"/></svg>
        <div class="flex items-center gap-2">
            <x-ui.demo-badge :show="$testimonial->is_demo" />
            <div class="flex gap-0.5 text-amber-400" role="img" aria-label="Rated {{ $testimonial->rating }} out of 5">
                @for ($star = 1; $star <= 5; $star++)
                    @if ($star <= $testimonial->rating)
                        <x-heroicon-s-star class="size-4" aria-hidden="true" />
                    @else
                        <x-heroicon-o-star class="size-4 text-border" aria-hidden="true" />
                    @endif
                @endfor
            </div>
        </div>
    </div>
    <blockquote class="mt-5 flex-1 text-[1.0625rem] leading-relaxed text-foreground">
        <p>“{{ $testimonial->feedback }}”</p>
    </blockquote>
    <figcaption class="mt-7 flex items-center gap-3 border-t border-border pt-5">
        @if ($testimonial->display_identity_consent && $testimonial->photo)
            <x-media.image :media="$testimonial->photo" sizes="44px" class="size-11 rounded-full object-cover" alt="" />
        @else
            <span class="grid size-11 place-items-center rounded-full bg-[linear-gradient(135deg,#6d28d9,#4f46e5_55%,#0284c7)] text-sm font-bold text-white" aria-hidden="true">{{ $testimonial->publicInitials() }}</span>
        @endif
        <div>
            <p class="font-semibold text-foreground">{{ $testimonial->publicName() }}</p>
            <p class="text-sm text-muted">
                {{ collect([$testimonial->display_identity_consent ? $testimonial->job_title : null, $testimonial->publicCompany()])->filter()->implode(', ') ?: ($testimonial->service?->title ?? 'Client') }}
            </p>
        </div>
    </figcaption>
</figure>
