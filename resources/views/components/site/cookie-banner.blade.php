@props(['consent' => null, 'policyUrl' => null])
<div x-data="cookieConsent" x-show="open" x-cloak data-endpoint="{{ route('cookie-consent') }}" data-version="{{ settings('privacy.consent_version') }}"
     class="fixed inset-x-3 bottom-3 z-[60] sm:inset-x-auto sm:left-6 sm:bottom-6 sm:w-[24rem] no-print"
     role="dialog" aria-modal="false" aria-labelledby="cookie-title" aria-describedby="cookie-desc"
     x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0">
    <div class="glass rounded-panel p-5">
        <div class="flex items-start gap-3">
            <span class="icon-tile-soft"><x-heroicon-o-shield-check aria-hidden="true" /></span>
            <div>
                <h2 id="cookie-title" class="font-display text-base font-bold">Your privacy choices</h2>
                <p id="cookie-desc" class="mt-1 text-sm text-muted">
                    We use essential cookies to run this site. With your permission we would also use analytics to improve it.
                    @if ($policyUrl)<a href="{{ $policyUrl }}" class="font-semibold text-primary underline-offset-4 hover:underline">Cookie policy</a>@endif
                </p>
            </div>
        </div>

        <div x-show="customizing" x-collapse class="mt-5 space-y-3">
            <div class="flex items-center justify-between gap-4 rounded-xl border border-border p-3">
                <div><p class="text-sm font-semibold">Essential</p><p class="text-xs text-muted">Security, forms and your preferences. Always on.</p></div>
                <span class="text-xs font-semibold text-muted">Required</span>
            </div>
            <label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-border p-3">
                <span><span class="block text-sm font-semibold">Analytics</span><span class="block text-xs text-muted">Anonymous usage statistics.</span></span>
                <input type="checkbox" class="form-check" x-bind:checked="analytics" x-on:change="toggleAnalytics">
            </label>
            <label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-border p-3">
                <span><span class="block text-sm font-semibold">Marketing</span><span class="block text-xs text-muted">Measuring campaign effectiveness.</span></span>
                <input type="checkbox" class="form-check" x-bind:checked="marketing" x-on:change="toggleMarketing">
            </label>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            <button type="button" x-on:click="acceptAll" class="btn btn-primary btn-sm flex-1" :disabled="saving">Accept all</button>
            <button type="button" x-on:click="rejectOptional" class="btn btn-secondary btn-sm flex-1" :disabled="saving">Essential only</button>
            <button type="button" x-show="!customizing" x-on:click="customize" class="btn btn-ghost btn-sm w-full">Customise</button>
            <button type="button" x-show="customizing" x-on:click="save" class="btn btn-ghost btn-sm w-full" :disabled="saving">Save my choices</button>
        </div>
    </div>
</div>
