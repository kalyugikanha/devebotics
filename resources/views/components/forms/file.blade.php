@props(['name', 'accept' => null, 'help' => null, 'required' => false])
<div x-data="fileInput" class="relative">
    <label for="field-{{ $name }}" class="flex cursor-pointer items-center gap-4 rounded-control border border-dashed border-border bg-surface px-4 py-4 transition hover:border-primary/50">
        <span class="icon-tile-soft"><x-heroicon-o-paper-clip aria-hidden="true" /></span>
        <span class="min-w-0 flex-1">
            <span class="block text-sm font-semibold text-foreground" x-text="hasFile ? fileName : 'Choose a file'">Choose a file</span>
            @if ($help)
                <span class="block text-xs text-muted">{{ $help }}</span>
            @endif
        </span>
    </label>
    <input id="field-{{ $name }}" type="file" name="{{ $name }}" @if ($accept) accept="{{ $accept }}" @endif @if ($required) required @endif
           class="sr-only" x-on:change="update" @error($name) aria-invalid="true" @enderror>
</div>
