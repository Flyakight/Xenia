/**
 * Tannic Studio — Global Scripts
 *
 * Handles: Dual typing animations, portfolio slider (user-driven),
 * portfolio scroll reveal, process nav, cursor follower,
 * mobile menu toggle, live clock.
 *
 * @package TannicStudio
 */

(function () {
  'use strict';

  const prefersReducedMotion = window.matchMedia(
    '(prefers-reduced-motion: reduce)'
  ).matches;

  /* ====================================================================
     1. TYPING ANIMATIONS (dual terminals)
     ==================================================================== */

  function initTypingAnimations() {
    var terminals = document.querySelectorAll('.hero-terminal');
    if (!terminals.length) return;

    terminals.forEach(function (terminal) {
      var codeEl = terminal.querySelector('.terminal-code');
      var typingBox = terminal.querySelector('[id^="typing-box"]');
      if (!codeEl || !typingBox) return;

      var text = codeEl.dataset.typingText || '';

      // Reduced motion: show full text immediately
      if (prefersReducedMotion) {
        typingBox.textContent = text;
        typingBox.style.borderRight = 'none';
        return;
      }

      var charIndex = 0;

      function typeChar() {
        if (charIndex < text.length) {
          typingBox.textContent += text.charAt(charIndex);
          charIndex++;
          setTimeout(typeChar, 50);
        } else {
          // Pause then reset
          setTimeout(function () {
            typingBox.textContent = '';
            charIndex = 0;
            typeChar();
          }, 5000);
        }
      }

      // Stagger start for second terminal
      var delay = terminal.classList.contains('hero-terminal--bottom') ? 1500 : 0;
      setTimeout(typeChar, delay);
    });
  }

  /* ====================================================================
     2. PORTFOLIO SLIDER (user-driven only, no auto-advance)
     ==================================================================== */

  function initPortfolioSlider() {
    var slider = document.querySelector('.portfolio-slider');
    if (!slider) return;

    // Navigate: next = rotate first child to end, prev = rotate last to start
    function activate(e) {
      var items = slider.querySelectorAll('.portfolio-slide');
      if (items.length < 2) return;

      if (e.target.closest('.portfolio-next')) {
        slider.append(items[0]);
      }

      if (e.target.closest('.portfolio-prev')) {
        slider.prepend(items[items.length - 1]);
      }
    }

    // Keyboard navigation
    function handleKeydown(e) {
      var items = slider.querySelectorAll('.portfolio-slide');
      if (items.length < 2) return;

      if (e.key === 'ArrowRight') slider.append(items[0]);
      if (e.key === 'ArrowLeft') slider.prepend(items[items.length - 1]);
    }

    document.addEventListener('click', activate, false);
    document.addEventListener('keydown', handleKeydown, false);
  }

  /* ====================================================================
     2b. PORTFOLIO SCROLL REVEAL
     ==================================================================== */

  function initPortfolioReveal() {
    var section = document.querySelector('.portfolio-section');
    if (!section) return;

    // If reduced motion, show immediately
    if (prefersReducedMotion) {
      section.classList.add('is-visible');
      return;
    }

    if (!('IntersectionObserver' in window)) {
      section.classList.add('is-visible');
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            section.classList.add('is-visible');
            observer.unobserve(section); // Only trigger once
          }
        });
      },
      { threshold: 0.15 }
    );

    observer.observe(section);
  }

  /* ====================================================================
     2c. EDITORIAL CARDS SCROLL REVEAL
     ==================================================================== */

  function initEditorialReveal() {
    var cards = document.querySelectorAll('[data-reveal]');
    if (!cards.length) return;

    if (prefersReducedMotion) {
      cards.forEach(function (card) { card.classList.add('is-visible'); });
      return;
    }

    if (!('IntersectionObserver' in window)) {
      cards.forEach(function (card) { card.classList.add('is-visible'); });
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );

    cards.forEach(function (card) {
      observer.observe(card);
    });
  }

  /* ====================================================================
     2d. IMAGE PARALLAX DEPTH
     ==================================================================== */

  function initParallax() {
    // Skip on reduced motion or touch devices
    if (prefersReducedMotion) return;
    if (window.matchMedia('(pointer: coarse)').matches) return;

    var elements = document.querySelectorAll('[data-parallax]');
    if (!elements.length) return;

    // Track which elements are currently in the viewport
    var activeSet = new Set();

    if (!('IntersectionObserver' in window)) return;

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            activeSet.add(entry.target);
          } else {
            activeSet.delete(entry.target);
          }
        });
      },
      { rootMargin: '100px' }
    );

    elements.forEach(function (el) {
      observer.observe(el);
    });

    function onScroll() {
      activeSet.forEach(function (el) {
        var speed = parseFloat(el.dataset.parallax) || 0.1;
        var rect = el.parentElement.getBoundingClientRect();
        var centerOffset = rect.top + rect.height / 2 - window.innerHeight / 2;
        var yShift = centerOffset * speed * -1;
        el.style.transform = 'translateY(' + yShift + 'px)';
      });
      requestAnimationFrame(onScroll);
    }

    requestAnimationFrame(onScroll);
  }

  /* ====================================================================
     2e. WORK INDEX — HOVER REVEAL
     ==================================================================== */

  function initWorkHoverReveal() {
    var rows = document.querySelectorAll('.work-row');
    if (!rows.length) return;

    // Skip hover effects on touch devices
    if (window.matchMedia('(pointer: coarse)').matches) return;

    rows.forEach(function (row) {
      var preview = row.querySelector('.work-row-preview');
      if (!preview) return;

      row.addEventListener('mousemove', function (e) {
        var rect = preview.getBoundingClientRect();
        var previewWidth = rect.width || 300;
        var previewHeight = rect.height || 200;
        var margin = 16;

        // Default placement: right of cursor, slightly above center.
        var left = e.clientX + 20;
        var top = e.clientY - 100;

        // Keep preview inside viewport bounds.
        left = Math.min(left, window.innerWidth - previewWidth - margin);
        left = Math.max(margin, left);
        top = Math.min(top, window.innerHeight - previewHeight - margin);
        top = Math.max(margin, top);

        preview.style.left = left + 'px';
        preview.style.top = top + 'px';
      });

      row.addEventListener('mouseenter', function () {
        preview.style.opacity = '1';
        preview.style.transform = 'scale(1)';
      });

      row.addEventListener('mouseleave', function () {
        preview.style.opacity = '0';
        preview.style.transform = 'scale(0.95)';
      });
    });
  }

  /* ====================================================================
     2g. SERVICES LEDGER ACTIVE STATE
     ==================================================================== */

  function initServicesLedger() {
    var track = document.querySelector('[data-service-track]');
    if (!track) return;
    var prevBtn = document.querySelector('[data-service-prev]');
    var nextBtn = document.querySelector('[data-service-next]');

    var items = Array.prototype.slice.call(
      track.querySelectorAll('[data-service-ledger-item]')
    );
    if (!items.length) return;

    function setActive(activeItem) {
      items.forEach(function (item) {
      item.classList.toggle('is-active', item === activeItem);
      });
    }

    function getStepWidth() {
      var first = items[0];
      if (!first) return track.clientWidth * 0.8;
      var style = window.getComputedStyle(track);
      var gap = parseFloat(style.columnGap || style.gap || '0') || 0;
      return first.getBoundingClientRect().width + gap;
    }

    function setActiveClosestToCenter() {
      var trackRect = track.getBoundingClientRect();
      var centerX = trackRect.left + trackRect.width / 2;
      var closest = null;
      var closestDistance = Infinity;

      items.forEach(function (item) {
        var rect = item.getBoundingClientRect();
        var itemCenter = rect.left + rect.width / 2;
        var distance = Math.abs(centerX - itemCenter);
        if (distance < closestDistance) {
          closestDistance = distance;
          closest = item;
        }
      });

      if (closest) setActive(closest);
    }

    items.forEach(function (item) {
      item.addEventListener('mouseenter', function () {
        setActive(item);
      });

      item.addEventListener('focus', function () {
        setActive(item);
      });

      item.addEventListener('click', function (e) {
        if (e.target.closest('a')) return;
        var targetLink = item.querySelector('.home-service-link');
        if (targetLink && targetLink.getAttribute('href')) {
          window.location.href = targetLink.getAttribute('href');
        }
      });

      item.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        var targetLink = item.querySelector('.home-service-link');
        if (targetLink && targetLink.getAttribute('href')) {
          e.preventDefault();
          window.location.href = targetLink.getAttribute('href');
        }
      });
    });

    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        track.scrollBy({ left: -getStepWidth(), behavior: prefersReducedMotion ? 'auto' : 'smooth' });
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        track.scrollBy({ left: getStepWidth(), behavior: prefersReducedMotion ? 'auto' : 'smooth' });
      });
    }

    var scrollTimer = null;
    track.addEventListener('scroll', function () {
      if (scrollTimer) window.clearTimeout(scrollTimer);
      scrollTimer = window.setTimeout(setActiveClosestToCenter, 80);
    }, { passive: true });

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            setActive(entry.target);
          }
        });
      },
      {
        root: track,
        threshold: 0.65,
      }
    );

    items.forEach(function (item) {
      observer.observe(item);
    });
  }

  /* ====================================================================
     2h. PHILOSOPHY ACCORDION (touch-friendly)
     ==================================================================== */

  function initPhilosophyAccordion() {
    var accordion = document.querySelector('.gallery-accordion');
    if (!accordion) return;

    var strips = Array.prototype.slice.call(
      accordion.querySelectorAll('.accordion-strip')
    );
    if (!strips.length) return;

    var isTouchLike = window.matchMedia('(pointer: coarse)').matches || window.innerWidth <= 992;
    if (!isTouchLike) return;

    strips.forEach(function (strip) {
      strip.addEventListener('click', function (e) {
        if (e.target.closest('.strip-cta-link')) return;

        strips.forEach(function (item) {
          if (item !== strip) item.classList.remove('is-active');
        });
        strip.classList.toggle('is-active');
      });
    });
  }

  /* ====================================================================
     2f. SCROLL-AWARE HEADER
     ==================================================================== */

  function initHeaderScroll() {
    var header = document.querySelector('.site-header');
    if (!header) return;

    var threshold = 50;

    function checkScroll() {
      if (window.scrollY > threshold) {
        header.classList.add('is-scrolled');
      } else {
        header.classList.remove('is-scrolled');
      }
    }

    window.addEventListener('scroll', checkScroll, { passive: true });
    checkScroll(); // initial check on page load
  }

  /* ====================================================================
     2i. MOBILE HERO TAP-TO-FILL
     ==================================================================== */

  function initMobileHeroFill() {
    var hero = document.querySelector('.hero');
    if (!hero) return;

    var words = hero.querySelectorAll('[data-hero-fill-word]');
    if (!words.length) return;

    var isMobile = window.innerWidth <= 992;
    var isCoarsePointer = window.matchMedia('(pointer: coarse)').matches;
    if (!isMobile && !isCoarsePointer) return;

    var hint = document.getElementById('hero-fill-hint');
    var ctaLink = hero.querySelector('.hero-cta a');
    var maxPercent = 78;
    var duration = 900;
    var played = false;

    function setFill(percent) {
      hero.style.setProperty('--hero-fill-percent', percent.toFixed(1) + '%');
    }

    if (prefersReducedMotion) {
      setFill(maxPercent);
      if (hint) hint.classList.add('is-hidden');
      return;
    }

    function animateFill() {
      if (played) return;
      played = true;
      if (hint) hint.classList.add('is-hidden');

      var start = null;

      function frame(timestamp) {
        if (start === null) start = timestamp;

        var elapsed = timestamp - start;
        var t = Math.min(1, elapsed / duration);
        var eased = 1 - Math.pow(1 - t, 3);
        setFill(eased * maxPercent);

        if (t < 1) {
          window.requestAnimationFrame(frame);
        }
      }

      window.requestAnimationFrame(frame);
    }

    hero.addEventListener('pointerdown', animateFill, { once: true, passive: true });
    if (ctaLink) ctaLink.addEventListener('focus', animateFill, { once: true });

    window.setTimeout(animateFill, 600);
  }

  /* ====================================================================
     3. PROCESS SIDEBAR NAV
     ==================================================================== */

  function initProcessNav() {
    var stream = document.querySelector('.process-stream');
    var navLinks = document.querySelectorAll('.process-nav a');
    var panels = document.querySelectorAll('.process-step-panel');

    if (!stream || !navLinks.length || !panels.length) return;

    // Click handler — smooth scroll within the stream
    navLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        var targetId = this.getAttribute('href').substring(1);
        var target = document.getElementById(targetId);
        if (target) {
          target.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
        }
      });
    });

    // IntersectionObserver to track active panel
    if (!('IntersectionObserver' in window)) return;

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var activeId = entry.target.id;

            navLinks.forEach(function (link) {
              var linkTarget = link.getAttribute('href').substring(1);
              if (linkTarget === activeId) {
                link.classList.add('active');
              } else {
                link.classList.remove('active');
              }
            });
          }
        });
      },
      {
        root: stream,
        threshold: 0.5,
      }
    );

    panels.forEach(function (panel) {
      observer.observe(panel);
    });
  }

  /* ====================================================================
     4. CURSOR FOLLOWER
     ==================================================================== */

  function initCursorFollower() {
    // Skip on touch devices or reduced motion
    if (prefersReducedMotion) return;
    if (window.matchMedia('(pointer: coarse)').matches) return;

    const cursor = document.querySelector('.cursor-follower');
    if (!cursor) return;

    let mouseX = 0;
    let mouseY = 0;
    let cursorX = 0;
    let cursorY = 0;
    const speed = 0.15;

    document.addEventListener('mousemove', function (e) {
      mouseX = e.clientX;
      mouseY = e.clientY;

      if (!cursor.classList.contains('visible')) {
        cursor.classList.add('visible');
      }
    });

    // Expand on hover over interactive elements
    const interactiveSelectors =
      'a, button, input, textarea, select, [role="button"], .accordion-strip, .mini-img, .portfolio-slide, .home-service-card, .home-service-ledger-item, .editorial-card, .work-row';

    document.addEventListener('mouseover', function (e) {
      if (e.target.closest(interactiveSelectors)) {
        cursor.classList.add('hover');
      }
    });

    document.addEventListener('mouseout', function (e) {
      if (e.target.closest(interactiveSelectors)) {
        cursor.classList.remove('hover');
      }
    });

    // Hide when mouse leaves window
    document.addEventListener('mouseleave', function () {
      cursor.classList.remove('visible');
    });

    function animateCursor() {
      cursorX += (mouseX - cursorX) * speed;
      cursorY += (mouseY - cursorY) * speed;
      cursor.style.transform =
        'translate(' + cursorX + 'px, ' + cursorY + 'px) translate(-50%, -50%)';
      requestAnimationFrame(animateCursor);
    }

    animateCursor();
  }

  /* ====================================================================
     5. MOBILE MENU TOGGLE
     ==================================================================== */

  function initMobileMenu() {
    const toggle = document.querySelector('.nav-toggle');
    const menu = document.querySelector('.nav-menu');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', function () {
      const isOpen = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!isOpen));
      menu.classList.toggle('is-open');
      document.body.classList.toggle('menu-open');
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menu.classList.contains('is-open')) {
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-open');
        document.body.classList.remove('menu-open');
        toggle.focus();
      }
    });

    // Close when clicking outside
    document.addEventListener('click', function (e) {
      if (
        menu.classList.contains('is-open') &&
        !menu.contains(e.target) &&
        !toggle.contains(e.target)
      ) {
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-open');
        document.body.classList.remove('menu-open');
      }
    });

    // Focus trap within open menu
    menu.addEventListener('keydown', function (e) {
      if (e.key !== 'Tab') return;
      if (!menu.classList.contains('is-open')) return;

      const focusable = menu.querySelectorAll(
        'a[href], button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
      );
      if (!focusable.length) return;

      const first = focusable[0];
      const last = focusable[focusable.length - 1];

      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    });
  }

  /* ====================================================================
     6. LIVE CLOCK
     ==================================================================== */

  function initClock() {
    const el = document.getElementById('local-time');
    if (!el) return;

    var formatter = new Intl.DateTimeFormat('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      timeZoneName: 'short',
    });

    function tick() {
      el.textContent = formatter.format(new Date());
    }

    tick();
    setInterval(tick, 1000);
  }

  /* ====================================================================
     7. FOOTER NEWSLETTER CTA LABEL
     ==================================================================== */

  function initFooterNewsletterCTA() {
    var form = document.querySelector('.footer-newsletter .wpcf7-form');
    if (!form) return;

    var label = 'Reach Out';
    if (window.tannicTheme && typeof window.tannicTheme.footerCtaLabel === 'string') {
      var trimmed = window.tannicTheme.footerCtaLabel.trim();
      if (trimmed) label = trimmed;
    }

    var submitInput = form.querySelector('input[type="submit"]');
    if (submitInput) {
      submitInput.value = label;
    }

    var submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.textContent = label;
    }
  }

  /* ====================================================================
     INIT
     ==================================================================== */

  document.addEventListener('DOMContentLoaded', function () {
    initTypingAnimations();
    initPortfolioSlider();
    initPortfolioReveal();
    initEditorialReveal();
    initParallax();
    initWorkHoverReveal();
    initServicesLedger();
    initPhilosophyAccordion();
    initMobileHeroFill();
    initHeaderScroll();
    initProcessNav();
    initCursorFollower();
    initMobileMenu();
    initClock();
    initFooterNewsletterCTA();
  });
})();
