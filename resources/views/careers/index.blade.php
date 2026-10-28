<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="Careers" title="Build the future [with us]"
        intro="Join a team of engineers, designers and consultants who care about craft, learning and making a real difference for our clients." illustration="startup" />

    <section class="section pt-12">
        <div class="container-site">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <h2 class="heading-section text-3xl">Open positions</h2>
                    <p class="mt-2 text-muted">{{ $jobs->count() }} {{ \Illuminate\Support\Str::plural('opening', $jobs->count()) }}</p>
                </div>
            </div>
            @if ($jobs->isEmpty())
                <div class="mt-10 rounded-panel border border-dashed border-border p-12 text-center">
                    <x-heroicon-o-briefcase class="mx-auto size-10 text-muted" aria-hidden="true" />
                    <h3 class="heading-card mt-4">No open positions right now</h3>
                    <p class="mt-2 text-muted">We are always happy to hear from talented people.</p>
                </div>
            @else
                <ul class="mt-10 space-y-4">
                    @foreach ($jobs as $job)
                        <li class="card card-interactive group flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" data-reveal style="--i:{{ $loop->index }}">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($job->department)<span class="badge-primary badge">{{ $job->department }}</span>@endif
                                    <x-ui.demo-badge :show="$job->is_demo" />
                                </div>
                                <h3 class="heading-card mt-3 text-xl"><a href="{{ $job->publicUrl() }}" class="card-link focus-visible:outline-none">{{ $job->title }}</a></h3>
                                <p class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-muted">
                                    @if ($job->location)<span class="flex items-center gap-1.5"><x-heroicon-o-map-pin class="size-4" aria-hidden="true" />{{ $job->location }}</span>@endif
                                    <span class="flex items-center gap-1.5"><x-heroicon-o-briefcase class="size-4" aria-hidden="true" />{{ $job->employment_type->getLabel() }}</span>
                                    <span class="flex items-center gap-1.5"><x-heroicon-o-building-office class="size-4" aria-hidden="true" />{{ $job->workplace_type->getLabel() }}</span>
                                    @if ($salary = $job->salaryRange())<span class="flex items-center gap-1.5"><x-heroicon-o-banknotes class="size-4" aria-hidden="true" />{{ $salary }}</span>@endif
                                </p>
                            </div>
                            <span class="link-arrow shrink-0">View role <x-heroicon-m-arrow-right aria-hidden="true" /></span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($openApplications)
                <div class="mt-12 flex flex-col items-start justify-between gap-4 rounded-panel border border-border bg-surface p-8 sm:flex-row sm:items-center">
                    <div><h3 class="heading-card text-xl">Don’t see the right role?</h3><p class="mt-1 text-muted">Send us an open application and tell us how you would like to contribute.</p></div>
                    <a href="{{ route('careers.general') }}" class="btn btn-primary shrink-0">Open application <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                </div>
            @endif
        </div>
    </section>
</x-layout>
