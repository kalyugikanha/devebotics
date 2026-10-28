<x-layout :seo="$seo" :is-preview="$isPreview">
    @php $hasHero = ($sections->first()['section'] ?? null)?->type === \App\Enums\SectionType::Hero; @endphp
    @unless ($hasHero)
        <x-site.page-hero :seo="$seo" :title="$page->title" :intro="$page->excerpt" :media="$page->heroMedia" :illustration="$page->template === 'about' ? 'professional' : null" />
    @endunless

    @if ($body['html'])
        <section class="section">
            <div class="container-site">
                <div class="prose-brand mx-auto max-w-3xl text-lg" data-reveal>{!! $body['html'] !!}</div>
            </div>
        </section>
    @endif

    @foreach ($sections as $item)
        @include($item['section']->type->view(), ['section' => $item['section'], 'records' => $item['records']])
    @endforeach
</x-layout>
