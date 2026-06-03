/**
 * motion-v8.js — Animations T1 du V8 Villa Plaisance.
 *
 * 1. Stagger reveal sur les cards (.article-card, .av-card, .room-card-x,
 *    .cellule, .chambre, .saison, .qf-card) via IntersectionObserver.
 *    Index par groupe parent stocké dans --i.
 * 2. Form focus : ajoute .has-focus au wrapper .champ/.field au focus
 *    d'un input/textarea/select pour que le label puisse réagir en CSS.
 * 3. Submit feedback : intercepte le submit du #contactForm pour ajouter
 *    .is-loading sur le bouton (spinner CSS) et auto-restore après 8s
 *    si la requête traîne.
 *
 * Respecte prefers-reduced-motion (early-return si activé).
 * Vanilla JS, IIFE, pas de dépendance.
 */
(() => {
  'use strict';

  const REDUCE = matchMedia('(prefers-reduced-motion: reduce)').matches;

  // --- 1. STAGGER REVEAL ---
  const CARD_SELECTOR = [
    '.article-card',
    '.av-card',
    '.room-card-x',
    '.cellule',
    '.chambre',
    '.saison',
    '.qf-card',
  ].join(',');

  // Sélecteurs T3 : footers, observés sans stagger (un seul élément par page).
  const FOOTER_SELECTOR = '.pied, .footer';

  function initStagger() {
    if (REDUCE) {
      // Forcer .is-in immédiat (CSS reduce neutralise déjà mais on garde
      // l'effet "carte visible" sans transition).
      document.querySelectorAll(CARD_SELECTOR).forEach((el) => el.classList.add('is-in'));
      return;
    }

    const cards = document.querySelectorAll(CARD_SELECTOR);
    if (cards.length === 0) return;

    // Calcule l'index de chaque card dans son parent direct, modulo 6.
    // Modulo 6 pour éviter qu'un grand stagger ne crée un trou de 2-3s
    // sur les longs listings (journal avec 12 articles, etc.).
    const groups = new Map();
    cards.forEach((card) => {
      const parent = card.parentElement;
      if (!parent) return;
      if (!groups.has(parent)) groups.set(parent, 0);
      const idx = groups.get(parent);
      card.style.setProperty('--i', idx % 6);
      groups.set(parent, idx + 1);
    });

    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-in');
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
    );

    cards.forEach((card) => io.observe(card));
  }

  // --- 2. FORM FOCUS ---
  function initFormFocus() {
    const inputs = document.querySelectorAll(
      '.champ input, .champ textarea, .champ select,'
      + '.field input, .field textarea, .field select'
    );
    inputs.forEach((el) => {
      const wrap = el.closest('.champ, .field');
      if (!wrap) return;
      el.addEventListener('focus', () => wrap.classList.add('has-focus'));
      el.addEventListener('blur', () => wrap.classList.remove('has-focus'));
    });
  }

  // --- 3. SUBMIT FEEDBACK ---
  function initSubmitFeedback() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', () => {
      const btn = form.querySelector('button[type="submit"]');
      if (!btn || btn.disabled) return;

      btn.classList.add('is-loading');
      btn.disabled = true;

      // Auto-restore après 8s si la requête n'a pas redirigé / rendu un
      // success div entretemps. Évite d'avoir un bouton bloqué pour de bon
      // en cas d'erreur réseau silencieuse.
      setTimeout(() => {
        btn.disabled = false;
        btn.classList.remove('is-loading');
      }, 8000);
    });

    // Si le success div existe déjà à l'arrivée (réponse serveur après
    // POST), on l'anime vers .is-shown.
    const success = document.getElementById('success');
    if (success && success.offsetParent !== null) {
      // Force reflow puis ajoute la classe pour déclencher la transition
      void success.offsetWidth;
      success.classList.add('is-shown');
    }
  }

  // --- 4. FOOTER REVEAL ---
  function initFooterReveal() {
    const footers = document.querySelectorAll(FOOTER_SELECTOR);
    if (footers.length === 0) return;

    if (REDUCE) {
      footers.forEach((el) => el.classList.add('is-in'));
      return;
    }

    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-in');
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.08, rootMargin: '0px 0px -4% 0px' }
    );
    footers.forEach((el) => io.observe(el));
  }

  // --- INIT ---
  function run() {
    initStagger();
    initFormFocus();
    initSubmitFeedback();
    initFooterReveal();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
})();
