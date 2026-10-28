<x-layout :seo="$seo" :is-preview="$isPreview">
    <article>
        <header class="relative overflow-hidden border-b border-border">
            <div class="bg-grid absolute inset-0 opacity-50" aria-hidden="true"></div>
            <div class="container-site relative max-w-4xl py-14 text-center lg:py-20">
                <x-site.breadcrumbs :items="$seo->breadcrumbs" class="mb-8 flex justify-center" />
                <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-sm text-muted">
                    @if ($post->category)<a href="{{ $post->category->publicUrl() }}" class="font-semibold text-primary">{{ $post->category->name }}</a><span aria-hidden="true">·</span>@endif
                    <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->published_at?->format('F j, Y') ?? 'Unpublished' }}</time>
                    <span aria-hidden="true">·</span><span>{{ $post->reading_time_minutes }} min read</span>
                    <x-ui.demo-badge :show="$post->is_demo" />
                </div>
                <h1 class="heading-display mt-5 text-4xl sm:text-5xl lg:text-[3.25rem]">{{ $post->title }}</h1>
                @if ($post->excerpt)<p class="lead mx-auto mt-6 max-w-2xl">{{ $post->excerpt }}</p>@endif
                @if ($post->author)
                    <a href="{{ $post->author->publicUrl() }}" class="mt-8 inline-flex items-center gap-3 rounded-full p-1 pr-4 transition hover:bg-foreground/5">
                        @if ($post->author->avatar)
                            <x-media.image :media="$post->author->avatar" sizes="44px" class="size-11 rounded-full object-cover" alt="" />
                        @else
                            <span class="grid size-11 place-items-center rounded-full bg-gradient-to-br from-brand-purple to-brand-blue text-sm font-bold text-white">{{ $post->author->initials() }}</span>
                        @endif
                        <span class="text-left"><span class="block text-sm font-semibold text-foreground">{{ $post->author->name }}</span><span class="block text-xs text-muted">{{ $post->author->job_title }}</span></span>
                    </a>
                @endif
            </div>
        </header>

        <div class="container-site max-w-5xl">
            <div class="relative -mt-px aspect-[2/1] overflow-hidden rounded-b-panel border-x border-b border-border" data-image-reveal>
                <x-art.visual :media="$post->featuredMedia" :seed="'post-'.$post->slug" icon="newspaper" sizes="(min-width: 1024px) 64rem, 100vw" :eager="true" />
            </div>
        </div>

        <div class="container-site grid gap-12 py-16 lg:grid-cols-[1fr_16rem] lg:py-20">
            <div class="mx-auto w-full max-w-3xl">
                <div class="prose-brand text-[1.0625rem]" data-toc-content>{!! $content['html'] !!}</div>

                @if ($post->is_ai_assisted)
                    <p class="mt-10 text-sm text-muted">This article was drafted with AI assistance and reviewed by our editorial team.</p>
                @endif

                @if ($post->tags->isNotEmpty())
                    <ul class="mt-12 flex flex-wrap gap-2" aria-label="Tags">
                        @foreach ($post->tags as $tag)<li><a href="{{ $tag->publicUrl() }}" class="chip hover:border-primary/40 hover:text-primary">#{{ $tag->name }}</a></li>@endforeach
                    </ul>
                @endif

                <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-border pt-8" x-data="shareLinks" data-url="{{ $post->publicUrl() }}">
                    <p class="text-sm font-semibold">Share</p>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($post->publicUrl()) }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full border border-border hover:text-primary" aria-label="Share on LinkedIn"><x-ui.social-icon network="linkedin" class="size-4" /></a>
                    <a href="https://x.com/intent/post?url={{ urlencode($post->publicUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full border border-border hover:text-primary" aria-label="Share on X"><x-ui.social-icon network="x" class="size-4" /></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($post->publicUrl()) }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full border border-border hover:text-primary" aria-label="Share on Facebook"><x-ui.social-icon network="facebook" class="size-4" /></a>
                    <button type="button" x-on:click="copy" class="btn btn-secondary btn-sm"><x-heroicon-o-link class="size-4" aria-hidden="true" /><span x-text="copied ? 'Link copied' : 'Copy link'">Copy link</span></button>
                    <span class="sr-only" role="status" x-text="copied ? 'Link copied to clipboard' : ''"></span>
                </div>

                @if ($post->author && $post->author->bio)
                    <div class="mt-10 flex gap-5 rounded-panel border border-border bg-surface p-6">
                        <span class="grid size-14 shrink-0 place-items-center rounded-full bg-gradient-to-br from-brand-purple to-brand-blue font-bold text-white">{{ $post->author->initials() }}</span>
                        <div>
                            <p class="text-xs font-semibold tracking-wide text-muted uppercase">Written by</p>
                            <p class="mt-1 font-display text-lg font-bold"><a href="{{ $post->author->publicUrl() }}" class="hover:text-primary">{{ $post->author->name }}</a></p>
                            <p class="mt-2 text-sm text-muted">{{ $post->author->bio }}</p>
                        </div>
                    </div>
                @endif

                @if ($commentsEnabled)
                    @include('blog.partials.comments')
                @endif
            </div>

            @if ($content['toc'])
                <aside class="hidden lg:block" x-data="tableOfContents">
                    <nav aria-label="Table of contents" class="sticky top-28">
                        <p class="text-xs font-semibold tracking-[0.16em] text-muted uppercase">On this page</p>
                        <ul class="mt-4 space-y-2 border-l border-border text-sm">
                            @foreach ($content['toc'] as $entry)
                                <li><a href="#{{ $entry['id'] }}" class="-ml-px block border-l border-transparent py-0.5 text-muted transition hover:text-foreground {{ $entry['level'] === 3 ? 'pl-7' : 'pl-4' }}" :class="{ 'border-primary text-primary font-semibold': isCurrent('{{ $entry['id'] }}') }">{{ $entry['text'] }}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                </aside>
            @endif
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="section section-muted" aria-labelledby="related-heading">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Keep reading" title="Related [insights]" id="related-heading" />
                <div class="mt-12 grid gap-10 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)<x-cards.post :post="$item" :index="$loop->index" />@endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="pt-20 md:pt-28"><x-site.cta-band title="Want to put these [ideas into practice]?" /></div>
</x-layout>
