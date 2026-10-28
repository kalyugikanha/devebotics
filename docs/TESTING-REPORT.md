# Testing & acceptance report

Date: 26 October 2026 · Environment: macOS, PHP 8.5.12, MySQL 26.10 (dev) / SQLite in-memory (tests), Node 24.8,
Google Chrome (Playwright channel `chrome`). All numbers below are from real runs on this machine.

## 1. Summary

| Check | Result |
|---|---|
| PHPUnit (feature + unit) | **287 tests, 2,178 assertions — all passing** (`php artisan test`, ≈82 s) |
| Browser tests (Playwright + axe-core) | **14/14 passing** (`npm run test:browser`) |
| Accessibility (axe, WCAG 2.0/2.1/2.2 A + AA tags) | **0 serious/critical violations** on home, service, contact, blog post, feedback (light) and colour-contrast in dark mode |
| Console errors on key pages | 0 |
| Migrations | Up, rollback, re-run verified on MySQL and SQLite (`MigrationTest`) |
| Seeders | `migrate:fresh --seed` verified on MySQL (permissions, super admin, core + demo content); `ContentRefreshSeeder` applied to the existing dev database and verified idempotent (`ContentSeedTest`) |
| Production caches | `php artisan optimize` + `filament:optimize` succeed; site and admin render with caches |
| Code style | `vendor/bin/pint` clean |
| Dependency audit | `composer audit`: no advisories · `npm audit --omit=dev`: 0 · dev-only: 2 moderate (see SECURITY.md) |
| Backups | `backup:run --only-db` produced a healthy archive (`backup:list` ✅) |

## 2. What the automated tests cover

| Requirement | Tests |
|---|---|
| Authentication, inactive users, password reset (no account enumeration), MFA reset rules, security alert emails | `UserManagementTest`, `AccountSecurityTest`, `AuditLogTest` (failed logins, masked email) |
| RBAC per role, admin route protection, privilege escalation | `ServiceResourceTest`, `ProductResourceTest`, `PageResourceTest`, `NavigationResourceTest`, `PortfolioResourceTest`, `CompanyResourcesTest`, `RoleEscalationTest` (11 tests incl. tampered payloads), `DashboardTest` (every default role), `SettingsPagesTest`, `SystemHealthTest` |
| Record-level access (IDOR) | `InquiryCrmTest` (support agent sees only assigned leads, direct URLs denied), `BlogWorkflowTest` (authors see own posts), `ContactFormTest::test_attachment_download_is_authorised`, `FeedbackAndCareersTest` (résumé download) |
| Page / section / service / product CRUD | `PageResourceTest`, `SeededContentAdminTest`, `ServiceResourceTest`, `ProductResourceTest` (plans, gallery) |
| Blog publishing, workflow, scheduling, AI outline | `BlogWorkflowTest` (9), `PublishScheduledPostsTest`, `BlogCommentModerationTest` |
| SEO metadata, canonical, robots, sitemaps, structured data | `SeoTest` (9) |
| Contact form validation, spam, duplicates, rate limiting, notifications | `ContactFormTest` (8) |
| Testimonials submission, consent, moderation | `FeedbackAndCareersTest`, `TestimonialModerationTest` (6) |
| File uploads, public vs private files | `MediaResourceTest` (6, real GD image), `ContactFormTest`, `SecurityTest::test_private_uploads_are_not_web_accessible` |
| Theme configuration, draft preview, CSS-injection safety | `ThemeSettingsTest` (5), `SecurityTest` |
| Redirects (301, 410, loops, automatic slug redirects) | `RedirectResourceTest` (6), `SecurityTest`, `ServiceResourceTest` |
| Privacy & consent (cookie consent gating analytics, newsletter double opt-in) | `PrivacyAndNewsletterTest` |
| Security headers / CSP | `SecurityTest::test_public_pages_send_a_strict_nonce_based_csp_and_hardening_headers` |
| Sanitisation and safe URLs | `Unit\ContentSanitizerTest` |
| Console commands, scheduler | `ConsoleCommandsTest` |
| All public pages render (28 URLs incl. filters, archives, legal, search) | `PublicPagesTest` |
| Responsive & accessibility interactions | `tests/Browser/site.test.mjs`: axe scans, no horizontal overflow at 375 px, mobile menu focus trap + Escape, theme persistence without flash, reduced motion, skip link, cookie consent, WhatsApp chat (open, quick messages, Escape returns focus), end-to-end contact submission |
| WhatsApp chat widget | `WhatsAppWidgetTest` (4): `wa.me` link built from the configured number with URL-encoded quick replies, hidden in production until a number is set, preview-only locally, can be switched off |
| Seeded content quality | `ContentSeedTest` (6): 14 published articles ≥ 900 words with category, tags and meta lengths in range; every service ≥ 300 words + ≥ 3 FAQs; industries have SEO; **every internal link in seeded content returns 200**; home SEO, scroll statement after services and intro before FAQ; refresh seeder is idempotent |
| Illustration whitelist | `ServiceResourceTest::test_illustration_accepts_only_whitelisted_names` (path-like values rejected) |
| Query desk (teams, routing, statuses) | `QueryDeskTest` (15): routing by topic and service, round-robin auto-assign, notifications, team-scoped visibility incl. direct URLs to another team's inquiry, pick-up rules and row locking, team leads limited to their members (incl. tampered payloads), closed statuses, overdue tab, Team resource RBAC; `ContactFormTest` (topic validation, routing failures never break the form) |
| Header & mega menu | Browser test: the services panel opens **below** the nav (regression for the overlap under the announcement bar), category switching, Escape closes |
| Technology logos & feature spots | `TechLogoRegistryTest` (aliases, initials, theme colours, every bundled SVG is a single safe path), `SpotRegistryTest` + `SpotIllustrationsTest` (keyword matching, no repeats in one grid, unique ids, no `<style>`/scripts) |
| Admin colours | `AdminPaletteTest`: primary buttons use the exact brand colour with white text for the default and alternative palettes |

## 3. Performance (lab measurements)

Measured with Playwright/Chrome against the local `php artisan serve` server (seeded demo content). "Mobile" = 390×844,
4× CPU slowdown, 1.6 Mbps / 150 ms latency. Field data (CrUX) will differ; re-measure on the production stack.

**Uncompressed** (dev server has no gzip):

| Page | Desktop LCP | Mobile LCP | CLS | Requests | JS / CSS / fonts (KB) |
|---|---|---|---|---|---|
| Home | 240 ms | 3,576 ms | 0.007 | 15 | 112 (+112 lazy GSAP) / 193 / 74 |
| Service detail | 132 ms | 3,160 ms | 0.003 | 7 | 112 / 193 / 74 |
| Blog post | 136 ms | 2,920 ms | 0.001 | 9 | 112 (+112 lazy GSAP) / 193 / 74 |
| Contact | 96 ms | 3,156 ms | 0.001 | 7 | 112 / 193 / 74 |

**With gzip** (local compressing proxy, as Nginx would serve in production), mobile profile:

| Page | Mobile LCP | CLS | JS / CSS gzip (KB) | HTML (raw / gzip) |
|---|---|---|---|---|
| Home | 1,624 ms | 0 | 37 / 32 | 501 / 75 KB |
| Service detail | 1,460 ms | 0 | 37 / 32 | 263 / 46 KB |
| Blog post | 1,400 ms | 0 | 37 / 32 | 219 / 35 KB |
| Contact | 1,380 ms | 0 | 37 / 32 | 181 / 28 KB |

Inline, theme-aware SVG (illustrations, feature spots, technology logos, the mega menu on every page) makes the HTML
larger than a text-only page, but it compresses well and needs no image requests. Measures taken: technology logos are an
inline sprite (each path sent once per page), artwork in hidden industry tabs is only added to the DOM when a tab is opened,
and the mega menu uses a single featured illustration. The uncompressed mobile figures are dominated by HTML transfer at
1.6 Mbps; enable gzip/brotli in production.

Optimisations applied during testing: generated artwork moved to cacheable `/art/*.svg` images (home HTML 268 → 212 KB),
breadcrumbs no longer wrap on font swap (mobile CLS 0.106 → 0.001), metric-matched font fallback, GSAP code-split and
loaded only where needed, responsive WebP variants with intrinsic sizes for uploaded images.

## 4. Issues found during testing and fixed

| Issue | Fix |
|---|---|
| `settings('key', $default)` ignored registry defaults (empty budget/timeline lists, missing title suffix) | Precedence is now stored value → registry default → caller default |
| Mobile homepage reported **no LCP** in Chrome: the testimonial carousel's mandatory scroll-snap performed an automatic scroll on load | Added matching `scroll-padding` so no snap scroll occurs |
| Alpine components read data attributes from the clicked button (`$el`) instead of the component root (cookie consent posted to `/undefined`) | Use `$root` |
| Logo link accessible name did not contain its visible text (WCAG 2.5.3) | Visible text is now the accessible name |
| "Demo" badge contrast below 4.5:1 | Darker badge colours in both themes |
| 404/410 responses raised before the session middleware failed to render (`$errors` undefined in components) | Default error bag shared globally |
| Deactivated users saw a 403 instead of being signed out (middleware priority) | `AuthenticateAdmin` middleware signs them out immediately |
| Password-reset request revealed whether an account exists | Custom request page returns the same message for unknown/throttled accounts |
| Theme normaliser failed on enum values from the admin form | Normaliser accepts enums |
| Admin primary buttons rendered pastel (generated palette too light for white text) | `AdminPalette` pins the exact brand colour as shade 600 |
| Seeded service descriptions repeated the overview paragraph | Seeder and existing records corrected |
| A Blade `@if` written directly after a word (`day@if`) was not compiled, breaking the project-inquiry page layout | Fixed the partial; every public-page test now asserts no raw Blade directives reach the HTML |
| Redesign: the illustration migration was timestamped before the tables it alters, so fresh databases failed to migrate | Renamed to run after the table migrations (`…100900_…`) |
| Redesign: brand prose colours were overridden by the typography plugin defaults (`@apply prose` came after the variables) | Variables now follow the `@apply`; article links and bullets use the brand colours in both themes |
| Redesign: the scroll statement's unlit words failed axe colour-contrast (opacity 0.18) | Unlit words stay at 0.5 opacity, so this large text still has ≥ 3:1 contrast |
| Redesign: the illustration frame received both `relative` and `absolute`, so industry artwork did not fill its panel | The frame only adds `relative` when no position class is passed; root SVGs fill their wrapper |
| Redesign: the WhatsApp panel did not return focus to its button after Escape | Focus returns to the toggle; covered by a browser test |
| Redesign: two stacked white sections produced a double gap (≈ 224 px) | `.section-plain + .section-plain` shares one gap |
| Mega menu overlapped the nav labels when the announcement bar was visible (it was `position: fixed` at the header height) | Menus now attach to the header bar itself; browser test asserts the panel starts below the nav |
| At 1024 px the header wrapped "Case studies" and caused horizontal scrolling | Compact nav between 1024–1279 px, header CTA from 1280 px; overflow 0 |
| Mobile drawer's top row was covered by the announcement bar and floating buttons | Header is raised above them while the drawer is open |
| Palette change made admin primary buttons pastel with dark text (Filament needs white to pass on the 500 hover shade too) | `AdminPalette` picks the lightest 500 shade that still passes AA with white; regression test |
| Initials tiles for technologies without a licensed logo failed colour contrast | Solid tiles with white text |
| Technology logos were lazy-loaded per item (N+1) | `technologies.logo` / `with('logo')` eager loading |

## 5. Acceptance criteria

| Criterion | Status |
|---|---|
| Visitors can navigate all public pages on desktop and mobile | ✅ 28 URLs rendered in tests; mobile menu, overflow and focus checks in browser tests |
| All major content editable in the admin; changes appear without code changes | ✅ 37 resources + settings/theme/animation/system pages; navigation and sitemap caches flush on save |
| Light and dark themes across site and dashboard | ✅ Site (no-flash switcher, admin default, draft preview/publish); Filament light/dark/system |
| RBAC enforced on the server | ✅ policies on every resource (`strictAuthorization`), escalation guard, record scopes — tested per role |
| Services, products, blogs, clients, partners, testimonials manageable | ✅ |
| Contact and feedback forms save valid submissions securely | ✅ validation, spam checks, rate limits, private attachments, consent records |
| Reviews require moderation before publication | ✅ pending by default; approval requires publish permission |
| SEO metadata, canonical URLs, sitemap, robots, structured data | ✅ (`SeoTest`) |
| Animations smooth and respect reduced motion | ✅ CSS/IO-based, lazy GSAP; reduced-motion browser test; visitor toggle |
| Images optimised with alt text | ✅ pipeline generates WebP variants; alt text required in uploads guidance and "missing alt" filter |
| Unauthorised users cannot access protected routes or private records | ✅ guest redirects, 403s, IDOR tests, private file tests |
| Critical workflows have automated tests | ✅ 287 PHP + 14 browser tests |
| Setup and deployment instructions complete | ✅ README, DEPLOYMENT.md, SECURITY.md |
| Known limitations and unconfigured integrations documented | ✅ below |

## 6. Known limitations and items requiring real configuration

- **Content**: demo products, case studies, testimonials, clients, partners, statistics, team, milestones and job
  openings are placeholders (flagged and labelled). The 14 Insights articles, service/industry copy and SEO metadata are
  genuine general-guidance content (no invented clients, figures or prices). Review them so they match how you work. Replace them with verified content, then run
  `php artisan softec:purge-demo`. No certifications/awards are seeded by design.
- **Company facts**: contact details, address, business hours, social links, founding year, booking URL and the
  **WhatsApp number** are empty until entered. The site hides those elements rather than inventing them (the WhatsApp
  widget shows a local-only preview notice until then).
- **Legal pages** are templates and must be reviewed by legal counsel.
- **Email**: `MAIL_MAILER=log` locally; production needs SMTP/provider credentials and form recipients.
- **Integrations not configured**: Google Analytics 4 / Tag Manager (IDs), Search Console/Bing verification tokens,
  Cloudflare Turnstile keys, S3 storage, Redis, Anthropic API key (AI outline is hidden until configured), error tracking
  (Sentry/Flare not installed), external newsletter/CRM providers (newsletter is built-in double opt-in only).
- **Imagery**: no AI image-generation tool was available. Visuals are original hand-built SVG illustrations and generated
  artwork until real images are uploaded (see MEDIA-AND-ASSETS.md).
- **Performance**: lab numbers only; run Lighthouse/PageSpeed Insights on the production domain with HTTPS, compression
  and caching enabled. A full-page cache or CDN can further reduce TTFB.
- **Search** uses SQL `LIKE` queries — adequate for a corporate site; consider Laravel Scout/Meilisearch for large blogs.
- **Browser tests** run against a live local server and create one test inquiry per run in that database.
- **Admin CSP** is necessarily relaxed (`unsafe-inline`/`unsafe-eval`) because Filament/Livewire require it; the admin is
  authenticated and MFA-capable.
- **Dev dependency advisory**: `postcss-selector-parser` (moderate, build-time only) via `@tailwindcss/typography`; update
  when a compatible fixed release is available.
- Laravel Boost (dev-only AI tooling) injects a browser logger in local development; it is not installed in production
  (`composer install --no-dev`).
