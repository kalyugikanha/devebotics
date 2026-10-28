<x-layout :seo="$seo" :hero-dark="($sections->first()['section'] ?? null)?->type === \App\Enums\SectionType::Hero">
    @forelse ($sections as $item)
        @include($item['section']->type->view(), ['section' => $item['section'], 'records' => $item['records']])
    @empty
        <section class="section">
            <div class="container-site text-center">
                <h1 class="heading-display">{{ settings('company.name') }}</h1>
                <p class="lead mt-6">{{ settings('company.tagline') }}</p>
            </div>
        </section>
    @endforelse
</x-layout>
