@props(['title' => null, 'text' => null, 'primaryLabel' => 'Start a project', 'primaryUrl' => null, 'secondaryLabel' => 'Contact us', 'secondaryUrl' => null])
<section class="section pt-0">
    <div class="container-site">
        <div class="relative isolate overflow-hidden rounded-[2rem] bg-ink px-6 py-14 text-white sm:px-12 lg:flex lg:items-center lg:justify-between lg:gap-10 lg:py-16" data-reveal>
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(60%_90%_at_10%_0%,rgba(139,92,246,.5),transparent_60%),radial-gradient(50%_80%_at_100%_100%,rgba(34,211,238,.35),transparent_60%)]" aria-hidden="true"></div>
            <div class="max-w-2xl">
                <h2 class="font-display text-3xl font-extrabold tracking-tight text-white sm:text-4xl">{!! \App\Support\Typography::highlight($title ?: 'Ready to start your project?') !!}</h2>
                <p class="mt-4 text-lg text-white/75">{{ $text ?: 'Tell us about your goals and we will propose the right approach, team and plan.' }}</p>
            </div>
            <div class="mt-8 flex flex-wrap gap-3 lg:mt-0 lg:shrink-0">
                <a href="{{ $primaryUrl ?? route('project.create') }}" class="btn btn-lg btn-inverse">{{ $primaryLabel }} <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                <a href="{{ $secondaryUrl ?? route('contact.create') }}" class="btn btn-lg border border-white/30 text-white hover:bg-white/10">{{ $secondaryLabel }}</a>
            </div>
        </div>
    </div>
</section>
