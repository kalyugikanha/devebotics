# Deployment guide

This guide targets a Linux VPS (Ubuntu 24.04 LTS) with Nginx, PHP-FPM, MySQL and Supervisor. The same steps apply to
managed platforms (Laravel Cloud, Forge, Ploi, Docker) — map each section to the platform's equivalent.

## 1. Environments

| | Local | Staging | Production |
|---|---|---|---|
| `APP_ENV` | `local` | `staging` | `production` |
| `APP_DEBUG` | `true` | `false` | `false` (never true) |
| `APP_ALLOW_INDEXING` | `false` | `false` (forced noindex anyway) | `true` on the live domain only |
| `APP_FORCE_HTTPS` / `SESSION_SECURE_COOKIE` | `false` | `true` | `true` |
| Demo content | seeded | optional (`db:seed --class=DemoContentSeeder`) | **not seeded**; purge with `softec:purge-demo` if copied |
| Mail | `log` | sandbox SMTP (e.g. Mailpit, Mailtrap) | real SMTP / transactional provider |
| Queue / cache | `database` | `database` or Redis | Redis recommended |
| Search engines | blocked | blocked (`robots.txt: Disallow: /`, `noindex`) | allowed |

Non-production environments **cannot be indexed** even if the admin toggle is on: indexing requires
`APP_ENV=production` **and** `APP_ALLOW_INDEXING=true` **and** Settings › SEO › Allow indexing.

## 2. Server requirements

- PHP **8.3+** (8.4/8.5 recommended) with extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd` (with WebP; AVIF
  optional), `intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`, `sodium`; `redis` (phpredis) if using Redis.
- MySQL **8.0+** or MariaDB **10.6+**, `utf8mb4`.
- Nginx 1.24+ (or Apache with equivalent rules), TLS certificate (Let's Encrypt).
- Node **20+** and npm (build step only — can run in CI).
- Composer 2, Supervisor (queue workers), cron.
- `mysqldump` available for backups.
- Recommended: Redis 7 for cache/queue/session, ClamAV if you want antivirus scanning of uploads.

## 3. Database

```sql
CREATE DATABASE softec_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'softec_ai'@'127.0.0.1' IDENTIFIED BY '<strong-random-password>';
-- Least privilege: schema-scoped DML + DDL for migrations only.
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, REFERENCES, LOCK TABLES ON softec_ai.* TO 'softec_ai'@'127.0.0.1';
FLUSH PRIVILEGES;
```

For stricter separation, run migrations with a separate migration user and give the runtime user only DML.

## 4. Environment variables

Copy `.env.example` to `.env` on the server (never commit it) and set at least:

```dotenv
APP_NAME="Softec Ai Solution"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.example.com
APP_ALLOW_INDEXING=true
APP_FORCE_HTTPS=true
TRUSTED_PROXIES=            # load balancer IPs/CIDRs if behind a proxy

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=softec_ai
DB_USERNAME=softec_ai
DB_PASSWORD=...

SESSION_DRIVER=redis        # or database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
CACHE_STORE=redis
QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=...  MAIL_PORT=587  MAIL_USERNAME=...  MAIL_PASSWORD=...  MAIL_SCHEME=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="Softec Ai Solution"

MEDIA_DISK=public           # or s3 (+ AWS_* variables)
SEED_ADMIN_EMAIL=you@example.com
SEED_ADMIN_PASSWORD=        # leave empty to generate one (printed once)
BACKUP_DISK=local,s3
BACKUP_ARCHIVE_PASSWORD=... # encrypts backup archives
BACKUP_NOTIFICATION_EMAIL=ops@example.com
# Optional
TURNSTILE_SITE_KEY=  TURNSTILE_SECRET_KEY=
ANTHROPIC_API_KEY=
```

## 5. First deployment

```bash
# as the deploy user, e.g. /var/www/softec
git clone <repository> current && cd current
composer install --no-dev --optimize-autoloader --no-interaction
cp .env.example .env && nano .env
php artisan key:generate --force
npm ci && npm run build          # or build in CI and upload public/build
php artisan migrate --force
php artisan db:seed --force      # production: permissions, first super admin, core structure (no demo content)
php artisan storage:link
php artisan optimize             # config, routes, views, events
php artisan filament:optimize    # Filament components + Blade icons
```

Then sign in at `https://<domain>/admin`, change the generated password, enable two-factor authentication, and complete
the content checklist in the README.

### File permissions

```bash
sudo chown -R deploy:www-data /var/www/softec/current
sudo find storage bootstrap/cache -type d -exec chmod 2775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 0664 {} \;
chmod 0640 .env
```

Only `storage/` and `bootstrap/cache/` must be writable by PHP. `storage/app/private` must **never** be web-accessible
(it is outside `public/`; only `storage/app/public` is linked).

## 6. Nginx

```nginx
server {
    listen 80;
    server_name example.com www.example.com;
    return 301 https://www.example.com$request_uri;
}

server {
    listen 443 ssl http2;
    server_name www.example.com;
    root /var/www/softec/current/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/www.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/www.example.com/privkey.pem;

    client_max_body_size 16m;          # largest upload (10 MB attachments) + overhead
    server_tokens off;

    gzip on;
    gzip_types text/css application/javascript application/json image/svg+xml application/xml text/plain;
    # brotli on; brotli_types ... (if the module is installed)

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Long-lived caching for fingerprinted build assets and fonts.
    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Uploaded media: cache, never execute.
    location /storage/ {
        expires 30d;
        add_header X-Content-Type-Options nosniff;
        location ~ \.php$ { deny all; }
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

Security headers (CSP with nonces, HSTS, X-Frame-Options, Referrer-Policy, Permissions-Policy) are set by the
application (`App\Http\Middleware\SecurityHeaders`); do not add a conflicting CSP in Nginx.

## 7. Queue workers (Supervisor)

`/etc/supervisor/conf.d/softec-worker.conf`:

```ini
[program:softec-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/softec/current/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --memory=256
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/softec/shared/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start softec-worker:*
```

Queued work: emails (acknowledgements, notifications, invitations, security alerts), image variants.
Failed jobs appear in **Admin › System › System health** (retry/forget) and are pruned after 30 days.

## 8. Scheduler (cron)

```cron
* * * * * cd /var/www/softec/current && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks (`routes/console.php`): publish due blog posts (every minute), scheduler heartbeat (shown in System
health), data retention pruning (daily 03:15), failed-job pruning (weekly), backup cleanup (daily 01:30), database backup
(daily 02:00), full backup with uploads (weekly, Sunday 02:30).

## 9. Backups and restore

Backups use `spatie/laravel-backup` (`config/backup.php`): database dump + `storage/app` (uploads and private files),
stored on the disks listed in `BACKUP_DISK` (use at least one off-site disk such as S3) and optionally encrypted with
`BACKUP_ARCHIVE_PASSWORD`. Health is monitored (age ≤ 1 day, size limit) and visible in System health.

```bash
php artisan backup:run            # full
php artisan backup:run --only-db  # database only
php artisan backup:list           # status per disk
```

**Restore procedure**

1. `php artisan down --render="errors::503"`.
2. Download and unzip the chosen archive (enter the archive password if set).
3. Database: `mysql -u softec_ai -p softec_ai < db-dumps/mysql-softec_ai.sql`.
4. Files (full backups): restore `app/` into `storage/app/` (keep ownership/permissions as in §5).
5. `php artisan optimize:clear && php artisan optimize && php artisan filament:optimize`.
6. Restart workers: `php artisan queue:restart`.
7. `php artisan up`. Test a restore on staging at least quarterly.

## 10. Zero-downtime deployments and rollback

Recommended layout: `releases/<timestamp>`, `shared/` (`.env`, `storage/`), `current` symlink. Steps per release:

```bash
git clone --depth 1 --branch main <repo> releases/$TS && cd releases/$TS
ln -s ../../shared/.env .env && rm -rf storage && ln -s ../../shared/storage storage
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build
php artisan migrate --force                # migrations must be backward-compatible with the previous release
php artisan optimize && php artisan filament:optimize
ln -sfn releases/$TS ../../current         # atomic switch
sudo systemctl reload php8.4-fpm
php artisan queue:restart
```

**Rollback**: point `current` back to the previous release directory, reload PHP-FPM and restart queue workers. If the
failed release ran migrations, run `php artisan migrate:rollback --step=<n>` from the new release *before* switching back
(only if the down migrations are safe for production data), or restore the pre-deploy database backup.

Before every deploy: run `php artisan test`, `npm run build`, `composer audit` in CI; take a database backup.

## 11. Logging, monitoring and error tracking

- Logs: `LOG_CHANNEL=daily` (or `stack` with `daily` + an external channel), `LOG_LEVEL=warning` in production.
  Submitted personal data and passwords are not logged; exceptions never flash password fields.
- Health endpoint: `GET /up` for uptime monitors / load balancers.
- Admin › System health: queue backlog, failed jobs, scheduler heartbeat, backups, debug-mode warning.
- Error tracking: add Sentry/Flare/Bugsnag via their Laravel packages and DSN in `.env` (not installed by default).
- Monitor certificate expiry, disk space (`storage/`, backups) and MySQL slow queries.

## 12. Maintenance

```bash
composer outdated --direct && composer update --with-dependencies   # then test
npm outdated && npm update && npm run build
composer audit && npm audit
php artisan down --secret="<random>" --render="errors::503"         # maintenance mode with bypass URL /<random>
php artisan up
```

Maintenance mode can also be toggled by Super Administrators in Admin › System health (bypass token shown once).
