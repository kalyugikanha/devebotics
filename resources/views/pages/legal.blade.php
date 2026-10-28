<x-layout :seo="$seo" :is-preview="$isPreview">
    <x-site.page-hero :seo="$seo" :title="$page->title" :intro="$page->excerpt" />

    <section class="section pt-12">
        <div class="container-site grid gap-12 lg:grid-cols-[16rem_1fr]">
            @if ($body['toc'])
                <aside class="lg:sticky lg:top-28 lg:self-start" x-data="tableOfContents">
                    <nav aria-label="On this page" class="rounded-card border border-border p-5">
                        <p class="text-xs font-semibold tracking-[0.16em] text-muted uppercase">On this page</p>
                        <ul class="mt-3 space-y-1.5 text-sm">
                            @foreach ($body['toc'] as $entry)
                                <li class="{{ $entry['level'] === 3 ? 'pl-3' : '' }}">
                                    <a href="#{{ $entry['id'] }}" class="block rounded-md py-0.5 text-muted transition hover:text-primary" :class="{ 'text-primary font-semibold': isCurrent('{{ $entry['id'] }}') }">{{ $entry['text'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </aside>
            @endif
            <div class="{{ $body['toc'] ? '' : 'lg:col-span-2 mx-auto' }} max-w-3xl">
                <p class="text-sm text-muted">Last updated: <time datetime="{{ $page->updated_at->toDateString() }}">{{ $page->updated_at->format('F j, Y') }}</time></p>
                <div class="prose-brand mt-6" data-toc-content>{!! $body['html'] !!}</div>
            </div>
        </div>
    </section>
</x-layout>
