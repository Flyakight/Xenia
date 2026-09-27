# Proof Bridge Simplification Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Strip the `home-proof-bridge` section down to just the mono tag + tagline headline — a simple, confident typographic pause between the hero and case studies.

**Architecture:** Two files change — `front-page.php` (remove elements) and `style.css` (remove dead CSS + re-center the card layout). Mobile `display: none` stays untouched. No new elements, no new classes.

**Tech Stack:** PHP (WordPress template), vanilla CSS

---

### Task 1: Simplify the PHP template

**Files:**
- Modify: `front-page.php` (lines 105–140, the `home-proof-bridge` section)

**What to remove — delete each of these blocks entirely:**

**Block 1 — the orbit decorative span (line 106):**
```php
    <span class="home-proof-bridge-orbit" aria-hidden="true"></span>
```

**Block 2 — the lead paragraph (lines 113–115):**
```php
            <p class="home-proof-bridge-lead">
                <?php esc_html_e( 'If your in-person experience is excellent but your site is leaking trust or revenue, we close that gap with strategy, design, and development that convert.', 'tannic-studio' ); ?>
            </p>
```

**Block 3 — the actions div with both CTAs (lines 116–123):**
```php
            <div class="home-proof-bridge-actions">
                <a href="<?php echo esc_url( '#work' ); ?>" class="home-proof-bridge-link">
                    <?php esc_html_e( 'See the proof in case studies', 'tannic-studio' ); ?> &rarr;
                </a>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-outline">
                    <?php esc_html_e( 'Start a Conversation', 'tannic-studio' ); ?>
                </a>
            </div>
```

**Block 4 — the entire glass aside (lines 126–138):**
```php
        <aside class="home-proof-bridge-glass" aria-label="<?php esc_attr_e( 'How we bridge brand and performance', 'tannic-studio' ); ?>">
            <p class="home-proof-glass-label mono"><?php esc_html_e( 'The bridge', 'tannic-studio' ); ?></p>
            <p class="home-proof-glass-line">
                <?php esc_html_e( 'Brand feeling', 'tannic-studio' ); ?>
                <span aria-hidden="true">&rarr;</span>
                <?php esc_html_e( 'Digital action', 'tannic-studio' ); ?>
            </p>
            <p class="home-proof-glass-line">
                <?php esc_html_e( 'Aesthetic precision', 'tannic-studio' ); ?>
                <span aria-hidden="true">&rarr;</span>
                <?php esc_html_e( 'Commercial clarity', 'tannic-studio' ); ?>
            </p>
        </aside>
```

**Expected result after all deletions** — the section should look like this:

```php
<!-- ====================================================================
     SECTION 1b: PAIN-POINT TO PROOF BRIDGE (Desktop Narrative)
     ==================================================================== -->

<section class="home-proof-bridge" aria-label="<?php esc_attr_e( 'How we solve core growth problems', 'tannic-studio' ); ?>">
    <div class="home-proof-bridge-inner">
        <div class="home-proof-bridge-copy">
            <p class="home-proof-bridge-label mono">&lt;strategy + design + development /&gt;</p>
            <h2 class="home-proof-bridge-headline">
                <?php echo esc_html( $hero_tagline ); ?>
            </h2>
        </div>
    </div>
</section>
```

**Verification:** Read lines 101–145 of `front-page.php` and confirm:
1. No `home-proof-bridge-orbit` in the output
2. No `home-proof-bridge-lead` in the output
3. No `home-proof-bridge-actions` in the output
4. No `home-proof-bridge-glass` in the output
5. The mono label and h2 headline are still present

---

### Task 2: Update CSS — layout + remove dead rules

**Files:**
- Modify: `style.css`

There are two parts: updating live rules and removing dead ones.

---

**Part A — Update `.home-proof-bridge-inner` to single-column centered**

Find this rule (around line 1288):
```css
.home-proof-bridge-inner {
    max-width: var(--container-max);
    margin: 0 auto;
    padding: clamp(1.35rem, 2.3vw, 2rem) clamp(1.25rem, 2.3vw, 2rem) clamp(2rem, 3.8vw, 3rem);
    border: 1px solid rgba(var(--col-light-rgb), 0.2);
    background: linear-gradient(136deg, rgba(15, 35, 32, 0.52) 0%, rgba(15, 35, 32, 0.3) 64%, rgba(15, 35, 32, 0.12) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 30px 90px -48px rgba(0, 0, 0, 0.85);
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(280px, 0.62fr);
    gap: clamp(1.25rem, 2.2vw, 2rem);
    align-items: end;
    position: relative;
}
```

Replace with (single column, centered, no gap needed):
```css
.home-proof-bridge-inner {
    max-width: var(--container-max);
    margin: 0 auto;
    padding: clamp(2rem, 3.5vw, 3.5rem) clamp(1.25rem, 2.3vw, 2rem);
    border: 1px solid rgba(var(--col-light-rgb), 0.2);
    background: linear-gradient(136deg, rgba(15, 35, 32, 0.52) 0%, rgba(15, 35, 32, 0.3) 64%, rgba(15, 35, 32, 0.12) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 30px 90px -48px rgba(0, 0, 0, 0.85);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
}
```

---

**Part B — Make `.home-proof-bridge-headline` larger and centered**

Find this rule (around line 1321):
```css
.home-proof-bridge-headline {
    font-family: var(--font-heading);
    font-size: clamp(2.2rem, 1.8rem + 1.35vw, 3.15rem);
    line-height: 1.03;
    color: var(--col-light);
    max-width: 14ch;
}
```

Replace with:
```css
.home-proof-bridge-headline {
    font-family: var(--font-heading);
    font-size: clamp(2.8rem, 2rem + 2vw, 4rem);
    line-height: 1.03;
    color: var(--col-light);
    max-width: 20ch;
    margin-top: var(--space-xs);
}
```

---

**Part C — Remove dead CSS rules (delete each block entirely)**

Find and delete these rule blocks — they are orphaned now that the elements are gone:

**1. `.home-proof-bridge-orbit` and `::after` (around lines 1264–1286):**
```css
.home-proof-bridge-orbit {
    position: absolute;
    width: 68px;
    height: 68px;
    border: 1px solid rgba(var(--col-accent-rgb), 0.75);
    border-radius: 50%;
    top: clamp(1.2rem, 2.5vw, 2rem);
    right: clamp(2rem, 6vw, 5rem);
    opacity: 0.7;
    pointer-events: none;
}

.home-proof-bridge-orbit::after {
    content: '';
    position: absolute;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--col-accent);
    top: -4px;
    left: 50%;
    transform: translateX(-50%);
}
```

**2. `.home-proof-bridge-inner::before` radial glow (around lines 1304–1314):**
```css
.home-proof-bridge-inner::before {
    content: '';
    position: absolute;
    width: clamp(180px, 24vw, 340px);
    height: clamp(180px, 24vw, 340px);
    right: -14%;
    bottom: -28%;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(var(--col-accent-rgb), 0.2) 0%, rgba(var(--col-accent-rgb), 0) 68%);
    pointer-events: none;
}
```

**3. `.home-proof-bridge-lead` (around lines 1333–1337):**
```css
.home-proof-bridge-lead {
    margin-top: var(--space-sm);
    max-width: 66ch;
    color: rgba(var(--col-light-rgb), 0.85);
}
```

**4. `.home-proof-bridge-actions` (around lines 1339–1345):**
```css
.home-proof-bridge-actions {
    margin-top: var(--space-md);
    display: flex;
    align-items: center;
    gap: var(--space-md);
    flex-wrap: wrap;
}
```

**5. `.home-proof-bridge-link` and `:hover` (around lines 1347–1357):**
```css
.home-proof-bridge-link {
    font-family: var(--font-mono);
    font-size: var(--fs-xs);
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(var(--col-light-rgb), 0.86);
}

.home-proof-bridge-link:hover {
    color: var(--col-accent);
}
```

**6. `.home-proof-bridge-glass`, `::before`, `.home-proof-glass-label`, `.home-proof-glass-line`, `.home-proof-glass-line:last-child` (around lines 1359–1399):**
```css
.home-proof-bridge-glass {
    position: relative;
    align-self: center;
    justify-self: end;
    width: min(100%, 380px);
    padding: clamp(1rem, 1.8vw, 1.4rem);
    border: 1px solid rgba(var(--col-light-rgb), 0.28);
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0.06) 100%);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: 0 28px 60px -42px rgba(0, 0, 0, 0.85);
    transform: translate(10%, 18%);
}

.home-proof-bridge-glass::before {
    content: '';
    position: absolute;
    inset: 10px;
    border: 1px solid rgba(var(--col-light-rgb), 0.18);
    pointer-events: none;
}

.home-proof-glass-label {
    color: rgba(var(--col-light-rgb), 0.7);
    margin-bottom: 0.7rem;
}

.home-proof-glass-line {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    font-size: var(--fs-sm);
    color: rgba(var(--col-light-rgb), 0.86);
    padding: 0.55rem 0;
    border-top: 1px solid rgba(var(--col-light-rgb), 0.2);
}

.home-proof-glass-line:last-child {
    border-bottom: 1px solid rgba(var(--col-light-rgb), 0.2);
}
```

**7. Inside `@media (max-width: 992px)` — remove the glass override (around lines 4750–4754):**
```css
    .home-proof-bridge-glass {
        justify-self: start;
        transform: translate(0, 0);
        width: min(100%, 440px);
    }
```

And the inner grid override (around lines 4746–4748):
```css
    .home-proof-bridge-inner {
        grid-template-columns: 1fr;
    }
```

(The 992px block may now be empty of bridge rules — check and remove the whole block if it only contained bridge rules, otherwise leave the other rules inside it intact.)

---

**Verification after all CSS changes:**
1. grep for `home-proof-bridge-orbit` → 0 matches
2. grep for `home-proof-bridge-glass` → 0 matches
3. grep for `home-proof-bridge-lead` → 0 matches
4. grep for `home-proof-bridge-actions` → 0 matches
5. grep for `home-proof-bridge-link` → 0 matches
6. grep for `home-proof-glass` → 0 matches
7. Confirm `.home-proof-bridge-inner` still exists with the new centered flex layout
8. Confirm `.home-proof-bridge-headline` still exists with the larger font-size
9. Confirm `display: none` at 480px is still present for `.home-proof-bridge`
