<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="Industries" title="Technology shaped around [your sector]"
        intro="Every industry has its own regulations, workflows and customers. We bring domain understanding and modern engineering to each one." illustration="strategy" />
    <section class="section">
        <div class="container-site grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($industries as $industry)
                <x-cards.industry :industry="$industry" :index="$loop->index % 3" heading-level="h2" />
            @endforeach
        </div>
    </section>
    <x-site.cta-band title="Don’t see [your industry]?" text="We regularly work across sectors. Tell us about your organisation and challenges." />
</x-layout>
