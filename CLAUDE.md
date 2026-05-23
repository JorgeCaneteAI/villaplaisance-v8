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
- DB : depuis 2026-05-23, v8 tourne sur **sa propre DB `efkz3012_VPV8_dev`**
  (snapshot de VPV7), pour permettre les chantiers de schéma sans risque
  sur la prod. User MySQL dédié `efkz3012_VPV8_dev`. Cf.
  `../docs/2026-05-23-db-v8-separee.md` pour la procédure de re-sync prod → dev
  et le rollback `.env.shared-vpv7`.
- L'ancienne DB orpheline `efkz3012_VPV8` (≠ VPV8_dev) est conservée comme
  filet, à supprimer dans une session ultérieure.
- User SSH : `efkz3012`
- Pendant les travaux : htpasswd sur le sous-domaine.

## Workflow de déploiement v8 (IMPORTANT — pas automatique)

Le push GitHub **ne déclenche PAS** le déploiement. Il faut le lancer
manuellement après chaque push.

**Procédure complète depuis un Mac local :**

1. Commit + push local :
```
cd "/Users/jorgecanete/Documents/C.L.A.U.D.E/villaplaisance/Site Internet/v8" && git push origin main
```

2. Déclencher le déploiement côté serveur (terminal cPanel ou SSH) :
```
cd /home/efkz3012/repositories/villaplaisance-v8 && git pull origin main && /bin/cp -R * /home/efkz3012/v2.villaplaisance.fr/ && chmod -R 755 /home/efkz3012/v2.villaplaisance.fr/
```

3. Invalider OPcache (sinon ancien rendu en cache 1-2 min) :
```
touch /home/efkz3012/v2.villaplaisance.fr/public/index.php
```

4. Si tu as ajouté des fichiers dans `public/uploads/` en local, les uploader
   à part (gitignored). Depuis le Mac :
```
rsync -avz "/Users/jorgecanete/Documents/C.L.A.U.D.E/villaplaisance/Site Internet/v8/public/uploads/" efkz3012@efkz3012.odns.fr:/home/efkz3012/v2.villaplaisance.fr/public/uploads/
```

5. Cmd+Shift+R sur https://v2.villaplaisance.fr pour vider le cache navigateur.

Alternative à l'étape 2 : cPanel UI → Git Version Control → `villaplaisance-v8`
→ Update from Remote + Deploy HEAD Commit.

## Règles
- Jamais toucher au code de prod (`villaplaisance.fr` ni repo v7).
- Jamais toucher à la DB de prod (`efkz3012_VPV7`).
- `.env` jamais commité, jamais affiché en clair dans les docs.
- `public/uploads/` géré hors git, transfert manuel.
- 19 seeds doivent passer sur DB vide (voir `DEPLOY.md` hérité).

## Index `docs/`
- `2026-04-30-setup-v8.md` : décisions de setup, état Phase 0.

## Index `../docs/` (niveau maître, hors repo)
- `2026-05-23-db-v8-separee.md` : DB v8 séparée de la prod (piste A).
