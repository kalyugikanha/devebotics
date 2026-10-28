@props(['name' => 'privacy_consent'])
@php $policy = url('/privacy-policy'); @endphp
<div>
    <label class="flex items-start gap-3 text-sm text-muted">
        <input type="checkbox" name="{{ $name }}" value="1" required class="form-check" @checked(old($name)) @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
        <span>
            {{ settings('privacy.form_privacy_notice') }}
            <a href="{{ $policy }}" class="font-semibold text-primary underline-offset-4 hover:underline" target="_blank" rel="noopener">Privacy policy</a>.
            <span class="text-highlight" aria-hidden="true">*</span>
        </span>
    </label>
    @error($name)
        <p id="{{ $name }}-error" class="form-error"><x-heroicon-m-exclamation-circle class="size-4 shrink-0" aria-hidden="true" />{{ $message }}</p>
    @enderror
</div>
