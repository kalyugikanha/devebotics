@if ($errors->any())
    <div class="alert alert-error" role="alert" tabindex="-1" x-data x-init="$el.focus()">
        <x-heroicon-o-exclamation-triangle class="size-5 shrink-0" aria-hidden="true" />
        <div>
            <p class="font-semibold">Please check the form</p>
            @if ($errors->has('form'))
                <p class="mt-1">{{ $errors->first('form') }}</p>
            @else
                <p class="mt-1">Some fields need your attention. Errors are shown next to each field.</p>
            @endif
        </div>
    </div>
@endif
