<x-layout :seo="$seo" :is-preview="$isPreview">
    <x-site.page-hero :seo="$seo" eyebrow="Industry" :title="$industry->name" :intro="$industry->excerpt" :media="$industry->heroMedia" :illustration="\App\Support\Art\IllustrationRegistry::resolve($industry->illustration, $industry->slug, $industry->icon, 'professional')" :demo="$industry->is_demo">
        <a href="{{ route('project.create') }}" class="btn btn-primary btn-lg">Discuss your project <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
    </x-site.page-hero>

    @if ($description)
        <section class="section">
            <div class="container-site"><div class="prose-brand mx-auto max-w-3xl text-lg" data-reveal>{!! $description !!}</div></div>
        </section>
    @endif

    @if ($industry->challenges || $industry->solutions)
        <section class="section section-muted">
            <div class="container-site grid gap-14 lg:grid-cols-2">
                @if ($industry->challenges)
                    <div>
                        <x-ui.section-heading eyebrow="Challenges" title="What [{{ $industry->name }}] teams face" align="left" />
                        <x-site.item-grid :items="$industry->challenges" variant="check" class="mt-10 !grid-cols-1" />
                    </div>
                @endif
                @if ($industry->solutions)
                    <div>
                        <x-ui.section-heading eyebrow="Solutions" title="How we [help]" align="left" />
                        <x-site.item-grid :items="$industry->solutions" variant="check" class="mt-10 !grid-cols-1" />
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($industry->services->isNotEmpty())
        <section class="section">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Services" title="Relevant [services]" />
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($industry->services as $service)<x-cards.service :service="$service" :index="$loop->index" />@endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($industry->products->isNotEmpty())
        <section class="section section-muted">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Products" title="Solutions for [{{ $industry->name }}]" />
                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($industry->products as $product)<x-cards.product :product="$product" :index="$loop->index" />@endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($industry->portfolios->isNotEmpty())
        <section class="section">
            <div class="container-site">
                <x-ui.section-heading eyebrow="Case studies" title="Work in [{{ $industry->name }}]" />
                <div class="mt-12 grid gap-10 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($industry->portfolios as $portfolio)<x-cards.portfolio :portfolio="$portfolio" :index="$loop->index" />@endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.faq-list :faqs="$industry->faqs" />
    <div class="pt-20 md:pt-28"><x-site.cta-band :title="'Building for [' . $industry->name . ']?'" /></div>
</x-layout>
