@props(['seo', 'isPreview' => false, 'bodyClass' => '', 'heroDark' => false])

@php
    $nonce = app()->bound('csp-nonce') ? app('csp-nonce') : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-theme-default="{{ $themeDefault }}"
      data-theme-switcher="{{ $themeSwitcher ? '1' : '0' }}"
      data-hero="{{ $motion['enabled'] ? $motion['hero'] : 'none' }}"
      class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Applies the visitor's theme before first paint (prevents a flash of the wrong theme). --}}
    <script nonce="{{ $nonce }}">(function(){var d=document.documentElement,m=null,dark;try{if(d.dataset.themeSwitcher==='1'){m=localStorage.getItem('softec-theme')}if(localStorage.getItem('softec-reduce-motion')==='1'){d.classList.add('reduce-motion','motion-reduced')}}catch(e){}if(['light','dark','system'].indexOf(m)<0){m=d.dataset.themeDefault||'system'}dark=m==='dark'||(m==='system'&&window.matchMedia('(prefers-color-scheme: dark)').matches);d.classList.toggle('dark',dark);d.style.colorScheme=dark?'dark':'light';})();</script>

    <x-seo.head :seo="$seo" />

    <link rel="icon" href="{{ \App\Support\Branding::faviconUrl() }}" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#070b17" media="(prefers-color-scheme: dark)">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style nonce="{{ $nonce }}">{!! $themeCss !!}</style>
    <script type="application/json" id="motion-config" nonce="{{ $nonce }}">@json($motion)</script>

    @if ($analyticsAllowed && ($ga4 || $gtm))
        @if ($gtm)
            <script nonce="{{ $nonce }}">window.dataLayer=window.dataLayer||[];window.dataLayer.push({'gtm.start':Date.now(),event:'gtm.js'});</script>
            <script async src="https://www.googletagmanager.com/gtm.js?id={{ $gtm }}" nonce="{{ $nonce }}"></script>
        @endif
        @if ($ga4)
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}" nonce="{{ $nonce }}"></script>
            <script nonce="{{ $nonce }}">window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $ga4 }}',{anonymize_ip:true});window.__softecAnalyticsLoaded=true;</script>
        @endif
    @endif
</head>
<body class="min-h-dvh {{ $bodyClass }}" data-hero-dark="{{ $heroDark ? '1' : '0' }}" @if ($ga4) data-ga4="{{ $ga4 }}" @endif @if ($gtm) data-gtm="{{ $gtm }}" @endif>
    <div class="scroll-progress" aria-hidden="true"></div>
    <a href="#main" class="skip-link">Skip to main content</a>

    @if ($isThemePreview)
        <div class="relative z-50 bg-amber-400 px-4 py-2 text-center text-sm font-semibold text-ink">
            Previewing the <strong>draft theme</strong> — visitors still see the published theme.
            <a href="{{ route('theme-preview.stop') }}" class="ml-2 underline underline-offset-4">Exit preview</a>
        </div>
    @endif

    @if ($isPreview)
        <div class="relative z-50 bg-ink px-4 py-2 text-center text-sm font-semibold text-white" role="status">
            Preview of unpublished content — this page is not visible to the public.
        </div>
    @endif

    <x-site.announcement :announcement="$announcement" />
    <x-site.header :items="$navigation['header']['items'] ?? []" :mega="$megaMenu" :theme-switcher="$themeSwitcher" />

    <main id="main" tabindex="-1" class="outline-none">
        {{ $slot }}
    </main>

    <x-site.footer :navigation="$navigation" />

    <button type="button" class="back-to-top no-print" data-back-to-top aria-label="Back to top">
        <x-heroicon-m-arrow-up class="size-5" aria-hidden="true" />
    </button>

    <x-site.whatsapp-chat />

    @if ($cookieBanner)
        <x-site.cookie-banner :consent="$consent" :policy-url="$cookiePolicyUrl" />
    @endif
</body>
</html>
