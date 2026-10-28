@props(['items' => [], 'mega' => [], 'themeSwitcher' => true])
@php
    $ctaLabel = settings('company.header_cta_label');
    $ctaUrl = settings('company.header_cta_url');
    $current = url()->current();
    $isActive = fn (?string $url) => $url && ($current === $url || ($url !== url('/') && str_starts_with($current, rtrim($url, '/').'/')));
    $itemIsActive = fn (array $item) => $isActive($item['url']) || collect($item['children'])->contains(fn (array $child) => $isActive($child['url']));
@endphp
<header role="banner" x-data="siteHeader" x-on:keydown.escape.window="closeAll" x-on:resize.window.debounce.150ms="restIndicator"
        class="sticky top-0 z-40 transition-[padding] duration-500" :class="{ 'dark': onDark, 'pt-2 sm:pt-3': scrolled }" x-bind:style="mobileOpen ? 'z-index: 90' : ''">
    {{-- Soft backdrop while a desktop menu is open --}}
    <div x-show="anyOpen" x-cloak x-on:click="closeMenus" aria-hidden="true"
         x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 -z-10 hidden bg-ink/30 backdrop-blur-[3px] lg:block"></div>

    <div class="mx-auto w-full max-w-7xl px-3 sm:px-4 lg:px-6">
    <div class="relative flex h-[var(--header-height)] items-center justify-between gap-4 rounded-2xl border px-2 transition-all duration-500 ease-out-expo sm:px-4"
         :class="barClasses" x-bind:style="scrolled ? 'height: 4rem' : ''" x-ref="bar">
        <x-site.logo />

        <nav aria-label="Primary" class="hidden lg:block" x-on:focusout="focusOut" x-on:mouseleave="restIndicator">
            {{-- Gliding highlight that follows the hovered item --}}
            <span x-ref="indicator" class="nav-indicator" aria-hidden="true"></span>
            <ul class="nav-track flex items-center gap-0.5 rounded-full border p-1">
                @foreach ($items as $index => $item)
                    @php
                        $menuId = 'menu-'.$index;
                        $active = $itemIsActive($item);
                        $itemClasses = 'nav-link inline-flex items-center gap-1 whitespace-nowrap rounded-full px-2.5 py-2 text-sm font-medium transition-colors duration-300 xl:px-3.5 xl:text-[0.9375rem] '.($active ? 'text-foreground' : 'text-foreground/70 hover:text-foreground');
                    @endphp
                    <li @class(['relative' => ! $item['mega']]) x-on:mouseenter="enterItem" x-on:mouseleave="closeOnLeave" data-menu="{{ ($item['mega'] || $item['children']) ? $menuId : '' }}">
                        @if ($item['mega'] || $item['children'])
                            <button type="button" x-on:click="toggleMenu('{{ $menuId }}')" x-on:focus="focusItem" :aria-expanded="isOpen('{{ $menuId }}')" aria-controls="{{ $menuId }}"
                                    class="{{ $itemClasses }}" @if ($active) data-active @endif>
                                {{ $item['label'] }}
                                <span class="transition-transform duration-300 ease-out-expo" :class="{ 'rotate-180': isOpen('{{ $menuId }}') }"><x-heroicon-m-chevron-down class="size-4 opacity-70" aria-hidden="true" /></span>
                            </button>

                            @if ($item['mega'])
                                <div id="{{ $menuId }}" x-show="isOpen('{{ $menuId }}')" x-cloak x-on:mouseenter="keepOpen" x-on:mouseleave="closeOnLeave"
                                     x-transition:enter="transition duration-300 ease-out-expo" x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.985]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                                     class="absolute inset-x-0 top-full z-50 origin-top pt-3">
                                    <div class="mega-panel grid overflow-hidden rounded-[1.75rem] lg:grid-cols-[15rem_1fr] xl:grid-cols-[16rem_1fr_19rem]">
                                        {{-- Categories --}}
                                        <div class="border-r border-border/70 bg-surface/70 p-3">
                                            <p class="px-3 pt-2 pb-3 text-[0.6875rem] font-semibold tracking-[0.18em] text-muted uppercase">Capabilities</p>
                                            <ul class="space-y-1">
                                                @foreach ($mega as $group)
                                                    <li>
                                                        <button type="button" x-on:mouseenter="showGroup({{ $loop->index }})" x-on:focus="showGroup({{ $loop->index }})" x-on:click="showGroup({{ $loop->index }})"
                                                                :aria-pressed="isGroup({{ $loop->index }})" class="mega-category group/cat" :class="{ 'is-current': isGroup({{ $loop->index }}) }">
                                                            <span class="mega-category-icon"><x-ui.icon :name="$group['icon']" class="size-[1.1rem]" /></span>
                                                            <span class="min-w-0 flex-1 leading-tight">{{ $group['name'] }}</span>
                                                            <span class="text-xs font-medium text-muted tabular-nums">{{ count($group['services']) }}</span>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            @if ($item['url'])
                                                <a href="{{ $item['url'] }}" x-on:click="closeMenus" class="link-arrow mt-4 mb-1 ml-3 text-sm">All services <x-heroicon-m-arrow-right class="size-4" aria-hidden="true" /></a>
                                            @endif
                                        </div>

                                        {{-- Services in the selected category --}}
                                        <div class="relative min-h-[22rem] p-6">
                                            @foreach ($mega as $group)
                                                <div x-show="isGroup({{ $loop->index }})" @unless ($loop->first) x-cloak @endunless
                                                     x-transition:enter="transition duration-300 ease-out-expo" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">
                                                    <p class="flex items-center gap-2 text-[0.6875rem] font-semibold tracking-[0.18em] text-primary uppercase">
                                                        <x-ui.icon :name="$group['icon']" class="size-4" />{{ $group['name'] }}
                                                    </p>
                                                    <ul class="mt-4 grid gap-1 xl:grid-cols-2">
                                                        @foreach ($group['services'] as $service)
                                                            <li>
                                                                <a href="{{ $service['url'] }}" x-on:click="closeMenus" class="mega-link group/link">
                                                                    <span class="mega-link-icon"><x-ui.icon :name="$service['icon']" :fallback="$group['icon']" class="size-5" /></span>
                                                                    <span class="min-w-0">
                                                                        <span class="flex items-center gap-1.5 text-sm font-semibold text-foreground">
                                                                            {{ $service['title'] }}
                                                                            <x-heroicon-m-arrow-right class="size-3.5 -translate-x-1 opacity-0 transition duration-300 group-hover/link:translate-x-0 group-hover/link:opacity-100" aria-hidden="true" />
                                                                        </span>
                                                                        @if (filled($service['excerpt']))
                                                                            <span class="mt-0.5 line-clamp-2 block text-[0.8125rem] leading-snug text-muted">{{ $service['excerpt'] }}</span>
                                                                        @endif
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Featured --}}
                                        <div class="dark stage relative hidden flex-col p-5 xl:flex">
                                            <div class="aurora -top-16 -right-10 size-56 bg-[#4f46e5]/50" aria-hidden="true"></div>
                                            <div class="aurora -bottom-20 -left-10 size-52 bg-[#0891b2]/35" aria-hidden="true"></div>
                                            <div class="relative overflow-hidden rounded-2xl border border-white/10">
                                                <x-art.frame name="strategy" tone="violet" class="aspect-[16/11]" />
                                            </div>
                                            <p class="relative mt-5 text-[0.6875rem] font-semibold tracking-[0.18em] text-white/60 uppercase">Not sure where to start?</p>
                                            <p class="relative mt-2 font-display text-lg leading-snug font-bold text-white">Book a free discovery call with a solution architect.</p>
                                            <a href="{{ route('project.create') }}" x-on:click="closeMenus" class="btn btn-inverse btn-sm relative mt-6 w-fit">Start a project <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div id="{{ $menuId }}" x-show="isOpen('{{ $menuId }}')" x-cloak x-on:mouseenter="keepOpen"
                                     x-transition:enter="transition duration-300 ease-out-expo" x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                                     class="absolute top-full left-1/2 z-50 w-[22rem] origin-top -translate-x-1/2 pt-3">
                                    <ul class="mega-panel rounded-3xl p-2">
                                        @foreach ($item['children'] as $child)
                                            <li>
                                                <a href="{{ $child['url'] }}" x-on:click="closeMenus" @if ($child['new_tab']) target="_blank" rel="noopener" @endif
                                                   @if ($isActive($child['url'])) aria-current="page" @endif class="mega-link group/link">
                                                    <span class="mega-link-icon"><x-ui.icon :name="$child['icon']" fallback="squares-2x2" class="size-5" /></span>
                                                    <span class="min-w-0">
                                                        <span class="block text-sm font-semibold text-foreground">{{ $child['label'] }}</span>
                                                        @if ($child['description'])
                                                            <span class="mt-0.5 block text-[0.8125rem] leading-snug text-muted">{{ $child['description'] }}</span>
                                                        @endif
                                                    </span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @else
                            <a href="{{ $item['url'] }}" x-on:focus="focusItem" @if ($item['new_tab']) target="_blank" rel="noopener" @endif
                               @if ($active) aria-current="page" data-active @endif class="{{ $itemClasses }}">{{ $item['label'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex items-center gap-1 sm:gap-2">
            @if (settings('features.search_enabled'))
                <a href="{{ route('search') }}" class="grid size-10 shrink-0 place-items-center rounded-full text-foreground/80 transition hover:bg-foreground/5 hover:text-foreground" aria-label="Search the site">
                    <x-heroicon-o-magnifying-glass class="size-5" aria-hidden="true" />
                </a>
            @endif
            @if ($themeSwitcher)
                <x-site.theme-toggle />
            @endif
            @if ($ctaLabel && $ctaUrl)
                <a href="{{ $ctaUrl }}" class="btn btn-primary btn-sm ml-1 hidden sm:inline-flex lg:hidden xl:inline-flex" data-magnetic="0.2">{{ $ctaLabel }} <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
            @endif
            <button type="button" x-on:click="toggleMobile" class="grid size-10 place-items-center rounded-full text-foreground transition hover:bg-foreground/5 lg:hidden"
                    :aria-expanded="mobileOpen" aria-controls="mobile-menu" aria-label="Open menu">
                <x-heroicon-o-bars-3 class="size-6" aria-hidden="true" />
            </button>
        </div>
    </div>
    </div>

    {{-- Mobile drawer --}}
    <div id="mobile-menu" x-show="mobileOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Site menu">
        <div x-show="mobileOpen" x-transition.opacity class="absolute inset-0 bg-ink/50 backdrop-blur-sm" x-on:click="closeMobile"></div>
        <div x-show="mobileOpen" x-trap.inert.noscroll="mobileOpen"
             x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col overflow-y-auto bg-background shadow-2xl">
            <div class="flex h-[var(--header-height)] items-center justify-between border-b border-border px-5">
                <x-site.logo />
                <button type="button" x-on:click="closeMobile" class="grid size-10 place-items-center rounded-full hover:bg-foreground/5" aria-label="Close menu">
                    <x-heroicon-o-x-mark class="size-6" aria-hidden="true" />
                </button>
            </div>
            <nav aria-label="Mobile" class="flex-1 px-3 py-4">
                <ul class="space-y-1">
                    @foreach ($items as $index => $item)
                        @php $mobileId = 'mobile-'.$index; @endphp
                        <li>
                            @if ($item['mega'] || $item['children'])
                                <button type="button" x-on:click="toggleMenu('{{ $mobileId }}')" :aria-expanded="isOpen('{{ $mobileId }}')"
                                        class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-base font-semibold text-foreground hover:bg-foreground/5">
                                    {{ $item['label'] }}
                                    <span class="transition-transform duration-300" :class="{ 'rotate-180': isOpen('{{ $mobileId }}') }"><x-heroicon-m-chevron-down class="size-5" aria-hidden="true" /></span>
                                </button>
                                <div x-show="isOpen('{{ $mobileId }}')" x-collapse x-cloak>
                                    @if ($item['mega'])
                                        <div class="space-y-4 px-2 pt-1 pb-3">
                                            @foreach ($mega as $group)
                                                <div>
                                                    <p class="flex items-center gap-2 px-2 text-[0.6875rem] font-semibold tracking-[0.16em] text-primary uppercase"><x-ui.icon :name="$group['icon']" class="size-4" />{{ $group['name'] }}</p>
                                                    <ul class="mt-1">
                                                        @foreach ($group['services'] as $service)
                                                            <li><a href="{{ $service['url'] }}" class="block rounded-lg px-2 py-2 text-sm text-foreground/85 hover:bg-foreground/5 hover:text-primary">{{ $service['title'] }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                            @if ($item['url'])
                                                <a href="{{ $item['url'] }}" class="link-arrow px-2 text-sm">All services <x-heroicon-m-arrow-right class="size-4" aria-hidden="true" /></a>
                                            @endif
                                        </div>
                                    @else
                                        <ul class="space-y-0.5 px-2 pt-1 pb-3">
                                            @foreach ($item['children'] as $child)
                                                <li>
                                                    <a href="{{ $child['url'] }}" class="flex items-center gap-3 rounded-xl px-2 py-2 text-sm text-foreground/85 hover:bg-foreground/5 hover:text-primary">
                                                        <span class="mega-link-icon size-8"><x-ui.icon :name="$child['icon']" fallback="squares-2x2" class="size-4" /></span>{{ $child['label'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @else
                                <a href="{{ $item['url'] }}" class="block rounded-xl px-4 py-3 text-base font-semibold text-foreground hover:bg-foreground/5" @if ($isActive($item['url'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
            @if ($ctaLabel && $ctaUrl)
                <div class="border-t border-border p-5">
                    <a href="{{ $ctaUrl }}" class="btn btn-primary w-full">{{ $ctaLabel }} <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                </div>
            @endif
        </div>
    </div>
</header>
