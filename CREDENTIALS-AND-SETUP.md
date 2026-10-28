# Softec Ai Solution: credentials and setup (local development)

> **Keep this package private.** It contains the local `.env` file and a full database export. These credentials are
> for local development only. Do not upload them to a public repository or use them on the live site. Change every
> password and key before going live (see "Before going live" below).

## What is in this package

| Item | Contents |
|---|---|
| `softec-ai-solution/` | Complete source code, including `vendor/` (PHP dependencies), built front-end assets (`public/build`), uploaded media (`storage/app/public`), the local `.env`, documentation (`docs/`) and tests |
| `softec_ai.sql` | Full MySQL export of the local database: all pages, services, 14 Insights articles, products, case studies, settings, roles and permissions, query-desk teams, the admin user and the test inquiries. Sessions, cache and job tables are exported empty. |
| `CREDENTIALS-AND-SETUP.md` | This file |

Not included: `node_modules/` (only needed to rebuild CSS/JS or run the browser tests: `npm install`), log files and
caches.

## Credentials

### Admin panel

| | |
|---|---|
| URL | `http://127.0.0.1:8000/admin` (or `APP_URL/admin`) |
| Email | `admin@softecai.test` |
| Password | `Softec!Local2026` |
| Role | Super Administrator (full access) |

Two-factor authentication is available under the user menu › Profile. Turn it on for every real admin account.

### Database (MySQL)

| | |
|---|---|
| Host / port | `127.0.0.1` / `3306` |
| Database | `softec_ai` |
| Username | `root` |
| Password | *(empty: local MySQL default)* |

These are set in `softec-ai-solution/.env` (`DB_*`). Change them there if your MySQL user is different.

### Application and integrations (`.env`)

| Setting | Value in this package |
|---|---|
| `APP_KEY` | Local key (already set). It encrypts sessions and any secrets saved in the admin. |
| `APP_ENV` / `APP_DEBUG` | `local` / `true` |
| Mail | `MAIL_MAILER=log`: emails are written to `storage/logs/laravel.log`, not sent |
| AWS S3, Cloudflare Turnstile, Anthropic API, Google Analytics / Tag Manager | **Not configured** (empty) |
| WhatsApp number | Not set: add it in Admin › Settings › Company & contact |

## Run it locally

Requirements: PHP 8.3 or newer (built and tested on 8.5) with `pdo_mysql`, `gd`, `intl`, `mbstring`, `fileinfo`, `zip`; MySQL 8+.

```bash
# 1. Create the database and import the data
mysql -uroot -e "CREATE DATABASE softec_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -uroot softec_ai < softec_ai.sql

# 2. Start the application
cd softec-ai-solution
php artisan storage:link        # makes uploaded images public
php artisan optimize:clear
php artisan serve               # http://127.0.0.1:8000
php artisan queue:work          # second terminal: emails, image variants
```

- If your PHP version differs and you see dependency errors, run `composer install` once.
- To change CSS/JS: `npm install` then `npm run build` (or `npm run dev`).
- Tests: `php artisan test` (287 tests) and `npm run test:browser` (14 tests, needs `npm install` and the server running).
- Alternative to importing the SQL: `php artisan migrate --seed` builds a fresh database with the same seeded content
  (without the test inquiries).

## Data notes

- Placeholder products, case studies, testimonials, statistics, team members, clients and partners are flagged
  **Demo** and labelled on the site. Remove them all with `php artisan softec:purge-demo` once you add real content.
- Several inquiries named "Browser Test" (`@example.test`) were created by the automated tests. Delete them in
  Admin › Query desk › Inquiries.
- The three query-desk teams (*Sales & new projects*, *Client support*, *Talent & HR*) have no members yet. Create staff
  users (Admin › Users) with the *Support Agent*, *Team Lead* or *Sales Manager* role and add them to a team.
- The legal pages (privacy, terms, cookies) are templates and need review by legal counsel.

## Before going live

Follow `softec-ai-solution/docs/DEPLOYMENT.md` and `docs/SECURITY.md`. In short:

1. Create a dedicated MySQL user with a strong password (never `root` without a password).
2. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`, `APP_FORCE_HTTPS=true`,
   `SESSION_SECURE_COOKIE=true`, `APP_ALLOW_INDEXING=true` (live domain only).
3. Generate a new key with `php artisan key:generate` on the server **before** entering any secrets in the admin (encrypted
   settings depend on it).
4. Change the admin email and password (or create your own super admin and delete this one) and enable two-factor
   authentication.
5. Configure real SMTP credentials (`MAIL_*`) and the form notification recipients (Admin › Settings › Forms).
6. Enable gzip/brotli compression and HTTPS on the web server, set up the scheduler (`php artisan schedule:run` every
   minute) and a queue worker.
