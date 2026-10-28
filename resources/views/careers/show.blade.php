<x-layout :seo="$seo" :is-preview="$isPreview">
    <x-site.page-hero :seo="$seo" :eyebrow="$job->department ?: 'Careers'" :title="$job->title" :intro="$job->summary" :demo="$job->is_demo">
        @if ($job->isOpen())<a href="#apply" class="btn btn-primary btn-lg">Apply now <x-heroicon-m-arrow-down class="size-4" aria-hidden="true" /></a>@endif
    </x-site.page-hero>

    <section class="section pt-12">
        <div class="container-site grid gap-12 lg:grid-cols-[1.5fr_1fr]">
            <div class="space-y-12">
                @unless ($job->isOpen())
                    <p class="alert alert-info"><x-heroicon-o-information-circle class="size-5 shrink-0" aria-hidden="true" />This position is no longer accepting applications.</p>
                @endunless
                @if ($description)<div class="prose-brand" data-reveal>{!! $description !!}</div>@endif
                @foreach (['Responsibilities' => $job->responsibilities, 'Requirements' => $job->requirements, 'Benefits' => $job->benefits] as $heading => $items)
                    @if ($items)
                        <div data-reveal>
                            <h2 class="font-display text-2xl font-bold">{{ $heading }}</h2>
                            <x-site.item-grid :items="$items" variant="check" class="mt-5 !grid-cols-1" />
                        </div>
                    @endif
                @endforeach
                @if ($job->isOpen())
                    <div id="apply" class="scroll-mt-28 rounded-panel border border-border bg-background p-6 shadow-sm sm:p-10">
                        <h2 class="font-display text-2xl font-bold">Apply for this role</h2>
                        <div class="mt-6">@include('careers._form', ['job' => $job])</div>
                    </div>
                @endif
            </div>
            <aside class="lg:sticky lg:top-28 lg:self-start">
                <dl class="card card-muted space-y-4 text-sm">
                    @foreach (array_filter(['Location' => $job->location, 'Employment' => $job->employment_type->getLabel(), 'Workplace' => $job->workplace_type->getLabel(), 'Experience' => $job->experience_level, 'Salary' => $job->salaryRange(), 'Apply by' => $job->valid_through?->format('F j, Y')]) as $label => $value)
                        <div><dt class="text-muted">{{ $label }}</dt><dd class="mt-0.5 font-semibold text-foreground">{{ $value }}</dd></div>
                    @endforeach
                </dl>
            </aside>
        </div>
    </section>
</x-layout>
