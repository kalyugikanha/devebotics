<x-layout :seo="$seo">
    <section class="section">
        <div class="container-site max-w-xl text-center">
            <span class="icon-tile mx-auto"><x-heroicon-o-check-badge aria-hidden="true" /></span>
            <h1 class="heading-section mt-6">Thank you — feedback confirmed</h1>
            <p class="lead mt-4">Your email address has been confirmed. Our team will review your feedback shortly.</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-8">Back to home</a>
        </div>
    </section>
</x-layout>
