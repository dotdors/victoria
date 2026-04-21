# DS Site Plugin: Hawk for Texas

Site-specific plugin for **hawkfortexas.com** — built on the Dandysite Victoria base theme.

## What lives here

| File/Folder | Purpose |
|---|---|
| `ds-hawkfortexas.php` | Plugin entry point, asset loading, color palette override |
| `includes/cpt-endorsements.php` | Endorsements CPT + meta boxes |
| `includes/cpt-positions.php` | Issues/Positions CPT + meta boxes |
| `assets/css/site.css` | Campaign brand overrides (loads after Victoria) |
| `assets/js/site.js` | Site-specific JS (optional) |
| `assets/images/` | Campaign logo, candidate photos, etc. |
| `templates/` | Any custom template parts specific to this site |

## Custom Post Types

### Endorsements (`dshft_endorsement`)
- **Title**: Endorser's name
- **Featured Image**: Endorser's photo
- **Meta: Title/Organization**: e.g. "Mayor, City of Dallas"
- **Meta: Pull Quote**: Short quote for display
- **Meta: Featured**: Flag to show prominently on homepage

Use `dshft_get_endorsements($featured_only, $limit)` in templates.

### Positions (`dshft_position`)
- **Title**: Issue name (e.g. "Education")
- **Excerpt / Meta Summary**: Short homepage blurb
- **Editor**: Full position detail (linked page)
- **Meta: Dashicon**: Optional icon for visual display
- **Meta: Show on Homepage**: Flag for homepage issues section

Use `dshft_get_positions($homepage_only)` in templates.

## Brand colors

Update CSS custom property overrides in `assets/css/site.css`.
The editor color palette is registered in `ds-hawkfortexas.php` — update the hex values there too.

## Deployment

Install alongside Dandysite Victoria. No child theme needed.
