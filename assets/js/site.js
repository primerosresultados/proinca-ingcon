(function () {
  'use strict';

  // Scroll-aware nav border
  var nav = document.getElementById('siteNav');
  if (nav) {
    var onScroll = function () {
      if (window.scrollY > 8) nav.classList.add('is-scrolled');
      else nav.classList.remove('is-scrolled');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // Mobile menu
  var toggle = document.getElementById('navToggle');
  var menu = document.getElementById('mobileMenu');
  var menuClose = document.getElementById('mobileMenuClose');
  var openMenu = function () { if (menu) menu.classList.add('is-open'); document.body.style.overflow = 'hidden'; };
  var closeMenu = function () { if (menu) menu.classList.remove('is-open'); document.body.style.overflow = ''; };
  if (toggle) toggle.addEventListener('click', openMenu);
  if (menuClose) menuClose.addEventListener('click', closeMenu);
  if (menu) menu.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', closeMenu); });

  // Reveal on scroll
  var els = document.querySelectorAll('[data-reveal]');
  if ('IntersectionObserver' in window && els.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('is-in');
          io.unobserve(e.target);
        }
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
    els.forEach(function (el) { io.observe(el); });
  } else {
    els.forEach(function (el) { el.classList.add('is-in'); });
  }

  // Animated counters
  var counters = document.querySelectorAll('[data-count]');
  if ('IntersectionObserver' in window && counters.length) {
    var co = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (!e.isIntersecting) return;
        var el = e.target;
        var target = parseInt(el.getAttribute('data-count'), 10);
        var suffix = el.getAttribute('data-suffix') || '';
        var dur = 1400;
        var start = performance.now();
        var step = function (now) {
          var p = Math.min(1, (now - start) / dur);
          var eased = 1 - Math.pow(1 - p, 3);
          el.textContent = Math.round(target * eased) + suffix;
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
        co.unobserve(el);
      });
    }, { threshold: 0.5 });
    counters.forEach(function (c) { co.observe(c); });
  }

  // Year
  var y = document.getElementById('year');
  if (y) y.textContent = String(new Date().getFullYear());
})();
