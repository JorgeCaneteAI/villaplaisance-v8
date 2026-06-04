/* Tiny FR/EN toggle.
   Usage: any element with [data-en="..."] will swap textContent when EN active.
   The default text in the DOM is French. */

(function () {
  const KEY = 'vp_lang';
  const stored = (typeof localStorage !== 'undefined' && localStorage.getItem(KEY)) || 'fr';

  function apply(lang) {
    document.documentElement.lang = lang;
    document.querySelectorAll('[data-en]').forEach(el => {
      if (!el.hasAttribute('data-fr')) {
        // capture original FR once
        el.setAttribute('data-fr', el.innerHTML);
      }
      const fr = el.getAttribute('data-fr');
      const en = el.getAttribute('data-en');
      el.innerHTML = lang === 'en' ? en : fr;
    });
    document.querySelectorAll('[data-en-attr]').forEach(el => {
      // format: "placeholder|English text", sets el.placeholder when EN
      const spec = el.getAttribute('data-en-attr');
      const [attr, val] = spec.split('|');
      if (!el.hasAttribute('data-fr-attr')) {
        el.setAttribute('data-fr-attr', `${attr}|${el.getAttribute(attr) || ''}`);
      }
      const frSpec = el.getAttribute('data-fr-attr').split('|');
      if (lang === 'en') el.setAttribute(attr, val);
      else el.setAttribute(frSpec[0], frSpec[1] || '');
    });
    document.querySelectorAll('.lang-toggle button').forEach(b => {
      b.classList.toggle('active', b.dataset.lang === lang);
    });
  }

  function bind() {
    document.querySelectorAll('.lang-toggle button').forEach(b => {
      b.addEventListener('click', () => {
        const lang = b.dataset.lang;
        try { localStorage.setItem(KEY, lang); } catch (_) {}
        apply(lang);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => { bind(); apply(stored); });
  } else { bind(); apply(stored); }
})();
