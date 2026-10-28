{{-- Illustration on a soft brand-tinted canvas (used for cards and heroes without uploaded images). --}}
@props(['name' => 'software', 'tone' => null])
@php
    $tones = [
        'violet' => 'bg-[radial-gradient(120%_90%_at_0%_0%,#e0e7ff_0%,#f5f7ff_45%,#ecfeff_100%)] dark:bg-[radial-gradient(120%_90%_at_0%_0%,#161d4a_0%,#0c1428_55%,#082f3a_120%)]',
        'pink' => 'bg-[radial-gradient(120%_90%_at_100%_0%,#ede9fe_0%,#f8f7ff_45%,#eff6ff_100%)] dark:bg-[radial-gradient(120%_90%_at_100%_0%,#231a4f_0%,#0c1428_55%,#0b2447_130%)]',
        'cyan' => 'bg-[radial-gradient(120%_90%_at_0%_100%,#cffafe_0%,#f0fdfa_45%,#eef2ff_100%)] dark:bg-[radial-gradient(120%_90%_at_0%_100%,#083344_0%,#0c1428_55%,#1e1b4b_120%)]',
        'amber' => 'bg-[radial-gradient(120%_90%_at_100%_100%,#e0f2fe_0%,#f0f9ff_40%,#eef2ff_100%)] dark:bg-[radial-gradient(120%_90%_at_100%_100%,#0b2447_0%,#0c1428_55%,#161d4a_120%)]',
    ];
    $tone = $tone && isset($tones[$tone]) ? $tone : array_keys($tones)[crc32((string) $name) % count($tones)];
    $positioned = preg_match('/\b(absolute|fixed|sticky)\b/', (string) $attributes->get('class'));
@endphp
<div {{ $attributes->merge(['class' => ($positioned ? '' : 'relative ').'isolate overflow-hidden '.$tones[$tone]]) }}>
    <div class="bg-grid absolute inset-0 -z-10 opacity-50" aria-hidden="true"></div>
    <x-illustration :name="$name" class="mx-auto h-full w-full max-w-[34rem] p-5 transition duration-700 ease-out-expo group-hover:scale-[1.04] sm:p-6" />
</div>
