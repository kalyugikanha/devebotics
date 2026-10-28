# Design system — "Orbit"

The visual identity is derived from the Softec Ai Solution logo: the ribbon-like **S**, the **orbit ring** with its
sphere, the **pixel squares**, and the two gradients (violet → magenta → amber and sky → mint). These motifs recur
across the site — orbit rings in the hero and CTAs, pixel clusters in artwork, the gradient ribbon on the footer and
emails — so pages feel like one brand rather than a template.

Source: `resources/css/app.css` (tokens, components, motion utilities), Blade components in `resources/views/components`.

## Colour

Semantic tokens map to runtime CSS variables written by `ThemeManager` from **Admin › Appearance › Theme**
(separate light and dark palettes, draft/preview/publish, WCAG contrast warnings).

| Token | Utility | Default light | Default dark | Use |
|---|---|---|---|---|
| primary | `text-primary`, `bg-primary` | `#4F46E5` | `#818CF8` | Links, focus rings, key accents |
| secondary | `text-secondary` | `#0E7490` | `#22D3EE` | Cool accents |
| accent | `text-accent` | `#B45309` | `#FBBF24` | Warm accents (sparingly) |
| highlight | `text-highlight` | `#7C3AED` | `#C4B5FD` | Violet nod to the logo |
| background | `bg-background` | `#FFFFFF` | `#060B1A` | Page |
| surface | `bg-surface` | `#F5F7FD` | `#0C1428` | Muted sections, cards |
| foreground | `text-foreground` | `#0A1128` | `#E9EEF9` | Body text |
| muted | `text-muted` | `#4A5672` | `#9BA7C2` | Secondary text |
| border | `border-border` | `#E2E7F2` | `#1B2642` | Hairlines |

The palette is **"Midnight indigo"**: violet from the logo flowing into indigo and blue (`--gradient-brand`:
`#6D28D9 → #4F46E5 → #2563EB`, white text passes AA on every stop), cyan as the cool accent, deep navy dark mode. Dark
"stage" bands (`class="dark stage"`: homepage hero, statistics, CTA card, footer, mega-menu feature card) switch the
tokens to the dark palette locally, so light-mode pages get rhythm from deep-navy sections with indigo/teal glows.
Decorative multi-colour lines (scroll progress, footer ribbon) use `--gradient-spectrum`.

Fixed logo palette (artwork and gradients): violet `#8B5CF6`, pink `#EC4899`, amber `#F59E0B`, cyan `#22D3EE`,
mint `#34D399`, blue `#3B82F6`, ink `#0B1020`. All default text/background pairs meet WCAG 2.2 AA (≥ 4.5:1),
verified by the admin contrast checker and by axe in the browser tests (light and dark).

## Typography

| Role | Font | Notes |
|---|---|---|
| Display / headings | Plus Jakarta Sans Variable, 700–800 | Tight tracking (−0.02 to −0.03em), `text-wrap: balance` |
| Body / UI | Inter Variable | 17px base, relaxed leading; metric-matched Arial fallback limits layout shift on swap |
| Code | system monospace | Hero card, code blocks |

Scale utilities: `.heading-display` (hero, 36→68px), `.heading-section` (30→44px), `.heading-card`, `.lead`, `.eyebrow`
(uppercase 12px with gradient dot). Headings support inline emphasis by wrapping words in square brackets in the admin:
`Build smarter software with [AI at the core]` → gradient text (`App\Support\Typography`, HTML-escaped).

## Spacing, radius, elevation

- Container: `.container-site` (max 80rem, 20/24/32px gutters). Sections: `.section` (80px mobile, 96px tablet, 112px desktop).
- Two plain (default-background) sections in a row share one gap: `.section-plain + .section-plain` reduces the second
  section's top padding, so stacked white sections don't get a double gap.
- Radius presets (admin selectable): sharp / soft / rounded → `rounded-control`, `rounded-card`, `rounded-panel`.
- Elevation is restrained: hairline borders, soft coloured shadows on hover, glass panels (`.glass`) for floating UI.

## Components

| Component | Class / Blade |
|---|---|
| Buttons | `.btn` + `.btn-primary` (gradient), `.btn-secondary`, `.btn-ghost`, `.btn-inverse`; sizes `.btn-sm`, `.btn-lg`; `<x-forms.submit>` with busy state |
| Links | `.link-arrow` (animated arrow) |
| Cards | `.card`, `.card-muted`, `.card-interactive` (gradient border + lift), stretched-link pattern `.card-link`; `<x-cards.*>` for services, products, case studies, posts, industries, testimonials, team |
| Icon tiles | `.icon-tile`, `.icon-tile-soft`; icons from a curated Heroicons whitelist (`IconRegistry`) |
| Badges & chips | `.badge`, `.badge-primary`, `.badge-demo`, `.chip` |
| Forms | `.form-label`, `.form-control`, `.form-help`, `.form-error`, `.form-check`; `<x-forms.field>`, `<x-forms.consent>`, `<x-forms.star-rating>` (radio group), `<x-forms.file>`, `<x-forms.spam-protection>`, `<x-forms.form-errors>` (focuses the summary) |
| Alerts | `.alert-success`, `.alert-error`, `.alert-info` (live regions where they announce results) |
| Accordion | native `<details>` (`.accordion-item`) — keyboard and screen-reader friendly without JS |
| Tabs | `x-data="tabs"` (WAI-ARIA tabs, roving tabindex, arrow/Home/End keys) |
| Carousel | `x-data="carousel"` (scroll-snap, previous/next buttons, no autoplay) |
| Navigation | floating sticky header; nav items in a pill track with a highlight that glides to the hovered item (`.nav-indicator`); services **mega menu** with a category rail, services with icons and one-line descriptions, and a featured card; dropdowns with icons; hover-intent timing and a soft page dim; menus attach to the header bar so they never overlap it; focus-trapped mobile drawer grouped by category; breadcrumbs |
| Section heading | `<x-ui.section-heading>`; section wrapper `<x-site.section>` with backgrounds default / muted / gradient / contrast |
| Illustrations | `<x-illustration name="cloud">` (28 theme-aware animated SVG scenes, whitelist in `IllustrationRegistry`) and `<x-art.frame>` (illustration on a tinted canvas); chosen per service/industry/product/case study in the admin or derived from the slug |
| Service bento | `<x-cards.service variant="feature|wide|default">`: the first and last services get large illustrated cards |
| Statement | `statement` section type: a large sentence whose words light up while scrolling (`[data-scrub-words]`) |
| Stage & glow | `.stage` (deep-indigo band), `.aurora` / `.orb` (blurred colour fields), `.bg-grid`, `.border-gradient`, `.glass` |
| WhatsApp chat | `<x-site.whatsapp-chat>`: floating button + panel with greeting and quick replies, linking to `wa.me` (see below) |
| Technology logos | `<x-ui.tech-logo>` / `<x-ui.tech-chip>`: bundled brand logo (resources/svg/tech), capability icon, or a solid initials tile; brand colours adjusted per theme for visibility (`TechLogoRegistry`) |
| States | empty states on listings and admin tables, success/failure alerts, 403/404/410/419/429/500/503 pages |

## Theming behaviour

- Visitor switcher cycles light → dark → system (can be disabled in the admin); choice stored in `localStorage`.
- The class is applied by an inline head script before first paint — no flash of the wrong theme.
- Colour transitions are enabled only while toggling (`.theme-transition`), avoiding animated paints on load.
- The admin panel follows the published primary colour and has its own light/dark/system mode.

## WhatsApp chat widget

Configured in **Admin › Settings › Company & contact**: WhatsApp number (international format), "show chat widget"
toggle, greeting text and up to six quick-reply messages. The `wa.me` links are built on the server; no API key or token
is involved and nothing is sent until the visitor taps a message in WhatsApp. In production the widget only appears once
a number is entered. In local and staging environments it shows a "preview" notice instead of a link until then. The
button uses WhatsApp teal `#128C7E` (white-on-`#25D366` fails contrast).

## Motion rules

- Purposeful, short (≈0.6–0.9s), ease-out; no spinning, bouncing or flashing; no autoplaying carousels.
- Hero text reveal is CSS-only and starts immediately; reveals never hide content without JavaScript.
- Decorative ambient motion (orbs, orbit rings, floating cards) can be switched off in the admin, is reduced on phones,
  and stops entirely for `prefers-reduced-motion` or the visitor's footer toggle.
- Counters render the final value server-side; the count-up is an enhancement.
- Interaction layer (`resources/js/effects`, no dependencies): cursor spotlight on cards, magnetic primary buttons,
  gentle 3D tilt on artwork, mouse parallax in the hero scene, rotating "Specialists in …" words, a constellation canvas,
  a scroll-progress bar and back-to-top. Pointer effects run only on fine pointers (not touch) and none run with reduced motion.
- Scroll effects (lazy GSAP + ScrollTrigger): statement words light up, the process line fills and activates each step,
  and decorative parallax runs on desktop only.
- Admin settings: global on/off, intensity, duration scale (0.5–1.5×), hero preset (rise/blur/fade/none), reveal preset
  (fade-up/fade/zoom/slide/none), stagger, parallax, counters, ambient, logo marquee, decorative motion on mobile.

## Accessibility checklist built into components

Skip link · landmarks (`header`, `nav`, `main`, `footer`) · one H1 per page and ordered headings · visible focus rings ·
labelled form fields with `aria-invalid` + described errors · error summary receives focus · status messages in live
regions · accessible star rating (native radios) · decorative art `aria-hidden` · meaningful alt text from the media
library · colour never the only signal (icons + text on statuses) · 44px touch targets · reduced-motion support.
