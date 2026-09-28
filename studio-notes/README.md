# Studio Notes

Behind-the-scenes sketches (wireframes, illustrations, brand explorations,
phone photos of your sketchbook) turned into taped-card posts for Instagram,
Pinterest and LinkedIn, in the forest-green Tannic look.

You add sketches and a few words. GitHub renders the images.

## Make a post

1. On GitHub, open `studio-notes/posts/` on the **main** branch.
2. Click **Add file → Upload files** and drag in your sketches. In the path
   box at the top, type a new folder name first, like
   `studio-notes/posts/2026-10-juniper-homepage/`. Dates at the front keep
   posts in order.
3. Commit. That's enough for a post: the sketches appear in filename order,
   labelled from their filenames (`01-homepage.jpg` becomes "homepage").
4. For titles, notes and your own labels, add a `post.yml` to the same
   folder. Copy the one in `_template/`, which explains every line.
5. Wait a minute or two. Open the **Actions** tab to watch it run. The
   finished images show up in the post's `out/` folder:

| File | Size | For |
|---|---|---|
| `instagram-01.png` … | 1080×1350 | Carousel: a cover collage, then one slide per sketch |
| `pinterest.png` | 1000×1500 | A pin with the collage |
| `linkedin.png` | 1200×1200 | A square post with the collage |

Collages show up to four sketches. The Instagram carousel shows every one.
A post with a single sketch becomes one Instagram image, not a carousel.

To change anything, edit `post.yml` or swap an image and commit. The post
re-renders. Posts you haven't touched are left alone.

## Getting good results

- **Phone photos:** shoot straight down in even daylight and crop to the paper
  before uploading. The renderer lifts the pencil or ink off the paper,
  evens out shadows and vignetting, and trims to the drawing. Only the paper
  should be in frame. A table or hand at the edge will be kept as "drawing".
- **Scans and digital drawings:** PNGs with a transparent background are used
  as they are. Set `paper: remove` for a white-background export you want lifted.
- **Light art** (cream or white lines) lands on a moss-green card automatically.
  Dark art lands on cream paper. Override per sketch with `card: moss` or
  `card: cream`.
- **Size:** anything around 2000px on the long side is plenty.

## Under the hood

- `render/render.mjs` finds post folders, skips unchanged ones, and screenshots
  each image from `render/template.html`, which does the paper removal, card
  layout, tape and texture.
- `.github/workflows/studio-notes.yml` runs it on every push to `main` that
  touches `studio-notes/`, and commits the images back. You can also run it by
  hand from the Actions tab, with **force** ticked to re-render everything.
- To run it on your own machine: `cd studio-notes/render && npm install &&
  npx playwright install chromium && node render.mjs`.
- None of this is part of the website theme, so it never deploys to Bluehost.
