# Luxe Hospitality — static prototype

A plain-HTML mirror of the `luxe-hospitality-wp` theme, retinted to the new
palette. No build step, no dependencies: open `index.html` in a browser, or
serve the folder (`python3 -m http.server`) if you want the Google Fonts and
GSAP CDN files to load.

Edit the layout and design here. When you are happy, hand it back and it gets
folded into the theme's PHP templates.

## Pages

| File | Becomes / came from | Status |
| --- | --- | --- |
| `index.html` | `front-page.php` | **New** — the theme has no front page template yet |
| `properties.html` | `archive-property.php` | **New** — CPT registered with `has_archive`, no template exists |
| `property.html` | `single-property.php` → `template-parts/content-property.php` | Ported |
| `experiences.html` | `archive-experience.php` | **New** — CPT registered, no template exists |
| `journal.html` | `home.php` | Ported |
| `archive.html` | `index.php` (category / tag / search) | Ported |
| `post.html` | `single.php` | Ported |
| `404.html` | `404.php` | Ported |
| `styleguide.html` | — | Prototype reference only, do not port |

Every page carries HTML comments marking which WordPress template and which
template tag each block corresponds to (`<!-- WP: the_content() -->` and so on),
so the port back is mechanical.

## Stylesheets

Load order matters — `tokens.css` first, everything else reads its variables.

| File | What it is |
| --- | --- |
| `assets/css/tokens.css` | **New.** The whole palette. Change a hex here and the site re-tints. |
| `assets/css/style.css` | The theme's stylesheet, palette block removed, otherwise near-identical |
| `assets/css/layout.css` | The theme's header/footer/gallery styles, recoloured via tokens |
| `assets/css/components.css` | **New.** Components added in the prototype + fixes to inherited behaviour |

## The palette

| Name | Hex | Token | Replaces |
| --- | --- | --- | --- |
| Fern | `#568259` | `--fern` | *(new — no green in the old palette)* |
| Beige | `#EBEBD3` | `--beige` | `--cream` `#f5f3f0` |
| Ink Black | `#0D1F22` | `--ink` | `--charcoal` `#1a1410` |
| Terracotta Clay | `#AD5D4E` | `--terracotta` | `--gold` `#d4af37` |
| Smoky Rose | `#9B6A6C` | `--rose` | `--wine` `#5a3d3a` |

The old variable names (`--gold`, `--charcoal`, `--cream`…) still resolve — they
are aliased onto the new palette in `tokens.css`, so none of the existing theme
CSS had to be rewritten. Components reference semantic roles
(`--color-accent`, `--color-nature`, `--color-inverse-bg`) rather than raw hues,
which is what makes a re-skin a five-line edit.

## Placeholder imagery

`assets/img/*.svg` are generated gradient placeholders in the palette, so the
prototype works offline and nothing here needs licensing. Swap in real
photography at will — the markup expects roughly 4:3 for cards and 16:9 for
heroes.

## Things found in the theme while porting

Fixed in the static files, still open in the WordPress theme:

1. **`.scroll-fade` can leave the page blank.** `style.css` sets
   `opacity: 0` and relies on GSAP to reveal it. If the CDN fails, or the
   visitor has `prefers-reduced-motion`, nothing ever reveals it. Here the rule
   is scoped to `.has-anim`, which `main.js` only sets on `<html>` when GSAP
   loaded *and* motion is allowed.
2. **Invalid CSS:** `margin-left: -var(--spacing-lg)` in `.image-break--overflow`
   is not valid syntax and browsers drop the declaration. Now
   `calc(-1 * var(--spacing-lg))`.
3. **`ScrollToPlugin` is never enqueued** but `main.js` calls
   `gsap.to(window, { scrollTo: ... })`, so every in-page anchor silently does
   nothing. The prototype loads the plugin; `luxe_enqueue_assets()` needs the
   same.
4. **`.grid-3` renders as five columns** on a desktop monitor —
   `repeat(auto-fit, minmax(250px, 1fr))` inside a 1600px container. Capped at
   three above 900px.
5. **Tailwind is enqueued from `cdn.tailwindcss.com`** but no template uses a
   Tailwind class. It is the dev-only JIT build, which is not meant for
   production. Omitted here; recommend removing the `wp_enqueue_style( 'tailwind' )` call.
6. **No skip link is rendered.** `layout.css` styles `.skip-link` but no
   template outputs one. Added to every page here; belongs in `header.php`.

Not fixed, flagged for the port:

7. **`data-expand-text` fights the hero.** The handler animates `font-size`
   from `1rem` up to `clamp(1.5rem, 3vw, 2.5rem)`, which *shrinks* an `h1`
   that is `clamp(2.5rem, 8vw, 6rem)`. The prototype does not use the
   attribute; the animation needs rethinking before it goes on a heading.
8. **The hero character-split is destructive.** `main.js` takes
   `document.querySelector('h1')` — the first `h1` anywhere on the page, not
   necessarily the hero — and replaces its `innerHTML` with per-character
   spans, discarding any markup inside and flattening it for screen readers.
   Worth scoping to `[data-split-text]` and adding an `aria-label`.
9. **Header and footer are duplicated across the eight pages.** That is the
   cost of a build-free prototype. Change one, and either mirror it or say
   which file is the source of truth.
