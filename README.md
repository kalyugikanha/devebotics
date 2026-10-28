# Softec Ai Solution — Website & CMS

Corporate website and content-management platform for **Softec Ai Solution** (IT services, products and innovation).
A fully dynamic Laravel 13 application with a premium public website, a role-based Filament admin panel, built-in CRM,
blog workflow, moderated client reviews, SEO tooling, light/dark theming and an admin-controlled motion system.

| | |
|---|---|
| **Backend** | Laravel 13 · PHP 8.3+ (developed on 8.5) · MySQL 8 / MariaDB 10.6+ |
| **Admin** | Filament 5 (Livewire 4) with policies on every resource, MFA, audit logging |
| **Frontend** | Blade components · Tailwind CSS 4 · Alpine.js (CSP build) · Vite · GSAP (lazy-loaded) |
| **Packages** | spatie/laravel-permission, spatie/laravel-backup, intervention/image, enshrined/svg-sanitize |
| **Tests** | 287 PHPUnit feature/unit tests · 14 Playwright + axe browser tests |

## Documentation

| Document | Contents |
|---|---|
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | System architecture, technology choices, directory structure, key subsystems, decisions |
| [docs/DATABASE.md](docs/DATABASE.md) | Schema overview, Mermaid ERD, relationships, JSON-column policy |
| [docs/PERMISSIONS.md](docs/PERMISSIONS.md) | Role/permission matrix, public route map, admin module inventory |
| [docs/DESIGN-SYSTEM.md](docs/DESIGN-SYSTEM.md) | Visual identity, tokens, components, theming, motion and accessibility rules |
| [docs/MEDIA-AND-ASSETS.md](docs/MEDIA-AND-ASSETS.md) | Logo processing, generated artwork, media pipeline, image-generation workflow |
| [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) | Server requirements, production deployment, queues, scheduler, backups, rollback |
| [docs/SECURITY.md](docs/SECURITY.md) | Security model and production hardening checklist |
| [docs/TESTING-REPORT.md](docs/TESTING-REPORT.md) | Test results, accessibility and performance measurements, known limitations |

## Quick start (local development)

Requirements: PHP 8.3+ with `pdo_mysql`, `gd` (WebP), `intl`, `mbstring`, `fileinfo`, `zip`; Composer 2; Node 20+; MySQL 8+.

```bash
git clone <repository> softec-ai-solution && cd softec-ai-solution
composer install
cp .env.example .env            # then edit DB_* and SEED_ADMIN_* values
php artisan key:generate
mysql -uroot -e "CREATE DATABASE softec_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
php artisan migrate --seed      # permissions, first super admin, core content (+ demo content outside production)
php artisan storage:link
npm install && npm run build    # or `npm run dev` while developing
php artisan serve               # http://127.0.0.1:8000 — set APP_URL to the same address
php artisan queue:work          # in a second terminal (emails, image variants)
```

- Website: `APP_URL` · Admin panel: `APP_URL/admin`
- The first **Super Administrator** is created from `SEED_ADMIN_NAME`, `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD`.
  If the password is empty, a strong random password is generated and printed once by the seeder.
- `composer run dev` starts the server, queue worker, log viewer and Vite together.

### Demo content

Outside production, `db:seed` also adds **clearly labelled demonstration content** (products, case studies, testimonials,
clients, partners, statistics, team members, job openings). Every demo record is flagged `is_demo`, shows a
"Demo" badge on the public site and can be removed in one step:

```bash
php artisan softec:purge-demo            # asks for confirmation (use --force in scripts)
```

No fictional record is ever presented as real. Certifications/awards, prices and verified outcome metrics are never seeded.

### Seeded website copy and Insights articles

Core content (seeded in every environment) includes genuine, SEO-ready copy written for the site:

- **14 Insights articles** (about 1,000 words each), for example *What are AI agents?*, *How much does custom software
  development cost?*, *How to choose a software development company*, *Web app vs mobile app vs PWA*, *Cybersecurity
  checklist for growing businesses*. They are written as general guidance, not case studies: no invented clients,
  statistics or prices. Each has a meta title, meta description, focus keyword, category, tags, an FAQ section and
  internal links to the relevant services.
- **18 service pages** with long descriptions and 3 FAQs each (also listed on `/faq`), **industry** descriptions, and
  meta titles/descriptions for services, industries and the home, about and engagement-model pages.
- A home-page introduction and a scroll statement section.

Review the copy before launch so it matches how you actually work. To apply the latest seeded copy to an existing
database (it overwrites the seeded fields for services, industries, page SEO and articles):

```bash
php artisan db:seed --class=ContentRefreshSeeder
```

### Query desk (inquiries, teams and statuses)

Every contact form and project brief becomes an inquiry in **Admin › Query desk › Inquiries**, with a reference number,
topic (new project, support, partnership, careers, other), status (New → In progress → Contacted → Qualified →
Proposal sent → On hold → Won / Resolved / Lost / Closed), priority, notes, follow-ups and an activity timeline.

- **Teams** (Admin › Query desk › Teams) act as sub-panels: add staff as members or leads, choose which topics and services
  each team handles, an optional shared mailbox and optional round-robin auto-assign. Three empty teams are created:
  *Sales & new projects*, *Client support*, *Talent & HR*.
- New inquiries are routed to the matching team automatically. Team members see only their team's queue, get a
  notification and can **Pick up** unassigned inquiries. Team leads assign within their team, and managers see and
  reassign everything. All of this is enforced on the server.
- Tabs: My queue, Team queue, Unassigned, Overdue (no response within the target, 24 h by default in Settings › Forms),
  Follow-up due, Closed. A dashboard widget shows each person's open, waiting and overdue inquiries.
- To give someone access, create a user with the *Support Agent*, *Team Lead* or *Sales Manager* role and add them to a team.

### WhatsApp chat

A floating WhatsApp chat button (greeting + quick-reply messages) appears on every page once a number is entered in
**Admin › Settings › Company & contact** (international format, e.g. `+919876543210`). It opens `wa.me` links, so no API key is
needed. Locally it shows a "preview" notice until a number is set; in production it stays hidden until then.

## Everyday commands

| Command | Purpose |
|---|---|
| `php artisan test --compact` | PHP test suite (SQLite in-memory) |
| `npm run test:browser` | Browser + accessibility tests against a running app (`BROWSER_BASE_URL`, default `http://127.0.0.1:8000`) |
| `vendor/bin/pint` | Code style (Laravel preset) |
| `php artisan softec:publish-scheduled` | Publish blog posts whose scheduled time has passed (runs every minute via the scheduler) |
| `php artisan softec:prune-data [--dry-run]` | Apply retention rules to inquiries, applications, logs |
| `php artisan softec:purge-demo [--force]` | Remove all demonstration content and demo media |
| `php artisan db:seed --class=ContentRefreshSeeder` | Apply the latest seeded service/industry/SEO copy and Insights articles to an existing database |
| `php artisan db:seed --class=PermissionSeeder` | Re-sync permissions/default roles after editing `PermissionRegistry` (idempotent) |
| `php artisan backup:run [--only-db]` | Create a backup (database + uploads) |

## What administrators can manage (no code changes)

Company profile, logos, contact details, social links · header & footer navigation · announcement bar ·
homepage and page sections (add, reorder, enable/disable, edit content) · CMS pages incl. legal pages ·
services, products (plans, galleries), industries, case studies, technologies · team, milestones, certifications,
statistics, FAQs, clients, partners (with logo-usage approvals) · blog posts with review/scheduling workflow,
categories, tags, authors, comments · client feedback moderation · inquiries CRM (assignment, status, notes, follow-ups,
CSV export) · careers and applications · media library · SEO defaults, per-page SEO, redirects, robots rules ·
theme colours for light & dark mode with draft preview and publish · animation presets · email templates ·
form recipients, privacy and feature toggles · users, roles & permissions · audit and security logs · system health,
queues, cache, backups and maintenance mode.

## Configuration that still needs real values

Before launch, configure in the admin panel or `.env` (none of these are invented by the application):

- Company contact details, address, business hours, social profiles, booking URL, WhatsApp number (Settings › Company & contact)
- SMTP / transactional email credentials (`MAIL_*` in `.env`) and form notification recipients (Settings › Forms & privacy)
- Real services copy review, verified statistics, client/partner logos with approvals, case studies, testimonials, team
- Legal pages: the seeded privacy policy, terms and cookie policy are **templates that must be reviewed by legal counsel**
- Optional integrations: GA4/GTM IDs, Cloudflare Turnstile keys, S3 storage, Anthropic API key for AI drafting
- Production: `APP_ENV=production`, `APP_DEBUG=false`, `APP_ALLOW_INDEXING=true` (live domain only), `APP_FORCE_HTTPS=true`, `SESSION_SECURE_COOKIE=true`

See [docs/TESTING-REPORT.md](docs/TESTING-REPORT.md) for the full list of known limitations.
