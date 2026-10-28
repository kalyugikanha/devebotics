@props(['label' => 'Submit'])
<button type="submit" {{ $attributes->merge(['class' => 'btn btn-primary btn-lg']) }} :disabled="submitting" :aria-busy="submitting">
    <svg x-show="submitting" x-cloak class="size-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="3"/><path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
    <span>{{ $label }}</span>
    <x-heroicon-m-arrow-right class="btn-arrow" x-show="!submitting" aria-hidden="true" />
</button>
