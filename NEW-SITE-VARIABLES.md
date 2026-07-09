# New Site Setup — Variable Checklist

When creating a new `ds-[sitename]` plugin, set all of these in your `:root {}` block
in `assets/css/site.css`. Every variable has a Victoria default — this list tells you
which ones you must consciously choose and which you can leave alone.

---

## Must Set — Brand Colors

These have opinionated Victoria defaults that will look wrong on any real site.

```css
:root {
    /* Primary brand color — headers, headings, issues section bg */
    --color-primary:      #C8102E;

    /* Secondary brand color — bio section bg, dark accents */
    --color-secondary:    #1d3557;

    /* Accent — buttons, links, category labels, card borders */
    --color-accent:       #C8102E;

    /* Button defaults (usually match accent) */
    --color-button-bg:    #C8102E;
    --color-button-text:  #ffffff;

    /* Surface — card/section backgrounds (default: Honeydew #f1faee) */
    --color-surface:      #f5f5f5;
}
```

---

## Must Set — Header

Victoria's header defaults to near-black (`#1a1a1a`). Almost every political site
will want a colored header.

```css
:root {
    /* Solid header background (initial state + scroll-revealed state) */
    --header-solid-bg:          #1d3557;

    /* Text/icon color on solid header */
    --header-solid-color:       #ffffff;

    /* Border beneath solid header (transparent = none) */
    --header-solid-border:      transparent;

    /* Revealed header background (after scroll-up) — usually same as solid */
    --header-revealed-solid-bg:    #1d3557;
    --header-revealed-solid-color: #ffffff;

    /* Mobile menu panel */
    --header-mobile-menu-bg:    #1d3557;
    --header-mobile-menu-color: #ffffff;
}
```

---

## Usually Set — Typography

Victoria defaults to system sans-serif + Georgia. Set these if you're loading a
web font for the site.

```css
:root {
    --font-primary:   'Open Sans', system-ui, sans-serif;
    --font-secondary: Georgia, serif;
}
```

Don't forget to enqueue the font in the plugin's `wp_enqueue_scripts` hook if using
a web font.

---

## Usually Set — Footer

Footer inherits brand colors by default. Only override if the footer needs a
distinct treatment (e.g. always dark regardless of brand color).

```css
:root {
    /* Light footer (default) — inherits from color vars, usually fine */
    /* --footer-bg, --footer-text, --footer-link etc. auto-derive from brand */

    /* Dark footer — only set these if using footer-dark body class */
    /* --footer-bg:         var(--color-secondary); */
    /* --footer-link-hover: var(--color-accent);    */
}
```

---

## Optional — Header Sizing

Rarely needed. Change only if the site's logo or nav requires different proportions.

```css
:root {
    --header-height:             70px;   /* Victoria default */
    --header-height-mobile:      60px;
    --header-logo-height:        44px;
    --header-logo-height-mobile: 36px;
    --header-nav-gap:            2rem;
}
```

---

## Optional — Layout

Victoria's container max-width is 1200px. Adjust if the design calls for it.

```css
:root {
    --container-max-width: 1200px;   /* Victoria default */
    --section-padding:     4rem 0;   /* Victoria default */
}
```

---

## Leave Alone — Structural

These derive correctly from the brand color vars and rarely need direct override:

- `--color-text`, `--color-text-light`, `--color-background` — neutral, site-agnostic
- `--color-border`, `--color-surface-alt` — derived from palette
- `--border-radius`, `--box-shadow`, `--transition` — structural, not brand
- `--spacing-*` — spacing scale, consistent across sites
- `--font-size-*`, `--line-height-*` — type scale, consistent across sites

---

## Quick-Start `:root` Block

Minimum viable override for a new political candidate site:

```css
:root {
    /* Brand */
    --color-primary:      #YOUR_PRIMARY;
    --color-secondary:    #YOUR_DARK;
    --color-accent:       #YOUR_PRIMARY;
    --color-surface:      #f5f5f5;
    --color-button-bg:    #YOUR_PRIMARY;
    --color-button-text:  #ffffff;

    /* Header */
    --header-solid-bg:             #YOUR_DARK;
    --header-solid-color:          #ffffff;
    --header-solid-border:         transparent;
    --header-revealed-solid-bg:    #YOUR_DARK;
    --header-revealed-solid-color: #ffffff;
    --header-mobile-menu-bg:       #YOUR_DARK;
    --header-mobile-menu-color:    #ffffff;

    /* Typography (if using web font) */
    --font-primary:   'Your Font', system-ui, sans-serif;
}
```

---

*Last updated: April 2026*
