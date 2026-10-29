@props(['seo'])

@php
    $nonce = app()->bound('csp-nonce') ? app('csp-nonce') : null;
    $siteName = (string) settings('company.name');
    $schemas = array_values(array_filter($seo->schema));
@endphp
<title>{{ $seo->fullTitle() }}</title>
<meta name="description" content="{{ $seo->metaDescription() }}">
<meta name="robots" content="{{ $seo->robots() }}">
<link rel="canonical" href="{{ $seo->canonicalUrl() }}">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:title" content="{{ $seo->ogTitle ?: $seo->fullTitle() }}">
<meta property="og:description" content="{{ $seo->ogDescription ?: $seo->metaDescription() }}">
<meta property="og:url" content="{{ $seo->canonicalUrl() }}">
<meta property="og:image" content="{{ $seo->imageUrl() }}">
<meta property="og:image:secure_url" content="{{ $seo->imageUrl() }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:alt" content="{{ $seo->ogTitle ?: $seo->fullTitle() }}">
<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) === 'en' ? 'en_US' : str_replace('-', '_', app()->getLocale()) }}">
<link rel="image_src" href="{{ $seo->imageUrl() }}">
@if ($seo->publishedTime)
    <meta property="article:published_time" content="{{ $seo->publishedTime }}">
@endif
@if ($seo->modifiedTime)
    <meta property="article:modified_time" content="{{ $seo->modifiedTime }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo->ogTitle ?: $seo->fullTitle() }}">
<meta name="twitter:description" content="{{ $seo->ogDescription ?: $seo->metaDescription() }}">
<meta name="twitter:image" content="{{ $seo->imageUrl() }}">
@if (filled(settings('seo.x_handle')))
    <meta name="twitter:site" content="{{ '@'.ltrim((string) settings('seo.x_handle'), '@') }}">
@endif

@if (filled($google = settings('seo.google_site_verification')))
    <meta name="google-site-verification" content="{{ $google }}">
@endif
@if (filled($bing = settings('seo.bing_site_verification')))
    <meta name="msvalidate.01" content="{{ $bing }}">
@endif

@foreach ($schemas as $schema)
    <script type="application/ld+json" nonce="{{ $nonce }}">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endforeach
