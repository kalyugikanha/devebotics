{{-- Floating WhatsApp chat widget. Hidden in production until a number is configured (Settings › Company & contact). --}}
@php
    $contact = settings()->group('contact');
    $number = preg_replace('/\D+/', '', (string) $contact['whatsapp_number']);
    $isPreview = $number === '' && ! app()->isProduction();
    $link = fn (?string $text) => $number === '' ? null : 'https://wa.me/'.$number.'?text='.rawurlencode((string) $text);
    $replies = array_slice(array_values(array_filter((array) $contact['whatsapp_quick_replies'])), 0, 6);
    $company = (string) settings('company.name');
@endphp
@if ($contact['whatsapp_widget_enabled'] && ($number !== '' || $isPreview))
    <div x-data="whatsappChat" x-on:keydown.escape.window="close" class="fixed right-4 bottom-4 z-[56] sm:right-6 sm:bottom-6 no-print">
        <div id="whatsapp-panel" x-show="open" x-cloak role="dialog" aria-modal="false" aria-labelledby="whatsapp-title"
             x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-2"
             class="absolute right-0 bottom-[4.5rem] w-[min(23rem,calc(100vw-2rem))] origin-bottom-right overflow-hidden rounded-3xl bg-background shadow-[0_30px_80px_-20px_rgba(5,8,22,0.45)] ring-1 ring-black/5 dark:ring-white/10">
            <div class="relative flex items-center gap-3 bg-[linear-gradient(135deg,#075e54,#0b7a6e)] px-5 py-4 text-white">
                <span class="relative grid size-11 shrink-0 place-items-center rounded-full bg-white">
                    <img src="{{ \App\Support\Branding::markUrl() }}" alt="" width="32" height="26" class="h-6 w-auto">
                    <span class="absolute right-0 bottom-0 size-3 rounded-full border-2 border-[#075e54] bg-whatsapp" aria-hidden="true"></span>
                </span>
                <div class="min-w-0 flex-1">
                    <p id="whatsapp-title" class="truncate font-display font-bold">{{ $company }}</p>
                    <p class="text-xs text-white/80">Chat with our team on WhatsApp</p>
                </div>
                <button type="button" x-on:click="close" class="grid size-9 place-items-center rounded-full text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="Close chat">
                    <x-heroicon-m-x-mark class="size-5" aria-hidden="true" />
                </button>
            </div>

            <div class="space-y-3 bg-[#efeae2] px-4 py-5 dark:bg-[#0b141a]">
                <div class="max-w-[88%] rounded-2xl rounded-tl-sm bg-white px-4 py-3 text-sm leading-relaxed text-[#111b21] shadow-sm dark:bg-[#202c33] dark:text-[#e9edef]">
                    {{ $contact['whatsapp_greeting'] }}
                    <span class="mt-1 block text-right text-[0.6875rem] text-[#667781] dark:text-[#8696a0]">{{ $company }}</span>
                </div>

                @if ($isPreview)
                    <p class="rounded-xl bg-amber-100 px-3 py-2 text-xs font-medium text-amber-900">
                        Preview only (not shown in production): add your WhatsApp number in Admin › Settings › Company &amp; contact to activate this chat.
                    </p>
                @endif

                @if ($replies)
                    <ul class="space-y-2" aria-label="Quick messages">
                        @foreach ($replies as $reply)
                            <li>
                                <a @if ($link($reply)) href="{{ $link($reply) }}" target="_blank" rel="noopener" @else aria-disabled="true" @endif
                                   @if ($loop->first) data-wa-first @endif
                                   class="flex items-center justify-between gap-3 rounded-2xl border border-[#0b7a6e]/25 bg-white/90 px-4 py-2.5 text-sm font-medium text-[#075e54] transition hover:border-[#0b7a6e] hover:bg-white dark:bg-[#111b21] dark:text-[#25d366]">
                                    <span>{{ $reply }}</span>
                                    <x-heroicon-m-paper-airplane class="size-4 shrink-0" aria-hidden="true" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="p-4">
                <a @if ($link($contact['whatsapp_message'])) href="{{ $link($contact['whatsapp_message']) }}" target="_blank" rel="noopener" @else aria-disabled="true" @endif
                   @unless ($replies) data-wa-first @endunless
                   class="btn w-full bg-whatsapp-deep text-white hover:bg-whatsapp-dark {{ $number === '' ? 'pointer-events-none opacity-60' : '' }}">
                    <x-ui.social-icon network="whatsapp" class="size-5" /> Start a WhatsApp chat
                </a>
                <p class="mt-2.5 text-center text-xs text-muted">Opens WhatsApp in a new tab. Prefer email? <a href="{{ route('contact.create') }}" class="font-semibold text-primary hover:underline">Contact form</a></p>
            </div>
        </div>

        <div x-show="hinted && !open" x-cloak x-transition.opacity class="absolute right-[4.5rem] bottom-2 hidden w-56 rounded-2xl bg-background px-4 py-3 text-sm shadow-xl ring-1 ring-black/5 sm:block dark:ring-white/10">
            <button type="button" x-on:click="toggle" class="text-left font-medium text-foreground">Questions? Chat with us on WhatsApp 👋</button>
            <button type="button" x-on:click="dismissHint" class="absolute -top-2 -right-2 grid size-6 place-items-center rounded-full bg-foreground text-background" aria-label="Dismiss">
                <x-heroicon-m-x-mark class="size-3.5" aria-hidden="true" />
            </button>
        </div>

        <button type="button" x-on:click="toggle" :aria-expanded="open" aria-controls="whatsapp-panel" aria-label="Chat with us on WhatsApp"
                class="relative grid size-14 place-items-center rounded-full bg-whatsapp-teal text-white shadow-[0_14px_34px_-10px_rgba(18,140,126,0.75)] transition duration-300 hover:scale-105 hover:bg-whatsapp-deep">
            <span class="ambient absolute inset-0 animate-pulse-ring rounded-full bg-whatsapp/50" x-show="!open" aria-hidden="true"></span>
            <span x-show="!open" class="relative"><x-ui.social-icon network="whatsapp" class="size-7" /></span>
            <span x-show="open" x-cloak class="relative"><x-heroicon-m-x-mark class="size-7" aria-hidden="true" /></span>
        </button>
    </div>
@endif
