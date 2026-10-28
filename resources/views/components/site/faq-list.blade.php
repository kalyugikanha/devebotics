@props(['faqs', 'title' => 'Frequently asked questions'])
@if ($faqs->isNotEmpty())
    <section class="section" aria-labelledby="faq-heading">
        <div class="container-site grid gap-12 lg:grid-cols-[0.8fr_1.2fr]">
            <x-ui.section-heading eyebrow="FAQ" :title="$title" align="left" id="faq-heading" />
            <div class="space-y-3">
                @foreach ($faqs as $faq)
                    <x-site.faq-item :faq="$faq" :index="$loop->index" />
                @endforeach
            </div>
        </div>
    </section>
@endif
