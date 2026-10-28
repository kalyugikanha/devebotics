{{-- Theme-aware, animated SVG illustration from resources/views/components/illustrations/{name}.blade.php. --}}
@props(['name' => 'software'])
@php
    $name = \App\Support\Art\IllustrationRegistry::isValid($name) ? $name : 'software';
    $uid = 'ill-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8));
@endphp
<div {{ $attributes->merge(['class' => 'illustration relative']) }} aria-hidden="true">
    @if (view()->exists('components.illustrations.'.$name))
        @include('components.illustrations.'.$name, ['uid' => $uid])
    @else
        <x-art.cover :seed="'ill-'.$name" />
    @endif
</div>
