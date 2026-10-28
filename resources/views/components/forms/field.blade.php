@props(['name', 'label', 'required' => false, 'help' => null, 'id' => null])
@php
    $id = $id ?? 'field-'.str_replace(['[', ']', '.'], '-', $name);
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
@endphp
<div {{ $attributes->merge(['class' => '']) }}>
    <label for="{{ $id }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-highlight" aria-hidden="true">*</span>
        @else
            <span class="optional">(optional)</span>
        @endif
    </label>
    {{ $slot }}
    @if ($help)
        <p id="{{ $id }}-help" class="form-help">{{ $help }}</p>
    @endif
    @error($errorKey)
        <p id="{{ $id }}-error" class="form-error"><x-heroicon-m-exclamation-circle class="size-4 shrink-0" aria-hidden="true" />{{ $message }}</p>
    @enderror
</div>
