/**
 * LUXE HOSPITALITY THEME - MAIN JAVASCRIPT
 * Cinematic scroll interactions without the polished AI formula
 */

(() => {
  'use strict';

  // Check for reduced motion preference
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasGsap = Boolean(window.gsap && window.ScrollTrigger);
  const canAnimate = hasGsap && !prefersReducedMotion;

  // Anything that starts at opacity 0 must only do so when there is a
  // working animation to reveal it. See .has-anim in style.css.
  document.documentElement.classList.toggle('has-anim', canAnimate);

  // Horizontal rails fall back to a hand-scrollable, snapping track.
  if (!canAnimate) {
    document.querySelectorAll('[data-horizontal-scroll]').forEach((section) => {
      section.classList.add('rail--scrollable');
    });
  }

  // ===================================================
  // GSAP SETUP
  // ===================================================

  if (canAnimate) {
    gsap.registerPlugin(ScrollTrigger);

    // ===================================================
    // HERO TITLE ANIMATION
    // ===================================================

    const heroTitle = document.querySelector('h1');
    if (heroTitle) {
      // Split text into characters
      const chars = heroTitle.textContent.split('');
      heroTitle.innerHTML = chars.map(char => `<span class="char">${char}</span>`).join('');

      gsap.set('.char', { opacity: 0, y: 20 });

      gsap.to('.char', {
        opacity: 1,
        y: 0,
        duration: 0.6,
        stagger: 0.02,
        ease: 'power2.out',
        delay: 0.2,
      });
    }

    // ===================================================
    // IMAGE PARALLAX ON SCROLL
    // ===================================================

    const parallaxImages = gsap.utils.toArray('.image-parallax');
    parallaxImages.forEach(image => {
      const img = image.querySelector('img');
      if (!img) return;

      gsap.to(img, {
        y: () => (image.offsetHeight - img.offsetHeight) * 0.3,
        ease: 'none',
        scrollTrigger: {
          trigger: image,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 1.5,
          markers: false,
        },
      });
    });

    // ===================================================
    // FRAME-BREAKING: IMAGES OVERFLOW & TILT
    // ===================================================

    const overflowImages = gsap.utils.toArray('.image-break--overflow');
    overflowImages.forEach(container => {
      gsap.fromTo(
        container,
        { x: -20, opacity: 0 },
        {
          x: 0,
          opacity: 1,
          duration: 0.8,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: container,
            start: 'top 80%',
            toggleActions: 'play none none none',
          },
        }
      );

      // Slight hover tilt effect
      container.addEventListener('mousemove', (e) => {
        if (prefersReducedMotion) return;

        const rect = container.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const rotateX = ((y - rect.height / 2) / rect.height) * 3;
        const rotateY = ((x - rect.width / 2) / rect.width) * 3;

        gsap.to(container, {
          rotationX: rotateX,
          rotationY: rotateY,
          duration: 0.3,
          overwrite: 'auto',
        });
      });

      container.addEventListener('mouseleave', () => {
        gsap.to(container, {
          rotationX: 0,
          rotationY: 0,
          duration: 0.4,
          ease: 'power2.out',
        });
      });
    });

    // ===================================================
    // SCROLL-FADE ELEMENTS
    // ===================================================

    const fadeElements = gsap.utils.toArray('.scroll-fade');
    fadeElements.forEach(el => {
      gsap.fromTo(
        el,
        { opacity: 0, y: 30 },
        {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: el,
            start: 'top 85%',
            toggleActions: 'play none none none',
          },
        }
      );
    });

    // ===================================================
    // STAGGERED CARD REVEALS
    // ===================================================

    const cardContainers = gsap.utils.toArray('[data-card-stagger]');
    cardContainers.forEach(container => {
      const cards = container.querySelectorAll('.card');
      if (cards.length === 0) return;

      gsap.fromTo(
        cards,
        { opacity: 0, y: 40 },
        {
          opacity: 1,
          y: 0,
          duration: 0.6,
          stagger: 0.15,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: container,
            start: 'top 75%',
            toggleActions: 'play none none none',
          },
        }
      );
    });

    // ===================================================
    // TEXT EXPANSION ON SCROLL
    // ===================================================

    const expandText = gsap.utils.toArray('[data-expand-text]');
    expandText.forEach(el => {
      gsap.fromTo(
        el,
        { fontSize: '1rem', letterSpacing: '0em', opacity: 0 },
        {
          fontSize: 'clamp(1.5rem, 3vw, 2.5rem)',
          letterSpacing: '0.02em',
          opacity: 1,
          duration: 1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: el,
            start: 'top 60%',
            end: 'top 20%',
            scrub: 1,
            markers: false,
          },
        }
      );
    });

    // ===================================================
    // HORIZONTAL SCROLL (LIKE AURA)
    // ===================================================

    const horizontalSections = document.querySelectorAll('[data-horizontal-scroll]');
    horizontalSections.forEach(section => {
      const track = section.querySelector('[data-horizontal-track]');
      if (!track) return;

      gsap.to(track, {
        x: () => -(track.scrollWidth - window.innerWidth),
        ease: 'none',
        scrollTrigger: {
          trigger: section,
          start: 'top top',
          end: () => '+=' + Math.max(1, track.scrollWidth - window.innerWidth),
          scrub: 1,
          pin: true,
          invalidateOnRefresh: true,
        },
      });
    });

    // ===================================================
    // REFRESH ON RESIZE
    // ===================================================

    window.addEventListener('resize', () => {
      ScrollTrigger.refresh();
    });
  }

  // ===================================================
  // SMOOTH SCROLL TO ANCHOR
  // ===================================================

  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        if (hasGsap && !prefersReducedMotion) {
          gsap.to(window, {
            duration: 0.5,
            scrollTo: { y: target, offsetY: 100 },
            ease: 'power2.inOut',
          });
        } else {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });

  // ===================================================
  // NAVIGATION HIGHLIGHT ON SCROLL
  // ===================================================

  if (hasGsap) {
    const navLinks = document.querySelectorAll('nav a[href^="#"]');
    if (navLinks.length > 0) {
      navLinks.forEach(link => {
        ScrollTrigger.create({
          trigger: document.querySelector(link.getAttribute('href')),
          onEnter: () => {
            navLinks.forEach(l => l.classList.remove('is-active'));
            link.classList.add('is-active');
          },
          onEnterBack: () => {
            navLinks.forEach(l => l.classList.remove('is-active'));
            link.classList.add('is-active');
          },
        });
      });
    }
  }

  // ===================================================
  // LAZY LOAD IMAGES
  // ===================================================

  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src || img.src;
          img.classList.add('is-loaded');
          observer.unobserve(img);
        }
      });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
      imageObserver.observe(img);
    });
  }

  // ===================================================
  // MOBILE MENU TOGGLE
  // ===================================================

  const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
  const mobileMenu = document.querySelector('[data-mobile-menu]');

  if (mobileMenuToggle && mobileMenu) {
    mobileMenuToggle.addEventListener('click', () => {
      mobileMenu.classList.toggle('is-open');
      mobileMenuToggle.setAttribute(
        'aria-expanded',
        mobileMenuToggle.getAttribute('aria-expanded') === 'true' ? 'false' : 'true'
      );
    });

    // Close menu when link clicked
    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.remove('is-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // ===================================================
  // INIT COMPLETE
  // ===================================================

  console.log('Luxe Hospitality Theme initialized');
})();
