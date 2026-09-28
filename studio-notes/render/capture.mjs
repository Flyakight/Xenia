// Studio Notes showcase capture.
//
// Opens a client's live site, tidies it (cookie banners, popups, lazy images),
// and saves what the showcase templates need into the post's capture/ folder:
//
//   desktop.jpg        1440×900 first screen
//   desktop-full.jpg   the whole desktop page (capped in height)
//   mobile.jpg         390×844 first screen, at 2× for crisp phones
//   mobile-full.jpg    the whole mobile page (capped)
//   logo.png           the site's logo, if one can be found
//   brand.json         palette, fonts, title and the settings used
//
// Captures are reused until the URL or capture settings change, or a
// re-capture is forced, so re-rendering a caption doesn't hit the site again.

import { createHash } from 'node:crypto';
import { mkdir, readFile, writeFile } from 'node:fs/promises';
import { join } from 'node:path';

const DESKTOP = { width: 1440, height: 900 };
const MOBILE = { width: 390, height: 844 };
const MAX_DESKTOP = 9000;
const MAX_MOBILE = 12000;
const UA_MOBILE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1';

export function captureKey(cfg) {
  return createHash('sha1').update(JSON.stringify({
    url: cfg.url, path: cfg.path || '/', hide: cfg.hide || [], wait: cfg.wait || 0, v: 4,
  })).digest('hex');
}

// Close cookie banners and popups, hide anything still covering the page,
// and scroll through once so lazy-loaded images arrive.
async function tidy(page, cfg) {
  await page.waitForTimeout(1200 + (+cfg.wait || 0));
  await page.evaluate(async () => {
    const words = /^(accept( all)?|allow( all)?|agree|i agree|got it|ok(ay)?|close|dismiss|no,? thanks|continue|×|✕)$/i;
    for (const b of document.querySelectorAll('button, [role=button], a')) {
      const t = (b.innerText || b.getAttribute('aria-label') || '').trim();
      if (t.length < 24 && words.test(t)) { try { b.click(); } catch {} }
    }
  });
  await page.keyboard.press('Escape').catch(() => {});
  await page.waitForTimeout(500);
  await page.evaluate(sel => {
    const vw = innerWidth, vh = innerHeight;
    for (const el of document.querySelectorAll('body *')) {
      const cs = getComputedStyle(el);
      if (cs.position !== 'fixed' && cs.position !== 'sticky') continue;
      const r = el.getBoundingClientRect();
      const covers = r.width * r.height > vw * vh * .3;
      const banner = /cookie|consent|gdpr|newsletter|popup|modal|klaviyo|privacy/i.test(el.id + ' ' + el.className);
      // Keep sticky site headers; hide overlays, banners and chat widgets.
      const header = r.top <= 1 && r.height < vh * .22 && !banner;
      if ((covers || banner || /chat|intercom|drift|tawk/i.test(el.id + ' ' + el.className)) && !header) el.style.setProperty('display', 'none', 'important');
    }
    for (const s of sel) document.querySelectorAll(s).forEach(el => el.style.setProperty('display', 'none', 'important'));
    document.documentElement.style.overflow = 'visible';
    document.body.style.overflow = 'visible';
  }, cfg.hide || []);
  if (cfg.lazy === false) return;
  const h = await page.evaluate(() => document.documentElement.scrollHeight);
  for (let y = 0; y < Math.min(h, 14000); y += 700) {
    await page.evaluate(yy => window.scrollTo(0, yy), y);
    await page.waitForTimeout(120);
  }
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(700);
}

// Read the brand straight off the page: area-weighted colours, the heading
// and body typefaces, the title and a logo element.
async function readBrand(page) {
  return page.evaluate(() => {
    const parse = c => { const m = c && c.match(/rgba?\(([^)]+)\)/); if (!m) return null; const [r, g, b, a = 1] = m[1].split(/[, /]+/).map(Number); return a < .6 ? null : [r, g, b]; };
    const hex = ([r, g, b]) => '#' + [r, g, b].map(v => Math.round(v).toString(16).padStart(2, '0')).join('');
    const bins = new Map();
    const add = (rgb, w, kind) => {
      if (!rgb || w <= 0) return;
      const k = rgb.map(v => Math.round(v / 12) * 12).join(',');
      const e = bins.get(k) || { rgb, w: 0, bg: 0, fg: 0 };
      e.w += w; e[kind] += w; bins.set(k, e);
    };
    const docH = Math.min(document.documentElement.scrollHeight, 6000);
    add(parse(getComputedStyle(document.body).backgroundColor) || parse(getComputedStyle(document.documentElement).backgroundColor) || [255, 255, 255], innerWidth * docH * .5, 'bg');
    for (const el of document.querySelectorAll('body *')) {
      const r = el.getBoundingClientRect();
      if (r.width < 4 || r.height < 4 || r.top + scrollY > docH) continue;
      const cs = getComputedStyle(el);
      if (cs.visibility === 'hidden' || cs.display === 'none' || +cs.opacity < .2) continue;
      add(parse(cs.backgroundColor), Math.min(r.width * r.height, innerWidth * 1200), 'bg');
      if (el.childNodes.length && [...el.childNodes].some(n => n.nodeType === 3 && n.textContent.trim())) add(parse(cs.color), el.textContent.trim().length * 400, 'fg');
      if (/^(svg|path)$/i.test(el.tagName) && cs.fill) add(parse(cs.fill), r.width * r.height * .5, 'bg');
    }
    const all = [...bins.values()].sort((a, b) => b.w - a.w);
    const dist = (a, b) => Math.hypot(a[0] - b[0], (a[1] - b[1]) * 1.2, a[2] - b[2]);
    const picked = [];
    for (const e of all) { if (picked.every(p => dist(p.rgb, e.rgb) > 46)) picked.push(e); if (picked.length === 6) break; }
    // Chroma rather than HSV saturation, which calls near-black colours "vivid".
    const sat = ([r, g, b]) => (Math.max(r, g, b) - Math.min(r, g, b)) / 255;
    const lum = ([r, g, b]) => (.2126 * r + .7152 * g + .0722 * b) / 255;
    const background = picked.find(p => p.bg > p.fg) || picked[0];
    const accent = picked.filter(p => p !== background).sort((a, b) => (sat(b.rgb) * Math.sqrt(b.w)) - (sat(a.rgb) * Math.sqrt(a.w)))[0] || background;
    const ink = picked.find(p => p.fg > 0 && Math.abs(lum(p.rgb) - lum(background.rgb)) > .35) || { rgb: lum(background.rgb) > .5 ? [20, 20, 20] : [245, 245, 245] };

    const fam = el => el ? getComputedStyle(el).fontFamily.split(',')[0].replace(/["']/g, '').trim() : '';
    const heading = fam(document.querySelector('h1, h2, .hero h1, [class*=title]'));
    const body = fam(document.querySelector('p') || document.body);

    // Logo: the most logo-like image or SVG in the header area.
    let logo = null, best = 0;
    // Image and SVG logos, plus text logos (a "site title" or brand link).
    for (const el of document.querySelectorAll('img, svg, [class*=logo], [id*=logo], [class*=site-title], [class*=brand], [class*=wordmark]')) {
      const r = el.getBoundingClientRect();
      if (r.width < 24 || r.height < 12 || r.top > 260 || r.width > innerWidth * .6 || r.height > 220) continue;
      const s = ((el.getAttribute('src') || '') + ' ' + (el.getAttribute('alt') || '') + ' ' + el.className?.baseVal + ' ' + el.className + ' ' + el.id + ' ' + (el.closest('a')?.getAttribute('href') || '')).toLowerCase();
      let score = /logo|wordmark/.test(s) ? 5 : /site-title|brand/.test(s) ? 4 : 0;
      if (el.closest('header, nav, [class*=header], [class*=nav]')) score += 3;
      if (/^(\/|https?:\/\/[^/]+\/?)$/.test(el.closest('a')?.getAttribute('href') || '')) score += 3;
      score += 2 - r.top / 150;
      if (score > best) { best = score; logo = { x: r.left, y: r.top + scrollY, width: r.width, height: r.height }; }
    }

    return {
      title: document.title,
      description: document.querySelector('meta[name=description]')?.content || '',
      palette: picked.map(p => hex(p.rgb)),
      background: hex(background.rgb),
      accent: hex(accent.rgb),
      ink: hex(ink.rgb),
      fonts: { heading, body },
      logoBox: best > 3 ? logo : null,
    };
  });
}

async function shoot(browser, url, viewport, opts, cfg, dir, name, maxH) {
  const ctx = await browser.newContext({ viewport, ...opts });
  const page = await ctx.newPage();
  await page.goto(url, { waitUntil: 'networkidle', timeout: 60000 }).catch(() => page.goto(url, { waitUntil: 'load', timeout: 60000 }));
  await tidy(page, cfg);
  await page.screenshot({ path: join(dir, `${name}.jpg`), type: 'jpeg', quality: 88 });
  const h = await page.evaluate(() => document.documentElement.scrollHeight);
  await page.screenshot({ path: join(dir, `${name}-full.jpg`), type: 'jpeg', quality: 84, fullPage: true, clip: h > maxH ? { x: 0, y: 0, width: viewport.width, height: maxH } : undefined });
  return { page, ctx, height: Math.min(h, maxH) };
}

export async function capture(browser, cfg, dir, { force = false } = {}) {
  const out = join(dir, 'capture');
  const key = captureKey(cfg);
  if (!force) {
    try {
      const prev = JSON.parse(await readFile(join(out, 'brand.json'), 'utf8'));
      if (prev.key === key) return { brand: prev, fresh: false };
    } catch {}
  }
  await mkdir(out, { recursive: true });
  const url = cfg.path ? new URL(cfg.path, cfg.url).href : cfg.url;

  const d = await shoot(browser, url, DESKTOP, { deviceScaleFactor: 1 }, cfg, out, 'desktop', MAX_DESKTOP);
  const brand = await readBrand(d.page);
  await d.ctx.close();
  if (brand.logoBox) {
    // The logo is usually small on the page, so crop it from a 3× render to
    // keep it sharp when the brand slide scales it up.
    const ctx = await browser.newContext({ viewport: DESKTOP, deviceScaleFactor: 3 });
    const page = await ctx.newPage();
    try {
      await page.goto(url, { waitUntil: 'networkidle', timeout: 60000 }).catch(() => page.goto(url, { waitUntil: 'load', timeout: 60000 }));
      await tidy(page, { ...cfg, lazy: false });
      const b = brand.logoBox, pad = 6;
      await page.screenshot({ path: join(out, 'logo.png'), omitBackground: true, clip: { x: Math.max(0, b.x - pad), y: Math.max(0, b.y - pad), width: b.width + pad * 2, height: b.height + pad * 2 } });
    } catch { brand.logoBox = null; }
    await ctx.close();
  }

  const m = await shoot(browser, url, MOBILE, { deviceScaleFactor: 2, isMobile: true, hasTouch: true, userAgent: UA_MOBILE }, cfg, out, 'mobile', MAX_MOBILE);
  await m.ctx.close();

  const meta = { ...brand, key, url, capturedAt: new Date().toISOString(), heights: { desktop: d.height, mobile: m.height }, hasLogo: !!brand.logoBox };
  await writeFile(join(out, 'brand.json'), JSON.stringify(meta, null, 2) + '\n');
  return { brand: meta, fresh: true };
}
