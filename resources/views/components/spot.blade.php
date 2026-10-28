{{-- Small theme-aware feature illustration from resources/views/components/spots/{name}.blade.php. --}}
@props(['name' => 'spark'])
@php
    $name = \App\Support\Art\SpotRegistry::isValid($name) ? $name : 'spark';
    $uid = 'spot-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8));
@endphp
<div {{ $attributes->merge(['class' => 'spot relative']) }} aria-hidden="true">
    @include('components.spots.'.$name, ['uid' => $uid])
</div>
