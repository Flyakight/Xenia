// Studio Notes renderer.
//
// For every folder in studio-notes/posts/ that has sketches in it, reads
// post.yml, lays the sketches out as taped cards (template.html) and saves
// the finished images to that folder's out/ directory.
//
//   node render.mjs              render posts whose inputs changed
//   node render.mjs --force      re-render everything
//   node render.mjs my-post      render just that folder

import { createServer } from 'node:http';
import { createHash } from 'node:crypto';
import { readFile, readdir, writeFile, mkdir, rm } from 'node:fs/promises';
import { dirname, extname, join, resolve, sep } from 'node:path';
import { fileURLToPath } from 'node:url';
import yaml from 'js-yaml';
import { chromium } from 'playwright';

const HERE = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(HERE, '../..');
const POSTS = join(ROOT, 'studio-notes', 'posts');
const IMAGE = /\.(jpe?g|png|webp)$/i;
const FORMATS = ['instagram', 'pinterest', 'linkedin'];
const TYPES = { '.html': 'text/html', '.js': 'text/javascript', '.mjs': 'text/javascript', '.png': 'image/png', '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.webp': 'image/webp', '.woff2': 'font/woff2', '.svg': 'image/svg+xml' };

const args = process.argv.slice(2);
const force = args.includes('--force');
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

function titleFromFile(f) {
  return f.replace(extname(f), '').replace(/^\d+[-_ ]*/, '').replace(/[-_]+/g, ' ').trim();
}

async function loadPost(slug) {
  const dir = join(POSTS, slug);
  const files = (await readdir(dir)).filter(f => IMAGE.test(f)).sort();
  if (!files.length) return null;

  let cfg = {};
  try { cfg = yaml.load(await readFile(join(dir, 'post.yml'), 'utf8')) || {}; }
  catch (e) { if (e.code !== 'ENOENT') throw new Error(`${slug}/post.yml: ${e.message}`); }

  const listed = Array.isArray(cfg.cards) && cfg.cards.length
    ? cfg.cards.map(c => (typeof c === 'string' ? { file: c } : c))
    : files.map(file => ({ file }));
  for (const c of listed) {
    if (!files.includes(c.file)) throw new Error(`${slug}/post.yml lists "${c.file}", but there's no such image in the folder.`);
  }

  const hash = createHash('sha1').update(renderVersion).update(JSON.stringify(cfg));
  for (const c of listed) hash.update(await readFile(join(dir, c.file)));

  const paperMode = m => (m === true || m === 'remove' ? 'remove' : m === false || m === 'keep' ? 'keep' : 'auto');
  return {
    dir,
    hash: hash.digest('hex'),
    post: {
      slug,
      title: cfg.title || titleFromFile(slug),
      script: cfg.script || '',
      kicker: cfg.kicker || 'From the sketchbook',
      note: cfg.note || '',
      client: cfg.client || '',
      cta: cfg.cta || 'Link in bio',
      paperMode: paperMode(cfg.paper),
      formats: (cfg.formats || FORMATS).map(f => String(f).toLowerCase()).filter(f => FORMATS.includes(f)),
      cards: listed.map(c => ({
        src: `../posts/${encodeURIComponent(slug)}/${encodeURIComponent(c.file)}`,
        label: c.label ?? titleFromFile(c.file),
        tag: c.tag || '',
        note: c.note || '',
        card: c.card === 'moss' || c.card === 'cream' ? c.card : undefined,
        paperMode: c.paper === undefined ? undefined : paperMode(c.paper),
      })),
    },
  };
}

const slugs = (await readdir(POSTS, { withFileTypes: true }))
  .filter(d => d.isDirectory() && !d.name.startsWith('.'))
  .map(d => d.name)
  .filter(s => !only.length || only.includes(s));

// CHROMIUM_PATH lets a machine with its own Chromium skip `playwright install`.
const browser = await chromium.launch(process.env.CHROMIUM_PATH ? { executablePath: process.env.CHROMIUM_PATH } : {});
let rendered = 0, failed = 0;

for (const slug of slugs) {
  let job;
  try { job = await loadPost(slug); } catch (e) { console.error(`✗ ${e.message}`); failed++; continue; }
  if (!job) continue;

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
    await writeFile(stamp, job.hash + '\n');
    console.log(`✓ ${slug}: ${frames.length} image${frames.length === 1 ? '' : 's'}`);
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
