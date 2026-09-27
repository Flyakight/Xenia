# Decision Guide — Tannic Design System Treatment
**Date:** 2026-03-02
**File:** `tannic-website-decision-guide.html` (standalone → eventually a WP page template)
**Status:** Approved, ready for implementation

---

## Goal
Transform the existing lead-magnet HTML guide into a flagship Tannic Studios artefact. Full brand alignment in color, typography, surface, and interaction — with a tactile "fill up / ooey-gooey" physical quality to every interactive element.

---

## Deployment Context
- Currently a standalone HTML file
- Will eventually live inside the WordPress theme as a page template
- Fonts must be self-included via Google Fonts `@import` for now
- No external JS dependencies — keep vanilla

---

## 1. Tokens & Color System

Replace all existing CSS custom properties with Tannic's system:

```css
--col-dark:   #162d27;
--col-accent: #d97d5d;
--col-light:  #f4f1ea;
--col-gold:   #8b802e;
--col-white:  #ffffff;

/* RGB variants */
--col-dark-rgb:   22, 45, 39;
--col-accent-rgb: 217, 125, 93;
--col-light-rgb:  244, 241, 234;
--col-gold-rgb:   139, 128, 46;

/* Glass */
--glass-bg:     rgba(var(--col-dark-rgb), 0.75);
--glass-border: rgba(var(--col-light-rgb), 0.15);
--glass-blur:   12px;

/* Grain texture (same SVG as theme) */
--texture-paper: url("data:image/svg+xml,...");

/* Transitions */
--ease-smooth:  cubic-bezier(0.25, 1, 0.5, 1);
--ease-spring:  cubic-bezier(0.34, 1.56, 0.64, 1);
--ease-bounce:  cubic-bezier(0.175, 0.885, 0.32, 1.275);
```

### Role Colors (on-palette)
| Role | Foreground | Background tint |
|------|-----------|-----------------|
| Owner | `#8b802e` (gold) | `rgba(139,128,46,0.12)` |
| Strategist | `#d97d5d` (terracotta) | `rgba(217,125,93,0.12)` |
| Designer | `#4a7a6d` (sage) | `rgba(74,122,109,0.12)` |
| Developer | `#162d27` (forest) | `rgba(22,45,39,0.10)` + light text |

---

## 2. Typography

| Use | Font | Style |
|-----|------|-------|
| `h1`, `h2`, `h3` | Fraunces | 400, optical sizing |
| Body prose | Inter | 400/600 |
| Labels, eyebrows, term names, filter buttons | Fira Code | uppercase, letter-spacing |
| Section "Part N" watermark numerals | Mrs Saint Delafield | large, low opacity, decorative |

Google Fonts import: Fraunces (optical), Inter (400,600), Fira Code (400,600), Mrs Saint Delafield (400).

---

## 3. Surface & Structure

- **Body bg:** `--col-light` `#f4f1ea`
- **Hero bg:** `--col-dark` + SVG grain texture (soft-light blend, opacity 0.12) — matches theme hero
- **Hero h1:** Fraunces, cream, `em` in `--col-accent` italic
- **Hero eyebrow:** Fira Code, uppercase, `--col-accent`
- **Topbar:** Fira Code lettering, `--col-dark` bg, `--col-accent` link
- **Section dividers:** `rgba(22,45,39,0.12)` hairlines on cream
- **"Why it matters" callouts:** `--col-dark` bg, cream text, `--col-accent` left border — inverted card
- **Companion badge:** glass-border left-side accent bar
- **Pull quote:** Fraunces italic, large, `--col-accent` left border
- **Matrix table:** headers in `--col-dark`, pill colors updated to palette

---

## 4. Glass Morphism — Decision Card Backs

Selected (flipped) card back face:
```css
background: var(--glass-bg);           /* rgba(22,45,39,0.75) */
backdrop-filter: blur(var(--glass-blur));
border: 1px solid var(--glass-border); /* rgba(244,241,234,0.15) */
```
- Text: `--col-light`
- Arrow "→": `--col-accent`
- Strong label: `--col-accent`, Fira Code uppercase

---

## 5. The Fill-Up Interaction System

Every interactive surface gets a physical, liquid quality. All animations respect `prefers-reduced-motion`.

### Progress Bar Segments
- Each `dt-step` gets a `::after` pseudo that `scaleX`s from 0→1 on the `--ease-bounce` curve
- Done: floods `--col-accent`; Active: `--col-dark` with subtle opacity pulse (`@keyframes breathe`)

### Card Front Hover (Pour Effect)
```css
.dt-card-front::before {
  content: '';
  position: absolute; inset: 0;
  background: rgba(var(--col-dark-rgb), 0.05);
  transform: translateY(100%);
  transition: transform 0.4s var(--ease-smooth);
}
.dt-card:hover .dt-card-front::before { transform: translateY(0); }
```
Border transitions to `--col-accent`. Label color shifts to `--col-dark`.

### Card Flip (Glass Back)
- Flip itself: `0.55s cubic-bezier(0.4,0,0.2,1)` (existing, keep)
- Dismissed sibling: `opacity: 0.15` + `filter: blur(1px)` + `pointer-events: none`
- Back content staggered fade-in: strong label at 300ms delay, body text at 380ms

### Accordion Open (Glossary)
- `grid-template-rows: 0fr → 1fr` easing: `--ease-spring` (cubic-bezier(0.34, 1.56, 0.64, 1))
- Open item gets a `::before` that `scaleY`s from 0→1 on the left edge in `--col-accent` (transform-origin: top)
- Trigger background: `rgba(var(--col-dark-rgb), 0.03)` floods in from left via `::after scaleX`

### Filter Buttons
- Active: `::before` scaleX from 0→1 (left→right fill), background becomes `--col-dark`, text white
- Ink-into-paper metaphor

### Connector Lines (Decision Tree)
- When lit: a `::after` pseudo drops from top to bottom over 400ms — a drip traveling down the wire

### Recommendation Panel
- `translateY(24px) → 0` + `opacity: 0→1` over 500ms `--ease-smooth`
- Each `dt-rec-insights li` staggers in at 80ms intervals (CSS animation delay)
- Panel: grain texture + `--col-accent` left border (4px)

### CTA Button
- Border + text initially
- Hover: `::before` floods in from bottom (`translateY(100%) → 0`), text color flips
- Active: slight scale-down `0.98`

### Search Field
- Focus: left border `--col-gold` blooms, subtle bg shift to near-white
- Transition: `border-color 0.25s --ease-smooth`

---

## 6. Lead Magnet Extras

### Sticky Progress Strip
- Appears after Q1 is answered (`position: fixed; bottom: 0`)
- Fira Code text: "Build Profile forming… X / 5"
- Slides up from bottom (`translateY(100%) → 0`) when first answer given
- Updates count live; at 5/5 reads "Your profile is ready ↓" with a single pulse animation
- Dark bg `--col-dark`, accent text, hairline top border

### Copy Result Button
- Inside recommendation panel
- Copies plain-text verdict + bullet insights to clipboard (no server call)
- Label: "Copy my result" → "Copied ✓" for 2s then reverts
- Fira Code, small, accent border treatment

---

## 7. Responsive
- Existing breakpoints preserved (`600px`, `640px`)
- Sticky strip hides on `prefers-reduced-motion`
- Glass blur degrades gracefully where unsupported

---

## Out of Scope
- No new HTML structure changes beyond adding `::before`/`::after` hooks where needed
- No external JS libraries
- Font registration in WordPress (`functions.php`) deferred — handled when template is built
