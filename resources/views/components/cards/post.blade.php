@props(['post', 'index' => 0, 'headingLevel' => 'h3', 'featured' => false])
<article class="group relative flex h-full flex-col {{ $featured ? 'lg:grid lg:grid-cols-[1.15fr_1fr] lg:items-center lg:gap-14' : '' }}" data-reveal style="--i:{{ $index }}">
    <div class="relative aspect-[16/10] overflow-hidden rounded-panel border border-border transition duration-700 ease-out-expo group-hover:-translate-y-1 group-hover:shadow-[0_30px_70px_-36px_rgba(79,70,229,0.5)]">
        @if ($post->featuredMedia)
            <x-media.image :media="$post->featuredMedia" :sizes="$featured ? '(min-width: 1024px) 55vw, 100vw' : '(min-width: 1024px) 33vw, 100vw'" class="h-full w-full object-cover transition duration-700 ease-out-expo group-hover:scale-105" />
        @else
            <x-art.frame :name="\App\Support\Art\IllustrationRegistry::forPost($post->category?->slug, $post->slug)" class="h-full" />
        @endif
        @if ($post->category)
            <span class="badge absolute top-4 left-4 border-transparent bg-foreground/85 text-background backdrop-blur">{{ $post->category->name }}</span>
        @endif
    </div>
    <div class="{{ $featured ? 'mt-7 lg:mt-0' : 'mt-5' }} flex flex-1 flex-col">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted">
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('M j, Y') }}</time>
            <span aria-hidden="true">·</span>
            <span class="inline-flex items-center gap-1"><x-heroicon-o-clock class="size-4" aria-hidden="true" />{{ $post->reading_time_minutes }} min read</span>
            <x-ui.demo-badge :show="$post->is_demo" />
        </div>
        <{{ $headingLevel }} class="heading-card mt-3 {{ $featured ? 'text-2xl sm:text-[2rem] sm:leading-tight' : 'text-xl' }}">
            <a href="{{ $post->publicUrl() }}" class="card-link transition group-hover:text-primary focus-visible:outline-none">{{ $post->title }}</a>
        </{{ $headingLevel }}>
        <p class="mt-3 {{ $featured ? 'line-clamp-4 text-lg' : 'line-clamp-3 text-[0.9375rem]' }} text-muted">{{ $post->excerpt }}</p>
        <div class="mt-auto flex items-center justify-between gap-4 pt-6">
            @if ($post->author)
                <p class="flex items-center gap-2.5 text-sm font-medium text-foreground">
                    <span class="grid size-8 place-items-center rounded-full bg-[linear-gradient(135deg,#6d28d9,#4f46e5_55%,#2563eb)] text-xs font-bold text-white">{{ $post->author->initials() }}</span>
                    {{ $post->author->name }}
                </p>
            @endif
            <span class="link-arrow">Read <x-heroicon-m-arrow-right aria-hidden="true" /></span>
        </div>
    </div>
</article>
