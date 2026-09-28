// Studio Notes renderer.
//
// For every folder in studio-notes/posts/ that has sketches, code or notes in
// it, reads post.yml, lays everything out as taped cards (template.html) and
// saves the finished images (and, with `motion: true`, MP4s) to out/.
//
//   node render.mjs              render posts whose inputs changed
//   node render.mjs --force      re-render everything
//   node render.mjs --recapture  re-capture showcase sites (implies a re-render)
//   node render.mjs my-post      render just that folder
//
// Showcase posts (`type: showcase` in post.yml) capture a live site first;
// see capture.mjs.
//
// Environment: CHROMIUM_PATH to use an existing Chromium, FFMPEG for a
// specific ffmpeg binary (defaults to `ffmpeg` on the PATH).

import { createServer } from 'node:http';
import { createHash } from 'node:crypto';
import { spawn } from 'node:child_process';
import { readFile, readdir, writeFile, mkdir, rm } from 'node:fs/promises';
import { dirname, extname, join, resolve, sep } from 'node:path';
import { fileURLToPath } from 'node:url';
import yaml from 'js-yaml';
import { chromium } from 'playwright';
import { capture } from './capture.mjs';

const HERE = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(HERE, '../..');
const POSTS = join(ROOT, 'studio-notes', 'posts');
const IMAGE = /\.(jpe?g|png|webp)$/i;
const CODE = /\.(js|mjs|ts|jsx|tsx|css|scss|php|html|svg|xml|vue|py|sh|ya?ml|json)$/i;
const NOTE = /\.(txt|md)$/i;
const FORMATS = ['instagram', 'story', 'pinterest', 'linkedin'];
const MOTION_FRAMES = { instagram: 'instagram-01', story: 'story' };
const FPS = 30;
const TYPES = { '.html': 'text/html', '.js': 'text/javascript', '.mjs': 'text/javascript', '.png': 'image/png', '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.webp': 'image/webp', '.woff2': 'font/woff2', '.svg': 'image/svg+xml' };

const args = process.argv.slice(2);
const force = args.includes('--force');
const recapture = args.includes('--recapture');
const only = args.filter(a => !a.startsWith('--'));

// Serve the repo so the page can read sketches, fonts and the vine art.
const server = createServer(async (req, res) => {
  const path = resolve(ROOT, '.' + decodeURIComponent(new URL(req.url, 'http://x').pathname));
  if (!path.startsWith(ROOT + sep)) { res.writeHead(403).end(); return; }
  try {
    const body = await readFile(path);
    res.writeHead(200, { 'Content-Type': TYPES[extname(path).toLowerCase()] || 'application/octet-stream' }).end(body);
  } catch { res.writeHead(404).end(); }
});
await new Promise(r => server.listen(0, '127.0.0.1', r));
const base = `http://127.0.0.1:${server.address().port}`;

const renderVersion = createHash('sha1')
  .update(await readFile(join(HERE, 'template.html')))
  .update(await readFile(join(HERE, 'render.mjs')))
  .digest('hex');

const nameOf = f => f.replace(extname(f), '').replace(/^\d+[-_ ]*/, '').replace(/[-_]+/g, ' ').trim();
const langOf = f => extname(f).slice(1).toLowerCase();
const paperMode = m => (m === true || m === 'remove' ? 'remove' : m === false || m === 'keep' ? 'keep' : 'auto');
const pick = (v, allowed, fallback) => (allowed.includes(String(v).toLowerCase()) ? String(v).toLowerCase() : fallback);

async function loadPost(slug) {
  const dir = join(POSTS, slug);
  const files = (await readdir(dir)).filter(f => IMAGE.test(f) || CODE.test(f) || NOTE.test(f)).sort();

  let cfg = {};
  try { cfg = yaml.load(await readFile(join(dir, 'post.yml'), 'utf8')) || {}; }
  catch (e) { if (e.code !== 'ENOENT') throw new Error(`${slug}/post.yml: ${e.message}`); }

  const showcase = cfg.type === 'showcase';
  if (showcase && !/^https?:\/\//.test(cfg.url || '')) throw new Error(`${slug}/post.yml: a showcase needs a url, like https://example.com`);
  const listed = Array.isArray(cfg.cards) && cfg.cards.length
    ? cfg.cards.map(c => (typeof c === 'string' ? { file: c } : c))
    : showcase ? [] : files.map(file => ({ file }));
  if (!listed.length && !showcase) return null;

  const hash = createHash('sha1').update(renderVersion).update(JSON.stringify(cfg));
  const cards = [];
  for (const c of listed) {
    const card = { label: c.label, tag: c.tag || '', note: c.note || '' };
    if (c.code !== undefined || c.text !== undefined || c.note_text !== undefined) {
      // Inline code or note written straight into post.yml.
      if (c.code !== undefined) Object.assign(card, { kind: 'code', text: String(c.code), lang: (c.lang || 'text').toLowerCase(), filename: c.filename || c.label || '' });
      else Object.assign(card, { kind: 'note', text: String(c.text ?? c.note_text), style: c.style });
    } else {
      if (!c.file) throw new Error(`${slug}/post.yml: every card needs a file, code or text.`);
      if (!files.includes(c.file)) throw new Error(`${slug}/post.yml lists "${c.file}", but there's no such file in the folder.`);
      const buf = await readFile(join(dir, c.file));
      hash.update(buf);
      if (IMAGE.test(c.file)) {
        Object.assign(card, {
          kind: 'image',
          src: `../posts/${encodeURIComponent(slug)}/${encodeURIComponent(c.file)}`,
          card: c.card === 'moss' || c.card === 'cream' ? c.card : undefined,
          paperMode: c.paper === undefined ? undefined : paperMode(c.paper),
        });
      } else if (NOTE.test(c.file) && !c.lang) {
        Object.assign(card, { kind: 'note', text: buf.toString('utf8').trim(), style: c.style });
      } else {
        Object.assign(card, { kind: 'code', text: buf.toString('utf8'), lang: (c.lang || langOf(c.file)).toLowerCase(), filename: c.filename || c.file });
      }
      card.label = card.label ?? nameOf(c.file);
    }
    card.label = card.label ?? '';
    cards.push(card);
  }

  const formats = (cfg.formats || ['instagram', 'pinterest', 'linkedin']).map(f => String(f).toLowerCase()).filter(f => FORMATS.includes(f));
  const motion = cfg.motion === true ? ['instagram', 'story']
    : Array.isArray(cfg.motion) ? cfg.motion.map(m => String(m).toLowerCase()).filter(m => MOTION_FRAMES[m]) : [];
  // A motion format needs its still frame too.
  for (const m of motion) if (!formats.includes(m)) formats.push(m);

  let logoFile = null;
  if (showcase && cfg.logo) {
    if (!files.includes(cfg.logo)) throw new Error(`${slug}/post.yml: logo "${cfg.logo}" isn't in the folder.`);
    hash.update(await readFile(join(dir, cfg.logo)));
    logoFile = `../posts/${encodeURIComponent(slug)}/${encodeURIComponent(cfg.logo)}`;
  }

  return {
    dir,
    cfg,
    showcase,
    logoFile,
    hash,
    motion,
    post: {
      slug,
      type: showcase ? 'showcase' : 'notes',
      title: cfg.title || nameOf(slug),
      script: cfg.script || '',
      kicker: cfg.kicker || (showcase ? 'Case study' : 'From the sketchbook'),
      services: (Array.isArray(cfg.services) ? cfg.services : []).map(String),
      quote: cfg.quote || '',
      quoteBy: cfg.quote_by || '',
      url: cfg.url || '',
      note: cfg.note || '',
      client: cfg.client || '',
      cta: cfg.cta || 'Link in bio',
      paperMode: paperMode(cfg.paper),
      layout: pick(cfg.layout, ['collage', 'fan', 'grid'], 'collage'),
      backdrop: pick(cfg.backdrop, ['forest', 'moss', 'kraft'], 'forest'),
      wallpaper: cfg.wallpaper !== false,
      formats,
      cards,
    },
  };
}

// Seek the page's paused timeline frame by frame and pipe PNGs into ffmpeg,
// so type stays crisp (a screen recording would smear it).
async function renderMotion(page, name, file) {
  const el = await page.$(`.frame[data-name="${name}"]`);
  const ms = await page.evaluate(n => window.durationOf(n), name);
  const frames = Math.min(Math.ceil(ms / 1000 * FPS), +process.env.STUDIO_NOTES_MAX_FRAMES || Infinity);
  const ff = spawn(process.env.FFMPEG || 'ffmpeg', [
    '-hide_banner', '-loglevel', 'error', '-y',
    '-f', 'image2pipe', '-framerate', String(FPS), '-i', '-',
    '-c:v', 'libx264', '-pix_fmt', 'yuv420p', '-crf', '18', '-preset', 'medium', '-movflags', '+faststart',
    file,
  ], { stdio: ['pipe', 'inherit', 'inherit'] });
  const done = new Promise((ok, fail) => { ff.on('error', fail); ff.on('close', code => (code ? fail(new Error(`ffmpeg exited with ${code}`)) : ok())); });
  for (let i = 0; i <= frames; i++) {
    await page.evaluate(([n, t]) => window.seek(n, t), [name, i * 1000 / FPS]);
    // High-quality JPEG frames: far faster to capture than PNG, and the
    // H.264 encode that follows is lossy anyway.
    const shot = await el.screenshot({ animations: 'allow', type: 'jpeg', quality: 94 });
    if (!ff.stdin.write(shot)) await new Promise(r => ff.stdin.once('drain', r));
  }
  ff.stdin.end();
  await done;
  // Leave the still at its finished state.
  await page.evaluate(n => window.seek(n, Infinity), name);
  return (frames / FPS).toFixed(1);
}

// Showcase posts: capture the live site (or reuse the last capture) and hand
// the screenshots plus the brand read off the page to the template.
async function prepareShowcase(job, browser) {
  const { brand, fresh } = await capture(browser, job.cfg, job.dir, { force: recapture || job.cfg.recapture === true });
  const cap = `../posts/${encodeURIComponent(job.post.slug)}/capture/`;
  for (const f of ['desktop.jpg', 'desktop-full.jpg', 'mobile.jpg', 'mobile-full.jpg']) job.hash.update(await readFile(join(job.dir, 'capture', f)));
  const o = job.cfg.brand || {};
  job.post.showcase = {
    desktop: cap + 'desktop.jpg', desktopFull: cap + 'desktop-full.jpg',
    mobile: cap + 'mobile.jpg', mobileFull: cap + 'mobile-full.jpg',
    logo: job.logoFile || (brand.hasLogo ? cap + 'logo.png' : ''),
    heights: brand.heights,
    host: new URL(job.cfg.url).host.replace(/^www\./, ''),
    palette: Array.isArray(o.colors) && o.colors.length ? o.colors.map(String) : brand.palette,
    background: o.background || brand.background,
    accent: o.accent || brand.accent,
    ink: o.ink || brand.ink,
    fonts: { heading: o.heading_font || brand.fonts.heading, body: o.body_font || brand.fonts.body },
  };
  return fresh;
}

const slugs = (await readdir(POSTS, { withFileTypes: true }))
  .filter(d => d.isDirectory() && !/^[._]/.test(d.name))   // _template and friends are skipped
  .map(d => d.name)
  .filter(s => !only.length || only.includes(s));

// CHROMIUM_PATH lets a machine with its own Chromium skip `playwright install`.
const browser = await chromium.launch(process.env.CHROMIUM_PATH ? { executablePath: process.env.CHROMIUM_PATH } : {});
let rendered = 0, failed = 0;

for (const slug of slugs) {
  let job;
  try { job = await loadPost(slug); } catch (e) { console.error(`✗ ${e.message}`); failed++; continue; }
  if (!job) continue;

  if (job.showcase) {
    try {
      const fresh = await prepareShowcase(job, browser);
      console.log(`  ${slug}: ${fresh ? 'captured' : 'reusing capture of'} ${job.cfg.url}`);
    } catch (e) { console.error(`✗ ${slug}: couldn't capture ${job.cfg.url}: ${e.message}`); failed++; continue; }
  }
  job.hash = job.hash.digest('hex');

  const out = join(job.dir, 'out');
  const stamp = join(out, '.hash');
  if (!force) {
    try { if ((await readFile(stamp, 'utf8')).trim() === job.hash) { console.log(`· ${slug} (unchanged)`); continue; } } catch {}
  }

  const page = await browser.newPage({ viewport: { width: 1400, height: 1000 } });
  page.on('pageerror', e => console.error(`  page error in ${slug}: ${e.message}`));
  await page.addInitScript(p => { window.POST = p; }, job.post);
  try {
    await page.goto(`${base}/studio-notes/render/template.html`);
    await page.waitForFunction(() => window.READY || window.READY_ERROR, null, { timeout: 120000 });
    const err = await page.evaluate(() => window.READY_ERROR);
    if (err) throw new Error(err);

    await rm(out, { recursive: true, force: true });
    await mkdir(out, { recursive: true });
    const frames = await page.$$('.frame');
    for (const f of frames) {
      const name = await f.getAttribute('data-name');
      await f.screenshot({ path: join(out, `${name}.png`) });
    }
    const made = [`${frames.length} image${frames.length === 1 ? '' : 's'}`];
    // The template can say which frame each motion format animates.
    const targets = { ...MOTION_FRAMES, ...(await page.evaluate(() => window.MOTION_TARGETS || {})) };
    for (const m of job.motion) {
      const secs = await renderMotion(page, targets[m], join(out, `motion-${m}.mp4`));
      made.push(`motion-${m}.mp4 (${secs}s)`);
    }
    await writeFile(stamp, job.hash + '\n');
    console.log(`✓ ${slug}: ${made.join(', ')}`);
    rendered++;
  } catch (e) {
    console.error(`✗ ${slug}: ${e.message}`);
    failed++;
  }
  await page.close();
}

await browser.close();
server.close();
console.log(`\n${rendered} rendered, ${failed} failed.`);
process.exit(failed ? 1 : 0);
