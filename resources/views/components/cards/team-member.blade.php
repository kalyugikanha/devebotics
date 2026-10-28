@props(['member', 'index' => 0])
<article class="group text-center" data-reveal style="--i:{{ $index }}">
    <div class="relative mx-auto aspect-square w-full max-w-[15rem] overflow-hidden rounded-panel border border-border bg-surface">
        @if ($member->photo)
            <x-media.image :media="$member->photo" sizes="240px" class="h-full w-full object-cover transition duration-700 ease-out-expo group-hover:scale-105" :alt="$member->name" />
        @else
            <x-art.cover :seed="'team-'.$member->id.$member->name" :label="$member->initials()" />
        @endif
    </div>
    <h3 class="heading-card mt-5">{{ $member->name }}</h3>
    <p class="text-sm text-muted">{{ $member->job_title }}</p>
    <x-ui.demo-badge :show="$member->is_demo" class="mt-2" />
    @if ($member->linkedin_url || $member->github_url || $member->x_url)
        <div class="mt-3 flex justify-center gap-2">
            @foreach (['linkedin' => $member->linkedin_url, 'github' => $member->github_url, 'x' => $member->x_url] as $network => $url)
                @if ($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="grid size-8 place-items-center rounded-full text-muted transition hover:bg-primary/10 hover:text-primary" aria-label="{{ $member->name }} on {{ ['x' => 'X', 'linkedin' => 'LinkedIn', 'github' => 'GitHub'][$network] }}">
                        <x-ui.social-icon :network="$network" class="size-4" />
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</article>
