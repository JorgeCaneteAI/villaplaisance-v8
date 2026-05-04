# 2026-05-04 — Mémo pour la prochaine session

Document destiné à **moi-même (Claude)** pour reprendre rapidement. Lire
ce fichier en début de session, après `CLAUDE.md`.

## État actuel (fin de session 2026-05-04)

Phase 3 entièrement terminée — le sous-domaine `https://v2.villaplaisance.fr/`
rend le design V2 sur les 14 pages publiques. Voir le récap complet dans
`docs/2026-04-30-setup-v8.md` (table d'avancement).

```
git log --oneline -8
3cce201  docs(session): clore la Phase 3 (V2 entièrement porté)
e5a0f5b  feat(views): porter étapes 3-E et 3-F (pages dynamiques + contact)
a42ab20  feat(produit): porter les 4 pages produit V2 (Étape D)
ce9314d  docs(session): point de reprise fin de session 2026-05-04
31eb670  feat(static): porter pages statiques V2 (Étape C)
704f82c  feat(home): porter la home V2 en PHP (Étape B)
e5fb07a  feat(layout): porter header + footer + overlay V8 dans le layout
db15d65  fix(deploy): rediriger .cpanel.yml vers v2.villaplaisance.fr
```

## Comment Jorge va me redonner le contexte

```
On reprend Villa Plaisance V8 dans
/Users/jorgecanete/Documents/C.L.A.U.D.E/villaplaisance-v8.
Lis le CLAUDE.md du projet et le dernier doc dans docs/, puis dis-moi
où on en est.
```

Je dois lire `CLAUDE.md` puis ce fichier (le plus récent dans `docs/`).

## Priorité 1 — Phase 4 : dynamisation des sections en dur

Le V2 a été porté en HTML statique. Pour que les modifications admin se
reflètent sur le V2, il faut brancher chaque section sur la DB.

Pour **chaque section listée ci-dessous** :

1. **Vérifier la structure des champs** avant de coder :
   ```bash
   MYSQL=/Applications/MAMP/Library/bin/mysql80/bin/mysql
   $MYSQL -u root -proot -h 127.0.0.1 -P 8889 villaplaisance_v8_dev \
     -e "SELECT * FROM <table> LIMIT 2 \G"
   ```
   Ne JAMAIS coder un `$row['fieldname']` sans avoir vérifié que ce champ
   existe vraiment.

2. **Vérifier que le Controller passe bien la variable à la View** :
   ```bash
   grep -n "compact" app/Controllers/Front/<Controller>.php
   ```

3. **Remplacer le HTML statique par une boucle PHP** avec
   `htmlspecialchars()` partout.

4. **Garder un fallback** : si la DB est vide, montrer le contenu V2 statique
   plutôt que rien (`<?php if (empty($articles)): ?>...<?php else: ?>...<?php endif; ?>`).

5. **Tester en local** sur `localhost:8767` avant de push.

### Sections à dynamiser, par ordre de priorité

| Page | Section | Variable Controller | Table DB | Difficulté |
|---|---|---|---|---|
| `journal.php` (liste) | Articles + filtres | `$articles`, `$categories` | `vp_articles` | 🟢 Facile |
| `surplace.php` (liste) | Articles + filtres | `$articles`, `$categories` | `vp_articles` | 🟢 Facile |
| `home.php` Acte 6 | 3 derniers articles journal | (à ajouter) `$recentArticles` | `vp_articles ORDER BY published_at DESC LIMIT 3` | 🟡 Moyen (modif Controller) |
| `home.php` Acte 5 | 4 témoignages featured | (à ajouter) `$featuredReviews` | `vp_reviews WHERE featured=1` | 🟡 Moyen (modif Controller) |
| `home.php` Acte 7 | FAQ home | (à ajouter) `$homeFaqs` | `vp_faq WHERE page_slug='accueil'` | 🟡 Moyen |
| `chambres.php` Acte 4 | FAQ | (à ajouter) `$faqs` | `vp_faq WHERE page_slug='chambres-d-hotes'` | 🟡 Moyen (le Controller charge déjà `$faqs` pour JSON-LD, juste à passer à la View) |
| `villa.php` Acte 4 | FAQ | (à ajouter) `$faqs` | `vp_faq WHERE page_slug='location-villa-provence'` | 🟡 Moyen |
| `hote.php` | Bio + 4 blocs CV | `$profile`, `$blocks` | `vp_host_profile`, `vp_host_blocks` | 🟡 Moyen |
| `home.php` Mappemonde | 22 destinations hard-codées | (à brancher dans `main-v8.js`) | `vp_reviews.location` | 🔴 Difficile (JS + endpoint JSON ?) |

**Toutes les sections sont marquées d'un commentaire `TODO` inline dans
chaque View** pour faciliter la recherche : `grep -rn "TODO" app/Views/front/`.

### Stratégie pour la Phase 4

Pour gagner du temps et éviter les erreurs :

1. **Faire d'abord les pages où le Controller charge déjà la variable**
   (journal, surplace, chambres FAQ, villa FAQ, hote). Pas besoin de
   modifier le Controller, juste la View.

2. **Puis enrichir HomeController** avec `recentArticles`, `featuredReviews`,
   `homeFaqs`. Modifier `home.php` en parallèle.

3. **Mappemonde en dernier** : ça touche au JS, pas à PHP. Solution probable :
   créer un endpoint `/api/guest-locations.json` côté PHP, et faire un
   `fetch()` dans `main-v8.js` au lieu du tableau hard-codé.

4. **Faire un commit par page** pour pouvoir rollback si ça plante.

## Priorité 2 (optionnel) — Pages encore en v7

Pas urgent. À faire seulement si Jorge le demande explicitement.

- **`/itineraire/{slug}`** (`Views/front/itinerary.php`, 537 lignes) :
  page très spécifique avec carte Google Maps embed, frise horaire des
  étapes, formulaire de commentaire. Pas dans le V2 statique. Si on doit
  la porter, il faudra inventer un design cohérent V2 (probablement basé
  sur `.post` + `.distance` pour les étapes).

- **`/livret`** (`Views/front/livret/`) : page protégée par mot de passe,
  livret d'accueil pour les guests. Pas dans le V2. Probablement pas
  prioritaire car peu vue.

## Priorité 3 (à terme) — Bascule prod

Quand Jorge validera le V8, basculer la prod. **Trois options
documentées dans `docs/2026-04-30-setup-v8.md`** (section "Bascule prod").

Avant de basculer :
- Faire un backup de la DB de prod (`vp_v7`) → un fichier `.sql` en
  lieu sûr.
- Vérifier que `vp_v8` a bien tout le contenu de `vp_v7` (puisqu'on
  l'avait copiée le 2026-05-01, il faut peut-être resynchroniser si
  Jorge a modifié des trucs en prod entre temps).
- Tester un échantillon d'URLs en prod après bascule.

## Pièges connus (à éviter de retomber dedans)

### Dump MariaDB → MySQL local
Si on ré-importe le dump dans MAMP MySQL 8 :
- `utf8mb4_uca1400_ai_ci` → remplacer par `utf8mb4_unicode_ci` :
  `sed 's/utf8mb4_uca1400_ai_ci/utf8mb4_unicode_ci/g' dump.sql > dump-mysql.sql`
- FK avec noms en doublon → renommer la 2e occurrence.

### Déploiement
- `.cpanel.yml` ne doit PAS copier le `.htaccess` racine vers
  `public/.htaccess`. C'est corrigé, mais si on touche au `.cpanel.yml`,
  ne pas réintroduire le bug.
- Au premier `git push`, cPanel ne déclenche pas auto le déploiement.
  Il faut `git pull` + `cp -R *` manuellement en SSH.

### Multilingue
- Toujours utiliser `LangService::url('slug')` pour les liens internes,
  jamais en dur (`/chambres-d-hotes/`).
- Sauf pour les `data-route` qui restent en français (servent à
  `aria-current` côté JS).

### Conventions Jorge (rappel global)
- Tutoiement, français, accents corrects (le ñ de Cañete est obligatoire).
- **Em dashes interdits** (`—`) : préférer virgule, deux-points, point médian.
- Faits avant suppositions : si Jorge mentionne un fichier, le LIRE avant
  de coder.
- Convention nommage doc : `YYYY-MM-DD-<sujet>.md`.

## Workflow de session type

```
1. Lire ce fichier + CLAUDE.md
2. Annoncer brièvement où on en est et ce qu'on va faire
3. Lire le code existant AVANT de modifier (Read tool)
4. Modifier (Edit/Write)
5. Tester en local : http://localhost:8767/ (le serveur PHP doit tourner :
   `php -S localhost:8767 -t public router.php` depuis le dossier projet)
6. Commit + push GitHub
7. Faire faire le déploiement à Jorge en SSH cPanel :
   cd /home/efkz3012/repositories/villaplaisance-v8
   git pull origin main
   export DEPLOYPATH=/home/efkz3012/v2.villaplaisance.fr/
   /bin/cp -R * $DEPLOYPATH
   chmod -R 755 $DEPLOYPATH
8. Vérifier sur https://v2.villaplaisance.fr/
9. En fin de session, mettre à jour le doc d'avancement.
```

## Variables d'environnement à connaître

```
Local MAMP :
  DB_HOST=127.0.0.1   DB_PORT=8889
  DB_USER=root        DB_PASS=root
  DB_NAME=villaplaisance_v8_dev
  Server PHP : http://localhost:8767

o2switch (DEV) :
  DB_NAME=efkz3012_VPV8     DB_USER=efkz3012_VPV8
  Domaine : https://v2.villaplaisance.fr
  Path : /home/efkz3012/v2.villaplaisance.fr/
  Repo : /home/efkz3012/repositories/villaplaisance-v8
  htpasswd : actif (Jorge a les credentials)

o2switch (PROD, on n'y touche pas) :
  DB_NAME=efkz3012_VPV7     DB_USER=efkz3012_vpuser
  Domaine : https://villaplaisance.fr
  Path : /home/efkz3012/villaplaisance.fr/
```

## Repos GitHub

- `villaplaisance-v7` (PROD) : `https://github.com/JorgeCaneteAI/villaplaisance-v7`
- `villaplaisance-v8` (DEV, ce projet) : `https://github.com/JorgeCaneteAI/villaplaisance-v8`
- `villaplaisance-impeccable-V2` (référence design statique) :
  `~/Documents/C.L.A.U.D.E/villaplaisance-impeccable-V2/` (pas de remote)
