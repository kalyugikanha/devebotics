<x-layout :seo="$seo">
    <section class="section">
        <div class="container-site max-w-xl text-center">
            <span class="icon-tile mx-auto"><x-heroicon-o-envelope-open aria-hidden="true" /></span>
            <h1 class="heading-section mt-6">{{ $title }}</h1>
            <p class="lead mt-4">{{ $message }}</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-8">Back to home</a>
        </div>
    </section>
</x-layout>
