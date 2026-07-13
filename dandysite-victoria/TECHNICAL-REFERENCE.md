# Dandysite Victoria — Technical Reference

**Theme:** Dandysite Victoria  
**Author:** Nancy Dorsner / Dabbled Studios  
**Base theme for:** Political candidate websites  
**Derived from:** Dandysite Jane (Integrity Wines project)  

---

## Architecture Overview

Victoria is a **base theme** for political candidate sites. It is never installed alone in production — it always works alongside a site-specific plugin (`ds-[sitename]`).

```
dandysite-victoria/        ← base theme (this repo)
ds-[sitename]/             ← site plugin: brand overrides + candidate-specific content
```

The site plugin handles:
- CSS custom property overrides (brand colors, fonts)
- Any CPT meta extensions or additional CPTs unique to that candidate
- Site-specific template customizations via filters
- Donate/volunteer URL configuration

---

## Repository Structure

```
dandysite-victoria/
├── assets/
│   ├── css/
│   │   ├── style.css          # Compiled from style.less — DO NOT edit directly
│   │   ├── header.css         # Header system styles + search overlay
│   │   ├── footer.css         # Footer styles
│   │   ├── homepage.css       # Homepage section base styles
│   │   └── editor-style.css   # Block editor styles
│   ├── js/
│   │   ├── main.js            # General scripts
│   │   ├── header.js          # Header scroll behavior + search overlay JS
│   │   └── header-admin.js    # Admin-side header settings UI
│   └── images/
│       ├── logo.svg           # Fallback logo (replace per site or use WP Customizer)
│       ├── icon-search.svg    # Search icon (used in header toggle + overlay submit)
│       ├── icon-facebook.svg
│       └── icon-instagram.svg
├── includes/
│   ├── cpts/
│   │   ├── cpt-endorsements.php   # Endorsements CPT (universal to all Victoria sites)
│   │   └── cpt-positions.php      # Positions/Issues CPT (universal to all Victoria sites)
│   ├── header-settings.php    # Header style/behavior Customizer options
│   ├── header-functions.php   # Header rendering functions
│   ├── header-meta.php        # Per-page header meta boxes
│   ├── footer-settings.php    # Footer Customizer options
│   ├── social-settings.php    # Social media URL settings
│   ├── site-identity.php      # Logo variant system
│   └── homepage-meta.php      # Hero section meta box + dsp_get_hero_meta()
├── page-templates/
│   ├── page-hero-fullbleed.php
│   ├── page-hero-split-left.php
│   └── page-hero-split-right.php
├── template-parts/
│   ├── hero.php               # Hero section dispatcher (reads layout arg)
│   ├── hero-inner.php         # Hero content (headline, tagline, CTA)
│   ├── section-bio.php        # Meet the candidate section
│   ├── section-issues.php     # Issues/Positions grid (reads dsp_position CPT)
│   ├── section-news.php       # Recent posts / in the news
│   ├── section-endorsements.php  # Endorsements grid (reads dsp_endorsement CPT)
│   └── section-get-involved.php  # CTA section (volunteer/donate/signup)
├── front-page.php             # Homepage — filterable section list
├── single-dsp_position.php    # Single position/issue template
├── archive-dsp_endorsement.php   # Full endorsements listing
├── style.less                 # Source file — edit this, compile to style.css
├── style.css                  # Theme header + compiled CSS
├── functions.php
├── header.php
├── footer.php
└── index.php
```

---

## Color System

### Base Palette ("Victoria")

| Name | Hex | Role |
|---|---|---|
| Oxford Navy | `#1d3557` | `--color-primary` — dominant brand, headers, footer |
| Cerulean | `#457b9d` | `--color-secondary` — supporting brand |
| Frosted Blue | `#a8dadc` | `--color-surface-alt` — callouts, tags |
| Honeydew | `#f1faee` | `--color-surface` — card/section backgrounds |
| Punch Red | `#e63946` | `--color-accent` — CTA, buttons, highlights |
| White | `#ffffff` | `--color-background` |

### How to Override in a Site Plugin

In `ds-[sitename]/assets/css/site.less` (or `site.css`):

```less
// Define campaign-specific vars
@campaign-primary: #C8102E;
@campaign-dark:    #1a1a2e;

// Override semantic roles
:root {
    --color-primary:    @campaign-dark;
    --color-secondary:  @campaign-primary;
    --color-accent:     @campaign-primary;
    --color-button-bg:  @campaign-primary;
}
```

The site plugin's stylesheet loads after Victoria's (`['dsp-style']` dependency), so `:root` overrides cascade correctly.

### CSS Custom Properties Reference

```css
/* Palette (named, available for direct reference) */
--color-punch-red
--color-honeydew
--color-frosted-blue
--color-cerulean
--color-oxford-navy

/* Semantic roles (override these in site plugin) */
--color-primary
--color-secondary
--color-accent
--color-text
--color-text-light
--color-background
--color-surface
--color-surface-alt
--color-border
--color-button-bg
--color-button-text

/* Dark-surface context (override this SET in site plugin) */
--color-dark-bg              /* background of dark sections; default: var(--color-primary) */
--color-on-dark-heading
--color-on-dark-text
--color-on-dark-text-light
--color-on-dark-link         /* accent usable ON dark — often NOT the brand accent */
--color-on-dark-link-hover
--color-on-dark-label        /* section eyebrows on dark */
--color-on-dark-border
```

### Surface Context System

Every homepage section can sit on a **light**, **surface**, or **dark** background, assigned per section in **Appearance → Homepage Settings → Section Order & Backgrounds** (stored as `dsp_hp_bg_{section}`, default `'default'` = the section's built-in background). `dsp_section_bg_class( $section )` returns the context class (` section--light` / ` section--surface` / ` section--dark`, filterable via `dsp_section_bg_class`), and each section template appends it to its wrapper.

**How theming works:** components consume `--ctx-*` tokens with light-context fallbacks, e.g.

```css
.section-title { color: var(--ctx-heading, var(--color-text)); }
```

Context classes define the `--ctx-*` set — `.section--dark` maps them to the `--color-on-dark-*` tokens; `.section--light` / `.section--surface` map them back to light values. Sections that are dark or branded by default (`.section-issues`, `.section-get-involved`) carry the dark tokens automatically, no class needed.

**The rule for site plugins:** never re-style dark sections component-by-component. Override the `--color-on-dark-*` set once in `:root` and every dark surface — current and future — follows. This exists because brand accents are frequently unreadable on dark backgrounds (Hawk's red-on-navy needed a cornflower substitute); `--color-on-dark-link` forces that decision up front. The `SURFACE CONTEXTS` block must remain at the **end** of the section styles in `style.css` so context classes win the cascade over per-section defaults.

Ctx tokens available inside any section: `--ctx-heading`, `--ctx-text`, `--ctx-text-light`, `--ctx-link`, `--ctx-link-hover`, `--ctx-label`, `--ctx-border`.

---

## Custom Post Types

Both CPTs are registered in the theme (not the site plugin) because every Victoria site uses them.

### Endorsements (`dsp_endorsement`)

**Admin label:** Endorsements  
**Public:** No (displayed only via template functions)  
**Supports:** title, thumbnail

| Meta Key | Type | Description |
|---|---|---|
| `dsp_endorser_title_org` | text | e.g. "Mayor, City of Dallas" |
| `dsp_endorser_quote` | textarea | Short pull quote |
| `dsp_endorser_link` | url | Optional link (name becomes clickable — opens in new tab) |
| `dsp_endorsement_featured` | checkbox | Show on homepage section |

**Template helper:**
```php
dsp_get_endorsements( $featured_only = false, $limit = 0 )
// Returns WP_Post[]
```

**Templates:** `archive-dsp_endorsement.php`, `template-parts/section-endorsements.php`

---

### Positions (`dsp_position`)

**Admin label:** Issues & Positions  
**Public:** Yes — each position has its own page at `/issues/[slug]`  
**Archive slug:** `/issues/`  
**Supports:** title, editor, thumbnail, excerpt, page-attributes

| Meta Key | Type | Description |
|---|---|---|
| `dsp_position_summary` | textarea | Short homepage blurb (falls back to excerpt) |
| `dsp_position_icon` | text | Dashicon class, e.g. `dashicons-heart` |
| `dsp_position_on_homepage` | checkbox | Include in homepage issues section |

**Template helper:**
```php
dsp_get_positions( $homepage_only = false )
// Returns WP_Post[]
```

**Templates:** `single-dsp_position.php`, `template-parts/section-issues.php`

---

## Homepage System

### Get Involved / CTA Settings & Media Kit

The CTA section's heading, text, and up to three buttons (label / URL / solid-or-outline style) are editable in **Appearance → Homepage Settings → Get Involved / CTA**. Blank heading/text falls back to the theme defaults; the form prefills the theme's default buttons the first time so saving never changes a site that hasn't customized them. The `dsp_cta_title` / `dsp_cta_text` / `dsp_cta_actions` filters still run after settings for programmatic overrides. Site plugins can seed officeholder-mode content on activation (see ds-sarahstogner's `dsss_seed_cta_defaults()` — seeds only options that have never been set).

A **media kit** (PDF/ZIP attachment, `dsp_hp_media_kit_id`) can be uploaded in the same settings panel; when set, a "Download Media Kit" link renders at the bottom of the CTA section (`.get-involved__media-kit`, label filterable via `dsp_media_kit_label`). The link locally remaps `--ctx-link` to the section's text color, so it stays readable on accent, dark, or light CTA backgrounds.

### Section Order & Backgrounds

The Section Order table in Homepage Settings also has a **Background** dropdown per section (Default / Light / Surface / Dark) — see the Surface Context System section above. Hero is excluded (it has its own image/overlay system).

Sections render inside a `.homepage-sections` flex-column wrapper, and each section gets a CSS `order` value editable in **Appearance → Homepage Settings → Section Order & Backgrounds**. Values are spaced by 10 (hero 0, bio 10, issues 20, articles 30, news 40, endorsements 50, get-involved 60, connect 70) so a section can be slotted between two others without renumbering. Defaults live in `dsp_hp_section_order_defaults()` (`includes/homepage-settings.php`); values are stored as `dsp_hp_order_{section}` options (hyphens become underscores, e.g. `dsp_hp_order_get_involved`). The generated CSS is filterable via `dsp_homepage_order_css`, and site plugins can still override with their own `order` rules.

`front-page.php` also assembles the homepage from an ordered list of section slugs, filterable by the site plugin:

```php
// Default section list (visibility toggled in Homepage Settings)
$sections = apply_filters( 'dsp_homepage_sections', [
    'hero',
    'bio',
    'issues',
    'articles',
    'news',
    'endorsements',
    'get-involved',
    'connect',
] );
```

**To reorder or remove sections in site plugin:**
```php
add_filter( 'dsp_homepage_sections', function( $sections ) {
    // Remove endorsements, move news before issues
    $sections = array_diff( $sections, ['endorsements'] );
    // reorder as needed
    return $sections;
} );
```

**To add a custom section:**
```php
add_filter( 'dsp_homepage_sections', function( $sections ) {
    $sections[] = 'donate-cta'; // custom slug
    return $sections;
} );

// Then handle it:
add_action( 'dsp_homepage_section_donate-cta', function() {
    get_template_part( 'template-parts/section-donate-cta' );
} );
```

### Section Label Filters

Each section exposes filters for its title and subtitle:

| Filter | Default |
|---|---|
| `dsp_bio_section_label` | "About the Candidate" |
| `dsp_bio_read_more_text` | "Read More" |
| `dsp_bio_page_slug` | "about" |
| `dsp_issues_title` | "Issues & Positions" |
| `dsp_issues_subtitle` | "Where I Stand" |
| `dsp_news_title` | "In the News" |
| `dsp_news_count` | 4 |
| `dsp_news_view_all` | "View All Articles" |
| `dsp_endorsements_title` | "Endorsements" |
| `dsp_endorsements_subtitle` | "Trusted Community Leaders" |
| `dsp_cta_title` | "Get Involved" |
| `dsp_cta_text` | (default body copy) |
| `dsp_cta_actions` | Array of volunteer/donate/signup buttons |
| `dsp_connect_heading` | "Get in Touch" |
| `dsp_connect_text` | (Homepage Settings value) |
| `dsp_connect_email` | (Homepage Settings value) |
| `dsp_show_single_featured_image` | Theme Settings toggle (bool, post ID passed) |
| `dsp_homepage_order_css` | Generated section-order CSS string |

### Bio Image Options

**Appearance → Homepage Settings → Bio** controls the bio section image:

- **Image display** (`dsp_hp_bio_image_mode`): `featured` (beside text, default), `none` (text only, centered column), or `background` (image fills the section with a darkening gradient; text overlays in white, right-aligned on desktop).
- **Image override** (`dsp_hp_bio_image_id`): media-library picker; overrides the Bio page's Featured Image in either display mode. `0` = use featured image.

### Endorsements Layout

**Appearance → Homepage Settings → Endorsements → Layout** (`dsp_hp_endorsements_layout`): `grid` (default) or `carousel`. Carousel crossfades one card at a time — auto-advances every 7s (`data-interval` on `.endorsements-grid`), pauses on hover/focus, dot navigation, respects `prefers-reduced-motion`. JS (`assets/js/endorsements.js`) is enqueued only on the front page when carousel is selected. No-JS fallback: styles are gated behind the `.is-ready` class, so cards degrade to a stacked list.

### Contact / Connect Section

`template-parts/section-connect.php` — heading, short paragraph, prominent mailto link (email obfuscated via `antispambot()`), and social icons pulled from the `[ds_socials]` platform registry. Content configured in **Homepage Settings → Contact / Connect** (`dsp_hp_connect_heading`, `dsp_hp_connect_text`, `dsp_hp_connect_email`, toggle `dsp_hp_show_connect`). Section skips itself if all three are empty. Contact form option planned; starting with the mailto approach.

### Single Post Featured Image

**Appearance → Theme Settings → Single Post Featured Image** (`dsp_single_featured_image`, default on) controls whether the featured image renders at the top of single posts sitewide. Filterable per-post via `dsp_show_single_featured_image`. Image is now styled: full-width, 480px max-height crop, rounded corners, shadow.

### Hero

The hero layout is set via **Page Attributes → Template** on the page designated as the static front page.

| Template | Layout |
|---|---|
| Page Hero: Full Bleed | Full viewport, content over image |
| Page Hero: Split Left | Image left column, content right |
| Page Hero: Split Right | Image right column, content left |

Hero content (headline, tagline, CTA) is set in the **Hero Section** meta box in the page editor. Hero image = page Featured Image.

---

## Logo System

### Always-White Header Logo

Victoria applies `filter: brightness(0) invert(1)` to `.site-logo` universally, since political sites typically have a dark/colored header. Override in site plugin if needed:

```css
.site-logo { filter: none; } /* restore original colors */
```

### Logo Variants (site-identity.php)

The theme supports multiple logo variants via Appearance → Site Identity or files in `assets/images/`:

- `logo.svg` / `logo.png` — primary logo
- `logo-white.svg` — explicit white variant (used on dark backgrounds)
- `logo-mono.svg` — monochrome variant

`dsp_display_logo()` — outputs the logo or falls back to site title text.  
`dsp_get_logo_url( $variant )` — returns URL for a specific variant.

---

## Search Overlay

Fully implemented. No configuration needed.

- **Trigger:** `.header-search-toggle` button in `header.php`
- **Markup:** `#search-overlay` in `header.php`
- **JS:** `assets/js/header.js`
- **CSS:** `assets/css/header.css` (search overlay section)
- **Behavior:** Opens on toggle click, closes on close button, backdrop click, or Escape key. Body scroll locked while open.

---

## Site Plugin Pattern (`ds-[sitename]`)

### Minimum file structure
```
ds-sitename/
├── ds-sitename.php        # Plugin entry — enqueues assets, palette override
├── assets/
│   ├── css/
│   │   ├── site.css       # Compiled — loaded after Victoria
│   │   └── site.less      # Source
│   ├── js/site.js
│   └── images/            # Campaign logo, candidate photos
├── includes/              # Site-specific includes (custom meta, integrations)
└── templates/             # Any template overrides for this site
```

### Enqueue order

Site plugins MUST hook `wp_enqueue_scripts` at **priority 20** (theme uses default 10). Plugins load before themes, so at a tied priority the plugin's callback runs first and `site.css` prints before conditionally-loaded theme styles (`homepage.css`, `footer.css`), letting the theme win the cascade. Priority 20 guarantees `site.css` always prints last. The `['dsp-style', 'dsp-header-style']` dependency array is kept as a load guard, but the priority is what actually fixes ordering against all Victoria stylesheets.

```php
add_action('wp_enqueue_scripts', 'dsxx_enqueue_assets', 20);
```

---

## Campaign Mode (Planned)

Three modes planned. `active` is the default (no option stored = active campaign).

| Mode | Option Value | Use Case |
|---|---|---|
| Active Campaign | *(default / not set)* | Running for office |
| Staying Relevant | `staying-relevant` | Lost, maintaining presence for future run |
| Officeholder | `officeholder` | Won, constituent services focus |

Mode is read via `get_option('dsp_campaign_mode')`. Homepage sections, nav CTAs, and section labels respond to the current mode. See TO-DO for full implementation plan.

---

## Key Functions Reference

| Function | File | Description |
|---|---|---|
| `dsp_theme_setup()` | functions.php | Theme support, menus, text domain |
| `dsp_enqueue_assets()` | functions.php | Enqueue styles and scripts |
| `dsp_display_logo()` | functions.php | Output logo or site title |
| `dsp_get_custom_logo()` | functions.php | Return logo img tag |
| `dsp_get_hero_meta($post_id)` | includes/homepage-meta.php | Get hero fields for a page |
| `dsp_get_endorsements($featured, $limit)` | includes/cpts/cpt-endorsements.php | Query endorsements |
| `dsp_get_positions($homepage_only)` | includes/cpts/cpt-positions.php | Query positions |
| `victoria_register_footer_widgets()` | functions.php | Register footer sidebars |

---

*Last updated: April 2026*
