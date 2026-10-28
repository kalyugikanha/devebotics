@php
    $contact = settings()->group('contact');
    $whatsapp = filled($contact['whatsapp_number']) ? 'https://wa.me/'.preg_replace('/\D+/', '', (string) $contact['whatsapp_number']).'?text='.rawurlencode((string) $contact['whatsapp_message']) : null;
    $address = collect([$contact['address_line1'], $contact['address_line2'], trim(($contact['city'] ?? '').' '.($contact['postal_code'] ?? '')), $contact['region'], $contact['country']])->filter()->implode(', ');
    $map = filled($contact['map_embed_url']) && str_starts_with((string) $contact['map_embed_url'], 'https://www.google.com/maps/embed') ? $contact['map_embed_url'] : null;
    $hours = collect($contact['business_hours'] ?? [])->filter(fn ($row) => is_array($row) && filled($row['days'] ?? null));
@endphp
<div class="space-y-4">
    @foreach ([
        ['envelope', 'Email', $contact['email'], $contact['email'] ? 'mailto:'.$contact['email'] : null],
        ['briefcase', 'Sales', $contact['sales_email'], $contact['sales_email'] ? 'mailto:'.$contact['sales_email'] : null],
        ['phone', 'Phone', $contact['phone'], $contact['phone'] ? 'tel:'.preg_replace('/[^0-9+]/', '', (string) $contact['phone']) : null],
        ['chat-bubble-left-right', 'WhatsApp', $whatsapp ? 'Chat with us' : null, $whatsapp],
        ['calendar-days', 'Book a call', filled($contact['booking_url']) ? 'Schedule a consultation' : null, \App\Support\SafeUrl::make($contact['booking_url'])],
    ] as [$icon, $label, $value, $href])
        @if ($value && $href)
            <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener" @endif class="card card-interactive flex items-center gap-4 p-5 sm:p-5">
                <span class="icon-tile-soft"><x-ui.icon :name="$icon" /></span>
                <span><span class="block text-xs font-semibold tracking-wide text-muted uppercase">{{ $label }}</span><span class="block font-semibold break-all text-foreground">{{ $value }}</span></span>
            </a>
        @endif
    @endforeach

    @if ($contact['show_address'] && $address)
        <div class="card flex items-start gap-4 p-5 sm:p-5">
            <span class="icon-tile-soft"><x-heroicon-o-map-pin aria-hidden="true" /></span>
            <div><p class="text-xs font-semibold tracking-wide text-muted uppercase">Office</p><address class="not-italic font-semibold">{{ $address }}</address></div>
        </div>
    @endif

    @if ($hours->isNotEmpty())
        <div class="card p-5 sm:p-5">
            <p class="flex items-center gap-2 text-xs font-semibold tracking-wide text-muted uppercase"><x-heroicon-o-clock class="size-4" aria-hidden="true" />Business hours</p>
            <dl class="mt-3 space-y-1 text-sm">
                @foreach ($hours as $row)<div class="flex justify-between gap-4"><dt class="text-muted">{{ $row['days'] }}</dt><dd class="font-medium">{{ $row['hours'] ?? '' }}</dd></div>@endforeach
            </dl>
        </div>
    @endif

    @if ($map)
        <div class="overflow-hidden rounded-card border border-border">
            <iframe src="{{ $map }}" title="Office location map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="h-64 w-full"></iframe>
        </div>
    @endif
</div>
