# Design

<!-- impeccable:design-schema 1 -->

## Overview

The public site has two designed worlds.

1. **Drafting sheet** (all other public pages): monochrome paper world, graph-paper backdrop, hard-offset `card-3d` depth, Instrument Serif display + JetBrains Mono data. Documented by the existing `resources/css/app.css` systems.
2. **Home stage** (home only, `/`): the reference composition — status strip, stencil name, centered cut-out portrait, HUD spec table, credential card, data deck — drawn with world 1's own tokens, so it reads as the drafting sheet in light and dark. The earlier amber "night stage" was removed at the owner's request (2026-10-03); no separate home palette exists.

## Home stage tokens

All stage tokens are aliases of the site theme, defined on `.stage` in `resources/js/pages/frontend/Home.vue`:

| Token | Resolves to | Use |
| --- | --- | --- |
| `--rx-ink` | `var(--color-foreground)` | Primary text and values |
| `--rx-ink-dim` | `var(--color-muted-foreground)` | Body copy, roles, secondary rows |
| `--rx-ink-soft` | muted-foreground at 58% | Micro-labels, hints, arrows |
| `--rx-rule` / `--rx-rule-soft` | `var(--color-border)` / 65% | Panel borders, row separators |
| `--rx-rule-strong` | foreground at 28% | Icon-button outlines |
| `--rx-solid` / `--rx-solid-hover` | `var(--color-foreground)` / 85% | Primary action fill and hover |
| `--rx-on-solid` | `var(--color-background)` | Text on the solid fill |
| `--rx-ease` | `cubic-bezier(0.16, 1, 0.3, 1)` | All stage motion |

There is no field gradient and no vignette. The page background and graph-paper backdrop come from the layout, exactly as on every other route; the only stage paint layers are a 5% foreground wash (`.stage-field`) and the pointer bloom, both barely visible in light and dark. The site accent (`--accent-ink`, the red pencil) marks the name slashes and link hover states.

## Type

| Face | Role |
| --- | --- |
| Michroma (400) | Stencil name, stat numerals, panel titles, card name. Uppercase, tight leading. |
| Chakra Petch (300/400/600) | Body copy, roles, spec values, link rows, CTA. |
| JetBrains Mono (400) | Status strip, labels, telemetry values, tickers. Tabular numerals for time. |

Loaded in `resources/views/app.blade.php` alongside the existing Instrument set (which stays for the drafting-sheet pages).

## Layout

- **Hero** (`min-height: 100svh`, minus the dock on ≥768px): 12-track grid, min-height `calc(100svh - 12.75rem)` so the portrait stays bottom-anchored without clipping. Name, copy and controls occupy tracks 1–6; the spec table and credential card form a **right rail** (`.stage-side`, tracks 9–13, top-aligned, stacked with a 1.25rem gap). Portrait (`.stage-figure`) is a centered absolute layer, width `min(52vw, 42.5rem, calc(100svh - 15rem))`, bottom-anchored, painted under the text columns (text layers `z-index: 2`, portrait `z-index: 1`). The dev switch row is centered under the portrait on desktop; below 1024px the rail collapses to the end of the single-column flow (name → portrait → copy → controls → specs → card).
- **Under 1024px**: single column, order = strip, name, portrait, copy, controls, specs, card. `.stage-intro` becomes `display: contents` so the portrait can sit between the name and the copy.
- **Deck** below the fold: four stat cells on hairlines (the only place the counts, the stack total and the work-entry totals appear), then Elsewhere links and the Daily drivers chip grid, closed by a mono footer strip. Facts appear exactly once: the spec table carries stack summary/base/time/status, the deck carries the counts.
- Radii: panels 14px, key strips 12px; pills only for controls and chips.

## Components

- **Status strip**: hairline bar on a translucent card surface with backdrop blur; pulse dot for availability.
- **Icon buttons**: 44px circles, 1px `--rx-rule-strong` outline, solid ink fill on hover, background-colored icon.
- **Spec table**: translucent card panel with blur; mono uppercase labels left, values right; live rows carry a 6px emerald dot and mono tabular value.
- **Credential actions row**: borderless, single row under the specs panel — the solid ink `See projects` pill and the underlined mono `Get in touch` link pushed right. No monogram, avatar or backend image; identity and role live in the hero headline only. No border, background or blur.
- **Portrait reveal**: the primary portrait (suit, `me-man-cutout`) is always visible; the dev cutout (`man-aligned.png`, head-aligned with the base portrait) is revealed through a soft radial mask — radius `clamp(64px, 8vw, 120px)`, 78% solid then a soft feather, no outline ring — that glides toward the pointer (position lerps at 0.22 per frame, so it trails and settles instead of snapping). rAF-driven `pointermove` on desktop, passive `touchmove` on touch, collapsing only on a real pointer exit (a spurious `pointerleave` while the pointer is inside the frame is ignored). Mask, position and radius are plain CSS custom properties (`--spot-x/--spot-y/--spot-r`) set imperatively on `.figure-frame`, so no render loop is involved.
- **Dev switch**: clicking the portrait or the `Show dev / Switch to dev` control toggles the dev state — the dev cutout at full opacity with the same bottom mask as the base portrait, plus the accent rim glow, so it reads as the same body in different clothes with no square edges, vignette or scrim needed. The control is an icon circle plus a two-line mono label, `aria-pressed`, flips to “Dev on”. On desktop the control row is centered under the portrait and the redundant name tag is hidden; on smaller screens the name tag returns and the row sits left. Touch drags never trigger the click toggle.- **Driver chips**: static wrapped pills; never auto-scroll (motion is reserved for the single load moment).
- **Focus rings** use `--accent-ink` at 2px with 3px offset; text selection follows the browser default in the theme's colors.
- **Dock and backdrop** are the shared site components, untouched; home no longer overrides them.

## Motion

One authored moment: a staged power-on. Every element with `.reveal` starts at `opacity: 0`, `translateY(16px)`, `blur(8px)` and resolves once via `stage-in` (900ms, `--rx-ease`, staggered by `--d`). The portrait is excluded from transform conflicts by using the `translate` property for its centering. A slow foreground-tinted bloom (900ms) follows the pointer at low opacity. The portrait spotlight tracks the pointer directly with no transition; the aura trace is a single sweep on switch. `prefers-reduced-motion` disables reveals, bloom easing, and all other transitions.

## Assets and provenance

- `public/me-1-cutout.png` — derived from `public/me-1.png` (501×498): corner floodfill (fuzz 12%), 2× Lanczos, stripped. Provenance is stamped into the PNG metadata (`comment` field).
- `public/me-man.png` (source, 6400×6363 high-res suit photo) → `public/me-man-cutout.png` (served, 1002×996, ~404 KB): `magick public/me-man.png -filter Lanczos -resize 1002x -strip -background none -gravity center -extent 1002x996`. Its subject boxes match the old `me-1-cutout` exactly (head 204×232+399+28 vs 205×232+399+28; full 527×968+197+28), so the `man-aligned.png` reveal still locks on. No user-upload image is used anywhere on this page — portraits are these fixed assets.
- `public/man.png` (source, 3200×3150) → `public/man-aligned.png` (served, 1002×996, ~595 KB): 35.04% Lanczos, composited at −66/+5 on the portrait canvas so the head lands over the base portrait's head (aligned head bands: 215×232+397+28 vs 205×232+399+28). Regenerate with `magick public/man.png -filter Lanczos -resize 35.04% /tmp/man-scaled.png && magick -size 1002x996 canvas:none /tmp/man-scaled.png -geometry -66+5 -composite -strip public/man-aligned.png`.
- `public/dev.png`, `public/dev-aligned.png`, `public/me-2-cutout.png`, `public/me-dev.png`, `public/me-dev.webp` and `public/me-1-cutout.png` remain in the repo for reference but are no longer referenced by the home page.
- Hero portrait stack: `me-1-cutout.png` (suit) sits on top of `me-2-cutout.png` (hoodie). The two crossfade in place — the top frame lifts and fades out while the base fades in — on hover for fine pointers, or via the mono `Switch` button in the figure ticker (`aria-pressed`, tap-friendly on touch, keyboard accessible). Both frames carry the same bottom mask (84% → transparent) and a soft neutral drop shadow; the spotlight layer sits above the base portrait and is masked, so the two silhouettes never ghost. The hover area shows a pointer cursor and clicking it toggles the aura; the `Show aura / Switch portrait` control covers touch and keyboard.

## Accessibility

- Body copy ≥4.5:1 on all surfaces via the theme tokens; large display ≥3:1. The light and dark themes are both designed states (verified 1440 light and dark).
- Focus-visible rings on all links/buttons; icon-only controls carry `aria-label` and `title`.
- Decorative layers are `aria-hidden`; spec table is a `dl`; link lists are real lists; the portrait has a descriptive alt.
- Portrait and controls stay usable at 360–390px widths; no horizontal scroll at any tested width (1280/1440/390).

## Known drift

- `app.blade.php` still loads Instrument Serif for the drafting-sheet pages; the detector flags it as an overused face, but that world is out of scope for the home rebuild.
