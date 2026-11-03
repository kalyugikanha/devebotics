@props(['size' => 'md'])
@php
    $name = (string) settings('company.name');
    $wordmark = \App\Support\Branding::showsWordmark();
    $light = \App\Support\Branding::logoLightUrl();
    $dark = \App\Support\Branding::logoDarkUrl();
    $isBrand = str_ends_with($name, 'Ai Solution');
@endphp
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'group inline-flex items-center gap-2.5 rounded-xl shrink-0']) }} @unless ($wordmark) aria-label="{{ $name }}" @endunless>
    @if ($wordmark)
        <img src="{{ \App\Support\Branding::markUrl() }}" alt="Softec AI Solution" width="48" height="39"
             class="{{ $size === 'lg' ? 'h-12' : 'h-9 sm:h-10' }} w-auto shrink-0 transition-transform duration-500 ease-out-expo group-hover:-rotate-6 group-hover:scale-105">
        <span class="flex flex-col leading-none shrink-0">
            <span class="font-display {{ $size === 'lg' ? 'text-2xl' : 'text-[1.35rem]' }} font-extrabold tracking-[-0.03em] text-foreground whitespace-nowrap">
                @if ($isBrand)
                    Softec<span class="text-gradient">Ai</span>
                @else
                    {{ $name }}
                @endif
            </span>
            @if ($isBrand)
                <span class="mt-1 text-[0.58rem] font-semibold tracking-[0.42em] text-muted uppercase whitespace-nowrap">Solution</span>
            @endif
        </span>
    @else
        <img src="{{ $light }}" alt="{{ $name }}" class="h-10 w-auto shrink-0 dark:hidden">
        <img src="{{ $dark }}" alt="{{ $name }}" class="hidden h-10 w-auto shrink-0 dark:block">
    @endif
</a>
