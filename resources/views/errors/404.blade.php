<x-error-page code="404" title="We can’t find that page" message="The page may have moved, been renamed or no longer exists. Try one of the links below or search the site.">
    @if (settings('features.search_enabled'))
        <form method="GET" action="{{ route('search') }}" role="search" class="relative mt-8 max-w-md">
            <label for="error-search" class="sr-only">Search the site</label>
            <input id="error-search" type="search" name="q" placeholder="Search the site" class="form-control rounded-full pr-28">
            <button type="submit" class="btn btn-primary btn-sm absolute top-1/2 right-1.5 -translate-y-1/2">Search</button>
        </form>
    @endif
    <ul class="mt-8 flex flex-wrap gap-2 text-sm">
        <li><a href="{{ route('services.index') }}" class="chip hover:text-primary">Services</a></li>
        <li><a href="{{ route('products.index') }}" class="chip hover:text-primary">Products</a></li>
        <li><a href="{{ route('portfolio.index') }}" class="chip hover:text-primary">Case studies</a></li>
        <li><a href="{{ route('blog.index') }}" class="chip hover:text-primary">Insights</a></li>
    </ul>
</x-error-page>
