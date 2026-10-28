@php
    $company = (string) settings('company.name');
    $contact = settings()->group('contact');
    $address = collect([$contact['address_line1'], $contact['city'], $contact['country']])->filter()->implode(', ');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $heading ?? $company }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f5fb;font-family:Inter,'Segoe UI',Helvetica,Arial,sans-serif;color:#0f172a;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f5fb;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #e2e6f0;">
                    <tr>
                        <td style="height:5px;background:linear-gradient(90deg,#7c3aed,#4f46e5,#2563eb,#06b6d4,#10b981);background-color:#4f46e5;font-size:0;line-height:0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding:28px 36px 8px;">
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="vertical-align:middle;padding-right:10px;">
                                        <img src="{{ asset('images/brand/softec-mark.png') }}" width="44" height="35" alt="" style="display:block;border:0;">
                                    </td>
                                    <td style="vertical-align:middle;font-size:18px;font-weight:800;letter-spacing:-0.02em;color:#0f172a;">{{ $company }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 36px 8px;">
                            @if (filled($heading))
                                <h1 style="margin:0 0 16px;font-size:24px;line-height:1.3;font-weight:800;letter-spacing:-0.02em;color:#0f172a;">{{ $heading }}</h1>
                            @endif
                            <div style="font-size:15px;line-height:1.7;color:#334155;">
                                {!! $bodyHtml !!}
                            </div>
                            @if (filled($actionLabel) && filled($actionUrl))
                                <table role="presentation" cellspacing="0" cellpadding="0" style="margin:28px 0 8px;">
                                    <tr>
                                        <td style="border-radius:999px;background:#4f46e5;">
                                            <a href="{{ $actionUrl }}" style="display:inline-block;padding:13px 26px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:999px;">{{ $actionLabel }}</a>
                                        </td>
                                    </tr>
                                </table>
                                <p style="margin:12px 0 0;font-size:12px;line-height:1.6;color:#64748b;">If the button does not work, copy this link into your browser:<br><span style="word-break:break-all;color:#4f46e5;">{{ $actionUrl }}</span></p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 36px 32px;">
                            <p style="margin:0;border-top:1px solid #e2e6f0;padding-top:20px;font-size:12px;line-height:1.7;color:#64748b;">
                                {{ $company }}@if ($address) · {{ $address }}@endif<br>
                                <a href="{{ url('/') }}" style="color:#4f46e5;text-decoration:none;">{{ parse_url(url('/'), PHP_URL_HOST) }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
