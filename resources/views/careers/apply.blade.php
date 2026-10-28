<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="Careers" title="Open [application]" intro="Tell us about yourself and the kind of work you love. We review every application." />
    <section class="section pt-12">
        <div class="container-site max-w-3xl">
            <div class="rounded-panel border border-border bg-background p-6 shadow-sm sm:p-10">@include('careers._form', ['job' => null])</div>
        </div>
    </section>
</x-layout>
