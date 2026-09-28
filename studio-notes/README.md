# Studio Notes

Behind-the-scenes work turned into taped-card posts for Instagram, Pinterest
and LinkedIn, in the Tannic look. Use it for wireframes, illustrations, brand
explorations, phone photos of your sketchbook, code in progress and notes.
Posts can be stills or motion.

You add the pieces and a few words. GitHub renders the posts.

## Make a post

1. On GitHub, open `studio-notes/posts/` on the **main** branch.
2. Click **Add file → Upload files** and drag in your files. In the path box at
   the top, type a new folder name first, like
   `studio-notes/posts/2026-10-juniper-homepage/`. Dates at the front keep
   posts in order.
3. Commit. That's enough for a post. Every file becomes a card, in filename
   order, labelled from its filename (`01-homepage.jpg` becomes "homepage").
4. For titles, notes, your own labels, the look and motion, add a `post.yml`
   to the same folder. Copy the one in `_template/`, which explains every line.
5. Wait a minute or two, or a few more if the post has motion. Open the
   **Actions** tab to watch it run. The finished files show up in the post's
   `out/` folder.

To change anything, edit `post.yml` or swap a file and commit. That post
re-renders. Posts you haven't touched are left alone.

## Cards

| You add | You get |
|---|---|
| A sketch, drawing or phone photo (`.jpg` `.png` `.webp`) | A sketch card. Paper is lifted off photos automatically. |
| A code file (`.js` `.css` `.php` `.html` `.svg` `.py` `.sh` …), or `code:` in `post.yml` | A dark editor card with a filename tab, line numbers and syntax colours |
| A `.txt` or `.md` file, or `text:` in `post.yml` | A note: a ruled `index` card, a gold `sticky` note or a brown `kraft` tag |

Every card gets a number, a small typed tag and a handwritten label.

## Looks

- **Layouts:** `collage` (a loose pin-up, the default), `fan` (overlapping, like
  a hand of cards) or `grid` (tidy, best for code).
- **Backdrops:** `forest` (the default), `moss` (a lighter green) or `kraft`
  (brown paper).
- **Sizes:**

| File | Size | For |
|---|---|---|
| `instagram-01.png` … | 1080×1350 | Carousel: a cover collage, then one slide per card |
| `story.png` | 1080×1920 | Stories and Reels covers (add `story` to `formats`) |
| `pinterest.png` | 1000×1500 | A pin with the collage |
| `linkedin.png` | 1200×1200 | A square post with the collage |

Collages show up to four cards. The Instagram carousel shows every one. A post
with a single card becomes one Instagram image, not a carousel.

## Motion

Set `motion: true` in `post.yml` and the post also gets:

- `motion-instagram.mp4` (1080×1350): the cover, animated
- `motion-story.mp4` (1080×1920): for Stories and Reels

The title settles in, then each card drops onto the page and gets taped down.
Sketches reveal top to bottom, code types itself out line by line, and notes
write themselves on. It holds on the finished layout for a beat, so it loops
cleanly. Each clip is rendered frame by frame, so the text stays sharp. Use
`motion: [story]` or `motion: [instagram]` for just one of them.

Motion takes longer to render, about a minute per clip.

## Getting good results

- **Phone photos:** shoot straight down in even daylight and crop to the paper
  before uploading. The renderer lifts the pencil or ink off the paper, evens
  out shadows and vignetting, and trims to the drawing. Only the paper should
  be in frame. A table or hand at the edge will be kept as "drawing".
- **Scans and digital drawings:** PNGs with a transparent background are used
  as they are. Set `paper: remove` for a white-background export you want lifted.
- **Light art** (cream or white lines) lands on a moss-green card automatically.
  Dark art lands on cream paper. Override per sketch with `card: moss` or
  `card: cream`.
- **Code:** show the interesting 5 to 20 lines, not the whole file. Lines
  longer than about 70 characters are cut off at the card's edge.
- **Size:** anything around 2000px on the long side is plenty for images.

## Under the hood

- `render/render.mjs` finds post folders (skipping ones that start with `_`),
  skips unchanged posts, screenshots each image from `render/template.html`,
  and for motion seeks the page's animation frame by frame into ffmpeg.
- `render/template.html` does the paper removal, syntax highlighting, card
  layout, tape, texture and animation.
- `.github/workflows/studio-notes.yml` runs it on every push to `main` that
  touches `studio-notes/`, and commits the results back. You can also run it
  by hand from the Actions tab, with **force** ticked to re-render everything.
- To run it on your own machine (needs ffmpeg for motion):
  `cd studio-notes/render && npm install && npx playwright install chromium && node render.mjs`.
- None of this is part of the website theme, so it never deploys to Bluehost.
