@props(['navigation' => []])
@php
    $company = settings()->group('company');
    $contact = settings()->group('contact');
    $social = array_filter(settings()->group('social'));
    $columns = collect(['footer_1', 'footer_2', 'footer_3'])->map(fn ($key) => $navigation[$key] ?? null)->filter(fn ($menu) => $menu && $menu['items']);
    $legal = $navigation['footer_legal']['items'] ?? [];
    $address = collect([$contact['address_line1'], $contact['address_line2'], trim(($contact['city'] ?? '').' '.($contact['postal_code'] ?? '')), $contact['region'], $contact['country']])->filter()->implode(', ');
    $whatsapp = filled($contact['whatsapp_number']) ? 'https://wa.me/'.preg_replace('/\D+/', '', (string) $contact['whatsapp_number']).'?text='.rawurlencode((string) $contact['whatsapp_message']) : null;
@endphp
<footer role="contentinfo" class="dark stage">
    <div class="ribbon opacity-90"></div>
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(80%_70%_at_20%_0%,#1a1442_0%,#050816_60%)]" aria-hidden="true"></div>
    <div class="aurora ambient animate-aurora -top-40 left-1/4 size-[30rem] bg-[#4f46e5]/25" aria-hidden="true"></div>
    <div class="aurora ambient animate-aurora -right-32 bottom-0 size-[24rem] bg-[#06b6d4]/15 [animation-delay:-10s]" aria-hidden="true"></div>
    <div class="bg-grid-lines absolute inset-0 -z-10 opacity-30" aria-hidden="true"></div>

    <div class="container-site relative py-16 lg:py-20">
        <div class="grid gap-12 lg:grid-cols-[1fr_2.3fr]">
            <div data-reveal>
                <x-site.logo size="lg" />
                <p class="mt-5 max-w-sm text-muted">{{ $company['short_description'] }}</p>

                @if (settings('features.newsletter_enabled'))
                    <form id="newsletter" method="POST" action="{{ route('newsletter.store') }}" class="mt-8 max-w-sm" x-data="formState" x-on:submit="submit">
                        @csrf
                        <x-forms.spam-protection />
                        <label for="newsletter-email" class="text-sm font-semibold text-foreground">Get insights in your inbox</label>
                        <div class="mt-2 flex gap-2">
                            <input id="newsletter-email" type="email" name="email" required autocomplete="email" placeholder="you@company.com"
                                   class="form-control rounded-full" @error('email', 'newsletter') aria-invalid="true" @enderror>
                            <button type="submit" class="btn btn-primary shrink-0" :disabled="submitting">Subscribe</button>
                        </div>
                        @if (session('newsletter_status'))
                            <p class="form-help text-emerald-600 dark:text-emerald-400" role="status">{{ session('newsletter_status') }}</p>
                        @else
                            <p class="form-help text-xs">Occasional updates. Confirm by email; unsubscribe anytime.</p>
                        @endif
                    </form>
                @endif
            </div>

            <div class="grid gap-10 sm:grid-cols-2 xl:grid-cols-[1.35fr_1fr_1fr_1.1fr]">
                @foreach ($columns as $column)
                    <nav aria-label="{{ $column['title'] ?? 'Footer' }}" data-reveal style="--i:{{ $loop->index }}">
                        @if ($column['title'])
                            <h2 class="text-sm font-semibold text-foreground">{{ $column['title'] }}</h2>
                        @endif
                        <ul class="mt-4 space-y-2.5">
                            @foreach ($column['items'] as $item)
                                @if ($item['url'])
                                    <li><a href="{{ $item['url'] }}" @if ($item['new_tab']) target="_blank" rel="noopener" @endif class="text-sm text-muted transition hover:text-primary">{{ $item['label'] }}</a></li>
                                @endif
                            @endforeach
                            @if ($loop->last && settings('company.brochure_media_id'))
                                <li><a href="{{ route('brochure') }}" class="text-sm text-muted transition hover:text-primary">Company profile (PDF)</a></li>
                            @endif
                        </ul>
                    </nav>
                @endforeach

                <div data-reveal style="--i:3">
                    <h2 class="text-sm font-semibold text-foreground">Get in touch</h2>
                    <ul class="mt-4 space-y-3 text-sm text-muted">
                        @if ($contact['email'])
                            <li class="flex gap-2.5"><x-heroicon-o-envelope class="mt-0.5 size-4 shrink-0 text-primary" aria-hidden="true" /><a href="mailto:{{ $contact['email'] }}" class="break-all hover:text-primary">{{ $contact['email'] }}</a></li>
                        @endif
                        @if ($contact['phone'])
                            <li class="flex gap-2.5"><x-heroicon-o-phone class="mt-0.5 size-4 shrink-0 text-primary" aria-hidden="true" /><a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $contact['phone']) }}" class="hover:text-primary">{{ $contact['phone'] }}</a></li>
                        @endif
                        @if ($whatsapp)
                            <li class="flex gap-2.5"><x-ui.social-icon network="whatsapp" class="mt-0.5 size-4 shrink-0 text-primary" /><a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="hover:text-primary">Chat on WhatsApp</a></li>
                        @endif
                        @if ($contact['show_address'] && $address)
                            <li class="flex gap-2.5"><x-heroicon-o-map-pin class="mt-0.5 size-4 shrink-0 text-primary" aria-hidden="true" /><span>{{ $address }}</span></li>
                        @endif
                        <li><a href="{{ route('contact.create') }}" class="link-arrow">Contact us <x-heroicon-m-arrow-right aria-hidden="true" /></a></li>
                    </ul>
                    @if ($social)
                        <ul class="mt-6 flex flex-wrap gap-2" aria-label="Social media">
                            @foreach ($social as $network => $url)
                                <li>
                                    <a href="{{ $url }}" target="_blank" rel="noopener me" class="grid size-10 place-items-center rounded-full border border-border bg-background text-muted transition hover:-translate-y-0.5 hover:border-primary/40 hover:text-primary" aria-label="{{ settings('company.name') }} on {{ ['x' => 'X', 'linkedin' => 'LinkedIn', 'github' => 'GitHub', 'youtube' => 'YouTube'][$network] ?? ucfirst($network) }}">
                                        <x-ui.social-icon :network="$network" class="size-4" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="divider-gradient mt-14"></div>

        <div class="mt-8 flex flex-col gap-4 text-sm text-muted lg:flex-row lg:items-center lg:justify-between">
            <p>&copy; {{ now()->year }} {{ $company['legal_name'] ?: $company['name'] }}. All rights reserved.</p>
            <ul class="flex flex-wrap items-center gap-x-5 gap-y-2">
                @foreach ($legal as $item)
                    @if ($item['url'])
                        <li><a href="{{ $item['url'] }}" class="hover:text-primary">{{ $item['label'] }}</a></li>
                    @endif
                @endforeach
                <li><button type="button" x-data x-on:click="$dispatch('open-cookie-preferences')" class="hover:text-primary">Cookie preferences</button></li>
                <li x-data="motionToggle"><button type="button" x-on:click="toggle" class="hover:text-primary" x-text="label">Reduce motion</button></li>
            </ul>
        </div>
    </div>
</footer>
