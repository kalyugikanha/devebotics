@props(['name' => null, 'fallback' => 'sparkles'])
{{ svg(\App\Support\IconRegistry::component($name, $fallback), $attributes->get('class', 'size-5'), ['aria-hidden' => 'true']) }}
