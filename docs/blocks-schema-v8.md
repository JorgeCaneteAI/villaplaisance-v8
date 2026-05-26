# Schéma JSON des blocs V8 — `vp_sections.content`

**Date** : 2026-05-26
**Phase** : 2 du chantier B (port `vp_sections`)
**Statut** : référence vivante — à mettre à jour quand un type évolue

---

## Champs communs à TOUS les types

Tous les blocs V8 acceptent ces champs en plus de leurs champs propres :

| Champ | Type | Valeurs | Défaut | Rôle |
|---|---|---|---|---|
| `surface` | string | `default`, `stone`, `sage`, `sage-light` | `default` | Variation de fond de section. `stone` = `linen-100`, `sage` = `sage-200`, `sage-light` = color-mix sage léger. |
| `label_numeral` | string | `01`, `02`, … (optionnel) | auto-calculé sur `position` | Numérotation typographique (`— 01 / Titre`). |
| `label_text` | string | mini-md autorisé | absent | Texte qui suit le numéral (`— 01 / La maison`). Si absent, pas de label affiché. |

**Mini-markdown** dans tous les champs `title`/`heading`/`label_text` :
- `*texte*` → `<em>texte</em>` (italique)
- retour à la ligne réel → `<br>`

Helpers : `TextService::renderTitle($t)` côté rendu HTML, `TextService::stripMarkdown($t)` pour SEO/meta.

---

## Types hérités V7 (rendu réécrit en design V8)

### `hero`
Bandeau de tête de page avec image plein écran, h1, lede, CTAs.

```json
{
  "title": "Villa\n*Plaisance*",
  "lede": "Une maison, deux façons d'y séjourner.",
  "image_id": 42,
  "image_ids": [42, 43, 44],
  "tags": ["Bédarrides · Vaucluse", "Triangle d'Or"],
  "ctas": [
    { "label": "Sept → Juin · Chambres d'hôtes", "url": "/chambres-d-hotes", "style": "primary" },
    { "label": "Juil → Août · Maison d'hôtes", "url": "/location-villa-provence", "style": "ghost" }
  ]
}
```

`image_id` = image principale. `image_ids` = optionnel, pour slideshow. Si présent, `image_id` ignoré.

### `prose`
Bloc texte avec ou sans image latérale.

```json
{
  "heading": "Une maison\nde *charme*\nen Provence.",
  "text": "Premier paragraphe.\n\nDeuxième paragraphe.",
  "image_id": 12,
  "cta": { "label": "En savoir plus", "url": "/chambres-d-hotes" },
  "layout": "text-image-right"
}
```

`layout` : `text-only` (centré, max 65ch) | `text-image-right` | `text-image-left`.

### `cartes`
Grille de chambres/espaces depuis `vp_pieces`.

```json
{
  "heading": "Nos chambres",
  "offer": "bb"
}
```

`offer` : `bb` | `villa` | `both` (filtre `vp_pieces.offer`). Pas de cartes en dur dans le JSON, lecture directe BDD.

### `liste`
Liste structurée d'items.

```json
{
  "heading": "Les espaces extérieurs",
  "items": [
    { "title": "Piscine 12 × 6 m", "text": "Clôturée, terrasse plein sud.", "icon": "pool" },
    { "title": "Jardin clos", "text": "1500 m², oliviers, lavandes." }
  ]
}
```

### `tableau`
Tableau simple (entêtes + lignes).

```json
{
  "heading": "Équipements de la villa",
  "columns": ["Pièce", "Détail"],
  "rows": [
    ["Cuisine", "Lave-vaisselle, four, plaques induction"],
    ["Salon", "Cheminée, TV, wifi fibre"]
  ]
}
```

### `cta`
Bloc d'appel à l'action standalone.

```json
{
  "heading": "Prêt à *réserver* ?",
  "text": "Réponse sous 24h, conseils personnalisés.",
  "cta": { "label": "Nous contacter", "url": "/contact", "style": "primary" }
}
```

### `avis`
Sélection d'avis clients depuis `vp_reviews`.

```json
{
  "heading": "Ce qu'en disent nos hôtes",
  "limit": 4,
  "display": "cards"
}
```

`display` : **`cards`** (grille V7-style) | **`testimonial`** (nouveau V8 — gros article avec citation typographique).

### `faq`
FAQ depuis `vp_faq` filtrée par page.

```json
{
  "heading": "Questions fréquentes",
  "page_slug": "accueil"
}
```

### `stats`
Chiffres clés en grille.

```json
{
  "heading": "En quelques chiffres",
  "items": [
    { "number": "4", "label": "chambres" },
    { "number": "10", "label": "voyageurs" },
    { "number": "12×6", "label": "m piscine" },
    { "number": "9.4", "label": "/10 Booking", "suffix": "/10" }
  ]
}
```

### `territoire`
Carte des distances depuis Villa Plaisance.

```json
{
  "heading": "Au cœur du *Triangle d'Or*",
  "places": [
    { "name": "Avignon", "distance": "15 min", "transport": "voiture" },
    { "name": "Châteauneuf-du-Pape", "distance": "8 min", "transport": "voiture" },
    { "name": "Orange", "distance": "18 min", "transport": "voiture" }
  ]
}
```

### `galerie`
Galerie photos depuis `vp_media`.

```json
{
  "heading": "La villa en images",
  "image_ids": [42, 43, 44, 45, 46],
  "layout": "grid"
}
```

`layout` : `grid` | `masonry` | `carousel`.

### `articles`
Extraits d'articles depuis `vp_articles`.

```json
{
  "heading": "Journal",
  "type": "journal",
  "limit": 3,
  "display": "list"
}
```

`type` : `journal` | `itineraire` | `all`.
`display` : **`list`** (vertical) | **`grid`** (nouveau V8 — mosaïque 2 colonnes pour journal-grid).

### `petit-dejeuner` / `piscine` / `mappemonde`
Types spécifiques V7, à porter en V8 quand on en a besoin. Schéma à
documenter au moment du port.

---

## Nouveau type V8

### `formula` (le seul vrai nouveau type)
Bloc à 2 (ou plus) cartes formules — design signature de la home V8
(B&B / Villa entière).

```json
{
  "heading": "Une maison, *deux* façons d'y séjourner",
  "formulas": [
    {
      "label_numeral": "01",
      "title": "Chambres *d'hôtes*",
      "period": "Septembre — Juin",
      "text": "Vous séjournez chez l'habitant. Deux chambres communicantes, petit-déjeuner inclus.",
      "stats": [
        { "number": "2", "label": "chambres" },
        { "number": "4", "label": "voyageurs max" }
      ],
      "cta": { "label": "Découvrir", "url": "/chambres-d-hotes", "style": "primary" }
    },
    {
      "label_numeral": "02",
      "title": "La *Villa* en exclusivité",
      "period": "Juillet — Août",
      "text": "Maison entière, 4 chambres, piscine privée, jardin clos.",
      "stats": [
        { "number": "4", "label": "chambres" },
        { "number": "10", "label": "voyageurs" },
        { "number": "12×6", "label": "m piscine" }
      ],
      "cta": { "label": "Découvrir", "url": "/location-villa-provence", "style": "primary" }
    }
  ]
}
```

Champs par formule :
- `label_numeral` : numérotation (`01`, `02`) — affichée en haut de la carte.
- `title` : nom de la formule (mini-md).
- `period` : période d'ouverture (texte libre).
- `text` : court paragraphe descriptif.
- `stats` : tableau de chiffres clés (3 max recommandé).
- `cta` : appel à l'action de la carte.

Le rendu place les `formulas[]` côte à côte sur desktop (grille 1fr 1fr),
empilées en colonne sur mobile.

---

## Convention pour les URLs internes dans les CTAs

- URL relative simple : `/chambres-d-hotes` — sera traitée par
  `LangService::url()` au rendu pour le slug localisé.
- URL absolue : `https://…` — ouverte telle quelle (lien externe).
- URL `mailto:` / `tel:` : ouvrir tel quel.
