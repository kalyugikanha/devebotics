<x-site.section :section="$section">
    <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />
    <div class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($records as $member)
            <x-cards.team-member :member="$member" :index="$loop->index" />
        @endforeach
    </div>
    <div class="mt-12 text-center" data-reveal><a href="{{ route('team.index') }}" class="btn btn-secondary">Meet the whole team</a></div>
</x-site.section>
