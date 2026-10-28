<x-site.section :section="$section">
    <div class="grid items-center gap-12 lg:grid-cols-2">
        <div class="relative aspect-[4/3] overflow-hidden rounded-panel border border-border shadow-xl" data-reveal data-image-reveal>
            <x-art.visual :media="$section->media" :seed="'section-'.$section->id" sizes="(min-width: 1024px) 50vw, 100vw" />
        </div>
        <div>
            <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" align="left" id="section-heading-{{ $section->id }}" />
            @if ($section->content)
                <div class="prose-brand mt-6" data-reveal>{!! app(\App\Support\Html\ContentProcessor::class)->process($section->content, false)['html'] !!}</div>
            @endif
            <x-site.cta-buttons :primary="$section->setting('primary_cta')" :secondary="$section->setting('secondary_cta')" class="mt-8" data-reveal />
        </div>
    </div>
</x-site.section>
