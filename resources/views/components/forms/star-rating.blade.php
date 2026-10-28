@props(['name', 'label', 'required' => false, 'value' => null, 'size' => 'lg'])
@php $current = (int) old($name, $value); @endphp
<fieldset {{ $attributes }}>
    <legend class="form-label">
        {{ $label }}
        @if ($required)<span class="text-highlight" aria-hidden="true">*</span>@else<span class="optional">(optional)</span>@endif
    </legend>
    {{-- Radios are in reverse DOM order so the CSS sibling selector can highlight lower stars; arrow keys still work natively. --}}
    <div class="star-rating {{ $size === 'sm' ? '[&_svg]:size-6' : '' }}" @error($name) aria-invalid="true" @enderror>
        @for ($star = 5; $star >= 1; $star--)
            <input type="radio" id="{{ $name }}-{{ $star }}" name="{{ $name }}" value="{{ $star }}" @checked($current === $star) @if ($required && $star === 5) required @endif>
            <label for="{{ $name }}-{{ $star }}" title="{{ $star }} {{ \Illuminate\Support\Str::plural('star', $star) }}">
                <x-heroicon-s-star aria-hidden="true" />
                <span class="sr-only">{{ $star }} {{ \Illuminate\Support\Str::plural('star', $star) }}</span>
            </label>
        @endfor
    </div>
    @error($name)
        <p class="form-error"><x-heroicon-m-exclamation-circle class="size-4 shrink-0" aria-hidden="true" />{{ $message }}</p>
    @enderror
</fieldset>
