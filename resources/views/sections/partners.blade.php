<x-site.section :section="$section">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />
    <ul class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($records as $partner)
            <li class="card card-interactive flex flex-col items-center text-center" data-reveal style="--i:{{ $loop->index }}">
                <div class="flex h-14 items-center"><x-media.image :media="$partner->logo" sizes="160px" :alt="$partner->name" class="logo-mono max-h-12 w-auto max-w-[9rem] object-contain" /></div>
                <p class="mt-4 font-semibold text-foreground">{{ $partner->name }}</p>
                <p class="text-xs text-muted">{{ $partner->type->getLabel() }}</p>
                <x-ui.demo-badge :show="$partner->is_demo" class="mt-2" />
                @if ($partner->website_url)
                    <a href="{{ \App\Support\SafeUrl::make($partner->website_url) }}" target="_blank" rel="noopener" class="card-link"><span class="sr-only">Visit {{ $partner->name }}</span></a>
                @endif
            </li>
        @endforeach
    </ul>
</x-site.section>
