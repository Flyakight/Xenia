# Mobile Hero Fill Refinement Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Remove the `↖ tap to fill` hint arrow from the mobile hero and make the fill animation auto-play at page load (600ms delay), with improved vertical spacing so "Tannic" breathes away from the nav edge.

**Architecture:** Three isolated changes — PHP template (remove hint element), CSS (remove hint styles + fix spacing), JS (reduce auto-play timeout from 1800ms to 600ms). No new dependencies, no logic changes to the fill animation itself.

**Tech Stack:** PHP (WordPress template), vanilla CSS, vanilla ES6 JS (IIFE)

---

### Task 1: Remove the hint element from the PHP template

**Files:**
- Modify: `front-page.php:64-67`

**Step 1: Remove the `<p class="hero-fill-hint">` block**

In `front-page.php`, delete lines 64–67 (the entire hint paragraph):

```php
// DELETE these lines:
<p class="hero-fill-hint" id="hero-fill-hint" aria-live="polite">
    <span class="hero-fill-hint-arrow" aria-hidden="true">↖</span>
    <?php esc_html_e( 'tap to fill', 'tannic-studio' ); ?>
</p>
```

The `hero-headline-wrap` div should go straight from the `</h1>` to the `<?php if ( $hero_tagline )` line.

**Step 2: Verify the template looks correct**

Open `front-page.php` around line 59–72. It should now read:

```php
<div class="hero-headline-wrap">
    <h1 class="hero-headline">
        <span class="hero-headline-main hero-fill-word hero-fill-word--main" data-hero-fill-word><?php echo esc_html( $hero_headline ); ?></span>
        <span class="hero-headline-script script hero-fill-word hero-fill-word--script" data-hero-fill-word><?php echo esc_html( $hero_subtitle ); ?></span>
    </h1>
    <?php if ( $hero_tagline ) : ?>
        <p class="hero-mobile-value"><?php echo esc_html( $hero_tagline ); ?></p>
    <?php endif; ?>
    <p class="hero-mobile-code mono">&lt;strategy + design + development /&gt;</p>
</div>
```

**Step 3: Commit**

```bash
git add front-page.php
git commit -m "feat(hero): remove tap-to-fill hint element from mobile hero"
```

---

### Task 2: Remove hint CSS (styles + animation)

**Files:**
- Modify: `style.css`

There are three blocks to remove. Find and delete each one:

**Step 1: Remove the desktop `display: none` rule for hint**

Find and delete this block (around line 506):

```css
.hero-fill-hint,
.hero-mobile-value,
.hero-mobile-code {
    display: none;
}
```

Replace with just:

```css
.hero-mobile-value,
.hero-mobile-code {
    display: none;
}
```

(`.hero-mobile-value` and `.hero-mobile-code` still need `display: none` at desktop — only `.hero-fill-hint` is removed from this rule.)

**Step 2: Remove the `heroHintArrowNudge` keyframe animation**

Find and delete this entire block (around line 512):

```css
@keyframes heroHintArrowNudge {
    0%,
    100% {
        transform: translateY(-2px) rotate(-10deg);
    }
    50% {
        transform: translateY(-5px) rotate(-16deg);
    }
}
```

**Step 3: Remove the hint styles inside the `@media (max-width: 992px)` block**

Find and delete these four blocks inside the `992px` breakpoint (around lines 4233–4262):

```css
.hero-fill-hint {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    margin-top: 0.2rem;
    font-family: var(--font-script);
    font-size: clamp(1.3rem, 4.8vw, 1.65rem);
    line-height: 1;
    color: rgba(116, 47, 29, 0.85);
    opacity: 0.9;
    transform: rotate(-2deg);
    transform-origin: left center;
    transition: opacity 220ms var(--ease-smooth), transform 220ms var(--ease-smooth);
    user-select: none;
}

.hero-fill-hint-arrow {
    display: inline-block;
    font-family: var(--font-mono);
    font-size: 0.9rem;
    line-height: 1;
    opacity: 0.85;
    animation: heroHintArrowNudge 1.35s ease-in-out infinite;
}

.hero-fill-hint.is-hidden {
    opacity: 0;
    transform: translateY(-4px) rotate(-2deg);
    pointer-events: none;
}
```

**Step 4: Remove the `prefers-reduced-motion` rule for the hint arrow**

Find and delete this block at the very bottom of the file (around line 4959):

```css
@media (prefers-reduced-motion: reduce) {
    .hero-fill-hint-arrow {
        animation: none;
    }
}
```

**Step 5: Commit**

```bash
git add style.css
git commit -m "feat(hero): remove tap-to-fill hint CSS and arrow animation"
```

---

### Task 3: Fix mobile vertical spacing

**Files:**
- Modify: `style.css`

**Step 1: Increase top padding at 992px breakpoint**

Find this rule inside `@media (max-width: 992px)` (around line 4191):

```css
.hero {
    min-height: 100svh;
    align-items: flex-start;
    padding-top: calc(var(--nav-height) + var(--space-md));
    padding-bottom: var(--space-xl);
}
```

Change `padding-top` to:

```css
padding-top: calc(var(--nav-height) + var(--space-lg));
```

**Step 2: Increase top padding at 480px breakpoint**

Find this rule inside `@media (max-width: 480px)` (around line 4882):

```css
.hero {
    padding-top: calc(var(--nav-height) + 1rem);
}
```

Change to:

```css
.hero {
    padding-top: calc(var(--nav-height) + var(--space-md));
}
```

**Step 3: Commit**

```bash
git add style.css
git commit -m "fix(hero): increase mobile top padding so Tannic breathes from nav"
```

---

### Task 4: Reduce auto-play timeout in JS

**Files:**
- Modify: `js/main.js`

**Step 1: Find the setTimeout call**

Around line 470 in `js/main.js`, find:

```js
window.setTimeout(animateFill, 1800);
```

**Step 2: Change the delay to 600ms**

```js
window.setTimeout(animateFill, 600);
```

**Step 3: Verify the hint reference in JS won't throw**

The `animateFill` function references `hint`:

```js
var hint = document.getElementById('hero-fill-hint');
// ...
if (hint) hint.classList.add('is-hidden');
```

The `if (hint)` guard means removing the element from the DOM is safe — no null error will be thrown. No further JS changes needed.

**Step 4: Commit**

```bash
git add js/main.js
git commit -m "feat(hero): reduce fill auto-play delay from 1800ms to 600ms"
```

---

### Task 5: Manual verification on mobile viewport

**Step 1: Open the site in browser dev tools**

Open Chrome DevTools → Toggle device toolbar → Set to a 390px wide device (iPhone 14 Pro size).

**Step 2: Check visual spacing**

- "Tannic" should sit comfortably below the nav with clear breathing room
- No hint arrow or "tap to fill" text should be visible
- The warm gradient fill should auto-play ~600ms after page load, smoothly over 900ms

**Step 3: Check the fill still fires on tap**

Tap anywhere in the hero section *before* 600ms (fast tap immediately on load) — the fill should play immediately.

**Step 4: Check reduced motion**

In DevTools → Rendering → Enable "Emulate CSS media feature prefers-reduced-motion" → Reload. The text should appear in its filled/accent state immediately with no animation.

**Step 5: Check at 480px**

Switch device to 375px (iPhone SE). Verify spacing still looks correct at the smaller breakpoint.
