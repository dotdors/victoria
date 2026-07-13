# DS Site — Sarah Stogner

Site-specific plugin for **SarahStogner.com**. Pairs with the **Dandysite Victoria** base theme (requires the Surface Context system, Victoria 1.0+).

## Site mode

**Officeholder** — Sarah won her election (143rd District Attorney). This site is not a campaign site: it archives her writing and commentary and positions her for speaking engagements, media appearances, and paid work.

## Design direction — "Ink & cream editorial"

Legal-journal / longform-magazine feel. Deliberately the opposite of ds-hawkfortexas's primary-color campaign styling.

| Role | Color | Hex |
|---|---|---|
| Primary (headlines, dark sections, footer) | Ink Slate | `#22303C` |
| Accent (links, buttons, rules) | Oxblood | `#9E3E33` |
| Metadata (dates, sources, bylines) | Saddle Brown | `#8A6A4F` |
| Accent on dark surfaces | Dusty Terracotta | `#E2A69B` |
| Surface | Cream | `#F7F4EE` |
| Background | Warm White | `#FFFDF9` |

**Typography:** Fraunces (headings) + Source Serif 4 (body). Serif throughout — no sans except where inherited from admin.

**Geometry:** square corners (`--border-radius: 2px`, most components hard 0), hairline 1px borders, **no** box shadows, no hover lifts. Whitespace and thin rules do the separating.

The dusty terracotta on-dark accent is the oxblood family lightened for contrast on ink — set via Victoria's `--color-on-dark-*` token set so dark sections theme consistently everywhere (the "cornflower lesson" from Hawk, solved up front).

## What the plugin does

- Enqueues Google Fonts (Fraunces + Source Serif 4) and `assets/css/site.css` after all Victoria styles (priority 20).
- Overrides the block-editor color palette with Sarah's palette.
- Repurposes campaign sections via Victoria filters:
  - Articles → **Writing & Commentary**
  - News → **In the Media**
  - Endorsements → **Praise** (open pull-quote styling, no cards)
  - Get Involved → **Book Sarah** (Request Speaking / Media Inquiries buttons, both pointing at `#connect` until dedicated pages exist)
- Includes the ds-hawkfortexas pre-launch date-hiding filter, **commented out** — enable and set the cutoff if migrated content has meaningless publish dates.

## Setup notes

- In **Appearance → Homepage Settings**, the new per-section **Background** dropdowns pair well with this palette: alternate Light / Surface, and try setting Bio or Connect to **Dark** for an ink band.
- See `NEW-SITE-VARIABLES.md` in the repo root for the full variable checklist (this plugin sets all of them, including the on-dark set).
