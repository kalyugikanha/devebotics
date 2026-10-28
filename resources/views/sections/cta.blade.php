<section id="{{ $section->anchor ?: 'section-'.$section->id }}" class="section section-plain" aria-labelledby="section-heading-{{ $section->id }}">
    <div class="container-site">
        <div class="dark stage border-gradient rounded-[2.25rem] px-6 py-16 text-center sm:px-12 lg:py-24" data-reveal>
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(70%_100%_at_50%_0%,#1a2160_0%,#060b1a_70%)]" aria-hidden="true"></div>
            <div class="aurora ambient animate-aurora -top-32 left-[10%] size-[26rem] bg-[#4f46e5]/45" aria-hidden="true"></div>
            <div class="aurora ambient animate-aurora -right-24 -bottom-40 size-[24rem] bg-[#0891b2]/30 [animation-delay:-7s]" aria-hidden="true"></div>
            <div class="bg-grid-lines absolute inset-0 -z-10 opacity-40" aria-hidden="true"></div>
            <svg class="ambient absolute -top-28 -right-28 -z-10 size-[30rem] animate-orbit text-white/10" viewBox="0 0 400 400" aria-hidden="true"><circle cx="200" cy="200" r="190" fill="none" stroke="currentColor" stroke-dasharray="3 9"/><circle cx="200" cy="200" r="140" fill="none" stroke="currentColor"/><circle cx="200" cy="60" r="8" fill="#22d3ee"/></svg>
            <div class="mx-auto max-w-3xl">
                <span class="mx-auto grid size-16 place-items-center rounded-2xl border border-white/15 bg-white/5 backdrop-blur">
                    <img src="{{ \App\Support\Branding::markUrl() }}" alt="" width="48" height="39" class="h-9 w-auto">
                </span>
                <h2 id="section-heading-{{ $section->id }}" class="mt-8 font-display text-4xl font-extrabold tracking-[-0.03em] text-white sm:text-5xl lg:text-6xl">{!! \App\Support\Typography::highlight($section->title) !!}</h2>
                @if ($section->subtitle)
                    <p class="mx-auto mt-6 max-w-2xl text-lg text-white/70 sm:text-xl">{{ $section->subtitle }}</p>
                @endif
                @php
                    $primary = $section->setting('primary_cta');
                    $secondary = $section->setting('secondary_cta');
                    $primaryUrl = \App\Support\SafeUrl::make($primary['url'] ?? null);
                    $secondaryUrl = \App\Support\SafeUrl::make($secondary['url'] ?? null);
                @endphp
                <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                    @if ($primaryUrl && filled($primary['label'] ?? null))
                        <a href="{{ $primaryUrl }}" class="btn btn-primary btn-lg" data-magnetic="0.25">{{ $primary['label'] }} <x-heroicon-m-arrow-right class="btn-arrow" aria-hidden="true" /></a>
                    @endif
                    @if ($secondaryUrl && filled($secondary['label'] ?? null))
                        <a href="{{ $secondaryUrl }}" class="btn btn-glass btn-lg">{{ $secondary['label'] }}</a>
                    @endif
                </div>
                @php $number = preg_replace('/\D+/', '', (string) settings('contact.whatsapp_number')); @endphp
                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-white/60">
                    @if ($number !== '')
                        <a href="https://wa.me/{{ $number }}?text={{ rawurlencode((string) settings('contact.whatsapp_message')) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 font-semibold text-white/85 hover:text-white"><x-ui.social-icon network="whatsapp" class="size-4" /> Chat on WhatsApp</a>
                    @endif
                    @if (filled(settings('contact.booking_url')))
                        <a href="{{ \App\Support\SafeUrl::make(settings('contact.booking_url')) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 font-semibold text-white/85 hover:text-white"><x-heroicon-o-calendar-days class="size-4" aria-hidden="true" /> Book a consultation</a>
                    @endif
                    <span class="inline-flex items-center gap-2"><x-heroicon-o-shield-check class="size-4" aria-hidden="true" /> Your details stay private</span>
                </div>
            </div>
        </div>
    </div>
</section>
