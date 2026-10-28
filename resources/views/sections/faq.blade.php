<x-site.section :section="$section">
    <div class="grid gap-12 lg:grid-cols-[0.8fr_1.2fr]">
        <div class="lg:sticky lg:top-28 lg:self-start">
            <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" align="left" id="section-heading-{{ $section->id }}" />
            <div class="mt-8 flex flex-wrap gap-3" data-reveal>
                <a href="{{ route('faq.index') }}" class="btn btn-secondary">All FAQs</a>
                <a href="{{ route('contact.create') }}" class="btn btn-ghost">Ask a question</a>
            </div>
            <x-art.frame name="chat" tone="violet" class="mt-10 hidden aspect-[16/11] rounded-[1.75rem] border border-border lg:block" data-reveal />
        </div>
        <div class="space-y-3">
            @foreach ($records as $faq)
                <x-site.faq-item :faq="$faq" :index="$loop->index" />
            @endforeach
        </div>
    </div>
</x-site.section>
