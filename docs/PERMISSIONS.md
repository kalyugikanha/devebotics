# Roles, permissions and routes

Permissions are defined once in `app/Support/Permissions/PermissionRegistry.php` and synced by
`php artisan db:seed --class=PermissionSeeder` (idempotent; custom roles created in the admin are kept).
Every check runs on the server — in Filament resource policies (`strictAuthorization()` makes a policy mandatory for
every resource), page `canAccess()` methods, controllers and model guards. Hiding a button is never the control.

## Role / permission matrix

The table below is generated from `PermissionRegistry` (58 permissions, 14 default roles).

**SA** Super Administrator · **Adm** Administrator · **CM** Content Manager · **SEO** SEO Manager · **BEd** Blog Editor · **BAu** Blog Author · **PM** Product Manager · **SM** Service Manager · **Sal** Sales Manager · **TL** Team Lead · **Sup** Support Agent · **HR** HR Manager · **Mod** Reviewer / Moderator · **RO** Analyst (read-only)

| Permission | SA | Adm | CM | SEO | BEd | BAu | PM | SM | Sal | TL | Sup | HR | Mod | RO |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| **Dashboard** | | | | | | | | | | | | | | |
| `dashboard.view` — Access the admin panel and dashboard | ● | ● | ● | ● | ● | ● | ● | ● | ● | ● | ● | ● | ● | ● |
| **Users & roles** | | | | | | | | | | | | | | |
| `users.view` — View users | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `users.create` — Create / invite users | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `users.update` — Edit users, activate and deactivate accounts | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `users.delete` — Delete users | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `roles.manage` — Manage roles and permissions | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| **Settings** | | | | | | | | | | | | | | |
| `settings.manage` — Company, contact, social, forms and privacy settings | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `appearance.manage` — Theme colours, modes and animation settings | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `integrations.manage` — Analytics, maps, CAPTCHA and other integrations | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `email_templates.manage` — Email templates and notification recipients | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `system.manage` — Cache, queues, backups and maintenance tools | ● |  |  |  |  |  |  |  |  |  |  |  |  |  |
| **Audit & security** | | | | | | | | | | | | | | |
| `audit_logs.view` — View the administrative audit log | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `security_logs.view` — View login and security activity | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| **Website content** | | | | | | | | | | | | | | |
| `pages.view` — View pages and homepage sections | ● | ● | ● | ● |  |  |  |  |  |  |  |  |  | ● |
| `pages.manage` — Create and edit pages, sections and announcements | ● | ● | ● |  |  |  |  |  |  |  |  |  |  |  |
| `navigation.manage` — Manage header and footer navigation | ● | ● | ● |  |  |  |  |  |  |  |  |  |  |  |
| `media.view` — Browse the media library | ● | ● | ● | ● | ● | ● | ● | ● |  |  |  | ● |  | ● |
| `media.manage` — Upload and edit media | ● | ● | ● |  | ● | ● | ● | ● |  |  |  | ● |  |  |
| `media.delete` — Delete media files | ● | ● | ● |  |  |  |  |  |  |  |  |  |  |  |
| **Services & products** | | | | | | | | | | | | | | |
| `services.view` — View services, categories and technologies | ● | ● | ● | ● |  |  | ● | ● | ● |  |  |  |  | ● |
| `services.manage` — Manage services, categories and technologies | ● | ● | ● |  |  |  |  | ● |  |  |  |  |  |  |
| `products.view` — View products | ● | ● | ● | ● |  |  | ● | ● | ● |  |  |  |  | ● |
| `products.manage` — Manage products, plans and product categories | ● | ● | ● |  |  |  | ● |  |  |  |  |  |  |  |
| `industries.view` — View industries | ● | ● | ● | ● |  |  | ● | ● |  |  |  |  |  | ● |
| `industries.manage` — Manage industries | ● | ● | ● |  |  |  |  | ● |  |  |  |  |  |  |
| `portfolio.view` — View case studies | ● | ● | ● | ● |  |  | ● | ● |  |  |  |  |  | ● |
| `portfolio.manage` — Manage case studies and portfolio categories | ● | ● | ● |  |  |  |  | ● |  |  |  |  |  |  |
| **Company** | | | | | | | | | | | | | | |
| `company.view` — View team, milestones, achievements, statistics, FAQs | ● | ● | ● | ● |  |  |  |  |  |  |  | ● |  | ● |
| `company.manage` — Manage team, milestones, achievements, statistics, FAQs | ● | ● | ● |  |  |  |  |  |  |  |  |  |  |  |
| `clients.manage` — Manage clients and logo approvals | ● | ● | ● |  |  |  |  |  |  |  |  |  |  |  |
| `partners.manage` — Manage partners and alliances | ● | ● | ● |  |  |  |  |  |  |  |  |  |  |  |
| **Blog** | | | | | | | | | | | | | | |
| `blogs.view` — View all blog posts | ● | ● | ● | ● | ● |  |  |  |  |  |  |  | ● | ● |
| `blogs.create` — Write posts (own drafts only) | ● | ● | ● |  | ● | ● |  |  |  |  |  |  |  |  |
| `blogs.edit` — Edit posts written by others | ● | ● | ● |  | ● |  |  |  |  |  |  |  |  |  |
| `blogs.review` — Review submitted posts and leave feedback | ● | ● | ● |  | ● |  |  |  |  |  |  |  | ● |  |
| `blogs.publish` — Publish, schedule and unpublish posts | ● | ● | ● |  | ● |  |  |  |  |  |  |  |  |  |
| `blogs.delete` — Delete posts | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `blog_taxonomies.manage` — Manage blog categories, tags and authors | ● | ● | ● |  | ● |  |  |  |  |  |  |  |  |  |
| `comments.moderate` — Moderate blog comments | ● | ● | ● |  | ● |  |  |  |  |  |  |  | ● |  |
| **SEO** | | | | | | | | | | | | | | |
| `seo.manage` — Edit SEO metadata, global SEO settings and robots rules | ● | ● |  | ● |  |  |  |  |  |  |  |  |  |  |
| `redirects.manage` — Manage URL redirects | ● | ● |  | ● |  |  |  |  |  |  |  |  |  |  |
| **Reviews** | | | | | | | | | | | | | | |
| `testimonials.view` — View client feedback | ● | ● | ● |  |  |  |  |  | ● | ● | ● |  | ● | ● |
| `testimonials.moderate` — Approve, reject, feature and reply to feedback | ● | ● |  |  |  |  |  |  |  |  |  |  | ● |  |
| `testimonials.delete` — Delete feedback | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| **Query desk (leads & CRM)** | | | | | | | | | | | | | | |
| `inquiries.view` — View inquiries assigned to me | ● | ● |  |  |  |  |  |  | ● | ● | ● | ● |  | ● |
| `inquiries.view_team` — View and pick up inquiries routed to my teams | ● | ● |  |  |  |  |  |  | ● | ● | ● | ● |  |  |
| `inquiries.view_all` — View every inquiry (including unrouted ones) and move inquiries between teams | ● | ● |  |  |  |  |  |  | ● |  |  |  |  | ● |
| `inquiries.assign` — Assign inquiries (team leads: within their team) | ● | ● |  |  |  |  |  |  | ● | ● |  |  |  |  |
| `inquiries.update` — Update status, priority, notes and follow-ups | ● | ● |  |  |  |  |  |  | ● | ● | ● | ● |  |  |
| `inquiries.export` — Export inquiries to CSV | ● | ● |  |  |  |  |  |  | ● | ● |  |  |  |  |
| `inquiries.delete` — Delete inquiries | ● | ● |  |  |  |  |  |  |  |  |  |  |  |  |
| `teams.manage` — Manage query desk teams, members and routing rules | ● | ● |  |  |  |  |  |  | ● |  |  |  |  |  |
| `newsletter.manage` — Manage newsletter subscribers | ● | ● |  |  |  |  |  |  | ● |  |  |  |  |  |
| **Careers** | | | | | | | | | | | | | | |
| `careers.view` — View job openings | ● | ● | ● | ● |  |  |  |  |  |  |  | ● |  | ● |
| `careers.manage` — Manage job openings | ● | ● |  |  |  |  |  |  |  |  |  | ● |  |  |
| `applications.view` — View job applications and résumés | ● | ● |  |  |  |  |  |  |  |  |  | ● |  |  |
| `applications.update` — Update application status and notes | ● | ● |  |  |  |  |  |  |  |  |  | ● |  |  |
| `applications.delete` — Delete job applications | ● | ● |  |  |  |  |  |  |  |  |  | ● |  |  |

Super Administrators additionally pass every permission check through `Gate::before` — but policy guard rails still
apply to them (see below). Administrators have every permission except `system.manage`.

## Guard rails (apply to everyone)

| Rule | Where |
|---|---|
| Inactive accounts cannot sign in; deactivating an account deletes its sessions and signs it out on the next request | `User::canAccessPanel`, `User` saved hook, `AuthenticateAdmin` middleware |
| Nobody can delete or deactivate themselves, or change their own roles | `UserPolicy` |
| Only Super Administrators can edit other Super Administrators; the last active Super Administrator can never be deleted, deactivated or demoted | `UserPolicy::isLastActiveSuperAdmin` |
| Non-super-admins can only assign roles whose permissions they hold, and only grant permissions they hold; never the Super Administrator role (tampered requests fail validation) | `EscalationGuard` + Role/User forms |
| The Super Administrator role is locked; Administrator and Super Administrator roles cannot be deleted; roles in use cannot be deleted | `RolePolicy` |
| Users without `inquiries.view_all` only see inquiries assigned to them, plus — with `inquiries.view_team` — inquiries routed to teams they belong to (lists, tabs, widgets, direct URLs → 404, attachments → 403, exports). Unrouted inquiries need `view_all` | `ContactInquiry::visibleTo`, `ContactInquiryPolicy` |
| Team leads (`inquiries.assign` without `view_all`) can only assign an inquiry to members of that inquiry's team; enforced in `InquiryManager::assign` and by the assignee select's server-side option validation (tampered payloads fail) | `InquiryManager`, `InquiryActions::assigneeSelect` |
| Picking up ("claiming") needs `inquiries.update` + `inquiries.view_team` + membership of the inquiry's team, and only works while the inquiry is unassigned (row lock prevents double claims) | `ContactInquiryPolicy::claim`, `InquiryManager::claim` |
| Moving an inquiry to another team needs `inquiries.view_all` + `inquiries.assign` | `ContactInquiryPolicy::changeTeam` |
| Team members must be active users whose role allows working on inquiries (validated server-side) | `TeamForm::eligibleUsers` |
| Blog authors only see/edit their own drafts; only `blogs.publish` may schedule/publish/unpublish (also enforced in a model save hook) | `BlogPostPolicy`, `BlogPost` booted guard, `BlogWorkflow` |
| SEO-only users can edit SEO metadata of content but every other field is disabled and therefore never saved | `ModulePolicy::$seoEditable`, `ContentFields::lockedUnless` |
| System pages (home, about, legal …) cannot be deleted and keep their slug/template | `PagePolicy`, Page form |
| The audit log is append-only (no create/edit/delete for anyone) | `AuditLogPolicy` |
| Media still referenced by content or settings cannot be deleted | `MediaUsage` |
| Private files are only downloadable after a policy check; each download is audited | `FileDownloadController` |

## Admin modules

Admin URL: `/admin`. Sidebar groups (in order): Query desk · Website content · Services & products · Portfolio & clients ·
Blog · Company · Reviews · Careers · SEO · Appearance · Settings · Users & security · System.

| Module | Required permission(s) |
|---|---|
| Dashboard (my query desk, stats, inquiries chart, lead pipeline, latest inquiries, pending reviews, recent activity — each widget permission-aware) | `dashboard.view` |
| Inquiries (query desk: queues — all open, my queue, team queue, unassigned, overdue, follow-up due, closed; pick up, assign, move team, status, priority, notes, follow-ups, response-time tracking, activity timeline, email history, CSV export) | `inquiries.view` / `view_team` / `view_all` / `assign` / `update` / `export` / `delete` |
| Teams (members and leads, topics and services handled, round-robin auto-assignment, shared mailbox) | `teams.manage` |
| Newsletter subscribers | `newsletter.manage` |
| Pages + sections, Navigation menus, Announcements | `pages.view`/`pages.manage`, `navigation.manage` |
| Media library | `media.view` / `media.manage` / `media.delete` |
| Services, Service categories, Technologies | `services.view` / `services.manage` (+ `seo.manage` for SEO fields) |
| Products (+ plans, gallery), Product categories | `products.view` / `products.manage` |
| Industries | `industries.view` / `industries.manage` |
| Case studies, Portfolio categories | `portfolio.view` / `portfolio.manage` |
| Clients / Partners (logo approvals) | `clients.manage` / `partners.manage` (view: `company.view`) |
| Team, Milestones, Achievements, Statistics, FAQs, FAQ categories | `company.view` / `company.manage` |
| Blog posts (workflow, revisions, AI outline), Categories, Tags, Authors, Comments | `blogs.*`, `blog_taxonomies.manage`, `comments.moderate` |
| Testimonials (moderation, private replies, consent trail) | `testimonials.view` / `moderate` / `delete` |
| Job openings, Job applications (résumé download) | `careers.*`, `applications.*` |
| SEO settings, Redirects | `seo.manage`, `redirects.manage` |
| Theme settings (draft, preview, publish, contrast check), Animation settings | `appearance.manage` |
| Company & contact, Forms/privacy/features settings | `settings.manage` |
| Integrations (status only for secrets) | `integrations.manage` |
| Email templates (test send, restore default), Email log | `email_templates.manage` |
| Users (invitations, activation, MFA reset), Roles | `users.*`, `roles.manage` |
| Audit log / Login & security activity | `audit_logs.view` / `security_logs.view` |
| System health (queues, failed jobs, scheduler heartbeat, cache, backups, maintenance mode) | `system.manage` (Super Administrators) |

## Public route map

| Method | Path | Name | Notes |
|---|---|---|---|
| GET | `/` | home | Homepage built from sections |
| GET | `/about` | about | About page (sections) |
| GET | `/services`, `/services/{slug}` | services.index/show | |
| GET | `/products`, `/products/{slug}` | products.index/show | Filters: `category`, `industry`, `availability`, `q` (filtered views are noindex) |
| GET | `/industries`, `/industries/{slug}` | industries.index/show | |
| GET | `/case-studies`, `/case-studies/{slug}` | portfolio.index/show | Filters: `category`, `industry` |
| GET | `/blog`, `/blog/{slug}`, `/blog/category/{slug}`, `/blog/tag/{slug}`, `/blog/author/{slug}` | blog.* | Search `?q=` (noindex) |
| POST | `/blog/{slug}/comments` | blog.comments.store | Throttled; held for moderation; feature toggle |
| GET | `/testimonials` | testimonials.index | Approved + permitted reviews only |
| GET/POST | `/feedback` | feedback.create/store | Throttled; spam-protected |
| GET | `/feedback/verify/{testimonial}` | feedback.verify | Signed URL |
| GET | `/contact`, `/start-a-project` | contact.create, project.create | Quick and detailed forms |
| POST | `/contact` | contact.store | Throttled (5/min, 30/day per IP); spam-protected; private attachments |
| GET | `/careers`, `/careers/{slug}`, `/careers/apply` | careers.* | Feature toggle |
| POST | `/careers/apply/{slug?}` | careers.apply | Throttled; résumé stored privately; closed roles return 410 |
| GET | `/team`, `/faq`, `/search` | team.index, faq.index, search | Search is noindex and throttled |
| GET | `/company-profile` | brochure | Redirects to the configured PDF |
| POST | `/newsletter` | newsletter.store | Double opt-in; throttled |
| GET | `/newsletter/confirm/{subscriber}`, `/newsletter/unsubscribe/{subscriber}` | newsletter.* | Signed URLs |
| POST | `/cookie-consent` | cookie-consent | Stores choice + anonymous consent record |
| GET | `/sitemap.xml`, `/sitemaps/{type}.xml`, `/robots.txt` | sitemap, sitemap.type, robots | Empty / disallow-all unless indexing is allowed |
| GET | `/art/{seed}.svg` | art | Generated artwork; cookie-less, cached 1 year |
| GET | `/preview/{type}/{id}` | preview.show | Signed + authenticated + `view` policy; noindex |
| GET | `/theme-preview/start`, `/theme-preview/stop` | theme-preview.* | `appearance.manage` |
| GET | `/admin/files/inquiries/{inquiry}/attachment` · `/admin/files/applications/{application}/resume` · `/admin/files/testimonials/{testimonial}/photo` | files.* | Authenticated, policy-checked, audited |
| GET | `/{slug}` | pages.show | CMS pages (registered last; reserved slugs in `config/softec.php`) |
| GET | `/up` | — | Health check |
