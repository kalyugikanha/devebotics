{{-- Dependency-free error page (no database, no view composers) for 500 / 503. --}}
@php
    $name = 'Softec Ai Solution';
    try { $name = (string) settings('company.name'); } catch (\Throwable) {}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} | {{ $name }}</title>
    <link rel="icon" href="/favicon.ico">
    <style>
        :root { color-scheme: light dark; --bg:#fff; --fg:#0f172a; --muted:#475569; --primary:#4f46e5; }
        @media (prefers-color-scheme: dark) { :root { --bg:#070b17; --fg:#e7eaf3; --muted:#9aa4b8; --primary:#818cf8; } }
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; display:grid; place-items:center; background:var(--bg); color:var(--fg); font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif; padding:24px; }
        main { max-width:560px; text-align:center; }
        .bar { height:4px; width:120px; margin:0 auto 32px; border-radius:99px; background:linear-gradient(90deg,#7c3aed,#4f46e5,#2563eb,#06b6d4,#10b981); }
        img { width:72px; height:auto; margin-bottom:24px; }
        h1 { font-size:clamp(28px,5vw,44px); letter-spacing:-0.02em; margin:0 0 16px; }
        p { color:var(--muted); line-height:1.7; font-size:17px; margin:0 0 28px; }
        a { display:inline-block; padding:12px 24px; border-radius:999px; background:var(--primary); color:#fff; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
    <main>
        <div class="bar"></div>
        <img src="/images/brand/softec-mark.png" alt="Softec AI Solution">
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
        @if (! empty($link))<a href="/">Back to home</a>@endif
    </main>
</body>
</html>
