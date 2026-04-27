/**
 * ALEXIS BELLO — PROINCA · INGCON
 * Main JavaScript
 * Modular, vanilla JS — WordPress-ready (no framework dependencies)
 */

'use strict';

/* ============================================================
   NAV — Sticky + scroll state + mobile toggle
============================================================ */
const Nav = (() => {
  const nav      = document.getElementById('nav');
  const toggle   = document.getElementById('navToggle');
  const menu     = document.getElementById('navMenu');
  const navLinks = document.querySelectorAll('.nav__link, .footer__link, .hero__scroll');

  let lastScroll = 0;

  function onScroll() {
    const y = window.scrollY;

    // Add scrolled class after 60px
    nav.classList.toggle('is-scrolled', y > 60);

    lastScroll = y;
  }

  function toggleMenu() {
    const isOpen = menu.classList.toggle('is-open');
    toggle.classList.toggle('is-open', isOpen);
    toggle.setAttribute('aria-expanded', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  }

  function closeMenu() {
    menu.classList.remove('is-open');
    toggle.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  function init() {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    toggle.addEventListener('click', toggleMenu);

    // Close on nav link click (mobile)
    navLinks.forEach(link => {
      link.addEventListener('click', closeMenu);
    });

    // Close on outside click
    document.addEventListener('click', e => {
      if (menu.classList.contains('is-open') &&
          !menu.contains(e.target) &&
          !toggle.contains(e.target)) {
        closeMenu();
      }
    });

    // Close on ESC
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeMenu();
    });
  }

  return { init };
})();


/* ============================================================
   SCROLL ANIMATIONS — IntersectionObserver
   Observes [data-animate] elements and adds .is-visible
============================================================ */
const ScrollAnimations = (() => {
  let observer;

  function getDelay(el) {
    return parseInt(el.dataset.delay || '0', 10);
  }

  function init() {
    const elements = document.querySelectorAll('[data-animate]');

    if (!elements.length) return;

    // IntersectionObserver with staggered delay
    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el    = entry.target;
          const delay = getDelay(el);

          setTimeout(() => {
            el.classList.add('is-visible');
          }, delay);

          observer.unobserve(el);
        }
      });
    }, {
      threshold: 0.12,
      rootMargin: '0px 0px -60px 0px'
    });

    elements.forEach(el => observer.observe(el));
  }

  return { init };
})();


/* ============================================================
   SMOOTH SCROLL — Anchor links with nav offset
============================================================ */
const SmoothScroll = (() => {
  function getNavHeight() {
    const nav = document.getElementById('nav');
    return nav ? nav.offsetHeight : 0;
  }

  function scrollTo(targetId) {
    const target = document.querySelector(targetId);
    if (!target) return;

    const top = target.getBoundingClientRect().top + window.scrollY - getNavHeight();

    window.scrollTo({
      top: Math.max(0, top),
      behavior: 'smooth'
    });
  }

  function init() {
    document.addEventListener('click', e => {
      const link = e.target.closest('a[href^="#"]');
      if (!link) return;

      const href = link.getAttribute('href');
      if (!href || href === '#') return;

      e.preventDefault();
      scrollTo(href);
    });
  }

  return { init };
})();


/* ============================================================
   ACTIVE NAV LINK — Highlights current section in nav
============================================================ */
const ActiveNav = (() => {
  const sections  = document.querySelectorAll('section[id], header[id]');
  const navLinks  = document.querySelectorAll('.nav__link');

  function onScroll() {
    const scrollY     = window.scrollY;
    const navHeight   = document.getElementById('nav')?.offsetHeight || 76;
    let currentId     = '';

    sections.forEach(section => {
      const top = section.offsetTop - navHeight - 40;
      if (scrollY >= top) {
        currentId = section.id;
      }
    });

    navLinks.forEach(link => {
      const href = link.getAttribute('href');
      link.classList.toggle('nav__link--active', href === `#${currentId}`);
    });
  }

  function init() {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  return { init };
})();


/* ============================================================
   CONTACT FORM — Validation + simulated submission
   Future: Replace with AJAX call to WordPress REST API or CF7
============================================================ */
const ContactForm = (() => {
  function validateField(field) {
    const value = field.value.trim();
    let valid   = true;
    let message = '';

    if (field.required && !value) {
      valid   = false;
      message = 'Este campo es obligatorio.';
    } else if (field.type === 'email' && value) {
      const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRe.test(value)) {
        valid   = false;
        message = 'Ingresa un correo electrónico válido.';
      }
    } else if (field.type === 'tel' && value) {
      const telRe = /^[\d\s\+\-\(\)]{7,20}$/;
      if (!telRe.test(value)) {
        valid   = false;
        message = 'Ingresa un teléfono válido.';
      }
    }

    setFieldState(field, valid, message);
    return valid;
  }

  function setFieldState(field, valid, message) {
    const parent = field.closest('.form__group');
    if (!parent) return;
    const existing = parent.querySelector('.form__error');
    if (existing) existing.remove();

    field.style.borderColor = '';

    if (!valid) {
      field.style.borderColor = '#C0392B';
      const errEl = document.createElement('span');
      errEl.className   = 'form__error';
      errEl.textContent = message;
      errEl.style.cssText = 'display:block;font-size:.75rem;color:#C0392B;margin-top:4px;';
      parent.appendChild(errEl);
    }
  }

  function setupForm(formId, successId) {
    const form    = document.getElementById(formId);
    const success = document.getElementById(successId);
    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const trap = form.querySelector('[name="_trap"]');
      if (trap && trap.value) return;

      const fields   = form.querySelectorAll('input:not([type="hidden"]):not([name="_trap"]), select, textarea');
      let allValid = true;

      fields.forEach(field => {
        if (!validateField(field)) allValid = false;
      });

      if (!allValid) return;

      const btn = form.querySelector('button[type="submit"]');
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Enviando...';

      setTimeout(() => {
        form.hidden = true;
        if (success) {
          success.hidden = false;
          success.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }, 1200);
    });

    form.addEventListener('blur', (e) => {
      const field = e.target;
      if (['INPUT', 'SELECT', 'TEXTAREA'].includes(field.tagName)) {
        if (field.name !== '_trap') validateField(field);
      }
    }, true);
  }

  function init() {
    setupForm('contactForm', 'formSuccess');
    setupForm('modalForm', 'modalSuccess');
  }

  return { init };
})();


/* ============================================================
   MODAL MODULE
   ============================================================ */
const Modal = (() => {
  const modal = document.getElementById('contactModal');
  const btns  = document.querySelectorAll('.btn--proinca-hero, .hero__bar-cta, .nav__cta');
  const close = document.getElementById('modalClose');
  const backdrop = document.getElementById('modalBackdrop');

  function open() {
    if (!modal) return;
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closeAll() {
    if (!modal) return;
    modal.classList.remove('is-open');
    document.body.style.overflow = '';
    // Reset form success if needed
    const form = document.getElementById('modalForm');
    const success = document.getElementById('modalSuccess');
    if (form && success) {
      form.hidden = false;
      success.hidden = true;
      form.reset();
    }
  }

  function init() {
    if (!modal) return;

    // Queremos capturar todos los botones que deberían abrir el formulario
    // Incluyendo los de las secciones de servicios
    const btns = document.querySelectorAll('.btn--proinca-hero, .hero__bar-cta, .nav__cta, .services__cta .btn, .project__card-btn');

    btns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        // Prevenir el scroll por defecto si es un anclaje
        if (btn.getAttribute('href')?.startsWith('#')) {
          e.preventDefault();
        }
        open();
      });
    });

    close?.addEventListener('click', closeAll);
    backdrop?.addEventListener('click', closeAll);

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.classList.contains('is-open')) {
        closeAll();
      }
    });
  }

  return { init };
})();


/* ============================================================
   HERO PARALLAX — Disabled
============================================================ */
const HeroParallax = (() => {
  function init() { /* parallax removed */ }
  return { init };
})();


/* ============================================================
   COUNTER ANIMATION — Animate numbers in hero stats
============================================================ */
const CounterAnimation = (() => {
  const counters = document.querySelectorAll('.hero__stat-number');

  function animateCounter(el, target, duration = 1200) {
    const isPlus   = el.textContent.startsWith('+');
    const start    = 0;
    const startTime = performance.now();

    function update(currentTime) {
      const elapsed  = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      // Ease out cubic
      const eased   = 1 - Math.pow(1 - progress, 3);
      const value   = Math.round(start + (target - start) * eased);

      el.textContent = (isPlus ? '+' : '') + value;

      if (progress < 1) {
        requestAnimationFrame(update);
      }
    }

    requestAnimationFrame(update);
  }

  function init() {
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el     = entry.target;
          const text   = el.textContent;
          const target = parseInt(text.replace('+', ''), 10);

          if (!isNaN(target)) {
            animateCounter(el, target);
          }

          observer.unobserve(el);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
  }

  return { init };
})();


/* ============================================================
   DIFFERENTIALS SLIDER
============================================================ */
const DiffSlider = (() => {
  const track    = document.getElementById('diffTrack');
  const dotsWrap = document.getElementById('diffDots');
  const btnPrev  = document.getElementById('diffPrev');
  const btnNext  = document.getElementById('diffNext');

  const VISIBLE  = 3; // elementos visibles a la vez
  let current    = 0;
  let slides     = 0;
  let positions  = 0; // total de posiciones = slides - VISIBLE + 1
  let autoTimer  = null;

  function goTo(index) {
    current = Math.max(0, Math.min(index, positions - 1));
    // Mover 1/slides * 100% por cada paso
    track.style.transform = `translateX(-${current * (100 / slides)}%)`;
    dotsWrap.querySelectorAll('.diff-slider__dot').forEach((d, i) => {
      d.classList.toggle('is-active', i === current);
    });
  }

  function buildDots() {
    slides    = track.children.length;
    positions = Math.max(1, slides - VISIBLE + 1);
    dotsWrap.innerHTML = '';
    for (let i = 0; i < positions; i++) {
      const dot = document.createElement('button');
      dot.className = 'diff-slider__dot' + (i === 0 ? ' is-active' : '');
      dot.setAttribute('aria-label', `Grupo ${i + 1}`);
      dot.addEventListener('click', () => { goTo(i); resetAuto(); });
      dotsWrap.appendChild(dot);
    }
  }

  function resetAuto() {
    clearInterval(autoTimer);
    autoTimer = setInterval(() => goTo(current + 1 >= positions ? 0 : current + 1), 5000);
  }

  function init() {
    if (!track) return;
    buildDots();
    btnPrev.addEventListener('click', () => { goTo(current - 1 < 0 ? positions - 1 : current - 1); resetAuto(); });
    btnNext.addEventListener('click', () => { goTo(current + 1 >= positions ? 0 : current + 1); resetAuto(); });

    // Pause on hover
    track.closest('.diff-slider').addEventListener('mouseenter', () => clearInterval(autoTimer));
    track.closest('.diff-slider').addEventListener('mouseleave', resetAuto);

    // Touch/swipe
    let startX = 0;
    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', e => {
      const diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) { goTo(current + (diff > 0 ? 1 : -1)); resetAuto(); }
    });

    resetAuto();
  }

  return { init };
})();

/* ============================================================
   Services showcase — interactive tabs
============================================================ */
const ServicesShowcase = (() => {
  function init() {
    const items = document.querySelectorAll('.svc__item');
    const panels = document.querySelectorAll('.svc__panel');
    if (!items.length || !panels.length) return;

    const activate = (id) => {
      items.forEach(i => {
        const on = i.dataset.svc === id;
        i.classList.toggle('is-active', on);
        i.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      panels.forEach(p => p.classList.toggle('is-active', p.dataset.svc === id));
    };

    items.forEach(item => {
      item.addEventListener('mouseenter', () => activate(item.dataset.svc));
      item.addEventListener('focus',      () => activate(item.dataset.svc));
      item.addEventListener('click',      () => activate(item.dataset.svc));
    });
  }
  return { init };
})();

/* ============================================================
   INIT — Bootstrap all modules
============================================================ */
document.addEventListener('DOMContentLoaded', () => {
  Nav.init();
  SmoothScroll.init();
  ScrollAnimations.init();
  ActiveNav.init();
  ContactForm.init();
  CounterAnimation.init();
  DiffSlider.init();
  Modal.init();
  ServicesShowcase.init();
});
