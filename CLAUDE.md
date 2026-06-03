# Villa Plaisance — code source (anciennement « V8 »)

Ce repo (nom historique `villaplaisance-v8`) sert la **prod** depuis la
bascule du 2026-06-03 : c'est ce code qui répond sur
`villaplaisance.fr`. Le nom v8 est conservé pour ne pas casser deploy
key + clone serveur + remote local ; un renommage GitHub viendra plus
tard.

## Repos liés
- **`villaplaisance-v8`** (PROD actuelle, ce repo) :
  https://github.com/JorgeCaneteAI/villaplaisance-v8.git → déployé sur
  `villaplaisance.fr` via SSH (le `.cpanel.yml` n'est PAS auto-déclenché
  par push, cf. section Workflow).
- **`villaplaisance-v7`** (ARCHIVÉ) : ancien repo, ne plus toucher. Le
  code correspondant côté serveur est dans le dossier archive
  `/home/efkz3012/villaplaisance.fr-V7-LEGACY-2026-06-03/` (à supprimer
  mi-juin).
- **`villaplaisance-impeccable-V2`** (référence design) : site HTML
  statique du design cible. Déjà porté en PHP ici, gardé en référence.

## Stack
- **PHP 8** vanilla + autoloader PSR-4 maison (pas Composer).
- **MySQL/MariaDB** via PDO (singleton `app/Services/Database.php`,
  supporte `DB_PORT` pour MAMP local).
- Router maison (`app/Router.php`) + Controllers Front/Admin séparés.
- Layout `Views/layouts/front.php` avec SEO depuis `vp_pages`, GA4,
  JSON-LD, OG.
- Multilingue **FR/EN/ES** via `LangService` (le DE a été abandonné).
- CMS à blocs (`vp_sections`, 14 types) + admin Pages/Sections refondue
  V2 (sidebar + header + badge DB + toggle thème).
- Config par `.env` à la racine (jamais commité).

## Stack design
- Palette OKLCH : `--ivory`, `--ink`, `--terre`, `--sauge`, `--or`,
  `--olive-900`.
- Fontes front : **Bricolage Grotesque** (display variable) + **EB
  Garamond** (serif).
- Fontes admin : **Inter** + **JetBrains Mono**, tokens slate/zinc +
  accent indigo (#4F46E5).
- Header `mix-blend-mode: difference`, overlay menu mobile, footer 3 lignes.
- Motion `cubic-bezier(.16, 1, .3, 1)`, respect `prefers-reduced-motion`.
- Em dashes interdits dans les textes (front + admin), ton sobre /
  cinématique / chaleureux.

## Local — MAMP

- DB locale : `vp_local` (importer le dernier dump depuis
  `database/backups/`).
- Serveur : `cd "/Users/jorgecanete/Documents/C.L.A.U.D.E/villaplaisance/Site Internet/v8" && php -S localhost:8767 -t public`
- Setup détaillé : voir `../CLAUDE.md` du dossier maître (section
  « Setup local hors-ligne »).

## Production — o2switch

- Domaine principal : `villaplaisance.fr`
- Dossier serveur : `/home/efkz3012/villaplaisance.fr/`
- DocumentRoot Apache : `/home/efkz3012/villaplaisance.fr/public/`
- DB : **`efkz3012_VP_Prod`** (unique source de vérité).
- User SSH : `efkz3012`, clé ED25519 (voir `../o2switch-acces-ssh.md`).
- Cron iCal : `0,30 * * * * /usr/local/bin/php /home/efkz3012/villaplaisance.fr/bin/sync_ical.php >> /home/efkz3012/logs/ical_sync.log 2>&1`

## Workflow de déploiement (IMPORTANT — pas automatique)

Le push GitHub **ne déclenche PAS** le déploiement. Il faut le lancer
manuellement.

**Procédure complète** (depuis 2026-06-03, je peux exécuter ces étapes
directement via SSH/git, plus besoin de copier-coller à Jorge — cf.
mémoire `feedback_claude_autonomie_ssh_git`) :

1. Commit + push local :
```
cd "/Users/jorgecanete/Documents/C.L.A.U.D.E/villaplaisance/Site Internet/v8" && git push origin main
```

2. Déclencher le déploiement côté serveur (SSH ou terminal cPanel) :
```
cd /home/efkz3012/repositories/villaplaisance-v8 && git pull origin main && /bin/cp -R * /home/efkz3012/villaplaisance.fr/ && chmod -R 755 /home/efkz3012/villaplaisance.fr/
```

3. Invalider OPcache (sinon ancien rendu 1-2 min) :
```
touch /home/efkz3012/villaplaisance.fr/public/index.php
```

4. Si nouveaux fichiers dans `public/uploads/` (gitignored) :
```
rsync -avz "/Users/jorgecanete/Documents/C.L.A.U.D.E/villaplaisance/Site Internet/v8/public/uploads/" efkz3012@efkz3012.odns.fr:/home/efkz3012/villaplaisance.fr/public/uploads/
```

5. Vérif navigateur : Cmd+Shift+R sur https://villaplaisance.fr

Alternative à l'étape 2 : cPanel UI → Git Version Control →
`villaplaisance-v8` → Update from Remote + Deploy HEAD Commit.

## Règles
- Toute modif Villa Plaisance passe par ce repo (plus de v7 actif).
- DB de prod = `VP_Prod`. Pas de `TRUNCATE`/`ALTER`/`DROP`/`UPDATE`
  groupé sans consentement explicite Jorge (cf.
  `feedback_no_db_changes_without_consent`).
- `.env` jamais commité, jamais affiché en clair dans les docs.
- `public/uploads/` géré hors git, transfert manuel ou via l'admin.
- Mot **"luxe"** interdit (et « luxury » EN / « lujo » ES).
- **Aucun tarif** sur le site.
- `declare(strict_types=1)` en tête de chaque fichier PHP.
- SQL : **PDO préparé uniquement**, jamais d'interpolation.
- Images : **WebP uniquement**, < 200 Ko, `alt` obligatoire.

## Index `docs/` (interne à ce repo)
- `2026-04-30-setup-v8.md` — Décisions de setup, état Phase 0.

## Index `../docs/` (dossier maître, hors repo — voir aussi le CLAUDE.md maître)
- `2026-05-23-db-v8-separee.md`
- `2026-06-03-bascule-vpprod-et-refonte-admin.md`
- `2026-06-03-i18n-et-cleanup-serveur.md` — **À lire en premier** pour
  reprendre une session après la bascule du 2026-06-03 après-midi.
