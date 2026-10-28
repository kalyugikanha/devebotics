# Security

## Security model (what is implemented)

| Area | Implementation |
|---|---|
| Authentication | Filament login with throttling (per IP), password reset that never reveals whether an account exists (`App\Filament\Pages\Auth\RequestPasswordReset`), Laravel hashing (bcrypt, 12 rounds), strong password rules in production (12+ chars, mixed case, numbers, symbols, breached-password check) |
| MFA | Authenticator-app (TOTP, with recovery codes) and email codes, enabled per user from the profile page; secrets encrypted at rest; Super Admins can reset a user's MFA |
| Sessions | Encrypted database/Redis sessions, HttpOnly, SameSite=Lax, Secure in production, `AuthenticateSession` logs out other sessions after a password change, deactivation deletes sessions, logout invalidates the session |
| Authorization | Spatie permissions + policies for every Filament resource (`strictAuthorization()`), page `canAccess()`, controller `Gate::authorize`, record-level scopes (CRM), escalation guard for roles, last-super-admin protection — see [PERMISSIONS.md](PERMISSIONS.md) |
| CSRF | Laravel `PreventRequestForgery` on all web forms and the cookie-consent endpoint |
| Input validation | Form Requests for every public form; Filament validation rules; enum/whitelist validation for icons, section types, presets, theme colours, CTA links (`SafeUrl`) |
| Output escaping / XSS | Blade escaping by default; rich text sanitised **on write** with an allow-list (`SanitizedHtml` cast → `ContentSanitizer`): no scripts, event handlers, inline styles, iframes, `javascript:`/`data:` URLs; emails render placeholders as escaped text after Markdown conversion |
| Content Security Policy | Public site: nonce-based `script-src 'nonce-…' 'strict-dynamic'`, no `unsafe-eval` (Alpine CSP build), `object-src 'none'`, `frame-ancestors 'self'`, `base-uri 'self'`, `form-action 'self'`. Admin: relaxed policy required by Livewire/Filament (staff only). JSON-LD and inline config use nonces |
| Security headers | HSTS (production HTTPS), X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy, COOP; `X-Powered-By` removed |
| SQL injection | Eloquent/query builder bindings only; `LIKE` searches escape wildcards |
| Mass assignment | `#[Fillable]` allow-lists on every model; `preventSilentlyDiscardingAttributes` outside production |
| Uploads | Real MIME detection, extension allow-lists, size/dimension limits, SVG sanitising, EXIF/GPS stripping, random/slugged filenames (no path traversal), private disk for sensitive files, policy-checked + audited downloads with `nosniff`, sandbox CSP and `no-store` |
| Bot protection | Honeypot, encrypted render-time check (rejects instant and stale submissions), optional Cloudflare Turnstile (server-side verification, fails closed), rate limits (public forms 5/min + 30/day per IP, newsletter, search, downloads), duplicate collapsing |
| Privacy | Consent records with policy version, IPs stored as HMAC hashes in public-submission tables, encrypted emails for testimonials/comments, masked emails in logs, retention pruning, cookie consent before analytics, no third-party fonts |
| Secrets | Only in `.env`; never stored in the database or rendered in the admin (integrations page shows configured/not configured) |
| Audit logging | Model create/update/delete with diffs (sensitive attributes excluded), login/logout/failed logins/lockouts/password resets, role/permission and activation changes, CSV exports, private file downloads, theme publishing, system actions |
| Errors | Branded error pages; dependency-free 500/503; `APP_DEBUG=false` in production; System health warns when debug is on in production |
| Indexing protection | Non-production environments are always `noindex` and `robots.txt: Disallow: /` |
| Dependencies | `composer audit`: no advisories. `npm audit --omit=dev`: 0 (no runtime npm packages). Dev-only: 2 moderate advisories in `postcss-selector-parser` (pulled by `@tailwindcss/typography`, build-time CSS parsing only) |

## Production hardening checklist

**Environment**
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, unique `APP_KEY` (back it up securely — encrypted columns depend on it)
- [ ] `APP_URL` uses `https://`; `APP_FORCE_HTTPS=true`; `SESSION_SECURE_COOKIE=true`; `SESSION_ENCRYPT=true`
- [ ] `TRUSTED_PROXIES` set to the load balancer addresses (never `*` if the app is directly reachable)
- [ ] `.env` permissions `0640`, owned by the deploy user, readable by PHP only; not in version control
- [ ] Least-privilege database user (see DEPLOYMENT.md §3); database not exposed publicly

**Server**
- [ ] Web root is `public/`; dotfiles denied; PHP execution denied under `/storage`
- [ ] TLS 1.2+ only, HSTS enabled (application sends it in production), automatic certificate renewal
- [ ] OS and PHP security updates automated; firewall allows only 22/80/443; SSH keys only
- [ ] `client_max_body_size` and PHP `upload_max_filesize`/`post_max_size` ≥ 12M but not excessive
- [ ] Optional: ClamAV scanning of `storage/app/private` uploads; WAF / Cloudflare in front

**Application**
- [ ] Change the seeded super-admin password at first login and enable MFA for every staff account (consider `requiresMultiFactorAuthentication()` in `AdminPanelProvider` for all users)
- [ ] Review roles; give staff the least-privileged role; deactivate leavers immediately
- [ ] Configure form recipients, SMTP, and test email delivery (Admin › Email templates › Send test)
- [ ] Enable Cloudflare Turnstile if spam appears (keys in `.env`, toggle in Integrations)
- [ ] Run `php artisan softec:purge-demo --force` if demo data exists in production
- [ ] Review legal pages with counsel; set `privacy.consent_version` when policies change
- [ ] `php artisan optimize && php artisan filament:optimize` on each deploy

**Operations**
- [ ] Queue workers supervised; scheduler cron installed (heartbeat visible in System health)
- [ ] Off-site, encrypted backups (`BACKUP_DISK=local,s3`, `BACKUP_ARCHIVE_PASSWORD`); restore tested
- [ ] Error tracking and uptime monitoring (`/up`) configured; logs rotated (`daily`)
- [ ] `composer audit` and `npm audit` in CI; dependencies updated monthly
- [ ] Periodic review of Admin › Users & security › Login & security activity

## Reporting a vulnerability

Report suspected vulnerabilities privately to the security contact configured by Softec Ai Solution (add a
`/.well-known/security.txt` with the contact address before launch). Do not disclose publicly until fixed.
