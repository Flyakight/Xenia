/**
 * Who Held the Glass — interactive wine timeline.
 *
 * Draws the hemisphere chart as inline SVG, moves a gooey wine blob between
 * event markers, and opens citation popovers for every event.
 * Data arrives as window.tannicGlassData (inlined from data/who-held-the-glass.json).
 *
 * @package TannicStudio
 */

(function () {
  'use strict';

  var DATA = window.tannicGlassData;
  var root = document.getElementById('whg-board');
  if (!DATA || !root) return;

  var NS = 'http://www.w3.org/2000/svg';
  var HEMIS = ['N', 'S'];
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  var $ = function (id) { return document.getElementById(id); };
  var chartEl = $('whg-chart');
  var detailEl = $('whg-detail');
  var legendEl = $('whg-legend');
  var toggleEl = root.querySelector('.whg-toggle');
  var pop = $('whg-pop');
  var popBody = $('whg-pop-body');
  var popTitle = $('whg-pop-title');

  var state = { hemi: 'N', sel: 0, spotlight: null };
  var layout = null;     // positions from the last draw
  var popTrigger = null; // element that opened the popover

  // Sources are numbered once per hemisphere, in timeline order, so the
  // numbers on the chart match the cellar book below.
  HEMIS.forEach(function (h) {
    var n = 0;
    DATA.hemispheres[h].events.forEach(function (ev) {
      ev.sources.forEach(function (src) { src.n = ++n; });
    });
  });

  function fmtYear(y) { return y < 0 ? (-y) + ' BC' : String(y); }
  function pad(n) { return n < 10 ? '0' + n : String(n); }
  function el(tag, attrs, parent) {
    var node = document.createElementNS(NS, tag);
    for (var k in attrs) node.setAttribute(k, attrs[k]);
    if (parent) parent.appendChild(node);
    return node;
  }
  function hemi() { return DATA.hemispheres[state.hemi]; }


  /* ---------------------------------------------------------------------
     Legend
     --------------------------------------------------------------------- */

  function buildLegend() {
    legendEl.innerHTML = '';
    hemi().series.forEach(function (s, i) {
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'whg-chip';
      b.style.setProperty('--chip-color', 'var(--whg-s' + (i + 1) + ')');
      b.setAttribute('aria-pressed', 'false');
      b.innerHTML =
        '<svg width="28" height="8" aria-hidden="true"><line x1="1" x2="27" y1="4" y2="4" ' +
        'style="stroke:var(--whg-s' + (i + 1) + ')" stroke-width="2.6" stroke-linecap="round" ' +
        'stroke-dasharray="' + DATA.meta.dash[i] + '"/></svg><span></span>';
      b.querySelector('span').textContent = s.name;
      b.addEventListener('click', function () {
        state.spotlight = state.spotlight === i ? null : i;
        applySpotlight();
      });
      legendEl.appendChild(b);
    });
    applySpotlight();
  }

  function applySpotlight() {
    var on = state.spotlight !== null;
    legendEl.classList.toggle('has-spotlight', on);
    chartEl.classList.toggle('has-spotlight', on);
    legendEl.querySelectorAll('.whg-chip').forEach(function (b, i) {
      b.setAttribute('aria-pressed', String(state.spotlight === i));
    });
    chartEl.querySelectorAll('.whg-series').forEach(function (p, i) {
      p.classList.toggle('is-lit', state.spotlight === i);
    });
  }


  /* ---------------------------------------------------------------------
     Chart
     --------------------------------------------------------------------- */

  function draw(pour) {
    var d = hemi();
    var bk = d.breakpoints;
    var n = bk.length - 1;
    var W = chartEl.clientWidth;
    var small = W < 560;
    var L = small ? 30 : 40, R = 12, T = 14, PH = small ? 230 : 330;
    var r = small ? 11 : 13;

    var X = function (y) {
      for (var i = 0; i < n; i++) {
        if (y <= bk[i + 1]) return L + (i + (y - bk[i]) / (bk[i + 1] - bk[i])) / n * (W - L - R);
      }
      return W - R;
    };
    var Y = function (v) { return T + PH - v / 100 * PH; };

    // Pack markers into rows: each takes the first row it clears.
    var gap = 2 * r + 4;
    var rowsEnd = [];
    var place = d.events.map(function (ev) {
      var x = X(ev.year);
      var row = rowsEnd.findIndex(function (v) { return v + gap <= x; });
      if (row < 0) { row = rowsEnd.length; rowsEnd.push(-1e9); }
      rowsEnd[row] = x;
      return { x: x, row: row };
    });

    var axisY = T + PH + 22;
    var evTop = axisY + 24;
    var rowH = 2 * r + 8;
    var H = evTop + rowsEnd.length * rowH + 6;
    place.forEach(function (p) { p.y = evTop + p.row * rowH + r; });

    var svg = el('svg', {
      viewBox: '0 0 ' + W + ' ' + H, width: W, height: H, role: 'img',
      'aria-label': 'Line chart of estimated wine market influence by country in the ' +
        d.label + ' Hemisphere, with numbered historical events below the time axis'
    });

    [0, 25, 50, 75, 100].forEach(function (v) {
      el('line', { class: 'whg-grid', x1: L, x2: W - R, y1: Y(v), y2: Y(v) }, svg);
      if (v % 50 === 0) {
        el('text', { class: 'whg-axis-label', x: L - 10, y: Y(v) + 4, 'text-anchor': 'end' }, svg).textContent = v;
      }
    });

    bk.forEach(function (b, i) {
      if (!small || (i % 2 === 0 && i < n - 1) || i === n) {
        el('text', { class: 'whg-axis-label', x: X(b), y: axisY, 'text-anchor': 'middle' }, svg).textContent = fmtYear(b);
      }
    });

    d.events.forEach(function (ev, i) {
      el('line', { class: 'whg-guide', x1: place[i].x, x2: place[i].x, y1: T, y2: T + PH }, svg);
      el('line', { class: 'whg-stem', x1: place[i].x, x2: place[i].x, y1: T + PH, y2: place[i].y - r }, svg);
    });

    var activeGuide = el('line', { class: 'whg-guide-active', x1: 0, x2: 0, y1: T, y2: T + PH, pathLength: 1, 'stroke-dasharray': 1 }, svg);

    var wrap = el('g', { class: 'whg-series-wrap' }, svg);
    d.series.forEach(function (s, i) {
      var pl = el('polyline', {
        class: 'whg-series',
        style: 'stroke:var(--whg-s' + (i + 1) + ')',
        'stroke-dasharray': DATA.meta.dash[i],
        points: s.points.map(function (q) { return X(q[0]).toFixed(1) + ',' + Y(q[1]).toFixed(1); }).join(' ')
      }, wrap);
      el('title', {}, pl).textContent = s.name;
    });

    // The gooey layer: a lead blob, a lagging trail and a falling drip.
    var goo = el('g', { filter: 'url(#whg-goo)' }, svg);
    var trail = el('circle', { class: 'whg-blob whg-blob--trail', r: r * 0.7 }, goo);
    var blob = el('circle', { class: 'whg-blob', r: r + 1.5 }, goo);
    var drip = el('circle', { class: 'whg-drip', r: r * 0.6, style: 'opacity:0' }, goo);

    var marks = el('g', {}, svg);
    d.events.forEach(function (ev, i) {
      var p = place[i];
      var g = el('g', {
        class: 'whg-ev', 'data-i': i, role: 'button', tabindex: 0,
        'aria-label': fmtYear(ev.year) + ': ' + ev.title
      }, marks);
      el('circle', { class: 'whg-ev-hit', cx: p.x, cy: p.y, r: r + 4 }, g);
      el('circle', { class: 'whg-marker', cx: p.x, cy: p.y, r: r }, g);
      el('circle', { class: 'whg-ev-ring', cx: p.x, cy: p.y, r: r + 4 }, g);
      el('text', { class: 'whg-ev-num', x: p.x, y: p.y + 4, 'text-anchor': 'middle' }, g).textContent = i + 1;

      g.addEventListener('click', function () { select(i); });
      g.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); select(i); }
        if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
          e.preventDefault();
          select(i + (e.key === 'ArrowRight' ? 1 : -1));
          focusMarker(state.sel);
        }
      });
    });

    chartEl.innerHTML = '';
    chartEl.appendChild(svg);

    layout = { place: place, T: T, PH: PH, r: r, blob: blob, trail: trail, drip: drip, guide: activeGuide };

    applySpotlight();
    moveBlob(true);

    if (pour && !reduceMotion.matches) {
      requestAnimationFrame(function () {
        requestAnimationFrame(function () { wrap.classList.add('is-poured'); });
      });
    } else {
      wrap.classList.add('is-poured');
    }
  }

  function focusMarker(i) {
    var g = chartEl.querySelector('.whg-ev[data-i="' + i + '"]');
    if (g) g.focus();
  }

  // Slide the blob to the selected marker, then let a drop fall down the
  // guide line and melt into it.
  function moveBlob(instant) {
    if (!layout) return;
    var p = layout.place[state.sel];
    var t = 'translate(' + p.x + 'px,' + p.y + 'px)';

    chartEl.classList.toggle('is-instant', !!instant);
    layout.blob.style.transform = t;
    layout.trail.style.transform = t;
    if (instant) {
      // Commit the jump before transitions come back on.
      void layout.blob.getBoundingClientRect();
      requestAnimationFrame(function () { chartEl.classList.remove('is-instant'); });
    }

    chartEl.querySelectorAll('.whg-ev').forEach(function (g, i) {
      g.classList.toggle('is-on', i === state.sel);
    });

    var guide = layout.guide;
    guide.setAttribute('x1', p.x);
    guide.setAttribute('x2', p.x);

    if (reduceMotion.matches || !guide.animate) {
      guide.style.strokeDashoffset = 0;
      return;
    }

    guide.animate(
      [{ strokeDashoffset: 1 }, { strokeDashoffset: 0 }],
      { duration: 700, easing: 'cubic-bezier(.65,0,.35,1)', fill: 'forwards' }
    );

    var at = function (y, k) { return 'translate(' + p.x + 'px,' + y + 'px) scale(' + k + ')'; };
    layout.drip.animate(
      [
        { transform: at(layout.T, 0.2), opacity: 1 },
        { transform: at(layout.T + layout.PH - 30, 0.75), opacity: 1, offset: 0.55 },
        { transform: at(p.y, 1), opacity: 1 }
      ],
      { duration: 900, delay: 150, easing: 'cubic-bezier(.55,0,.8,.4)', fill: 'none' }
    );
  }


  /* ---------------------------------------------------------------------
     Detail card
     --------------------------------------------------------------------- */

  function renderDetail() {
    var d = hemi();
    var ev = d.events[state.sel];

    $('whg-count').textContent = pad(state.sel + 1) + ' / ' + pad(d.events.length);
    $('whg-year').textContent = fmtYear(ev.year);
    $('whg-event-title').textContent = ev.title;
    $('whg-text').textContent = ev.text;

    var cites = $('whg-cites');
    cites.innerHTML = '';
    ev.sources.forEach(function (src, k) {
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'whg-cite whg-cite--num';
      b.setAttribute('aria-expanded', 'false');
      b.setAttribute('aria-controls', 'whg-pop');
      b.setAttribute('aria-label', 'Source ' + src.n + ': ' + src.pub);
      b.innerHTML = '<span>' + src.n + '</span>';
      b.addEventListener('click', function () {
        if (popTrigger === b) { closePop(); return; }
        openEventSources(ev, k, b);
      });
      cites.appendChild(b);
    });

    // The meter glass fills as you move forward in time.
    var level = (state.sel + 1) / d.events.length;
    detailEl.querySelector('.whg-meter').style.setProperty('--whg-level', (104 - level * 82).toFixed(1) + 'px');

    if (!reduceMotion.matches) {
      detailEl.classList.remove('is-pouring');
      void detailEl.offsetWidth;
      detailEl.classList.add('is-pouring');
    }
  }

  function select(i) {
    var len = hemi().events.length;
    state.sel = (i + len) % len;
    closePop();
    moveBlob(false);
    renderDetail();
  }

  function setHemi(h, pour) {
    state.hemi = h;
    state.sel = 0;
    state.spotlight = null;
    closePop();
    toggleEl.setAttribute('data-active', h);
    toggleEl.querySelectorAll('.whg-toggle-btn').forEach(function (b) {
      b.setAttribute('aria-pressed', String(b.dataset.hemi === h));
    });
    buildLegend();
    draw(pour !== false);
    renderDetail();
  }


  /* ---------------------------------------------------------------------
     Citation popover
     --------------------------------------------------------------------- */

  function sourceCard(src, focused) {
    var a = document.createElement('a');
    a.className = 'whg-source' + (focused ? ' is-focus' : '');
    a.href = src.url;
    a.target = '_blank';
    a.rel = 'noopener';
    a.innerHTML =
      '<span class="whg-source-pub"></span>' +
      '<span class="whg-source-title"></span>' +
      '<span class="whg-source-go">Read the source &nearr;</span>';
    a.querySelector('.whg-source-pub').textContent = (src.n ? src.n + ' · ' : '') + src.pub;
    a.querySelector('.whg-source-title').textContent = src.title;
    return a;
  }

  function openEventSources(ev, focusIdx, trigger) {
    popTitle.textContent = 'Sources · ' + fmtYear(ev.year);
    popBody.innerHTML = '';
    ev.sources.forEach(function (src, k) { popBody.appendChild(sourceCard(src, k === focusIdx)); });

    var more = document.createElement('a');
    more.className = 'whg-pop-more';
    more.href = '#whg-src-' + state.hemi + '-' + state.sel;
    more.textContent = 'See it in the cellar book';
    more.addEventListener('click', function (e) {
      e.preventDefault();
      closePop(true);
      flashEntry(state.hemi, state.sel);
    });
    popBody.appendChild(more);

    openPop(trigger, popBody.children[focusIdx]);
  }

  function openMethod(trigger) {
    popTitle.textContent = 'How we scored this';
    popBody.innerHTML = '';
    [DATA.meta.index, DATA.meta.axis,
      'For measured figures from roughly 1960 onward, the OIV publishes export and production data by country.'
    ].forEach(function (t) {
      var p = document.createElement('p');
      p.textContent = t;
      popBody.appendChild(p);
    });
    DATA.meta.indexSources.forEach(function (src) { popBody.appendChild(sourceCard(src, false)); });
    openPop(trigger, popBody.querySelector('a'));
  }

  function openPop(trigger, focusTarget) {
    if (popTrigger && popTrigger !== trigger) popTrigger.setAttribute('aria-expanded', 'false');
    popTrigger = trigger;
    trigger.setAttribute('aria-expanded', 'true');

    // Place it just under the trigger, clamped inside the board, and grow it
    // out of the chip it came from.
    var br = root.getBoundingClientRect();
    var tr = trigger.getBoundingClientRect();
    pop.hidden = false;
    pop.classList.remove('is-open');
    var pw = pop.offsetWidth;
    var left = Math.min(Math.max(tr.left - br.left + tr.width / 2 - pw / 2, 16), br.width - pw - 16);
    pop.style.left = left + 'px';
    pop.style.top = (tr.bottom - br.top + 10) + 'px';
    pop.style.setProperty('--whg-pop-ox', (tr.left - br.left + tr.width / 2 - left) + 'px');
    pop.style.setProperty('--whg-pop-oy', '-10px');

    requestAnimationFrame(function () {
      pop.classList.add('is-open');
      // A tick later: the theme's reduced-motion rule gives every element a
      // 0.01ms transition, which keeps the popover invisible for one frame.
      setTimeout(function () {
        if (focusTarget && popTrigger === trigger) focusTarget.focus({ preventScroll: true });
      }, 30);
      var pr = pop.getBoundingClientRect();
      if (pr.bottom > window.innerHeight) {
        window.scrollBy({ top: pr.bottom - window.innerHeight + 24, behavior: reduceMotion.matches ? 'auto' : 'smooth' });
      }
    });
  }

  function closePop(keepFocus) {
    if (!popTrigger) return;
    var t = popTrigger;
    popTrigger = null;
    t.setAttribute('aria-expanded', 'false');
    pop.classList.remove('is-open');
    var hide = function () { if (!popTrigger) pop.hidden = true; };
    if (reduceMotion.matches) hide(); else setTimeout(hide, 560);
    if (!keepFocus && pop.contains(document.activeElement)) t.focus({ preventScroll: true });
  }

  $('whg-pop-close').addEventListener('click', function () { closePop(); });
  $('whg-method').addEventListener('click', function () {
    if (popTrigger === this) closePop(); else openMethod(this);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && popTrigger) closePop();
  });
  document.addEventListener('click', function (e) {
    if (popTrigger && !pop.contains(e.target) && !popTrigger.contains(e.target)) closePop(true);
  });


  /* ---------------------------------------------------------------------
     Cellar book (full bibliography)
     --------------------------------------------------------------------- */

  function buildNotes() {
    var notes = $('whg-notes');
    [DATA.meta.index, DATA.meta.axis].forEach(function (t) {
      var p = document.createElement('p');
      p.textContent = t;
      notes.appendChild(p);
    });
  }

  function buildBiblio() {
    var wrap = $('whg-biblio');
    HEMIS.forEach(function (h) {
      var d = DATA.hemispheres[h];
      var col = document.createElement('div');
      var head = document.createElement('h3');
      head.textContent = d.label + ' Hemisphere';
      col.appendChild(head);

      var ol = document.createElement('ol');
      d.events.forEach(function (ev, i) {
        var li = document.createElement('li');
        li.className = 'whg-entry';
        li.id = 'whg-src-' + h + '-' + i;
        li.innerHTML =
          '<span class="whg-entry-year"></span><span class="whg-entry-title"></span>' +
          '<ul class="whg-entry-sources"></ul>' +
          '<button type="button" class="whg-entry-show">Show on the chart &uarr;</button>';
        li.querySelector('.whg-entry-year').textContent = fmtYear(ev.year);
        li.querySelector('.whg-entry-title').textContent = ev.title;

        var ul = li.querySelector('.whg-entry-sources');
        ev.sources.forEach(function (src) {
          var s = document.createElement('li');
          s.innerHTML = '<span class="whg-entry-num"></span><a target="_blank" rel="noopener"></a>, <span class="whg-entry-pub"></span>';
          s.querySelector('.whg-entry-num').textContent = '[' + src.n + ']';
          var a = s.querySelector('a');
          a.href = src.url;
          a.textContent = src.title;
          s.querySelector('.whg-entry-pub').textContent = src.pub;
          ul.appendChild(s);
        });

        li.querySelector('.whg-entry-show').addEventListener('click', function () {
          if (state.hemi !== h) setHemi(h, true);
          select(i);
          root.scrollIntoView({ behavior: reduceMotion.matches ? 'auto' : 'smooth', block: 'center' });
          setTimeout(function () { focusMarker(i); }, reduceMotion.matches ? 0 : 600);
        });

        ol.appendChild(li);
      });
      col.appendChild(ol);
      wrap.appendChild(col);
    });
  }

  function flashEntry(h, i) {
    var li = $('whg-src-' + h + '-' + i);
    if (!li) return;
    li.scrollIntoView({ behavior: reduceMotion.matches ? 'auto' : 'smooth', block: 'center' });
    li.classList.add('is-flash');
    setTimeout(function () { li.classList.remove('is-flash'); }, 2400);
  }


  /* ---------------------------------------------------------------------
     Wire up
     --------------------------------------------------------------------- */

  toggleEl.querySelectorAll('.whg-toggle-btn').forEach(function (b) {
    b.addEventListener('click', function () {
      if (b.dataset.hemi !== state.hemi) setHemi(b.dataset.hemi, true);
    });
  });
  $('whg-prev').addEventListener('click', function () { select(state.sel - 1); });
  $('whg-next').addEventListener('click', function () { select(state.sel + 1); });

  var resizeTimer;
  var lastWidth = chartEl.clientWidth;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      if (chartEl.clientWidth === lastWidth) return;
      lastWidth = chartEl.clientWidth;
      closePop(true);
      draw(false);
    }, 120);
  });

  buildNotes();
  buildBiblio();
  setHemi('N', true);
})();
