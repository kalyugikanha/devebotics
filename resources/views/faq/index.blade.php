<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="FAQ" title="Frequently asked [questions]" intro="Answers to common questions about working with us. Can’t find what you need? Get in touch." />
    <section class="section pt-12">
        <div class="container-site grid gap-12 lg:grid-cols-[16rem_1fr]">
            @if ($categories->count() > 1)
                <nav aria-label="FAQ categories" class="lg:sticky lg:top-28 lg:self-start">
                    <ul class="flex flex-wrap gap-2 lg:flex-col">
                        @foreach ($categories as $category)<li><a href="#{{ $category->slug }}" class="chip lg:w-full hover:border-primary/40 hover:text-primary">{{ $category->name }}</a></li>@endforeach
                    </ul>
                </nav>
            @endif
            <div class="space-y-14 {{ $categories->count() > 1 ? '' : 'lg:col-span-2 max-w-3xl' }}">
                @forelse ($categories as $category)
                    <div id="{{ $category->slug }}" class="scroll-mt-28">
                        <h2 class="font-display text-2xl font-bold">{{ $category->name }}</h2>
                        <div class="mt-5 space-y-3">@foreach ($category->faqs as $faq)<x-site.faq-item :faq="$faq" :index="$loop->index" />@endforeach</div>
                    </div>
                @empty
                    <p class="text-muted">No FAQs published yet.</p>
                @endforelse
            </div>
        </div>
    </section>
    <x-site.cta-band title="Still have [questions]?" primary-label="Contact us" :primary-url="route('contact.create')" secondary-label="Start a project" :secondary-url="route('project.create')" />
</x-layout>
