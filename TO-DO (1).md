# Dandysite Victoria — TO-DO

## ds-hawkfortexas

- [ ] **Footer image is hardcoded in the hawk plugin** — needs to be made configurable (theme setting or site plugin option) eventually.

## Victoria theme

- [ ] Contact/Connect section: optional contact form to replace the mailto link (currently email-only by design).
- [ ] Campaign mode states (active / staying-relevant / officeholder) — define and implement.
- [ ] **Speaking/booking CTA section** — generalized get-involved variant (headline, blurb, buttons, optional topics list) for officeholder + staying-relevant modes. Content (heading/text/buttons) is now editable in Homepage Settings; remaining idea is an optional topics list + dedicated booking form.
- [ ] **"As seen in" press logo bar** — logo strip with optional links (both Hawk and Sarah have real media coverage). Settings page, or Endorsements CPT with a "press" type.
- [ ] Migrate Hawk's dark bio-section CSS to the Surface Context system (set bio → Dark in Homepage Settings + define `--color-on-dark-*` in ds-hawkfortexas; delete the hand-written `.section-bio` overrides). Not urgent — current CSS works.

## ds-sarahstogner

- [ ] Review with real content; expect styling tweaks (endorsements/"Praise" and hero especially).
- [ ] Point Book Sarah CTA buttons at real pages (edit in Homepage Settings → Get Involved / CTA; both currently seed to `#connect`).
- [ ] Decide whether to enable the pre-launch date-hiding filter (commented out in plugin) and set cutoff.

## Done recently

- [x] **Surface Context system** — dark/light/surface per-section backgrounds; `--color-on-dark-*` token set + `--ctx-*` consumption pattern; Background dropdown in Homepage Settings (the "cornflower lesson" fix)
- [x] ds-sarahstogner plugin scaffolded — ink & cream editorial direction (Besley + Source Serif 4, oxblood accent)
- [x] CTA section content (heading/text/buttons) editable in Homepage Settings; site plugins seed defaults on activation
- [x] **Media kit download** — upload in Homepage Settings → Get Involved / CTA; link renders at the bottom of the CTA section
- [x] Fixed `.btn--white` / `.btn--outline-white` never applying (a.btn base rule outranked the variants — affects Hawk's CTA buttons too, for the better)

- [x] Twitter/X added to social platform registry
- [x] Bio section image options (beside text / none / background + override picker)
- [x] Single post featured image toggle + improved styling
- [x] Homepage section ordering via Homepage Settings (CSS order)
- [x] Alternating section backgrounds computed in visual order
- [x] Contact/Connect homepage section
- [x] Endorsements carousel layout option
- [x] Site plugin enqueue priority fix (site.css now loads after all theme CSS)
- [x] Favicon tags now conditional on files existing in site root
