<x-site.section :section="$section">
    <div class="mx-auto max-w-3xl">
        @if ($section->eyebrow)<p class="eyebrow" data-reveal>{{ $section->eyebrow }}</p>@endif
        @if ($section->title)<h2 id="section-heading-{{ $section->id }}" class="heading-section mt-4" data-reveal>{!! \App\Support\Typography::highlight($section->title) !!}</h2>@endif
        <div class="prose-brand mt-6 text-lg" data-reveal>{!! app(\App\Support\Html\ContentProcessor::class)->process($section->content, false)['html'] !!}</div>
    </div>
</x-site.section>
