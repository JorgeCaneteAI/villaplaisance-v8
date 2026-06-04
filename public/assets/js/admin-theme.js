/* admin-theme.js, toggle light/dark persisté en localStorage.
   Auto par défaut (suit prefers-color-scheme), override manuel possible. */
(function () {
  'use strict';
  const KEY = 'vp-admin-theme';
  const root = document.documentElement;

  // Lecture immédiate (avant rendu) pour éviter le flash
  try {
    const saved = localStorage.getItem(KEY);
    if (saved === 'light' || saved === 'dark') {
      root.setAttribute('data-theme', saved);
    }
  } catch (_) {}

  function currentTheme() {
    const explicit = root.getAttribute('data-theme');
    if (explicit) return explicit;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function setTheme(theme) {
    if (theme === 'auto') {
      root.removeAttribute('data-theme');
      try { localStorage.removeItem(KEY); } catch (_) {}
    } else {
      root.setAttribute('data-theme', theme);
      try { localStorage.setItem(KEY, theme); } catch (_) {}
    }
  }

  function toggle() {
    setTheme(currentTheme() === 'dark' ? 'light' : 'dark');
  }

  // Branchement du bouton dès que le DOM est prêt
  function init() {
    const btn = document.querySelector('[data-theme-toggle]');
    if (btn) {
      btn.addEventListener('click', toggle);
      btn.setAttribute('aria-label', 'Basculer le thème clair/sombre');
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

/* admin-sidebar.js, drawer mobile */
(function () {
  'use strict';
  function init() {
    const sidebar = document.querySelector('.admin-sidebar');
    const trigger = document.querySelector('[data-sidebar-toggle]');
    if (!sidebar || !trigger) return;

    let backdrop = document.querySelector('.sidebar-backdrop');
    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.className = 'sidebar-backdrop';
      document.body.appendChild(backdrop);
    }

    function open()  { sidebar.classList.add('is-open'); backdrop.classList.add('is-visible'); }
    function close() { sidebar.classList.remove('is-open'); backdrop.classList.remove('is-visible'); }

    trigger.addEventListener('click', () => sidebar.classList.contains('is-open') ? close() : open());
    backdrop.addEventListener('click', close);
    sidebar.addEventListener('click', (e) => {
      // Fermer le drawer après navigation sur un lien
      if (e.target.closest('a.sidebar-link')) close();
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

/* admin-sidebar-groups.js, collapsible des groupes "Contenu" / "SEO" */
(function () {
  'use strict';
  function init() {
    document.querySelectorAll('.sidebar-group').forEach(group => {
      const trigger = group.querySelector('.sidebar-group-trigger');
      if (!trigger) return;
      // Auto-ouverture si un enfant est actif
      if (group.querySelector('.sidebar-link.is-active')) group.classList.add('is-open');
      trigger.addEventListener('click', () => group.classList.toggle('is-open'));
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
