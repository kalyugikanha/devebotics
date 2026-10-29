@php($name = (string) settings('company.name'))
<span class="flex items-center gap-2.5">
    <img src="{{ \App\Support\Branding::markUrl() }}" alt="Softec AI Solution" class="h-9 w-auto" width="44" height="36">
    <span class="softec-brand-wordmark text-lg leading-none text-gray-950 dark:text-white">
        @if (str_ends_with($name, 'Ai Solution'))
            Softec<span class="softec-brand-gradient">Ai</span>
            <span class="mt-0.5 block text-[0.55rem] font-semibold uppercase tracking-[0.35em] text-gray-500 dark:text-gray-400">Solution</span>
        @else
            {{ $name }}
        @endif
    </span>
</span>
