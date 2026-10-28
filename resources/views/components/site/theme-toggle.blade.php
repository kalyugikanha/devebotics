<div x-data="themeToggle" {{ $attributes->merge(['class' => 'relative']) }}>
    <button type="button" x-on:click="cycle" class="grid size-10 place-items-center rounded-full text-foreground transition hover:bg-foreground/5"
            :aria-label="label" title="Switch theme (light, dark, system)">
        <x-heroicon-o-sun class="size-5" x-show="isLight" aria-hidden="true" />
        <x-heroicon-o-moon class="size-5" x-show="isDark" x-cloak aria-hidden="true" />
        <x-heroicon-o-computer-desktop class="size-5" x-show="isSystem" x-cloak aria-hidden="true" />
        <span class="sr-only" x-text="label">Theme</span>
    </button>
</div>
