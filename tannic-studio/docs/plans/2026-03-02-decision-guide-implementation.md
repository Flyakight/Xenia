# Decision Guide — Tannic Design System Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Reskin `tannic-website-decision-guide.html` to the full Tannic Studio design system with tactile fill-up/gooey interactions.

**Architecture:** Single standalone HTML file. All changes are in the `<style>` block and `<script>` block. No external dependencies added. Google Fonts loaded via `<link>` in `<head>`. File will eventually become a WordPress page template.

**Tech Stack:** Vanilla CSS (custom properties, pseudo-elements, keyframes, grid), Vanilla JS (classList, clipboard API), Google Fonts.

**Source file:**
```
/Users/katekight/Library/Application Support/Claude/local-agent-mode-sessions/ff333033-6d0c-43fd-96fb-20bebe3ae888/2157662f-bccc-4315-9c4d-ff4bf9ba2d90/local_73d8069e-ec04-42c2-99b6-f5a7154449a4/outputs/tannic-website-decision-guide.html
```
Referred to below as **`guide.html`**.

**Design doc:** `docs/plans/2026-03-02-decision-guide-tannic-design.md`

---

## Task 1: Google Fonts + Token Replacement

**Files:** Modify `guide.html`

**Step 1: Add Google Fonts `<link>` tags**

Replace the existing `<title>` line's immediate surroundings — add these four `<link>` tags directly after `<meta name="viewport" .../>` and before the existing `<title>`:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;1,9..144,300;1,9..144,400&family=Inter:wght@400;600&family=Fira+Code:wght@400;600&family=Mrs+Saint+Delafield&display=swap" rel="stylesheet">
```

**Step 2: Replace entire `:root {}` block**

Find the existing `:root { ... }` block (lines 29–48 in the original) and replace it entirely:

```css
:root {
  /* Tannic palette */
  --col-dark:       #162d27;
  --col-accent:     #d97d5d;
  --col-light:      #f4f1ea;
  --col-gold:       #8b802e;
  --col-white:      #ffffff;

  /* RGB variants */
  --col-dark-rgb:   22, 45, 39;
  --col-accent-rgb: 217, 125, 93;
  --col-light-rgb:  244, 241, 234;
  --col-gold-rgb:   139, 128, 46;

  /* Glass */
  --glass-bg:     rgba(var(--col-dark-rgb), 0.78);
  --glass-border: rgba(var(--col-light-rgb), 0.15);
  --glass-blur:   12px;

  /* Grain texture */
  --texture-paper: url("data:image/svg+xml,%3Csvg viewBox='0 0 220 220' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='paperNoise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.55' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23paperNoise)' opacity='0.5'/%3E%3C/svg%3E");

  /* Role colours — on Tannic palette */
  --role-owner:          #8b802e;  --role-owner-bg:      rgba(139,128,46,0.12);
  --role-strategist:     #d97d5d;  --role-strategist-bg: rgba(217,125,93,0.12);
  --role-designer:       #4a7a6d;  --role-designer-bg:   rgba(74,122,109,0.12);
  --role-developer:      #162d27;  --role-developer-bg:  rgba(22,45,39,0.10);

  /* Typography */
  --font-body:    'Inter', -apple-system, sans-serif;
  --font-heading: 'Fraunces', Georgia, serif;
  --font-script:  'Mrs Saint Delafield', cursive;
  --font-mono:    'Fira Code', 'Consolas', monospace;

  /* Transitions */
  --ease-smooth:  cubic-bezier(0.25, 1, 0.5, 1);
  --ease-spring:  cubic-bezier(0.34, 1.56, 0.64, 1);
  --ease-bounce:  cubic-bezier(0.175, 0.885, 0.32, 1.275);

  /* Layout */
  --max: 900px;
}
```

**Step 3: Verify**

Open `guide.html` in a browser. The page should look broken but with the new cream `#f4f1ea` background (not the old `#FAF7F4`). No fonts will have changed yet — that comes in Task 2.

**Step 4: Commit**
```bash
git add guide.html
git commit -m "style: replace tokens with Tannic design system palette"
```

---

## Task 2: Global Base Styles

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace `body`, `a`, `a:hover`, `::selection`**

Find and replace the body/link rules:

```css
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 16px; scroll-behavior: smooth; }

body {
  font-family: var(--font-body);
  color: var(--col-dark);
  background: var(--col-light);
  line-height: 1.75;
  -webkit-font-smoothing: antialiased;
}

::selection { background: var(--col-accent); color: var(--col-light); }

a { color: var(--col-accent); }
a:hover { color: var(--col-dark); }

.wrap { max-width: var(--max); margin: 0 auto; padding: 0 1.5rem; }
```

**Step 2: Verify**

Body text now renders in Inter. Background is warm cream.

**Step 3: Commit**
```bash
git commit -am "style: base typography — Inter body, Tannic palette links"
```

---

## Task 3: Topbar

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace `.topbar`, `.topbar-brand`, `.topbar-link` rules**

```css
.topbar {
  background: var(--col-dark);
  padding: 0.65rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(var(--col-light-rgb), 0.08);
}
.topbar-brand {
  font-family: var(--font-mono);
  font-size: 0.72rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--col-light);
  font-weight: 600;
}
.topbar-link {
  font-family: var(--font-mono);
  font-size: 0.7rem;
  color: rgba(var(--col-accent-rgb), 0.8);
  text-decoration: none;
  letter-spacing: 0.08em;
  transition: color 0.2s var(--ease-smooth);
}
.topbar-link:hover { color: var(--col-accent); }
```

**Step 2: Verify**

Topbar is deep forest green with warm cream brand name, terracotta link.

**Step 3: Commit**
```bash
git commit -am "style: topbar — Fira Code, Tannic dark bg"
```

---

## Task 4: Hero

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace all `.hero*` rules**

```css
.hero {
  background: var(--col-dark);
  padding: 5rem 1.5rem 4.5rem;
  position: relative;
  overflow: hidden;
}

/* Grain texture overlay */
.hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle at 18% 14%, rgba(var(--col-light-rgb), 0.06), transparent 34%),
    radial-gradient(circle at 84% 72%, rgba(var(--col-accent-rgb), 0.05), transparent 42%),
    var(--texture-paper);
  opacity: 0.18;
  pointer-events: none;
  mix-blend-mode: soft-light;
}

.hero-eyebrow {
  font-family: var(--font-mono);
  font-size: 0.68rem;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--col-accent);
  margin-bottom: 1.25rem;
  position: relative;
}

.hero h1 {
  font-family: var(--font-heading);
  font-size: clamp(2rem, 5vw, 3.25rem);
  font-weight: 400;
  color: var(--col-light);
  line-height: 1.15;
  max-width: 720px;
  margin-bottom: 1.25rem;
  position: relative;
  font-optical-sizing: auto;
}
.hero h1 em { font-style: italic; color: var(--col-accent); }

.hero-sub {
  font-size: 1rem;
  color: rgba(var(--col-light-rgb), 0.7);
  max-width: 580px;
  line-height: 1.75;
  margin-bottom: 2rem;
  position: relative;
}

.hero-meta {
  font-family: var(--font-mono);
  font-size: 0.68rem;
  color: rgba(var(--col-accent-rgb), 0.7);
  letter-spacing: 0.08em;
  position: relative;
}
```

**Step 2: Verify**

Hero is deep green with grain, Fraunces heading in cream, italic em in terracotta, Fira Code eyebrow.

**Step 3: Commit**
```bash
git commit -am "style: hero — Fraunces, grain texture, Tannic dark"
```

---

## Task 5: Companion Badge + Section Shell

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace `.companion-badge` and `.section*` rules**

```css
.companion-badge {
  background: rgba(var(--col-dark-rgb), 0.04);
  border-left: 3px solid var(--col-accent);
  padding: 1rem 1.5rem;
  margin: 2.5rem 0;
  font-size: 0.9rem;
  color: rgba(var(--col-dark-rgb), 0.75);
}
.companion-badge strong { color: var(--col-dark); }
.companion-badge a { color: var(--col-accent); }

/* Section shell */
.section { padding: 3.5rem 0; border-bottom: 1px solid rgba(var(--col-dark-rgb), 0.1); }
.section:last-of-type { border-bottom: none; }

.section-label {
  font-family: var(--font-mono);
  font-size: 0.62rem;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: var(--col-accent);
  margin-bottom: 0.4rem;
  display: block;
}

.section > h2 {
  font-family: var(--font-heading);
  font-size: clamp(1.5rem, 3vw, 2rem);
  font-weight: 400;
  color: var(--col-dark);
  margin-bottom: 1rem;
  line-height: 1.2;
  font-optical-sizing: auto;
}

.section > p { margin-bottom: 1rem; font-size: 1rem; }
```

**Step 2: Verify**

Sections have hairline dividers on cream. Eyebrows are Fira Code terracotta. Section headings are Fraunces.

**Step 3: Commit**
```bash
git commit -am "style: companion badge, section shell — Fraunces headings"
```

---

## Task 6: Search Field — Focus Bloom

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace `.glossary-controls`, `.search-wrap`, `#glossary-search` rules**

```css
.glossary-controls {
  position: sticky;
  top: 0;
  z-index: 10;
  background: var(--col-light);
  border-bottom: 1px solid rgba(var(--col-dark-rgb), 0.1);
  padding: 0.9rem 0;
  margin-bottom: 1.5rem;
}

.search-wrap { position: relative; margin-bottom: 0.75rem; }
.search-wrap svg {
  position: absolute; left: 0.75rem; top: 50%;
  transform: translateY(-50%);
  opacity: 0.35; pointer-events: none;
  color: var(--col-dark);
}

#glossary-search {
  width: 100%;
  padding: 0.65rem 1rem 0.65rem 2.4rem;
  border: 1px solid rgba(var(--col-dark-rgb), 0.18);
  border-left: 3px solid transparent;
  background: var(--col-white);
  font-family: var(--font-body);
  font-size: 0.875rem;
  color: var(--col-dark);
  outline: none;
  transition: border-color 0.25s var(--ease-smooth), background 0.25s var(--ease-smooth);
}
#glossary-search:focus {
  border-color: rgba(var(--col-dark-rgb), 0.18);
  border-left-color: var(--col-gold);
  background: #fefefe;
}
#glossary-search::placeholder { color: rgba(var(--col-dark-rgb), 0.3); }

.filter-row { display: flex; flex-wrap: wrap; gap: 0.4rem; align-items: center; }
.filter-label {
  font-family: var(--font-mono);
  font-size: 0.62rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(var(--col-dark-rgb), 0.45);
  margin-right: 0.25rem;
}

.no-results { display: none; font-family: var(--font-mono); font-size: 0.85rem; color: rgba(var(--col-dark-rgb), 0.4); padding: 1.5rem 0; font-style: italic; }
.no-results.visible { display: block; }
```

**Step 2: Verify**

Search field has no left gold border at rest. On focus, left border blooms gold. Placeholder is soft dark.

**Step 3: Commit**
```bash
git commit -am "style: search field focus bloom — gold left border"
```

---

## Task 7: Filter Buttons — Ink Fill

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace all `.filter-btn` rules**

```css
.filter-btn {
  font-family: var(--font-mono);
  font-size: 0.68rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  padding: 4px 12px;
  border-radius: 2px;
  border: 1px solid rgba(var(--col-dark-rgb), 0.2);
  cursor: pointer;
  background: transparent;
  color: rgba(var(--col-dark-rgb), 0.5);
  position: relative;
  overflow: hidden;
  transition: color 0.25s var(--ease-smooth), border-color 0.25s var(--ease-smooth);
}

/* Ink fill pseudo */
.filter-btn::before {
  content: '';
  position: absolute;
  inset: 0;
  background: var(--col-dark);
  transform: scaleX(0);
  transform-origin: left center;
  transition: transform 0.3s var(--ease-smooth);
  z-index: 0;
}

.filter-btn span, .filter-btn { position: relative; z-index: 1; }

.filter-btn:hover {
  border-color: var(--col-dark);
  color: var(--col-dark);
}

.filter-btn.active {
  color: var(--col-light);
  border-color: var(--col-dark);
}
.filter-btn.active::before { transform: scaleX(1); }

/* Role-specific active bg colours */
.filter-btn[data-role="all"].active::before       { background: var(--col-dark); }
.filter-btn[data-role="owner"].active::before      { background: var(--role-owner); }
.filter-btn[data-role="strategist"].active::before { background: var(--role-strategist); }
.filter-btn[data-role="designer"].active::before   { background: var(--role-designer); }
.filter-btn[data-role="developer"].active::before  { background: var(--role-developer); }

/* Text readable on Developer dark bg */
.filter-btn[data-role="developer"].active { color: var(--col-light); }
```

**Step 2: Verify**

Inactive buttons are transparent with soft border. Clicking one: background fills from left like ink bleeding into paper. Role buttons each fill their own palette color.

**Step 3: Commit**
```bash
git commit -am "style: filter buttons — ink fill animation, on-palette role colors"
```

---

## Task 8: Role Tags

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace all `.role-tag` and `.who-dot` rules**

```css
.role-tag {
  display: inline-block;
  font-family: var(--font-mono);
  font-size: 0.6rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 2px 7px;
  border-radius: 2px;
}
.role-tag.owner      { background: var(--role-owner-bg);      color: var(--role-owner); }
.role-tag.strategist { background: var(--role-strategist-bg); color: var(--role-strategist); }
.role-tag.designer   { background: var(--role-designer-bg);   color: var(--role-designer); }
.role-tag.developer  { background: var(--role-developer-bg);  color: var(--role-developer); }

/* Developer tag needs slightly lighter text since bg is near-black */
.role-tag.developer  { color: rgba(var(--col-dark-rgb), 0.85); }

.who-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
.who-dot.owner      { background: var(--role-owner); }
.who-dot.strategist { background: var(--role-strategist); }
.who-dot.designer   { background: var(--role-designer); }
.who-dot.developer  { background: var(--role-developer); }
```

**Step 2: Verify**

Role tags: Owner = gold, Strategist = terracotta, Designer = sage, Developer = forest. All readable.

**Step 3: Commit**
```bash
git commit -am "style: role tags — on-palette Tannic colors"
```

---

## Task 9: Glossary Accordion — Spring Open + Border Fill

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace all `.glossary-item`, `.glossary-trigger`, `.glossary-body*`, `.glossary-term-name`, `.glossary-chevron` rules**

```css
.glossary-item {
  border-bottom: 1px solid rgba(var(--col-dark-rgb), 0.1);
  transition: opacity 0.2s;
  position: relative;
}
.glossary-item.hidden { display: none; }
.glossary-item:last-child { border-bottom: none; }

/* Left border fill — scaleY on open */
.glossary-item::before {
  content: '';
  position: absolute;
  left: -1.5rem;
  top: 0;
  width: 3px;
  height: 100%;
  background: var(--col-accent);
  transform: scaleY(0);
  transform-origin: top center;
  transition: transform 0.4s var(--ease-spring);
}
.glossary-item.open::before { transform: scaleY(1); }

.glossary-trigger {
  width: 100%;
  background: none;
  border: none;
  cursor: pointer;
  padding: 1.25rem 0;
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 1rem;
  align-items: center;
  text-align: left;
  position: relative;
  overflow: hidden;
}

/* Trigger hover: whisper flood from left */
.glossary-trigger::after {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(var(--col-dark-rgb), 0.03);
  transform: scaleX(0);
  transform-origin: left center;
  transition: transform 0.35s var(--ease-smooth);
  pointer-events: none;
}
.glossary-trigger:hover::after { transform: scaleX(1); }
.glossary-trigger:hover .glossary-term-name { color: var(--col-accent); }

.glossary-trigger-left { display: flex; flex-direction: column; gap: 0.4rem; }

.glossary-term-name {
  font-family: var(--font-mono);
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--col-dark);
  transition: color 0.2s var(--ease-smooth);
}

.glossary-roles { display: flex; flex-wrap: wrap; gap: 0.3rem; }

.glossary-chevron {
  width: 20px; height: 20px;
  color: rgba(var(--col-dark-rgb), 0.3);
  transition: transform 0.3s var(--ease-spring), color 0.2s;
  flex-shrink: 0;
}
.glossary-item.open .glossary-chevron {
  transform: rotate(180deg);
  color: var(--col-accent);
}

/* Spring open animation */
.glossary-body {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.45s var(--ease-spring);
}
.glossary-item.open .glossary-body { grid-template-rows: 1fr; }
.glossary-body-inner { overflow: hidden; }

.glossary-body-content {
  padding-bottom: 1.75rem;
  display: grid;
  grid-template-columns: 180px 1fr;
  gap: 2rem;
}

.glossary-who { padding-top: 0.1rem; }
.glossary-who-label {
  font-family: var(--font-mono);
  font-size: 0.58rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: rgba(var(--col-dark-rgb), 0.4);
  margin-bottom: 0.5rem;
}
.glossary-who-list { display: flex; flex-direction: column; gap: 0.3rem; }
.who-row { display: flex; align-items: flex-start; gap: 0.4rem; font-family: var(--font-body); font-size: 0.75rem; }
.who-desc { color: rgba(var(--col-dark-rgb), 0.6); line-height: 1.4; }

.glossary-def { font-size: 0.95rem; }
.glossary-def p { margin-bottom: 0.85rem; }
.glossary-def p:last-child { margin-bottom: 0; }
.glossary-def strong { color: var(--col-dark); font-weight: 600; }
```

**Step 2: Verify**

Click any glossary term. It opens with a springy overshoot. The left accent border grows down from the top. Trigger background floods in on hover. Chevron spins with spring ease.

**Step 3: Commit**
```bash
git commit -am "style: glossary accordion — spring easing, border fill, hover pour"
```

---

## Task 10: "Why It Matters" Callouts — Inverted Card

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace `.glossary-def .why` rule**

```css
.glossary-def .why {
  margin-top: 0.85rem;
  font-size: 0.88rem;
  color: rgba(var(--col-light-rgb), 0.82);
  font-style: italic;
  padding: 0.85rem 1.1rem;
  background: var(--col-dark);
  border-left: 3px solid var(--col-accent);
  line-height: 1.6;
}
.glossary-def .why strong {
  color: var(--col-accent);
  font-style: normal;
  font-family: var(--font-mono);
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  display: block;
  margin-bottom: 0.25rem;
}
```

**Step 2: Verify**

"Why it matters" blocks are now inverted dark cards with cream text and terracotta "Why it matters:" label in Fira Code.

**Step 3: Commit**
```bash
git commit -am "style: why-it-matters callouts — inverted dark card"
```

---

## Task 11: Decision Matrix Table

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace `.matrix*` and `.pill*` rules**

```css
.matrix-wrap { overflow-x: auto; margin: 1.5rem 0; }
.matrix {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
  min-width: 620px;
}
.matrix th {
  background: var(--col-dark);
  color: var(--col-light);
  padding: 0.85rem 1rem;
  text-align: left;
  font-family: var(--font-mono);
  font-weight: 600;
  font-size: 0.68rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}
.matrix th:first-child { background: rgba(var(--col-dark-rgb), 0.85); }
.matrix td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgba(var(--col-dark-rgb), 0.1);
  vertical-align: top;
  line-height: 1.5;
  font-size: 0.875rem;
}
.matrix tr:nth-child(even) td { background: rgba(var(--col-dark-rgb), 0.03); }
.matrix td:first-child {
  font-family: var(--font-mono);
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--col-dark);
  white-space: nowrap;
}

/* Pills — palette-aligned */
.pill { display: inline-block; font-family: var(--font-mono); font-size: 0.62rem; padding: 2px 8px; border-radius: 2px; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase; }
.pill-green  { background: rgba(74,122,109,0.14);  color: #3a6359; }
.pill-yellow { background: rgba(139,128,46,0.14);  color: var(--col-gold); }
.pill-red    { background: rgba(217,125,93,0.14);  color: #b5613f; }

/* Theme note */
.theme-note {
  background: var(--col-dark);
  color: var(--col-light);
  padding: 1.5rem 2rem;
  margin: 2rem 0;
  position: relative;
  overflow: hidden;
}
.theme-note::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: var(--texture-paper);
  opacity: 0.08;
  pointer-events: none;
}
.theme-note h3 {
  font-family: var(--font-heading);
  font-size: 1rem;
  font-weight: 400;
  margin-bottom: 0.5rem;
  color: var(--col-accent);
  font-style: italic;
}
.theme-note p { font-size: 0.9rem; color: rgba(var(--col-light-rgb), 0.75); margin: 0; }
.theme-note a { color: var(--col-accent); }
```

**Step 2: Verify**

Table headers dark forest green with cream Fira Code text. Pills are palette-tinted (sage green / gold / terracotta).

**Step 3: Commit**
```bash
git commit -am "style: decision matrix — Tannic palette headers, pills"
```

---

## Task 12: Progress Bar — Liquid Fill

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace `.dt-progress-bar`, `.dt-steps`, `.dt-step` rules; add keyframes**

```css
/* Breathe pulse for active step */
@keyframes breathe {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.55; }
}

@keyframes scaleInX {
  from { transform: scaleX(0); }
  to   { transform: scaleX(1); }
}

.dt-progress-bar {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 2rem;
  font-family: var(--font-mono);
  font-size: 0.68rem;
  color: rgba(var(--col-dark-rgb), 0.4);
  letter-spacing: 0.06em;
  text-transform: uppercase;
}
.dt-steps { display: flex; gap: 0; flex: 1; }

.dt-step {
  flex: 1;
  height: 3px;
  background: rgba(var(--col-dark-rgb), 0.12);
  position: relative;
  overflow: hidden;
  transition: background 0.3s;
}
.dt-step + .dt-step { margin-left: 3px; }

/* Liquid fill pseudo */
.dt-step::after {
  content: '';
  position: absolute;
  inset: 0;
  transform: scaleX(0);
  transform-origin: left center;
  transition: transform 0s; /* overridden per state */
}
.dt-step.done::after {
  background: var(--col-accent);
  transform: scaleX(1);
  transition: transform 0.4s var(--ease-bounce);
}
.dt-step.active::after {
  background: var(--col-dark);
  transform: scaleX(1);
  transition: transform 0.3s var(--ease-smooth);
  animation: breathe 1.8s ease-in-out infinite;
}

.dt-step-label { white-space: nowrap; }
```

**Step 2: Verify**

Answer Q1. The first progress segment floods terracotta from left with a bounce. The second segment goes dark with a gentle pulse.

**Step 3: Commit**
```bash
git commit -am "style: progress bar — liquid scaleX fill with bounce ease"
```

---

## Task 13: Decision Tree — Questions, Headers, Answered Row

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace `.dt-question`, `.dt-q-header`, `.dt-q-num`, `.dt-q-text`, `.dt-answered-row`, `.dt-answered-*`, `.dt-connector` rules**

```css
.dt-wrap { margin-top: 2rem; }

.dt-question {
  margin-bottom: 1rem;
  transition: opacity 0.35s var(--ease-smooth);
}
.dt-question.inactive {
  opacity: 0.3;
  pointer-events: none;
  filter: blur(0.5px);
}
.dt-question.done { opacity: 0.65; }

.dt-q-header {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1rem;
}
.dt-q-num {
  font-family: var(--font-mono);
  font-size: 0.62rem;
  font-weight: 600;
  letter-spacing: 0.14em;
  color: var(--col-accent);
  padding-top: 0.2rem;
  min-width: 2rem;
  text-align: right;
  text-transform: uppercase;
}
.dt-q-text {
  font-family: var(--font-heading);
  font-size: 1.05rem;
  color: var(--col-dark);
  line-height: 1.45;
  font-optical-sizing: auto;
}

.dt-answered-row {
  display: none;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 1rem;
  background: rgba(var(--col-dark-rgb), 0.04);
  border: 1px solid rgba(var(--col-dark-rgb), 0.1);
  border-left: 3px solid var(--col-accent);
  margin-left: 3rem;
  font-family: var(--font-mono);
  font-size: 0.72rem;
  letter-spacing: 0.04em;
}
.dt-question.done .dt-answered-row { display: flex; }
.dt-question.done .dt-cards        { display: none; }

.dt-answered-label { color: rgba(var(--col-dark-rgb), 0.45); text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.62rem; }
.dt-answered-choice { color: var(--col-dark); font-weight: 600; }
.dt-answered-edit {
  margin-left: auto;
  background: none;
  border: none;
  cursor: pointer;
  font-family: var(--font-mono);
  font-size: 0.65rem;
  color: var(--col-accent);
  text-decoration: underline;
  padding: 0;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  transition: color 0.2s;
}
.dt-answered-edit:hover { color: var(--col-dark); }

/* Connector wire */
.dt-connector {
  width: 2px;
  height: 1.75rem;
  background: rgba(var(--col-dark-rgb), 0.12);
  margin: 0 0 0 calc(3rem + 0px);
  position: relative;
  overflow: hidden;
}

/* Drip animation — dot travels down the wire */
@keyframes drip {
  0%   { transform: translateY(-100%); opacity: 0; }
  20%  { opacity: 1; }
  80%  { opacity: 1; }
  100% { transform: translateY(500%); opacity: 0; }
}

.dt-connector.lit { background: rgba(var(--col-accent-rgb), 0.3); }
.dt-connector.lit::after {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 8px;
  background: var(--col-accent);
  border-radius: 2px;
  animation: drip 0.6s var(--ease-smooth) forwards;
}
```

**Step 2: Verify**

Answer Q1. The connector wire below it tints accent and a little drip dot runs down it. Answered row appears with accent left border.

**Step 3: Commit**
```bash
git commit -am "style: decision tree questions, answered row, drip connector"
```

---

## Task 14: Decision Card Fronts — Pour Effect

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace `.dt-cards`, `.dt-card`, `.dt-card-inner`, `.dt-card-front`, `.dt-card-option`, `.dt-card-label` rules**

```css
.dt-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
  margin-left: 3rem;
}

.dt-card {
  perspective: 900px;
  cursor: pointer;
  min-height: 120px;
}

.dt-card-inner {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 120px;
  transform-style: preserve-3d;
  transition: transform 0.55s cubic-bezier(0.4,0,0.2,1);
}
.dt-card.selected .dt-card-inner  { transform: rotateY(180deg); }
.dt-card.dismissed .dt-card-inner {
  opacity: 0.15;
  filter: blur(1px);
  pointer-events: none;
  transition: opacity 0.4s, filter 0.4s;
}

.dt-card-front, .dt-card-back {
  position: absolute;
  inset: 0;
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
  padding: 1.1rem 1.25rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.dt-card-front {
  border: 1px solid rgba(var(--col-dark-rgb), 0.15);
  background: var(--col-white);
  position: relative;
  overflow: hidden;
  transition: border-color 0.25s var(--ease-smooth);
}

/* Pour effect: liquid floods up from bottom on hover */
.dt-card-front::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(var(--col-dark-rgb), 0.05);
  transform: translateY(100%);
  transition: transform 0.4s var(--ease-smooth);
  pointer-events: none;
}

.dt-card:not(.selected):not(.dismissed):hover .dt-card-front::before {
  transform: translateY(0);
}
.dt-card:not(.selected):not(.dismissed):hover .dt-card-front {
  border-color: var(--col-accent);
}

.dt-card-option {
  font-family: var(--font-mono);
  font-size: 0.62rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--col-accent);
  margin-bottom: 0.4rem;
}
.dt-card-label {
  font-family: var(--font-heading);
  font-size: 0.95rem;
  color: var(--col-dark);
  line-height: 1.35;
  font-optical-sizing: auto;
}
```

**Step 2: Verify**

Hover over a decision card: the background floods up from the bottom like liquid. Border turns terracotta. Other cards aren't affected.

**Step 3: Commit**
```bash
git commit -am "style: decision card fronts — pour hover effect, Fraunces label"
```

---

## Task 15: Decision Card Backs — Glass Morphism + Staggered Fade

**Files:** Modify `guide.html` `<style>` block

**Step 1: Add keyframe + replace `.dt-card-back`, `.dt-card-back-arrow`, `.dt-card-result` rules**

```css
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(6px); }
  to   { opacity: 1; transform: translateY(0); }
}

.dt-card-back {
  transform: rotateY(180deg);
  background: var(--glass-bg);
  backdrop-filter: blur(var(--glass-blur));
  -webkit-backdrop-filter: blur(var(--glass-blur));
  border: 1px solid var(--glass-border);
}

.dt-card-back-arrow {
  font-family: var(--font-mono);
  font-size: 0.9rem;
  color: var(--col-accent);
  margin-bottom: 0.5rem;
  opacity: 0;
}
.dt-card.selected .dt-card-back-arrow {
  animation: fadeInUp 0.3s var(--ease-smooth) 0.32s forwards;
}

.dt-card-result {
  font-family: var(--font-body);
  font-size: 0.82rem;
  color: rgba(var(--col-light-rgb), 0.82);
  line-height: 1.5;
  opacity: 0;
}
.dt-card.selected .dt-card-result {
  animation: fadeInUp 0.3s var(--ease-smooth) 0.42s forwards;
}

.dt-card-result strong {
  font-family: var(--font-mono);
  color: var(--col-accent);
  display: block;
  margin-bottom: 0.3rem;
  font-size: 0.68rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-style: normal;
}
```

**Step 2: Verify**

Click a card. It flips to reveal a glass dark card. After the flip, the arrow and text fade in with a slight upward drift. Dismissed card blurs and fades.

**Step 3: Commit**
```bash
git commit -am "style: decision card backs — glass morphism, staggered content fade"
```

---

## Task 16: Recommendation Panel

**Files:** Modify `guide.html` `<style>` block

**Step 1: Add keyframe + replace `.dt-recommendation*` rules**

```css
@keyframes slideUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes bulletIn {
  from { opacity: 0; transform: translateX(-8px); }
  to   { opacity: 1; transform: translateX(0); }
}

.dt-recommendation {
  display: none;
  margin-top: 2rem;
  background: var(--col-dark);
  border-left: 4px solid var(--col-accent);
  padding: 2rem 2rem 2rem 1.75rem;
  position: relative;
  overflow: hidden;
}
.dt-recommendation::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: var(--texture-paper);
  opacity: 0.07;
  pointer-events: none;
}

.dt-recommendation.visible {
  display: block;
  animation: slideUp 0.5s var(--ease-smooth) forwards;
}

.dt-rec-eyebrow {
  font-family: var(--font-mono);
  font-size: 0.62rem;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: rgba(var(--col-accent-rgb), 0.7);
  margin-bottom: 0.5rem;
}

.dt-rec-verdict {
  font-family: var(--font-heading);
  font-size: clamp(1.3rem, 3vw, 1.65rem);
  color: var(--col-light);
  margin-bottom: 0.75rem;
  line-height: 1.25;
  font-optical-sizing: auto;
}
.dt-rec-verdict em { color: var(--col-accent); font-style: italic; }

.dt-rec-body {
  font-size: 0.9rem;
  color: rgba(var(--col-light-rgb), 0.7);
  line-height: 1.65;
  margin-bottom: 1.5rem;
}

.dt-rec-insights { list-style: none; margin-bottom: 1.75rem; }
.dt-rec-insights li {
  font-family: var(--font-mono);
  font-size: 0.75rem;
  color: rgba(var(--col-accent-rgb), 0.85);
  padding: 0.4rem 0;
  border-bottom: 1px solid rgba(var(--col-light-rgb), 0.08);
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  opacity: 0; /* animated in via JS stagger */
}
.dt-rec-insights li::before { content: "→"; color: var(--col-accent); flex-shrink: 0; }
.dt-rec-insights li.visible {
  animation: bulletIn 0.35s var(--ease-smooth) forwards;
}

/* Reset button */
.dt-reset-btn {
  background: none;
  border: 1px solid rgba(var(--col-light-rgb), 0.2);
  color: rgba(var(--col-light-rgb), 0.6);
  font-family: var(--font-mono);
  font-size: 0.68rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 0.5rem 1.25rem;
  cursor: pointer;
  transition: all 0.2s var(--ease-smooth);
  position: relative;
  overflow: hidden;
}
.dt-reset-btn::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(var(--col-light-rgb), 0.08);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.25s var(--ease-smooth);
}
.dt-reset-btn:hover::before { transform: scaleX(1); }
.dt-reset-btn:hover { color: var(--col-light); border-color: rgba(var(--col-light-rgb), 0.4); }
```

**Step 2: Verify**

Complete all 5 questions. Recommendation panel slides up with grain texture and terracotta left border. Verdict is Fraunces, bullets are Fira Code.

**Step 3: Commit**
```bash
git commit -am "style: recommendation panel — slide-up, grain, staggered bullets"
```

---

## Task 17: Pull Quote, Handles Grid, CTA Block, Footer

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace remaining component rules**

```css
/* Pull quote */
.pull-quote {
  border-left: 4px solid var(--col-accent);
  padding: 1rem 1.5rem;
  margin: 2rem 0;
  font-family: var(--font-heading);
  font-size: clamp(1rem, 2vw, 1.2rem);
  color: var(--col-dark);
  font-style: italic;
  line-height: 1.6;
  font-optical-sizing: auto;
}

/* Handles grid */
.handles-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
.handles-col {
  background: var(--col-white);
  border: 1px solid rgba(var(--col-dark-rgb), 0.12);
  padding: 1.5rem;
}
.handles-col h3 {
  font-family: var(--font-mono);
  font-size: 0.65rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  margin-bottom: 1rem;
}
.handles-col.yours h3 { color: var(--col-accent); }
.handles-col.ours  h3 { color: var(--col-dark); }
.handles-col ul { list-style: none; }
.handles-col ul li {
  font-size: 0.9rem;
  padding: 0.4rem 0;
  border-bottom: 1px solid rgba(var(--col-dark-rgb), 0.08);
  color: rgba(var(--col-dark-rgb), 0.8);
}
.handles-col ul li:last-child { border-bottom: none; }
.handles-col.yours ul li::before { content: "✓  "; color: var(--col-accent); font-family: var(--font-mono); }
.handles-col.ours  ul li::before { content: "→  "; color: var(--col-dark);   font-family: var(--font-mono); }

/* CTA block */
.cta-block {
  background: var(--col-dark);
  padding: 3rem 2rem;
  text-align: center;
  margin-top: 3rem;
  position: relative;
  overflow: hidden;
}
.cta-block::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: var(--texture-paper);
  opacity: 0.08;
  pointer-events: none;
}
.cta-block p {
  color: rgba(var(--col-light-rgb), 0.75);
  margin-bottom: 1.5rem;
  position: relative;
  font-size: 1rem;
}

/* CTA button — fill from bottom */
.cta-btn {
  display: inline-block;
  color: var(--col-dark);
  background: var(--col-accent);
  padding: 0.85rem 2.25rem;
  font-family: var(--font-mono);
  font-size: 0.72rem;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  text-decoration: none;
  font-weight: 600;
  position: relative;
  overflow: hidden;
  transition: color 0.3s var(--ease-smooth);
}
.cta-btn::before {
  content: '';
  position: absolute;
  inset: 0;
  background: var(--col-light);
  transform: translateY(100%);
  transition: transform 0.35s var(--ease-smooth);
}
.cta-btn:hover::before { transform: translateY(0); }
.cta-btn:hover { color: var(--col-dark); }
.cta-btn span { position: relative; z-index: 1; }
```

**Step 2: Also update the `<footer>` inline styles in the HTML**

Find `<footer style="background:var(--brand)...">` and replace the inline style attribute:

```html
<footer style="background:var(--col-dark);padding:2rem 1.5rem;text-align:center;margin-top:4rem;border-top:1px solid rgba(244,241,234,0.08);">
  <p style="font-family:'Fira Code',monospace;font-size:0.72rem;color:rgba(217,125,93,0.75);letter-spacing:0.14em;text-transform:uppercase;">Tannic Studios &nbsp;·&nbsp; Digital Estates for Hospitality Brands</p>
</footer>
```

**Step 3: Wrap `.cta-btn` text content in `<span>` tags in the HTML**

Find `<a href="..." class="cta-btn">Start the conversation</a>` and update to:
```html
<a href="https://tannicstudios.com/contact" class="cta-btn"><span>Start the conversation</span></a>
```

**Step 4: Verify**

Pull quote has Fraunces italic, accent left border. CTA button fills light cream from bottom on hover. Footer is Fira Code terracotta text.

**Step 5: Commit**
```bash
git commit -am "style: pull quote, handles, CTA fill button, footer"
```

---

## Task 18: Responsive Media Queries

**Files:** Modify `guide.html` `<style>` block

**Step 1: Replace existing responsive blocks**

```css
@media (max-width: 640px) {
  .glossary-body-content { grid-template-columns: 1fr; }
  .glossary-who { display: none; }
  .handles-grid { grid-template-columns: 1fr; }
  .dt-cards { grid-template-columns: 1fr; margin-left: 0; }
  .dt-answered-row { margin-left: 0; }
  .dt-card { min-height: 100px; }
  .dt-card-inner { min-height: 100px; }
  .glossary-item::before { left: -0.75rem; }
}
```

**Step 2: Commit**
```bash
git commit -am "style: responsive adjustments for mobile"
```

---

## Task 19: Sticky Progress Strip — HTML + CSS

**Files:** Modify `guide.html`

**Step 1: Add `.progress-strip` element to HTML**

Find the closing `</main>` tag and insert this element immediately before it:

```html
<div class="progress-strip" id="progress-strip" aria-live="polite" aria-atomic="true">
  <span class="progress-strip-bar">
    <span class="progress-strip-fill" id="strip-fill"></span>
  </span>
  <span class="progress-strip-text" id="strip-text">Build profile forming… <strong id="strip-count">0</strong> / 5</span>
</div>
```

**Step 2: Add CSS for `.progress-strip`**

Add to the `<style>` block:

```css
/* Sticky progress strip */
.progress-strip {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 100;
  background: var(--col-dark);
  border-top: 1px solid rgba(var(--col-light-rgb), 0.1);
  padding: 0.65rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transform: translateY(100%);
  transition: transform 0.4s var(--ease-spring);
  font-family: var(--font-mono);
  font-size: 0.68rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}
.progress-strip.visible { transform: translateY(0); }

.progress-strip-bar {
  flex: 1;
  max-width: 200px;
  height: 2px;
  background: rgba(var(--col-light-rgb), 0.15);
  position: relative;
  overflow: hidden;
}
.progress-strip-fill {
  position: absolute;
  left: 0; top: 0; bottom: 0;
  background: var(--col-accent);
  width: 0%;
  transition: width 0.4s var(--ease-bounce);
}

.progress-strip-text { color: rgba(var(--col-light-rgb), 0.55); }
.progress-strip-text strong { color: var(--col-accent); }

.progress-strip.complete .progress-strip-text { color: var(--col-accent); }

@keyframes stripPulse {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.5; }
}
.progress-strip.complete { animation: stripPulse 0.8s ease-in-out 2; }
```

**Step 3: Verify CSS only**

The strip should not be visible yet (transform hidden). Inspect DOM to confirm element is present.

**Step 4: Commit**
```bash
git commit -am "feat: sticky progress strip — HTML + CSS"
```

---

## Task 20: Sticky Progress Strip — JavaScript

**Files:** Modify `guide.html` `<script>` block

**Step 1: Add strip update logic**

Find the `updateProgress()` function in the existing script and replace it entirely:

```javascript
function updateProgress() {
  const answered = answers.filter(Boolean).length;

  // Update segment indicators
  for (let i = 0; i < TOTAL; i++) {
    const step = document.getElementById(`step-${i}`);
    step.classList.remove('done', 'active');
    if (i < answered)       step.classList.add('done');
    else if (i === answered) step.classList.add('active');
  }

  // Step label
  const label = document.getElementById('dt-step-label');
  label.textContent = answered === TOTAL
    ? 'All done — see your profile below'
    : `Question ${answered + 1} of ${TOTAL}`;

  // Sticky strip
  const strip     = document.getElementById('progress-strip');
  const stripFill = document.getElementById('strip-fill');
  const stripCount = document.getElementById('strip-count');

  if (answered > 0) {
    strip.classList.add('visible');
    strip.classList.remove('complete');
    stripFill.style.width = `${(answered / TOTAL) * 100}%`;
    stripCount.textContent = answered;

    if (answered === TOTAL) {
      strip.classList.add('complete');
      document.getElementById('strip-text').innerHTML =
        'Your profile is ready <strong>↓</strong>';
    } else {
      document.getElementById('strip-text').innerHTML =
        `Build profile forming… <strong id="strip-count">${answered}</strong> / ${TOTAL}`;
    }
  } else {
    strip.classList.remove('visible', 'complete');
  }
}
```

**Step 2: Verify**

Answer Q1. The strip slides up from the bottom showing "Build profile forming… 1 / 5". The mini fill bar fills 20%. Answer all 5 — text changes to "Your profile is ready ↓" with a pulse.

**Step 3: Commit**
```bash
git commit -am "feat: sticky progress strip JS — live count, fill bar, complete pulse"
```

---

## Task 21: Copy Result Button — HTML + CSS

**Files:** Modify `guide.html`

**Step 1: Add copy button to recommendation panel HTML**

Find `<button class="dt-reset-btn" id="dt-reset">Start over</button>` and add immediately before it:

```html
<button class="dt-copy-btn" id="dt-copy" aria-label="Copy result to clipboard">
  <span class="dt-copy-label">Copy my result</span>
</button>
```

**Step 2: Add CSS**

```css
.dt-copy-btn {
  background: none;
  border: 1px solid rgba(var(--col-accent-rgb), 0.5);
  color: var(--col-accent);
  font-family: var(--font-mono);
  font-size: 0.68rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 0.5rem 1.25rem;
  cursor: pointer;
  margin-right: 0.75rem;
  transition: all 0.2s var(--ease-smooth);
  position: relative;
  overflow: hidden;
}
.dt-copy-btn::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(var(--col-accent-rgb), 0.12);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.25s var(--ease-smooth);
}
.dt-copy-btn:hover::before { transform: scaleX(1); }
.dt-copy-btn:hover { border-color: var(--col-accent); }
.dt-copy-btn.copied { border-color: var(--role-designer); color: var(--role-designer); }
```

**Step 3: Commit**
```bash
git commit -am "feat: copy result button — HTML + CSS"
```

---

## Task 22: Copy Result Button — JavaScript

**Files:** Modify `guide.html` `<script>` block

**Step 1: Add copy handler**

Find `document.getElementById('dt-reset').addEventListener('click', ...)` and add this block immediately after the closing `});` of that handler:

```javascript
document.getElementById('dt-copy').addEventListener('click', () => {
  const verdict = document.getElementById('dt-rec-verdict').textContent.trim();
  const body    = document.getElementById('dt-rec-body').textContent.trim();
  const bullets = Array.from(document.querySelectorAll('#dt-rec-insights li'))
    .map(li => '→ ' + li.textContent.trim())
    .join('\n');

  const text = [
    'My Website Build Profile — Tannic Studios Guide',
    '─'.repeat(46),
    verdict,
    '',
    body,
    '',
    'Key insights:',
    bullets,
    '',
    'Take the full guide: https://tannicstudios.com'
  ].join('\n');

  navigator.clipboard.writeText(text).then(() => {
    const btn   = document.getElementById('dt-copy');
    const label = btn.querySelector('.dt-copy-label');
    btn.classList.add('copied');
    label.textContent = 'Copied ✓';
    setTimeout(() => {
      btn.classList.remove('copied');
      label.textContent = 'Copy my result';
    }, 2200);
  }).catch(() => {
    // Fallback for older browsers
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
  });
});
```

**Step 2: Verify**

Complete all 5 questions. Click "Copy my result". Label changes to "Copied ✓" in sage green. Paste into a text editor — should see formatted verdict, body, and bullets.

**Step 3: Commit**
```bash
git commit -am "feat: copy result to clipboard with 2s feedback state"
```

---

## Task 23: Staggered Bullet Animation — JavaScript

**Files:** Modify `guide.html` `<script>` block

**Step 1: Update `showRecommendation()` to stagger bullets**

Find the `showRecommendation()` function and update the `buildInsights` rendering section. Replace this block:

```javascript
const ul = document.getElementById('dt-rec-insights');
ul.innerHTML = '';
buildInsights().forEach(txt => {
  const li = document.createElement('li');
  li.textContent = txt;
  ul.appendChild(li);
});
```

With:

```javascript
const ul = document.getElementById('dt-rec-insights');
ul.innerHTML = '';
buildInsights().forEach((txt, i) => {
  const li = document.createElement('li');
  li.textContent = txt;
  ul.appendChild(li);
  // Stagger each bullet in after panel animation completes
  setTimeout(() => li.classList.add('visible'), 500 + (i * 90));
});
```

**Step 2: Verify**

Complete all 5 questions. Recommendation panel slides in. Then each bullet fades in from the left at 90ms intervals — like type being set.

**Step 3: Commit**
```bash
git commit -am "feat: staggered bullet animation in recommendation panel"
```

---

## Task 24: Reduced Motion Pass

**Files:** Modify `guide.html` `<style>` block

**Step 1: Add `prefers-reduced-motion` media query block**

Add at the very end of the `<style>` block, before `</style>`:

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }

  .dt-card-inner { transition: none; }
  .glossary-body { transition: none; }
  .progress-strip { transition: none; }

  /* Still show states — just without animation */
  .dt-card.selected .dt-card-back-arrow,
  .dt-card.selected .dt-card-result { opacity: 1; animation: none; }
  .dt-rec-insights li { opacity: 1; animation: none; }
}
```

**Step 2: Verify**

In macOS System Settings → Accessibility → Reduce Motion, enable it. Reload the page. Interactions should work but transitions are instant. Disable and re-enable — animations return.

**Step 3: Final commit**
```bash
git commit -am "style: prefers-reduced-motion — disable all animations"
```

---

## Done

All tasks complete. The guide is now fully styled to the Tannic design system with:
- Fraunces / Inter / Fira Code / Mrs Saint Delafield typography
- `#162d27` / `#d97d5d` / `#f4f1ea` / `#8b802e` palette
- Glass morphism decision card backs
- Fill-up interactions across every surface (pour, ink, liquid scaleX)
- Drip connector animation
- Sticky progress strip with live fill bar
- Copy-to-clipboard result button
- Staggered bullet animations
- Reduced motion support

Next step when integrating into WordPress: extract the `<style>` block into the theme's `style.css`, register the fonts in `functions.php` via `wp_enqueue_style`, and convert the page to a `page-decision-guide.php` template.
