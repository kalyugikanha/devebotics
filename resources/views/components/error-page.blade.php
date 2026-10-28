@props(['code', 'title', 'message'])
@php
    $seo = \App\Support\Seo\SeoData::make($title)->noindex();
@endphp
<x-layout :seo="$seo">
    <section class="relative isolate overflow-hidden">
        <div class="bg-grid absolute inset-0 -z-10" aria-hidden="true"></div>
        <div class="orb ambient-optional -top-32 left-1/4 -z-10 size-[28rem] bg-brand-violet/30" aria-hidden="true"></div>
        <div class="orb ambient-optional top-20 right-0 -z-10 size-[24rem] bg-brand-cyan/25" aria-hidden="true"></div>
        <div class="container-site grid min-h-[70vh] items-center gap-12 py-20 lg:grid-cols-[1.1fr_1fr]">
            <div>
                <p class="eyebrow">Error {{ $code }}</p>
                <h1 class="heading-display mt-4">{{ $title }}</h1>
                <p class="lead mt-6 max-w-xl">{{ $message }}</p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">Back to home <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                    <a href="{{ route('contact.create') }}" class="btn btn-secondary btn-lg">Contact us</a>
                </div>
                {{ $slot }}
            </div>
            <div class="relative hidden lg:block" aria-hidden="true">
                <p class="font-display text-[11rem] leading-none font-extrabold tracking-tighter text-gradient">{{ $code }}</p>
            </div>
        </div>
    </section>
</x-layout>
