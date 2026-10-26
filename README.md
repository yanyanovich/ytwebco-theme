# ytwebco-theme

Custom WordPress theme for **YT Web Co.** — the portfolio site of Yan Tokaruk, WordPress & Shopify developer from Lutsk, Ukraine.

No page builders, no parent theme: every section of the design is a custom ACF Pro Gutenberg block. Built for speed, with a **PageSpeed 95+ (mobile)** target.

> 🚧 **Work in progress.** The theme is being built step by step. Live site link, screenshots and real PageSpeed / Core Web Vitals results will be added after launch.

---

## Stack

- **WordPress 6.x**, PHP 8.2+
- **ACF Pro** — Gutenberg blocks (`block.json` + PHP template), field groups synced as JSON
- **Polylang** — English at `/`, Ukrainian at `/uk/`
- **SCSS + modern JS** (ES modules, bundled with webpack), **GSAP** for scroll and count-up animations
- **Rank Math**: titles, meta, sitemap, schema

## Features

**Done**
- [x] Modular theme core: `inc/` split into small, auto-included modules
- [x] Automatic ACF block registration from `template-parts/blocks/*/*/block.json`
- [x] Allow-listed blocks per post type, with custom block categories
- [x] Custom block inserter UI: allowed blocks only, manual order, search, previews
- [x] Editable archive pages: a hidden CPT whose Gutenberg content is attached to post-type archives (Polylang-aware)
- [x] Hardening and cleanup: comments disabled, extra upload MIME types, lowercase URL redirects, image srcset/lazy-load helpers

**Planned**
- [ ] Design tokens, self-hosted fonts (Inter Tight, JetBrains Mono, Latin + Cyrillic)
- [ ] Sticky header with compact state, Services dropdown, accessible full-screen mobile menu (focus trap, scroll lock)
- [ ] Footer with live Lutsk time (`Intl.DateTimeFormat`, auto UTC offset)
- [ ] Homepage blocks: hero, metrics strip, services, case grid, process, about, FAQ, contact
- [ ] `case` CPT with brand-colour hover cards and a Work archive with filtering
- [ ] EN / UA content, hreflang, self-referencing canonicals
- [ ] Contact form → REST endpoint → Telegram (nonce, honeypot, rate limiting), event-based conversion tracking
- [ ] Custom cookie banner with Google Consent Mode v2
- [ ] Schema: Person, ProfessionalService, Service, FAQPage, CreativeWork, BreadcrumbList
- [ ] Performance pass: WebP/AVIF, critical CSS, no jQuery on the frontend, no WP bloat
- [ ] Accessibility pass: keyboard navigation, visible focus states, `prefers-reduced-motion`

## Structure

```text
ytwebco-theme/
├── acf-json/               # ACF field groups (JSON sync)
├── assets/                 # compiled CSS / JS (committed, so the server needs no Node build)
│   ├── css/style.min.css
│   └── js/scripts.min.js
├── inc/
│   ├── custom_blocks_ui/   # custom block inserter for the editor
│   ├── post_types/         # one file per CPT, auto-included
│   ├── theme_settings/     # setup, media, images, security, redirects, uploads…
│   ├── acf_settings.php    # ACF JSON path, options pages, link "nofollow" option
│   ├── gutenberg_blocks.php# block registration, allowed blocks, categories
│   ├── CustomArchiveContent.php
│   ├── enqueue_scripts_and_styles.php
│   └── helpers.php
├── template-parts/
│   └── blocks/<group>/<block>/   # block.json + template.php + preview
├── fonts/  media/  js/
├── functions.php
└── style.css
```

## Local setup

1. Install WordPress 6.x locally (MAMP, LocalWP, Docker, etc.).
2. Clone this repo into `wp-content/themes/`:
   ```bash
   git clone https://github.com/yanyanovich/ytwebco-theme.git wp-content/themes/ytwebco-theme
   ```
3. Install and activate the required plugins (not included in this repo): **ACF Pro**, **Polylang**, **Rank Math**.
4. Activate the theme.
5. In **ACF → Field Groups**, sync the field groups from `acf-json/`.

The compiled assets in `assets/` are committed, so the theme works without running a build.

## Configuration & secrets

No API keys, tokens or credentials are stored in this repository. Integrations read them from `wp-config.php` constants. The required constants will be listed here when those features land, e.g. the Telegram bot token and chat ID for the leads form.

## Commit conventions

[Conventional Commits](https://www.conventionalcommits.org/) in English, e.g. `feat(header): add sticky header with compact state`. Work happens in `feat/step-N-*` branches that are merged into an always-deployable `main`.

---

© 2026 Yan Tokaruk / YT Web Co. All rights reserved.
