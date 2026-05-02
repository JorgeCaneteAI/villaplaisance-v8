/* Villa Plaisance V2 - motion minimal et cinematic, vanilla pur. */

(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- 1. Opening : reveal puis "detache" au scroll ---------- */
  const opening = document.querySelector('.opening');
  if (opening) {
    requestAnimationFrame(() => opening.classList.add('is-ready'));

    if (!reduce) {
      let detached = false;
      const onScroll = () => {
        if (detached) return;
        if (window.scrollY > 80) {
          opening.classList.add('is-detaching');
          detached = true;
          window.removeEventListener('scroll', onScroll);
        }
      };
      window.addEventListener('scroll', onScroll, { passive: true });
    }
  }

  /* ---------- 2. Reveal au scroll, IntersectionObserver ---------- */
  if (!reduce && 'IntersectionObserver' in window) {
    const targets = [
      '.acte__num',
      '.manifesto__corps',
      '.manifesto__plate',
      '.saison',
      '.distance',
      '.lieu__pont',
      '.mappemonde__viewport',
      '.mappemonde__legende',
      '.voix__bloc',
      '.article',
      '.qa__couple',
      '.contact__phrase',
      '.contact__sub',
      '.contact__actions',
      // Page chambres
      '.lead__corps',
      '.chambre',
      '.rituel__plate',
      '.rituel__corps',
      '.qa-item',
      // Page villa
      '.cellule',
      // Page contact
      '.duo__form',
      '.duo__cotes',
      // Page journal
      '.article-card',
      '.post__entete',
      '.post__cover',
      '.post__corps p',
      '.post__corps h2',
      '.post__cite',
      // Page extérieurs
      '.galerie__cell',
      // Page hôte
      '.bio__plate',
      '.bio__corps',
      '.cv__bloc',
    ];

    document.querySelectorAll(targets.join(',')).forEach((el, i) => {
      el.setAttribute('data-reveal', '');
      // Stagger doux pour les groupes
      if (el.matches('.distance, .qa__couple')) {
        const idx = Array.from(el.parentElement.children).indexOf(el);
        el.setAttribute('data-delay', String((idx % 3) + 1));
      }
    });

    const io = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-in');
          io.unobserve(entry.target);
        }
      }
    }, {
      threshold: 0.12,
      rootMargin: '0px 0px -8% 0px',
    });

    document.querySelectorAll('[data-reveal]').forEach((el) => io.observe(el));
  }

  /* ---------- 3. Mappemonde : carte SVG + pins equirectangulaires ---------- */
  const mappemonde = document.querySelector('[data-mappemonde]');
  if (mappemonde) {
    const W = 1000, H = 500;
    const project = (lat, lng) => [
      ((lng + 180) / 360) * W,
      ((90 - lat) / 180) * H,
    ];

    // Source : table vp_reviews du projet PHP existant
    const villa = { lat: 44.0410, lng: 4.8945, label: 'Villa Plaisance, Bedarrides' };
    const guests = [
      { label: 'Paris, France',                lat: 48.8566, lng:    2.3522 },
      { label: 'Tunisie',                      lat: 33.8869, lng:    9.5375 },
      { label: 'Grece',                        lat: 39.0742, lng:   21.8243 },
      { label: 'Austin, Texas',                lat: 30.2672, lng:  -97.7431 },
      { label: 'New York',                     lat: 40.7128, lng:  -74.0060 },
      { label: 'Norvege',                      lat: 60.4720, lng:    8.4689 },
      { label: 'Burtonsville, Maryland',       lat: 39.1115, lng:  -76.9325 },
      { label: 'Allemagne',                    lat: 51.1657, lng:   10.4515 },
      { label: 'Montreal, Canada',             lat: 45.5017, lng:  -73.5673 },
      { label: 'Georgie, Etats-Unis',          lat: 33.7490, lng:  -84.3880 },
      { label: 'Espagne',                      lat: 40.4168, lng:   -3.7038 },
      { label: 'Suisse',                       lat: 46.8182, lng:    8.2275 },
      { label: 'Pays-Bas',                     lat: 52.1326, lng:    5.2913 },
      { label: 'Costa Mesa, Californie',       lat: 33.6412, lng: -117.9187 },
      { label: 'Charlotte, Caroline du Nord',  lat: 35.2271, lng:  -80.8431 },
      { label: 'Quebec City, Canada',          lat: 46.8139, lng:  -71.2080 },
      { label: 'San Francisco, Californie',    lat: 37.7749, lng: -122.4194 },
      { label: 'Port Townsend, Washington',    lat: 48.1170, lng: -122.7604 },
      { label: 'Maine, Etats-Unis',            lat: 45.2538, lng:  -69.4455 },
      { label: 'Belgique',                     lat: 50.5039, lng:    4.4699 },
      { label: 'Royaume-Uni',                  lat: 54.0000, lng:   -2.0000 },
      { label: 'Sydney, Australie',            lat: -33.8688, lng: 151.2093 },
    ];

    // Charger la carte SVG (chemin absolu pour fonctionner sur toutes les routes)
    fetch('/assets/world.svg')
      .then(r => r.ok ? r.text() : Promise.reject(r.status))
      .then(svg => { mappemonde.querySelector('.mappemonde__map').innerHTML = svg; })
      .catch(() => { /* sans carte la legende reste lisible */ });

    // Dessiner les pins
    const pinsSvg = mappemonde.querySelector('.mappemonde__pins');
    const ns = 'http://www.w3.org/2000/svg';

    const [vx, vy] = project(villa.lat, villa.lng);
    const villaG = document.createElementNS(ns, 'g');
    villaG.setAttribute('class', 'villa');
    const haloV = document.createElementNS(ns, 'circle');
    haloV.setAttribute('class', 'pin-villa-halo');
    haloV.setAttribute('cx', vx); haloV.setAttribute('cy', vy); haloV.setAttribute('r', 12);
    const dotV = document.createElementNS(ns, 'circle');
    dotV.setAttribute('class', 'pin-villa');
    dotV.setAttribute('cx', vx); dotV.setAttribute('cy', vy); dotV.setAttribute('r', 4.5);
    const titleV = document.createElementNS(ns, 'title');
    titleV.textContent = villa.label;
    villaG.append(haloV, dotV, titleV);
    pinsSvg.appendChild(villaG);

    guests.forEach((g) => {
      const [x, y] = project(g.lat, g.lng);
      const grp = document.createElementNS(ns, 'g');
      grp.setAttribute('class', 'guest');
      const halo = document.createElementNS(ns, 'circle');
      halo.setAttribute('class', 'pin-guest-halo');
      halo.setAttribute('cx', x); halo.setAttribute('cy', y); halo.setAttribute('r', 3.2);
      const dot = document.createElementNS(ns, 'circle');
      dot.setAttribute('class', 'pin-guest');
      dot.setAttribute('cx', x); dot.setAttribute('cy', y); dot.setAttribute('r', 3.2);
      const t = document.createElementNS(ns, 'title');
      t.textContent = g.label;
      grp.append(halo, dot, t);
      pinsSvg.appendChild(grp);
    });

    // Liste des villes shufflee
    const villes = mappemonde.querySelector('[data-mappemonde-villes]') ||
                   document.querySelector('[data-mappemonde-villes]');
    if (villes) {
      const shuffled = guests
        .map(g => g.label)
        .sort(() => Math.random() - 0.5);
      villes.innerHTML = shuffled
        .map(l => `<span>${l}</span>`)
        .join('<span class="sep">·</span>');
    }
  }

  /* ---------- 4. aria-current sur la page courante ---------- */
  // Marque le lien dont data-route correspond a l'URL courante.
  // Normalise : retire query/hash, force trailing slash, racine = "/".
  {
    const here = (() => {
      let p = window.location.pathname;
      if (!p || p === '') return '/';
      // Si on tombe sur /foo/index.html on ramene a /foo/
      p = p.replace(/index\.html$/, '');
      // Force trailing slash sauf pour les fichiers terminaux (.html par ex.)
      if (!p.endsWith('/') && !/\.[a-z0-9]+$/i.test(p)) p += '/';
      return p;
    })();

    document.querySelectorAll('a[data-route]').forEach((a) => {
      const route = a.getAttribute('data-route');
      if (route === here) {
        a.setAttribute('aria-current', 'page');
      }
    });
  }

  /* ---------- 5. Menu overlay : ouvrir / fermer ---------- */
  {
    const btnOpen  = document.querySelector('[data-menu-open]');
    const btnClose = document.querySelector('[data-menu-close]');
    const overlay  = document.querySelector('[data-menu-overlay]');

    if (btnOpen && overlay) {
      let lastFocus = null;

      const open = () => {
        lastFocus = document.activeElement;
        overlay.hidden = false;
        // force reflow pour que la transition CSS s'applique
        void overlay.offsetWidth;
        overlay.classList.add('is-open');
        document.documentElement.classList.add('is-menu-open');
        btnOpen.setAttribute('aria-expanded', 'true');
        // focus sur le bouton fermer pour l'accessibilite clavier
        if (btnClose) requestAnimationFrame(() => btnClose.focus());
      };

      const close = () => {
        overlay.classList.remove('is-open');
        document.documentElement.classList.remove('is-menu-open');
        btnOpen.setAttribute('aria-expanded', 'false');
        // attendre la fin de la transition pour cacher reellement
        const onEnd = () => {
          overlay.hidden = true;
          overlay.removeEventListener('transitionend', onEnd);
        };
        if (reduce) {
          overlay.hidden = true;
        } else {
          overlay.addEventListener('transitionend', onEnd);
          // garde-fou si transitionend ne se declenche pas
          setTimeout(() => { if (!overlay.classList.contains('is-open')) overlay.hidden = true; }, 600);
        }
        if (lastFocus && lastFocus.focus) lastFocus.focus();
      };

      btnOpen.addEventListener('click', open);
      if (btnClose) btnClose.addEventListener('click', close);

      // Echap pour fermer
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
          close();
        }
      });

      // Cliquer sur un lien du menu = naviguer + fermer (utile pour les ancres)
      overlay.querySelectorAll('a[href]').forEach((a) => {
        a.addEventListener('click', () => {
          // pas besoin d'attendre, le navigate va detruire le DOM
          close();
        });
      });
    }
  }
})();
