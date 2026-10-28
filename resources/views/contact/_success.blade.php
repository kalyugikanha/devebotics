@if (session('inquiry_reference'))
    <div class="alert alert-success" role="status" tabindex="-1" x-data x-init="$el.focus()">
        <x-heroicon-o-check-circle class="size-6 shrink-0" aria-hidden="true" />
        <div>
            <p class="font-semibold">Thank you — your message has been received.</p>
            <p class="mt-1">Your reference is <strong class="font-mono">{{ session('inquiry_reference') }}</strong>. We usually reply within one business day.
                @if (settings('forms.send_acknowledgements', true))
                    A confirmation has been sent to your inbox.
                @endif
            </p>
        </div>
    </div>
@endif
