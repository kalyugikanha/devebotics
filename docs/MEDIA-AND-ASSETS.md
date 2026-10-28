# Media and visual assets

## What was produced — and what was not

- **No AI image-generation tool was available while building this project, so no AI-generated raster images were
  created.** Nothing in the codebase claims otherwise.
- Instead, every visual is original and produced in code:
  1. **Brand assets** derived from the supplied logo (`resources/brand/softec-logo-original.png`).
  2. **Procedural cover artwork** — deterministic SVG compositions (gradients from the logo palette, orbit rings, sphere,
     pixel squares, ribbon, glass icon tile) generated per record by `App\Support\Art\CoverArt` and served from
     `/art/{seed}.svg` (cached for a year). Used automatically for any service, industry, product, case study, article or
     team member without an uploaded image.
  3. **Illustration library**: 28 hand-built, theme-aware, animated SVG scenes
     (`resources/views/components/illustrations/*.blade.php`, styles in `resources/css/illustrations.css`). They cover
     code/modules, web and mobile apps, SaaS, e-commerce, APIs, AI networks, chat assistants, automation, analytics,
     cloud, DevOps, infrastructure, security, CRM, roadmap, design, support, and nine industries. They are drawn with
     CSS variables, so they adapt to light and dark mode, and they animate with CSS only (paused for reduced motion).
     Services, industries, products and case studies pick one in the admin (*Media › Illustration*) or get a default from
     their slug/icon (`App\Support\Art\IllustrationRegistry`); blog cards pick one from the category. Every scene can
     be previewed locally at `/dev/illustrations` (local environment only).
  4. **Interactive hero scene**: the brand mark in an orbit with floating "AI assistant", "Deploy pipeline", "Performance"
     and code cards (`resources/views/components/art/hero-scene.blade.php`), with mouse parallax on desktop.
  5. **Demo client/partner logos** — simple placeholder wordmarks clearly named "(demo)".
  6. **Technology logos**: 56 brand logos from [Simple Icons](https://simpleicons.org) (CC0 path data, rewritten as
     single-path SVGs in `resources/svg/tech`, licence notes in `resources/svg/tech/LICENSES.md`). They are shown only
     to identify technologies, which implies no partnership or endorsement. Icons carrying non-commercial or attribution
     licences (e.g. Vue.js, PHP) and logos withdrawn at their owners' request (e.g. AWS, Azure, Power BI, Salesforce) are
     **not** bundled. Those technologies show a neutral initials tile until an administrator uploads an official logo
     they are entitled to use (Admin › Technologies › Logo).

As soon as an administrator uploads an image for a record, it replaces the generated artwork.

## Brand assets (public/images/brand)

| File | Purpose |
|---|---|
| `softec-mark.webp` / `.png` | Transparent "S" mark cropped from the logo (soft-threshold background removal, checked on light and dark backgrounds); header, emails, hero |
| `softec-logo-full.png` | Full square logo (600px) for schema.org Organization `logo` |
| `og-default.jpg` | 1200×630 default social-sharing image |
| `favicon.ico` (16/32/48), `favicon-32/192/512.png`, `apple-touch-icon.png` | Browser and app icons; `public/site.webmanifest` |

Administrators can override the mark, light/dark logos, favicon and default share image in Settings › Company and
Settings › SEO. If full logos are uploaded and "show wordmark" is off, the header uses the uploaded logos instead of the
mark + live-text wordmark.

## Media library pipeline

Upload (admin) → `App\Support\Media\MediaLibrary`:

1. Real MIME detection from file contents (`finfo`); allowed: JPEG, PNG, WebP, AVIF, GIF, SVG, PDF.
2. Size (8 MB) and dimension (6000 px) limits.
3. SVGs sanitised (scripts, event handlers and remote references removed).
4. JPEG/PNG re-encoded with EXIF/GPS metadata stripped and orientation applied.
5. Stored as `media/YYYY/MM/<slugged-name>-<random>.<ext>` on the media disk (`MEDIA_DISK`, `public` or `s3`).
6. `GenerateMediaVariants` (queued) creates WebP variants at 480/960/1600 px.
7. Rendered with `<x-media.image>`: `<picture>` + WebP `srcset`/`sizes`, width/height attributes, lazy loading,
   `fetchpriority="high"` for above-the-fold images, alt text from the library.

Library features: search, categories, alt-text/caption editing, "missing alt text" filter, file replacement that keeps
the same media ID, usage tracking that blocks deleting referenced files, rich-editor attachments on the media disk.

**Alt text guidance**: describe what the image communicates in one short sentence; leave empty only for purely decorative
images; do not stuff keywords.

## Replacing artwork with photography or AI-generated images

1. Create or commission images (recommended sizes below) in the brand palette — violet/magenta/amber and sky/mint
   accents, clean technology settings, room for text on the left for hero images.
2. Export as JPEG/PNG/WebP at 2× the display size; the pipeline will generate responsive WebP variants.
3. Upload via **Admin › Website content › Media library** (or directly from the image field of a record) and write alt text.
4. Select the image as the record's hero/featured image. The generated artwork is replaced automatically.

| Usage | Recommended size |
|---|---|
| Homepage hero image (optional — replaces the orbit scene) | 1600×1200 |
| Service / industry / product / case-study hero | 1600×1100 |
| Blog featured image | 1600×800 |
| Product screenshots / galleries | 1920×1200 |
| Team photos | 800×800 |
| Client / partner logos | SVG or transparent PNG, ~360×120 |
| Social sharing (per record) | 1200×630 |

### Optional: AI image generation

If you use an image-generation service, generate images **outside the application** (or add an integration that keeps
the API key server-side in `.env`, never in browser code), review every image for accuracy and brand fit, and upload the
approved files through the media library. Do not depict real people, client logos or products that do not exist.

## Fonts

Inter and Plus Jakarta Sans variable fonts are bundled from Fontsource at build time (`resources/css/app.css`) —
self-hosted, no third-party font requests, `font-display: swap`, plus a metric-matched Arial fallback for Inter to limit
layout shift.
