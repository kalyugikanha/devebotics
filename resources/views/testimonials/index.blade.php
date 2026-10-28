<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="Client reviews" title="What our clients [say about us]"
        intro="Every review is submitted by a client, moderated by our team and published only with the client’s permission.">
        @if (settings('features.feedback_form_enabled'))
            <a href="{{ route('feedback.create') }}" class="btn btn-primary btn-lg">Share your feedback <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
        @endif
    </x-site.page-hero>

    <section class="section pt-12">
        <div class="container-site">
            @if ($summary['count'] > 0)
                <div class="mb-12 flex flex-wrap items-center gap-6 rounded-panel border border-border bg-surface p-6" data-reveal>
                    <p class="font-display text-5xl font-extrabold text-gradient">{{ number_format($summary['average'], 1) }}</p>
                    <div>
                        <div class="flex gap-0.5 text-amber-400" role="img" aria-label="Average rating {{ number_format($summary['average'], 1) }} out of 5">
                            @for ($star = 1; $star <= 5; $star++)
                                @if ($star <= round($summary['average']))<x-heroicon-s-star class="size-5" aria-hidden="true" />@else<x-heroicon-o-star class="size-5 text-border" aria-hidden="true" />@endif
                            @endfor
                        </div>
                        <p class="mt-1 text-sm text-muted">Average of {{ $summary['count'] }} published {{ \Illuminate\Support\Str::plural('review', $summary['count']) }}</p>
                    </div>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)<x-cards.testimonial :testimonial="$testimonial" :index="$loop->index % 3" />@endforeach
                </div>
                {{ $testimonials->links('vendor.pagination.site') }}
            @else
                <div class="rounded-panel border border-dashed border-border p-12 text-center">
                    <x-heroicon-o-chat-bubble-left-right class="mx-auto size-10 text-muted" aria-hidden="true" />
                    <h2 class="heading-card mt-4">No published reviews yet</h2>
                    <p class="mt-2 text-muted">Worked with us? We would love to hear about your experience.</p>
                </div>
            @endif
        </div>
    </section>
</x-layout>
