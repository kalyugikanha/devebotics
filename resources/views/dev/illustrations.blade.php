{{-- Local-only QA gallery for the illustration set (route registered only in the local environment). --}}
<x-layout :seo="\App\Support\Seo\SeoData::make('Illustration gallery')->noindex()">
    @foreach (['light' => '', 'dark' => 'dark'] as $mode => $class)
        <section class="{{ $class }} bg-background py-16 text-foreground">
            <div class="container-site">
                <h1 class="heading-section">Illustrations — {{ $mode }}</h1>
                <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach (\App\Support\Art\IllustrationRegistry::all() as $name => $label)
                        <figure class="card">
                            <x-illustration :name="$name" class="rounded-2xl" />
                            <figcaption class="mt-3 text-sm text-muted"><code>{{ $name }}</code> — {{ $label }}</figcaption>
                        </figure>
                    @endforeach
                </div>

                <h2 class="heading-section mt-20">Feature spots — {{ $mode }}</h2>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-spots>
                    @foreach (\App\Support\Art\SpotRegistry::all() as $name => $label)
                        <figure class="card p-4">
                            <x-spot :name="$name" class="overflow-hidden rounded-xl" />
                            <figcaption class="mt-3 text-sm text-muted"><code>{{ $name }}</code> — {{ $label }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach
</x-layout>
