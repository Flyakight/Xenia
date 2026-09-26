/* Palimpsest: behaviours for authors and practitioners.
   Each part wakes only if its markup is on the page, so any page can use any subset. */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const DAY = 864e5;
  function mulberry32(a) { return function () { a |= 0; a = a + 0x6D2B79F5 | 0; let t = Math.imul(a ^ a >>> 15, 1 | a); t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t; return ((t ^ t >>> 14) >>> 0) / 4294967296; }; }
  function whenSeen(el, fn, threshold = .3) {
    if (reduce || el.getBoundingClientRect().top < innerHeight * .8) { fn(); return false; }
    const io = new IntersectionObserver(([e]) => { if (e.isIntersecting) { fn(); io.disconnect(); } }, { threshold });
    io.observe(el); return true;
  }

  /* ---------- calendar and moon, shared ---------- */
  const SYN = 29.530588853, REF = Date.UTC(2000, 0, 6, 18, 14);
  const dn = (y, m, d) => Date.UTC(y, m - 1, d) / DAY;
  const ymd = n => { const d = new Date(n * DAY); return [d.getUTCFullYear(), d.getUTCMonth() + 1, d.getUTCDate()]; };
  const T = new Date(), todayN = dn(T.getFullYear(), T.getMonth() + 1, T.getDate());
  const moonAge = n => ((((n + .875) * DAY - REF) / DAY) % SYN + SYN) % SYN;
  const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  const WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  const fmtDate = (n, year = true) => { const [y, m, d] = ymd(n); return `${WEEKDAYS[new Date(n * DAY).getUTCDay()]} ${d} ${MONTHS[m - 1]}${year ? ' ' + y : ''}`; };
  const nextFullMoon = n => { const a = moonAge(n); let k = Math.round(n + ((SYN / 2 - a + SYN) % SYN)); if (k <= n) k += Math.round(SYN); return k; };
  const words = ['no', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'];
  const num = n => n <= 10 ? words[n] : String(n), cap = t => t[0].toUpperCase() + t.slice(1);

  /* ---------- candle light and mica ---------- */
  const root = document.documentElement;
  addEventListener('pointermove', e => { root.style.setProperty('--mx', e.clientX + 'px'); root.style.setProperty('--my', e.clientY + 'px'); }, { passive: true });
  const micaC = $('.mica');
  if (micaC) {
    const ctx = micaC.getContext('2d');
    const draw = () => {
      micaC.width = innerWidth; micaC.height = innerHeight;
      const r = mulberry32(11), n = Math.round(micaC.width * micaC.height / 2600);
      for (let i = 0; i < n; i++) {
        const x = r() * micaC.width, y = r() * micaC.height, s = r() * 1.1 + .3;
        ctx.fillStyle = r() < .7 ? `rgba(226,194,127,${.25 + r() * .5})` : `rgba(168,151,204,${.2 + r() * .4})`;
        ctx.fillRect(x, y, s, s);
      }
    };
    draw(); addEventListener('resize', draw);
  }

  /* ---------- threshold: hills, stones, hawthorn, stars, fog, parallax ---------- */
  const hero = $('.threshold');
  if (hero) {
    const ridge = (seed, base, amp, w = 1600, h = 600, step = 16) => {
      const r = mulberry32(seed), waves = [0, 1, 2, 3].map(i => ({ f: (i + 1) * (.0022 + r() * .003), a: amp / (i + 1.3), ph: r() * 6.28 }));
      const y = x => base - waves.reduce((s, v) => s + Math.sin(x * v.f + v.ph) * v.a, 0);
      let d = `M0 ${h}L0 ${y(0).toFixed(1)}`;
      for (let x = step; x <= w; x += step) d += `L${x} ${y(x).toFixed(1)}`;
      return { d: d + `L${w} ${h}Z`, y };
    };
    const far = ridge(3, 360, 46), mid = ridge(8, 430, 34), near = ridge(21, 520, 26);
    $('#farHills').innerHTML = `<path d="${far.d}" fill="#1A1622"/>`;
    const r = mulberry32(5);
    let stones = '';
    [1010, 1044, 1070, 1102, 1131, 1168].forEach(x => {
      const h = 22 + r() * 34, w = 10 + r() * 10, b = mid.y(x) + 4;
      stones += `<path d="M${x - w / 2} ${b}L${x - w / 2 + 2} ${b - h + 5}L${x - w / 5} ${b - h}L${x + w / 2 - 1} ${b - h + 7}L${x + w / 2} ${b}Z"/>`;
    });
    $('#midHills').innerHTML = `<g fill="#141119"><path d="${mid.d}"/>${stones}</g>`;
    const tr = mulberry32(42), segs = [];
    const branch = (x, y, a, len, w, depth) => {
      const x2 = x + Math.cos(a) * len, y2 = y + Math.sin(a) * len, bend = (tr() - .5) * len * .5;
      const mx = (x + x2) / 2 + Math.cos(a + Math.PI / 2) * bend, my = (y + y2) / 2 + Math.sin(a + Math.PI / 2) * bend;
      segs.push(`<path d="M${x.toFixed(1)} ${y.toFixed(1)}Q${mx.toFixed(1)} ${my.toFixed(1)} ${x2.toFixed(1)} ${y2.toFixed(1)}" stroke-width="${w.toFixed(2)}"/>`);
      if (depth <= 0) return;
      const n = tr() < .3 ? 3 : 2;
      for (let i = 0; i < n; i++) branch(x2, y2, a + (i - (n - 1) / 2) * (.55 + tr() * .35) + .14 + (tr() - .5) * .3, len * (.66 + tr() * .14), w * .66, depth - 1);
    };
    branch(360, near.y(360) + 6, -Math.PI / 2 + .28, 88, 16, 7);
    $('#nearHill').innerHTML = `<g fill="#0D0B11"><path d="${near.d}"/></g><g fill="none" stroke="#0D0B11" stroke-linecap="round">${segs.join('')}</g>`;

    const starC = $('#stars'), sctx = starC.getContext('2d');
    const sr = mulberry32(99), stars = Array.from({ length: 170 }, () => ({ x: sr(), y: sr() * .62, s: sr() * 1.2 + .25, a: sr() * .6 + .15, t: sr() * 6.28 }));
    const sizeCanvas = (c, cap) => { const d = Math.min(devicePixelRatio || 1, cap), b = c.getBoundingClientRect(); c.width = Math.max(1, Math.round(b.width * d)); c.height = Math.max(1, Math.round(b.height * d)); };
    const drawStars = t => {
      const w = starC.width, h = starC.height;
      sctx.clearRect(0, 0, w, h);
      for (const s of stars) {
        const tw = reduce ? 1 : .75 + .25 * Math.sin(t * .0006 + s.t);
        sctx.fillStyle = `rgba(232,224,207,${(s.a * tw).toFixed(3)})`;
        sctx.beginPath(); sctx.arc(s.x * w, s.y * h, s.s * (w / 1400 + .6), 0, 6.283); sctx.fill();
      }
    };
    const fogTexture = (w, h, seed, tint) => {
      const rr = mulberry32(seed), acc = new Float32Array(w * h);
      let amp = 1, tot = 0;
      for (let o = 0; o < 5; o++) {
        const gx = 4 << o, gy = 2 << o, lat = Float32Array.from({ length: gx * gy }, rr);
        for (let y = 0; y < h; y++) {
          const fy = y / h * gy, y0 = fy | 0, u = fy - y0, ty = u * u * (3 - 2 * u), y1 = (y0 + 1) % gy;
          for (let x = 0; x < w; x++) {
            const fx = x / w * gx, x0 = fx | 0, t = fx - x0, tx = t * t * (3 - 2 * t), x1 = (x0 + 1) % gx;
            const a = lat[y0 * gx + x0] + (lat[y0 * gx + x1] - lat[y0 * gx + x0]) * tx;
            const b = lat[y1 * gx + x0] + (lat[y1 * gx + x1] - lat[y1 * gx + x0]) * tx;
            acc[y * w + x] += (a + (b - a) * ty) * amp;
          }
        }
        tot += amp; amp *= .5;
      }
      const c = document.createElement('canvas'); c.width = w; c.height = h;
      const ctx = c.getContext('2d'), img = ctx.createImageData(w, h);
      for (let y = 0; y < h; y++) {
        const fall = Math.pow(Math.sin(Math.PI * y / h), 1.4);
        for (let x = 0; x < w; x++) {
          const v = acc[y * w + x] / tot, i = (y * w + x) * 4;
          img.data[i] = tint[0]; img.data[i + 1] = tint[1]; img.data[i + 2] = tint[2];
          img.data[i + 3] = Math.min(1, Math.max(0, (v - .4) * 2.3)) * fall * 255;
        }
      }
      ctx.putImageData(img, 0, 0);
      return c;
    };
    const fogs = [
      { c: $('#fogBack'), layers: [
        { img: fogTexture(256, 96, 1, [150, 138, 176]), speed: .006, scale: 1.7, y: .42, h: .42, a: .5 },
        { img: fogTexture(256, 96, 2, [120, 108, 150]), speed: -.004, scale: 2.2, y: .55, h: .38, a: .4 } ] },
      { c: $('#fogFront'), layers: [{ img: fogTexture(256, 80, 3, [176, 166, 194]), speed: .011, scale: 1.5, y: .7, h: .34, a: .42 }] }
    ];
    const drawFog = t => {
      for (const f of fogs) {
        const ctx = f.c.getContext('2d'), w = f.c.width, h = f.c.height;
        ctx.clearRect(0, 0, w, h);
        for (const L of f.layers) {
          const sw = w * L.scale, off = ((t * L.speed * (w / 1000)) % sw + sw) % sw;
          ctx.globalAlpha = L.a;
          for (let x = -off; x < w; x += sw) ctx.drawImage(L.img, x, L.y * h, sw, L.h * h);
        }
        ctx.globalAlpha = 1;
      }
    };
    const sizeHero = () => { sizeCanvas(starC, 2); fogs.forEach(f => sizeCanvas(f.c, 1)); drawStars(0); drawFog(performance.now()); };
    sizeHero(); addEventListener('resize', sizeHero);

    const layers = $$('.layer', hero), heroCopy = $('.hero-copy', hero);
    let px = 0, py = 0, tpx = 0, tpy = 0, heroOn = true;
    hero.addEventListener('pointermove', e => { const b = hero.getBoundingClientRect(); tpx = (e.clientX - b.left) / b.width - .5; tpy = (e.clientY - b.top) / b.height - .5; });
    hero.addEventListener('pointerleave', () => { tpx = 0; tpy = 0; });
    new IntersectionObserver(([e]) => { heroOn = e.isIntersecting; }).observe(hero);
    const frame = t => {
      if (heroOn) {
        px += (tpx - px) * .05; py += (tpy - py) * .05;
        const sy = scrollY;
        for (const L of layers) {
          const d = +L.dataset.depth, dr = +L.dataset.drift;
          L.style.transform = `translate3d(${(-px * dr * 26).toFixed(2)}px, ${(sy * (.55 - d * .6) - py * dr * 12).toFixed(2)}px, 0)`;
        }
        heroCopy.style.transform = `translate3d(0, ${(sy * -.12).toFixed(1)}px, 0)`;
        drawStars(t); drawFog(t);
      }
      requestAnimationFrame(frame);
    };
    if (!reduce) requestAnimationFrame(frame);
  }

  /* ---------- the Ogham stone, built from each chamber's own letter ---------- */
  const stone = $('.stone'), chambers = $$('.chamber[data-og]');
  if (stone && chambers.length) {
    chambers.forEach(ch => {
      const d = ch.dataset, n = +d.strokes, side = d.side === 'left' ? -1 : 1;
      const b = document.createElement('button');
      b.className = 'notch'; b.type = 'button'; b.dataset.id = ch.id;
      b.setAttribute('aria-label', `${d.title}: ${d.ogname}, the ${d.tree}`);
      let lines = '';
      for (let i = 0; i < n; i++) {
        const y = 22 + (i - (n - 1) / 2) * 6;
        lines += side > 0 ? `<line x1="22" y1="${y}" x2="36" y2="${y}"/>` : `<line x1="8" y1="${y}" x2="22" y2="${y}"/>`;
      }
      b.innerHTML = `<svg viewBox="0 0 44 44" aria-hidden="true"><circle class="halo-ring" cx="22" cy="22" r="19"/>${lines}</svg>
        <span class="tree-card" aria-hidden="true"><span class="tgo">${d.title}</span><span class="tname"><span class="oglyph">${d.og}</span>${d.ogname}, the ${d.tree}</span><span class="tlore">${d.lore}</span></span>`;
      b.addEventListener('click', () => ch.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth' }));
      stone.appendChild(b);
    });
    const seen = new Map();
    const io = new IntersectionObserver(es => {
      es.forEach(e => seen.set(e.target.id, e.intersectionRatio));
      let best = null, bv = 0;
      seen.forEach((v, k) => { if (v > bv) { bv = v; best = k; } });
      $$('.notch', stone).forEach(n => n.classList.toggle('active', n.dataset.id === best));
    }, { threshold: [0, .15, .3, .5, .75, 1] });
    chambers.forEach(c => io.observe(c));
  }

  /* ---------- side-notes set in the margin ---------- */
  const margin = $('.margin');
  if (margin) {
    const hint = $('.margin-hint', margin), narrow = () => matchMedia('(max-width: 1000px)').matches;
    const layout = () => {
      if (narrow()) return;
      const mb = margin.getBoundingClientRect();
      let floor = 0;
      $$('.term').forEach(t => {
        const n = document.getElementById(t.getAttribute('aria-controls'));
        if (!n.classList.contains('open')) return;
        const top = Math.max(t.getBoundingClientRect().top - mb.top - 4, floor);
        n.style.top = top + 'px'; floor = top + n.offsetHeight + 20;
      });
      margin.style.minHeight = floor + 'px';
    };
    $$('.term').forEach(t => t.addEventListener('click', () => {
      const n = document.getElementById(t.getAttribute('aria-controls'));
      const open = t.getAttribute('aria-expanded') !== 'true';
      t.setAttribute('aria-expanded', open);
      n.classList.toggle('open', open);
      if (open && narrow()) t.closest('p').after(n);
      else if (!open && n.parentElement !== margin) margin.appendChild(n);
      if (hint) hint.style.opacity = $$('.mnote.open').length ? 0 : 1;
      layout();
    }));
    addEventListener('resize', layout);
  }

  /* ---------- hedgerow plates: the wash bleeds in from where the brush began ---------- */
  $$('.plate[data-ink]').forEach(fig => {
    const canvas = $('canvas', fig), ctx = canvas.getContext('2d');
    const [cr, cg, cb] = fig.dataset.color.split(',').map(Number);
    const [ox, oy] = (fig.dataset.origin || '.5,.5').split(',').map(Number);
    const img = new Image();
    img.onload = () => {
      const scale = Math.min(1, 1000 / img.naturalWidth), w = Math.round(img.naturalWidth * scale), h = Math.round(img.naturalHeight * scale);
      canvas.width = w; canvas.height = h;
      // the reveal mask lives at quarter size and is smoothed up; it never reads the artwork's pixels
      const mw = Math.ceil(w / 4), mh = Math.ceil(h / 4), mask = document.createElement('canvas');
      mask.width = mw; mask.height = mh;
      const mctx = mask.getContext('2d'), mdata = mctx.createImageData(mw, mh), tmap = new Float32Array(mw * mh);
      const nr = mulberry32(7), g = 9, lat = Float32Array.from({ length: (g + 1) * (g + 1) }, nr);
      const maxD = Math.hypot(Math.max(ox, 1 - ox) * mw, Math.max(oy, 1 - oy) * mh);
      for (let y = 0; y < mh; y++) for (let x = 0; x < mw; x++) {
        const fx = x / mw * g, fy = y / mh * g, x0 = fx | 0, y0 = fy | 0, u = fx - x0, v = fy - y0;
        const su = u * u * (3 - 2 * u), sv = v * v * (3 - 2 * v);
        const a = lat[y0 * (g + 1) + x0] + (lat[y0 * (g + 1) + x0 + 1] - lat[y0 * (g + 1) + x0]) * su;
        const b = lat[(y0 + 1) * (g + 1) + x0] + (lat[(y0 + 1) * (g + 1) + x0 + 1] - lat[(y0 + 1) * (g + 1) + x0]) * su;
        tmap[y * mw + x] = Math.hypot(x - ox * mw, y - oy * mh) / maxD * .72 + (a + (b - a) * sv) * .28;
      }
      const paint = p => {
        for (let i = 0; i < tmap.length; i++) mdata.data[i * 4 + 3] = Math.max(0, Math.min(1, (p - tmap[i]) / .14)) * 255;
        mctx.putImageData(mdata, 0, 0);
        ctx.globalCompositeOperation = 'source-over'; ctx.clearRect(0, 0, w, h);
        ctx.drawImage(img, 0, 0, w, h);
        ctx.globalCompositeOperation = 'source-in'; ctx.fillStyle = `rgb(${cr},${cg},${cb})`; ctx.fillRect(0, 0, w, h);
        if (p < 1.2) { ctx.globalCompositeOperation = 'destination-in'; ctx.imageSmoothingEnabled = true; ctx.drawImage(mask, 0, 0, w, h); }
        ctx.globalCompositeOperation = 'source-over';
      };
      const reveal = () => {
        if (reduce) { paint(2); return; }
        const t0 = performance.now(), dur = 3600;
        const step = t => { const k = Math.min(1, (t - t0) / dur), e = k < .5 ? 2 * k * k : 1 - Math.pow(-2 * k + 2, 2) / 2; paint(-.15 + e * 1.4); if (k < 1) requestAnimationFrame(step); else paint(2); };
        requestAnimationFrame(step);
      };
      if (whenSeen(fig, reveal, .35)) paint(-1);
    };
    img.src = fig.dataset.ink;

    const labelG = $$('.labels > g', fig), items = $$('.legend li', fig.parentElement);
    if (!labelG.length) return;
    const show = (k, on) => {
      labelG.forEach(g => { if (k === '*' || g.dataset.k === k) g.classList.toggle('show', on); });
      items.forEach(li => { if (k === '*' || li.dataset.k === k) li.classList.toggle('lit', on); });
    };
    const art = $('.art', fig);
    art.tabIndex = 0;
    art.addEventListener('pointerenter', () => show('*', true));
    art.addEventListener('pointerleave', () => show('*', false));
    art.addEventListener('focus', () => show('*', true));
    art.addEventListener('blur', () => show('*', false));
    art.addEventListener('click', () => show('*', !labelG.some(g => g.classList.contains('show'))));
    items.forEach(li => {
      ['pointerenter', 'focus'].forEach(ev => li.addEventListener(ev, () => show(li.dataset.k, true)));
      ['pointerleave', 'blur'].forEach(ev => li.addEventListener(ev, () => show(li.dataset.k, false)));
    });
  });

  /* ---------- letters by moonlight: the ledger ---------- */
  const ledger = $('.ledger');
  if (ledger) {
    const fm = nextFullMoon(todayN), when = $('.when-moon', ledger);
    if (when) when.textContent = ' on ' + fmtDate(fm, false);
    ledger.addEventListener('submit', e => {
      e.preventDefault();
      const input = $('input', ledger), err = $('.err', ledger), done = $('.done', ledger);
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim())) { err.hidden = false; input.focus(); return; }
      err.hidden = true;
      done.textContent = `Your name is in the ledger. The first letter comes with the full moon on ${fmtDate(fm, false)}.`;
      done.hidden = false; requestAnimationFrame(() => done.classList.add('shown'));
    });
  }

  /* ---------- gatherings: how far away ---------- */
  $$('.gathering[data-date]').forEach(g => {
    const [y, m, d] = g.dataset.date.split('-').map(Number), n = dn(y, m, d) - todayN, el = $('.away', g);
    if (el) el.textContent = n === 0 ? 'Tonight' : n > 0 ? `In ${num(n)} night${n === 1 ? '' : 's'}` : 'Past';
  });

  /* ---------- the volvelle ---------- */
  const vol = $('#vol');
  if (vol) {
    const wheelStart = n => { const y = ymd(n)[0]; let s = dn(y, 10, 31); if (n < s) s = dn(y - 1, 10, 31); return s; };
    const wheelLen = s => dn(ymd(s)[0] + 1, 10, 31) - s;
    const angleOf = n => { const s = wheelStart(n); return (n - s) / wheelLen(s) * 360 - 90; };
    const fests = [
      { name: 'Samhain', say: 'said SOW-in', m: 10, d: 31, fire: true, lore: "Summer's end, and the start of the dark half of the year. Cattle came down from the summer pastures, and the dead were said to walk abroad.", hedge: 'Sloes after the first frost, hazelnuts, the last rowan berries. Leave the blackberries to the púca.' },
      { name: 'Winter Solstice', say: 'about 21 December', m: 12, d: 21, lore: 'The longest night. At Newgrange in the Boyne Valley, built around 3200 BCE, the rising sun reaches down a passage into the inner chamber for a few mornings around the solstice.', hedge: 'Holly and ivy, bare hawthorn, rosehips holding on.' },
      { name: 'Imbolc', say: 'said IM-bolk', m: 2, d: 1, fire: true, lore: "Brigid's day. The ewes come into milk, and Brigid's crosses are woven from rushes and hung over the door for the year.", hedge: 'Snowdrops, hazel catkins, the first nettles under the hedge.' },
      { name: 'Spring Equinox', say: 'about 20 March', m: 3, d: 20, lore: "Day and night in balance. No great Gaelic festival falls here. The name Ostara comes from 20th-century Wicca, borrowing Bede's brief mention of a goddess Eostre.", hedge: 'Blackthorn blossom before its leaves, wild garlic, primroses.' },
      { name: 'Beltaine', say: 'said BELL-tane', m: 5, d: 1, fire: true, lore: 'Bright fire. Cattle were driven between two bonfires for protection before going up to summer pasture. The hawthorn, "the May", blossoms now.', hedge: 'Hawthorn flower, bluebells, nettle tops for soup.' },
      { name: 'Summer Solstice', say: 'about 21 June', m: 6, d: 21, lore: "The longest day. In Ireland the midsummer fires are lit on St John's Eve, 23 June, a custom still kept in many places.", hedge: 'Elderflower, meadowsweet, dog roses.' },
      { name: 'Lughnasadh', say: 'said LOO-na-sa', m: 8, d: 1, fire: true, lore: 'The festival of the god Lugh and the first harvest. People climbed hills and holy mountains to gather bilberries and celebrate.', hedge: 'The first blackberries, bilberries (fraughans), meadowsweet going to seed.' },
      { name: 'Autumn Equinox', say: 'about 22 September', m: 9, d: 22, lore: 'Balance again, now tipping toward the dark. The name "Mabon" was coined in the 1970s; the older Irish year marks no festival here.', hedge: 'Blackberries, elderberries, rosehips, rowan and haws.' }
    ];
    const occ = (f, n) => { const y = ymd(n)[0]; return [y - 1, y, y + 1].map(yy => dn(yy, f.m, f.d)); };
    const lastFest = n => fests.map(f => ({ f, at: Math.max(...occ(f, n).filter(x => x <= n)) })).sort((a, b) => b.at - a.at)[0];
    const nextFest = n => fests.map(f => ({ f, at: Math.min(...occ(f, n).filter(x => x > n)) })).sort((a, b) => a.at - b.at)[0];

    const C = 500, rad = a => a * Math.PI / 180;
    const P = (r, a, cx = C, cy = C) => [+(cx + r * Math.cos(rad(a))).toFixed(2), +(cy + r * Math.sin(rad(a))).toFixed(2)];
    const circ = (r, cls, cx = C, cy = C) => `<circle class="${cls}" cx="${cx}" cy="${cy}" r="${r}"/>`;
    let uid = 0;
    const arcText = (txt, r, a1, a2, fs, cls) => {
      const mid = (a1 + a2) / 2, bottom = Math.sin(rad(mid)) > .05, id = 'at' + (uid++), large = a2 - a1 > 180 ? 1 : 0;
      let d;
      if (bottom) { const rr = r + fs * .7, [x1, y1] = P(rr, a2), [x2, y2] = P(rr, a1); d = `M${x1} ${y1}A${rr} ${rr} 0 ${large} 0 ${x2} ${y2}`; }
      else { const [x1, y1] = P(r, a1), [x2, y2] = P(r, a2); d = `M${x1} ${y1}A${r} ${r} 0 ${large} 1 ${x2} ${y2}`; }
      return `<path id="${id}" d="${d}" fill="none"/><text class="${cls}" font-size="${fs}"><textPath href="#${id}" startOffset="50%" text-anchor="middle">${txt}</textPath></text>`;
    };
    const rose = (cx, cy, r) => {
      let s = circ(r + 7, 'hl-f', cx, cy) + circ(r + 11, 'hl', cx, cy);
      for (let i = 0; i < 64; i++) { const [a, b] = P(r + 7, i * 5.625, cx, cy), [c, d] = P(r + (i % 2 ? 9 : 11), i * 5.625, cx, cy); s += `<line class="tick" x1="${a}" y1="${b}" x2="${c}" y2="${d}"/>`; }
      [3, 2, 1].forEach(lvl => {
        for (let i = 0; i < 16; i++) {
          const kind = i % 4 === 0 ? 1 : i % 2 === 0 ? 2 : 3;
          if (kind !== lvl) continue;
          const a = i * 22.5 - 90, len = [0, r, r * .66, r * .42][kind], hw = [0, r * .13, r * .1, r * .08][kind];
          const [tx, ty] = P(len, a, cx, cy), [lx, ly] = P(hw, a - 90, cx, cy), [rx, ry] = P(hw, a + 90, cx, cy);
          s += `<path class="rose-fill" d="M${cx} ${cy}L${tx} ${ty}L${lx} ${ly}Z"/><path class="hl" style="fill:var(--obsidian)" d="M${cx} ${cy}L${tx} ${ty}L${rx} ${ry}Z"/><path class="hl" d="M${lx} ${ly}L${tx} ${ty}L${rx} ${ry}"/>`;
        }
      });
      return s + circ(r * .07, 'hl', cx, cy);
    };
    const gear = (n, ro, rr) => {
      const st = 360 / n; let d = '';
      for (let i = 0; i < n; i++) {
        const a = i * st, pts = [P(rr, a - st * .3, 0, 0), P(ro, a - st * .16, 0, 0), P(ro, a + st * .16, 0, 0), P(rr, a + st * .3, 0, 0)];
        d += (i ? 'L' : 'M') + pts.map(p => p.join(' ')).join('L');
      }
      let s = `<path class="hl" style="fill:var(--obsidian)" d="${d}Z"/>` + circ(rr * .72, 'hl-f', 0, 0) + circ(rr * .2, 'hl', 0, 0);
      for (let i = 0; i < 5; i++) { const [a, b] = P(rr * .2, i * 72, 0, 0), [c, e] = P(rr * .72, i * 72, 0, 0); s += `<line class="hl-f" x1="${a}" y1="${b}" x2="${c}" y2="${e}"/>`; }
      return s;
    };
    const moonGlyph = (age, r = 11) => {
      const a = age / SYN, id = 'mg' + (uid++);
      let clip = '';
      if (a < .1) clip = `<rect x="${-r}" y="${-r}" width="${2 * r}" height="${2 * r}"/>`;
      else if (a > .2 && a < .3) clip = `<rect x="${-r}" y="${-r}" width="${r}" height="${2 * r}"/>`;
      else if (a > .7 && a < .8) clip = `<rect x="0" y="${-r}" width="${r}" height="${2 * r}"/>`;
      let hatch = '';
      for (let x = -r; x <= r; x += 2.6) hatch += `<line class="hatch" x1="${x}" y1="${-r}" x2="${x + 4}" y2="${r}"/>`;
      let rays = '';
      if (a > .45 && a < .55) for (let i = 0; i < 12; i++) { const [p1, q1] = P(r + 3, i * 30, 0, 0), [p2, q2] = P(r + 7, i * 30, 0, 0); rays += `<line class="tick" x1="${p1}" y1="${q1}" x2="${p2}" y2="${q2}"/>`; }
      return `<clipPath id="${id}"><circle r="${r}"/></clipPath>${clip ? `<g clip-path="url(#${id})"><clipPath id="${id}b">${clip}</clipPath><g clip-path="url(#${id}b)">${hatch}</g></g>` : ''}${circ(r, 'hl', 0, 0)}${rays}`;
    };

    const R = { plate: 480, insO: 470, insI: 438, dayI: 424, day5: 419, mon: 410, monI: 364, fest: 360, festI: 318, lunO: 312, lunI: 232, sq: 226, win: 150 };
    const S0 = wheelStart(todayN), L0 = wheelLen(S0), inWheel = (m, d) => { let n = dn(ymd(S0)[0], m, d); if (n < S0) n = dn(ymd(S0)[0] + 1, m, d); return n; };
    const ang0 = n => (n - S0) / L0 * 360 - 90;
    let s = `<defs>
      <filter id="vhalo" x="-30%" y="-30%" width="160%" height="160%"><feGaussianBlur in="SourceGraphic" stdDeviation="3" result="b"/><feColorMatrix in="b" values="1 0 0 0 .1  0 1 0 0 .08  0 0 1 0 0  0 0 0 .9 0" result="g"/><feMerge><feMergeNode in="g"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
      <radialGradient id="winGlow"><stop offset=".6" stop-color="#6B5A8E" stop-opacity=".22"/><stop offset=".92" stop-color="#E2C27F" stop-opacity=".06"/><stop offset="1" stop-color="#E2C27F" stop-opacity="0"/></radialGradient>
    </defs>`;
    s += `<rect class="hl" x="12" y="12" width="976" height="976"/><rect class="hl-f" x="22" y="22" width="956" height="956"/>`;
    s += rose(92, 92, 56) + rose(908, 92, 56) + rose(92, 908, 56);
    s += `<g id="gearA">${gear(18, 50, 44)}</g><g id="gearB">${gear(10, 30, 24)}</g>`;
    s += circ(R.plate, 'hl-f') + circ(R.insO, 'hl') + circ(R.insI, 'hl');
    s += arcText('A wheel for finding in any season the moon, the old feasts &amp; the hedgerow', R.insI + 9, -172, -8, 20, 'eng');
    s += arcText('Set for the Gaelic year, which begins at Samhain', R.insI + 7, 8, 172, 20, 'eng');
    s += circ(R.dayI - 14, 'hl-f');
    for (let i = 0; i < L0; i++) {
      const n = S0 + i, a = ang0(n), dd = ymd(n)[2], isM = dd === 1, is5 = dd % 5 === 0;
      const [x1, y1] = P(R.insI, a), [x2, y2] = P(isM ? R.mon : is5 ? R.day5 : R.dayI, a);
      s += `<line class="${isM ? 'tick-m' : 'tick'}" x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}"/>`;
    }
    s += circ(R.mon, 'hl') + circ(R.monI, 'hl');
    for (let m = 1; m <= 12; m++) {
      const a1 = ang0(inWheel(m, 1)), end = inWheel(m, 1) + new Date(Date.UTC(2001, m, 0)).getUTCDate();
      let a2 = ang0(end); if (a2 < a1) a2 += 360;
      s += arcText(MONTHS[m - 1], R.monI + 15, a1 + 1, a2 - 1, 19, 'eng');
    }
    s += circ(R.festI, 'hl-f');
    fests.forEach((f, i) => {
      const a = ang0(inWheel(f.m, f.d)), [x, y] = P(R.fest, a), lz = f.fire ? 7 : 5;
      s += `<g class="fest" data-i="${i}"><path class="fest-mark${f.fire ? ' fire' : ''}" d="M${x} ${y - lz}L${x + lz * .7} ${y}L${x} ${y + lz}L${x - lz * .7} ${y}Z" transform="rotate(${a + 90} ${x} ${y})"/>${arcText(f.name, R.festI + 13, a - 20, a + 20, 16, 'fest-name')}</g>`;
    });
    let lun = circ(R.lunO, 'hl') + circ(R.lunI, 'hl') + circ(R.lunO - 40, 'hl-f');
    for (let k = 0; k < 30; k++) {
      const a = -90 + k / SYN * 360, [x1, y1] = P(R.lunO, a), [x2, y2] = P(R.lunO - (k % 5 === 0 ? 14 : 9), a);
      lun += `<line class="${k % 5 === 0 ? 'tick-m' : 'tick'}" x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}"/>`;
      if (k > 0) { const [tx, ty] = P(R.lunO - 27, a); lun += `<text class="fig" font-size="17" x="${tx}" y="${ty}" text-anchor="middle" dominant-baseline="central" transform="rotate(${a + 90} ${tx} ${ty})">${k}</text>`; }
    }
    [0, SYN / 4, SYN / 2, SYN * 3 / 4].forEach(age => { const a = -90 + age / SYN * 360, [x, y] = P(R.lunI + 20, a); lun += `<g transform="translate(${x} ${y}) rotate(${a + 90})">${moonGlyph(age)}</g>`; });
    lun += arcText('the age of the moon', R.lunI + 12, 106, 164, 14, 'eng-i');
    s += `<g id="lunar">${lun}</g>`;
    let sq = '';
    [0, 45].forEach(rot => { const pts = [0, 90, 180, 270].map(a => P(R.sq, a + rot + 45)); sq += `<path class="hl-f" d="M${pts.map(p => p.join(' ')).join('L')}Z"/>`; });
    for (let i = 0; i < 8; i++) { const [x, y] = P(R.sq - 36, i * 45 + 22.5); sq += `<circle cx="${x}" cy="${y}" r="1.4" style="fill:var(--gilt)"/>`; }
    s += sq + `<circle cx="${C}" cy="${C}" r="${R.win + 12}" fill="url(#winGlow)"/>` + circ(R.win, 'hl glow') + circ(R.win - 6, 'hl-f');
    const moonSrc = vol.dataset.moon;
    if (moonSrc) s += `<clipPath id="winClip"><circle cx="${C}" cy="${C}" r="${R.win - 7}"/></clipPath><image href="${moonSrc}" x="${C - R.win + 7}" y="${C - R.win + 7}" width="${2 * (R.win - 7)}" height="${2 * (R.win - 7)}" clip-path="url(#winClip)"/>`;
    let ptr = `<path class="hl" style="fill:rgba(184,146,79,.18)" d="M${C - 4} ${C}L${C - 1.2} ${C - 400}L${C} ${C - 432}L${C + 1.2} ${C - 400}L${C + 4} ${C}Z"/>`;
    ptr += `<path class="hl" style="fill:var(--obsidian)" d="M${C} ${C - 232}L${C + 11} ${C - 200}L${C} ${C - 168}L${C - 11} ${C - 200}Z"/><circle class="hl" cx="${C}" cy="${C - 200}" r="4"/>`;
    ptr += `<path class="hl" d="M${C} ${C - 432}L${C} ${C - 470}"/><circle class="hl" cx="${C}" cy="${C - 452}" r="3"/>`;
    ptr += `<path class="hl" style="fill:rgba(184,146,79,.12)" d="M${C - 3} ${C}L${C} ${C + 70}L${C + 3} ${C}Z"/><circle class="hl" cx="${C}" cy="${C + 76}" r="6"/>`;
    ptr += `<rect class="grip" x="${C - 34}" y="${C - 480}" width="68" height="120"/>`;
    s += `<g id="ptr" class="glow">${ptr}</g>`;
    let hub = circ(24, 'hl') + circ(17, 'hl-f');
    for (let i = 0; i < 16; i++) { const [x1, y1] = P(5, i * 22.5), [x2, y2] = P(i % 2 ? 15 : 22, i * 22.5); hub += `<line class="tick" x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}"/>`; }
    s += hub + `<circle cx="${C}" cy="${C}" r="2.4" style="fill:var(--gilt)"/>`;
    vol.innerHTML = s;

    const ptrG = $('#ptr'), lunarG = $('#lunar'), gA = $('#gearA'), gB = $('#gearB'), festGs = $$('.fest', vol), el = id => document.getElementById(id);
    const phaseOf = age => { const p = age / SYN; return p < .0339 || p > .9661 ? 'new' : p < .216 ? 'a waxing crescent' : p < .284 ? 'at first quarter' : p < .466 ? 'waxing gibbous' : p < .534 ? 'full' : p < .716 ? 'waning gibbous' : p < .784 ? 'at last quarter' : 'a waning crescent'; };
    const theName = f => /Equinox|Solstice/.test(f.name) ? 'the ' + f.name.toLowerCase() : f.name;
    let shown = null;
    const setDay = n => {
      if (n === shown) return;
      shown = n;
      const a = angleOf(n), age = moonAge(n);
      ptrG.setAttribute('transform', `rotate(${(a + 90).toFixed(3)} ${C} ${C})`);
      lunarG.setAttribute('transform', `rotate(${(a + 90 - age / SYN * 360).toFixed(3)} ${C} ${C})`);
      gA.setAttribute('transform', `translate(893 885) rotate(${(n * 5) % 360})`);
      gB.setAttribute('transform', `translate(827 923) rotate(${(-n * 5 * 1.8 + 18) % 360})`);
      const lf = lastFest(n), nf = nextFest(n), since = n - lf.at, until = nf.at - n;
      festGs.forEach(g => g.classList.toggle('near', +g.dataset.i === fests.indexOf(nf.f) || (since === 0 && +g.dataset.i === fests.indexOf(lf.f))));
      const lit = Math.round((1 - Math.cos(2 * Math.PI * age / SYN)) / 2 * 100), dayOf = Math.floor(age) + 1;
      el('rDate').textContent = fmtDate(n);
      const off = n - todayN;
      el('rRel').textContent = off === 0 ? 'Today' : off > 0 ? `${cap(num(off))} day${off === 1 ? '' : 's'} from today` : `${cap(num(-off))} day${off === -1 ? '' : 's'} ago`;
      el('rMoon').textContent = `The moon is ${phaseOf(age)}, on the ${dayOf}${['th', 'st', 'nd', 'rd'][(dayOf % 100 > 10 && dayOf % 100 < 14) || dayOf % 10 > 3 ? 0 : dayOf % 10]} day of its 29½, with ${lit} parts in a hundred lit.`;
      el('rYear').textContent = (since === 0 ? `This is ${theName(lf.f)}` : `${cap(num(since))} day${since === 1 ? '' : 's'} past ${theName(lf.f)}`) + `, and ${until} night${until === 1 ? '' : 's'} to ${nf.f.name}.`;
      el('rFest').textContent = nf.f.name; el('rSay').textContent = nf.f.say;
      el('rLore').textContent = nf.f.lore; el('rHedge').textContent = lf.f.hedge;
      vol.setAttribute('aria-valuetext', fmtDate(n)); vol.setAttribute('aria-valuenow', String(n));
    };
    let winding = 0;
    const windTo = target => {
      target = Math.round(target);
      if (reduce || shown === null) { setDay(target); return; }
      const from = shown, dur = Math.min(2800, 500 + Math.abs(target - from) * 7), t0 = performance.now(), id = ++winding;
      const step = t => { if (id !== winding) return; const k = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - k, 3); setDay(Math.round(from + (target - from) * e)); if (k < 1) requestAnimationFrame(step); };
      requestAnimationFrame(step);
    };
    vol.setAttribute('aria-valuemin', String(todayN - 3650)); vol.setAttribute('aria-valuemax', String(todayN + 3650));
    if (whenSeen(vol, () => windTo(todayN), .3)) setDay(todayN - 47); else setDay(todayN);

    let dragging = false, lastFrac = null, base = null;
    const fracAt = e => {
      const pt = vol.createSVGPoint(); pt.x = e.clientX; pt.y = e.clientY;
      const p = pt.matrixTransform(vol.getScreenCTM().inverse());
      return { frac: ((Math.atan2(p.y - C, p.x - C) * 180 / Math.PI + 90) / 360 + 1) % 1, r: Math.hypot(p.x - C, p.y - C) };
    };
    const turn = e => {
      const { frac } = fracAt(e);
      if (lastFrac !== null) { if (frac - lastFrac > .5) base = wheelStart(base - 1); else if (lastFrac - frac > .5) base = base + wheelLen(base); }
      lastFrac = frac; setDay(Math.round(base + frac * wheelLen(base)));
    };
    vol.addEventListener('pointerdown', e => {
      if (e.pointerType === 'touch' && !e.target.classList.contains('grip')) return;
      const { r } = fracAt(e); if (r < 60 || r > 490) return;
      dragging = true; winding++; vol.classList.add('dragging'); vol.setPointerCapture(e.pointerId);
      base = wheelStart(shown); lastFrac = null; turn(e);
    });
    vol.addEventListener('pointermove', e => { if (dragging) turn(e); });
    ['pointerup', 'pointercancel'].forEach(ev => vol.addEventListener(ev, () => { dragging = false; vol.classList.remove('dragging'); }));
    vol.addEventListener('keydown', e => {
      const st = e.shiftKey ? 7 : 1, map = { ArrowRight: st, ArrowUp: st, ArrowLeft: -st, ArrowDown: -st, PageUp: 30, PageDown: -30 };
      if (e.key in map) { e.preventDefault(); winding++; setDay(shown + map[e.key]); }
      else if (e.key === 'Home') { e.preventDefault(); windTo(todayN); }
    });
    const winders = el('winders');
    fests.forEach(f => {
      const b = document.createElement('button'); b.type = 'button'; b.className = 'textbtn'; b.textContent = f.name;
      b.addEventListener('click', () => windTo(occ(f, shown).find(x => x > shown) ?? shown));
      winders.appendChild(b);
    });
    el('wBack').addEventListener('click', () => windTo(shown - 7));
    el('wFwd').addEventListener('click', () => windTo(shown + 7));
    el('wToday').addEventListener('click', () => windTo(todayN));
  }
})();
