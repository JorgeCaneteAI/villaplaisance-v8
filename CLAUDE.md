# Villa Plaisance V8

Refonte du site villaplaisance.fr en gardant **le back-office et la DB
existants intacts** (repo `villaplaisance-v7`). On remplace uniquement
le **front** (Views Front + assets) par le design "impeccable" V2.

## Repos liés
- **`villaplaisance-v7`** (PROD) : `https://github.com/JorgeCaneteAI/villaplaisance-v7.git` → déploie automatiquement sur `villaplaisance.fr` via `.cpanel.yml`. **On n'y touche pas.**
- **`villaplaisance-v8`** (DEV, ce projet) : déploie sur `v2.villaplaisance.fr` (sous-domaine de travail). Une fois validé, on basculera.
- **`villaplaisance-impeccable-V2`** (référence design) : site HTML statique avec le design cible. À porter en PHP ici.

## Stack héritée (on garde)
- PHP 8 + MySQL, autoloader PSR-4 maison (pas Composer).
- Router maison (`app/Router.php`) + Controllers Front/Admin.
- Layout `Views/layouts/front.php` avec SEO depuis `vp_pages`, GA4, JSON-LD, OG.
- Multilingue via `LangService` (fr/en/es).
- Config par `.env` (jamais commité), `config.php` à la racine.

## Stack design (on porte depuis V2)
- Palette OKLCH : `--ivory`, `--ink`, `--terre`, `--sauge`, `--or`.
- Fontes : **Bricolage Grotesque** (display variable) + **EB Garamond** (serif).
- Header `mix-blend-mode: difference`, overlay menu mobile, footer 3 lignes.
- Motion `cubic-bezier(.16, 1, .3, 1)`, respect `prefers-reduced-motion`.
- Em dashes interdits, ton sobre/cinématique/chaleureux.

## Ce qui change vs v7
- **Views Front** réécrites avec le HTML/CSS du V2.
- **Assets** : un seul `assets/styles.css` + un seul `assets/main.js` vanilla.
- **Database.php** : ajout du support `DB_PORT` (utile pour MAMP local sur 8889).

## Local — MAMP
- DB locale : `villaplaisance_v8_dev`
- Server : `php -S localhost:8767 -t public public/index.php`
- Voir `docs/2026-04-30-setup-v8.md` pour l'install pas-à-pas.

## Production — o2switch
- Sous-domaine de dev : `v2.villaplaisance.fr` → `/home/efkz3012/v2.villaplaisance.fr/`
- DB : `efkz3012_VPV8` (séparée de la prod `efkz3012_VPV7`)
- User SSH : `efkz3012`
- Déploiement auto via `.cpanel.yml` à chaque push GitHub.
- Pendant les travaux : htpasswd sur le sous-domaine.

## Règles
- Jamais toucher au code de prod (`villaplaisance.fr` ni repo v7).
- Jamais toucher à la DB de prod (`efkz3012_VPV7`).
- `.env` jamais commité, jamais affiché en clair dans les docs.
- `public/uploads/` géré hors git, transfert manuel.
- 19 seeds doivent passer sur DB vide (voir `DEPLOY.md` hérité).

## Index `docs/`
- `2026-04-30-setup-v8.md` : décisions de setup, état Phase 0.
