{{-- Technology name with its logo (service, product and case-study pages). --}}
@props(['technology'])
<li {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-full border border-border bg-background py-1 pr-3.5 pl-1 text-sm font-medium text-foreground']) }}>
    <x-ui.tech-logo :technology="$technology" class="size-7 rounded-full text-[0.625rem]" />{{ $technology->name }}
</li>
