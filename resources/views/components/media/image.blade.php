@props(['media', 'sizes' => '100vw', 'eager' => false, 'alt' => null])
@if ($media)
    @php
        $srcset = $media->isImage() && ! $media->isSvg() ? $media->webpSrcset() : null;
        $altText = filled($alt) ? (string) $alt : (filled($media->alt_text) ? (string) $media->alt_text : (filled($media->title) ? (string) $media->title : 'Softec AI Solution'));
    @endphp
    <picture>
        @if ($srcset)
            <source type="image/webp" srcset="{{ $srcset }}" sizes="{{ $sizes }}">
        @endif
        <img src="{{ $media->url() }}" alt="{{ $altText }}"
             @if ($media->width) width="{{ $media->width }}" @endif
             @if ($media->height) height="{{ $media->height }}" @endif
             loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="{{ $eager ? 'auto' : 'async' }}"
             @if ($eager) fetchpriority="high" @endif
             {{ $attributes }}>
    </picture>
@endif
