@props(['show' => false])
@if ($show && settings('features.show_demo_badges', true))
    <span {{ $attributes->merge(['class' => 'badge-demo']) }} title="Demonstration content — replace before launch">Demo</span>
@endif
