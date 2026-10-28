@php
    $guard = app(\App\Support\Forms\SpamGuard::class);
@endphp
{{-- Honeypot: hidden from people and assistive technology; bots tend to fill it. --}}
<div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
    <label for="{{ $guard->honeypotField() }}-{{ $id = \Illuminate\Support\Str::random(6) }}">Leave this field empty</label>
    <input type="text" id="{{ $guard->honeypotField() }}-{{ $id }}" name="{{ $guard->honeypotField() }}" value="" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="_form_token" value="{{ $guard->token() }}">
@if ($guard->turnstileEnabled())
    <div class="cf-turnstile mt-2" data-sitekey="{{ config('softec.turnstile.site_key') }}" data-theme="auto"></div>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer nonce="{{ app()->bound('csp-nonce') ? app('csp-nonce') : '' }}"></script>
@endif
