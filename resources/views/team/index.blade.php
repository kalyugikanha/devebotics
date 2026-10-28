<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="Our team" title="The people behind [the work]" intro="Engineers, designers, data specialists and consultants working together for our clients." />
    @if ($leadership->isNotEmpty())
        <section class="section">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Leadership" title="Leadership team" />
                <div class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">@foreach ($leadership as $member)<x-cards.team-member :member="$member" :index="$loop->index" />@endforeach</div>
            </div>
        </section>
    @endif
    @if ($members->isNotEmpty())
        <section class="section section-muted">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Team" title="Meet the team" />
                <div class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">@foreach ($members as $member)<x-cards.team-member :member="$member" :index="$loop->index % 4" />@endforeach</div>
            </div>
        </section>
    @endif
    @if (settings('features.careers_enabled'))
        <x-site.cta-band title="Want to [join us]?" text="We are always interested in meeting talented people." primary-label="View careers" :primary-url="route('careers.index')" />
    @endif
</x-layout>
