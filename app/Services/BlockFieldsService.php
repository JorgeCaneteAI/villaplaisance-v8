<?php
declare(strict_types=1);

/**
 * BlockFieldsService — définition des champs JSON pour chaque type de bloc V8.
 *
 * Sert de source de vérité pour générer le formulaire d'édition d'un bloc
 * dans l'admin, en remplacement du JSON brut.
 *
 * Types de champ supportés :
 *   - 'text'      : input texte simple
 *   - 'textarea'  : zone de texte multi-lignes (mini-md autorisé : *italique*, **gras**, retours ligne)
 *   - 'select'    : liste déroulante (options)
 *   - 'media_id'  : référence vp_media.id (en V1 : input number + bouton "Choisir une image" placeholder)
 *   - 'repeater'  : tableau d'items typés (en V1 : textarea JSON, en V2 : vrai repeater UI)
 *   - 'bool'      : checkbox
 *   - 'int'       : input number
 *
 * Chaque champ a au minimum :
 *   - name  : clé JSON dans vp_sections.content
 *   - label : libellé affiché
 *   - type  : type de champ
 * Et selon le type, des paramètres : options[], help, default, item_fields[], etc.
 */
class BlockFieldsService
{
    /** Champs communs ajoutés à TOUS les types (cf. blocks-schema-v8.md §1). */
    public static function commonFields(): array
    {
        return [
            ['name' => 'label_numeral', 'label' => 'Numéro de section', 'type' => 'text', 'help' => 'ex. "01" — affiché en mono italique ("— 01 / …"). Optionnel, auto-calculé sur position si vide.'],
            ['name' => 'label_text', 'label' => 'Label de section', 'type' => 'text', 'help' => 'ex. "La maison" — mini-md autorisé.'],
            ['name' => 'surface', 'label' => 'Fond de section', 'type' => 'select', 'options' => [
                'default' => 'Par défaut (linen-50)',
                'stone' => 'Stone (linen-100)',
                'sage' => 'Sage (sage-200)',
                'sage-light' => 'Sage clair (mix)',
            ], 'default' => 'default'],
        ];
    }

    /**
     * Cast récursivement une valeur POST brute selon la définition du champ.
     *
     * Sert dans SectionController::save() pour transformer ce que PHP a parsé
     * de $_POST en valeurs typées avant le json_encode → vp_sections.content.
     *
     * Règles :
     * - int / media_id : (int) si non vide, sinon null
     * - bool           : true si "1"/1/true, false sinon
     * - text/textarea/select : string telle quelle
     * - repeater       : array d'items, re-séquencé (sans trou),
     *                    chaque item casté selon item_type ou item_fields,
     *                    items vides supprimés
     */
    public static function castValue($value, array $field)
    {
        $type = (string)($field['type'] ?? 'text');

        if ($type === 'repeater') {
            // Décoder si valeur JSON brute (mode debug ou import)
            if (is_string($value)) {
                $value = trim($value);
                if ($value === '' || $value === '[]') return [];
                $decoded = json_decode($value, true);
                $value = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
            }
            if (!is_array($value)) return [];

            $itemType = $field['item_type'] ?? null;
            $itemFields = $field['item_fields'] ?? null;
            $out = [];
            foreach ($value as $rawItem) {
                if ($itemType === 'int') {
                    if ($rawItem === '' || $rawItem === null) continue;
                    $out[] = (int)$rawItem;
                } elseif ($itemType === 'string') {
                    $s = is_scalar($rawItem) ? trim((string)$rawItem) : '';
                    if ($s === '') continue;
                    $out[] = $s;
                } elseif (is_array($itemFields) && is_array($rawItem)) {
                    $castedItem = [];
                    foreach ($itemFields as $subField) {
                        $subName = (string)($subField['name'] ?? '');
                        if ($subName === '') continue;
                        if (!array_key_exists($subName, $rawItem)) continue;
                        $castedItem[$subName] = self::castValue($rawItem[$subName], $subField);
                    }
                    // Supprimer les sous-champs vides du JSON pour ne pas le polluer
                    $castedItem = array_filter($castedItem, static function ($v) {
                        return !($v === '' || $v === null || (is_array($v) && empty($v)));
                    });
                    if (!empty($castedItem)) $out[] = $castedItem;
                }
            }
            return $out;
        }

        if ($type === 'int' || $type === 'media_id') {
            if ($value === '' || $value === null) return null;
            return (int)$value;
        }
        if ($type === 'bool') {
            return ($value === '1' || $value === 1 || $value === true);
        }
        // text / textarea / select / default : string ou tel quel
        if (is_scalar($value)) return (string)$value;
        return $value;
    }

    /** Définition complète par type — surcharge ou complète commonFields. */
    public static function fieldsFor(string $blockType): array
    {
        $defs = self::registry()[$blockType] ?? null;
        if ($defs === null) {
            // Type inconnu : on retourne les communs uniquement
            return self::commonFields();
        }
        // Si la définition contient 'inherit_common' = false, on ne préfixe pas les communs.
        $common = ($defs['inherit_common'] ?? true) ? self::commonFields() : [];
        return array_merge($common, $defs['fields'] ?? []);
    }

    /** Tous les types et leur libellé d'affichage. */
    public static function allTypes(): array
    {
        $out = [];
        foreach (self::registry() as $key => $def) {
            $out[$key] = $def['label'] ?? $key;
        }
        return $out;
    }

    /** Registre central des 16 types V8. */
    private static function registry(): array
    {
        return [
            // ──────────────────────────────────────────────────────────────
            // HERO
            // ──────────────────────────────────────────────────────────────
            'hero' => [
                'label' => 'Hero (en-tête de page)',
                'fields' => [
                    ['name' => 'title', 'label' => 'Titre (h1)', 'type' => 'textarea', 'help' => 'Mini-md : *italique*, **gras**, retours ligne pour <br>.'],
                    ['name' => 'lede', 'label' => 'Lede (paragraphe sous le titre)', 'type' => 'textarea'],
                    ['name' => 'image_id', 'label' => 'Image de fond', 'type' => 'media_id', 'help' => 'Référence vp_media.id. Laisser vide pour un hero sans image.'],
                    ['name' => 'tags', 'label' => 'Tags (badges sous le titre)', 'type' => 'repeater', 'item_type' => 'string'],
                    ['name' => 'ctas', 'label' => 'Boutons d\'action', 'type' => 'repeater', 'item_fields' => [
                        ['name' => 'label', 'label' => 'Libellé', 'type' => 'text'],
                        ['name' => 'url', 'label' => 'URL', 'type' => 'text'],
                        ['name' => 'style', 'label' => 'Style', 'type' => 'select', 'options' => ['primary' => 'Primary', 'ghost' => 'Ghost']],
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // PROSE
            // ──────────────────────────────────────────────────────────────
            'prose' => [
                'label' => 'Texte (h2 + paragraphes)',
                'fields' => [
                    ['name' => 'layout', 'label' => 'Mise en page', 'type' => 'select', 'options' => [
                        'two-col' => '2 colonnes : titre à gauche, texte à droite',
                        'text-only' => 'Texte centré seul',
                        'text-image-right' => 'Texte + image à droite (éditorial asymétrique)',
                        'text-image-left' => 'Image à gauche + texte (éditorial asymétrique)',
                        'two-col-image-right' => '2 colonnes texte + image à droite (simple, aligné)',
                        'two-col-image-left' => '2 colonnes image à gauche + texte (simple, aligné)',
                    ], 'default' => 'two-col'],
                    ['name' => 'heading', 'label' => 'Titre (h2)', 'type' => 'textarea', 'help' => 'Mini-md : *italique*, **gras**, retours ligne.'],
                    ['name' => 'text', 'label' => 'Texte (1 ligne vide = nouveau paragraphe)', 'type' => 'textarea', 'help' => 'Le 1er paragraphe = lede, les suivants = body-lg.'],
                    ['name' => 'image_id', 'label' => 'Image (si layout image)', 'type' => 'media_id'],
                    ['name' => 'cta', 'label' => 'Bouton d\'action (optionnel)', 'type' => 'repeater', 'max' => 1, 'item_fields' => [
                        ['name' => 'label', 'label' => 'Libellé', 'type' => 'text'],
                        ['name' => 'url', 'label' => 'URL', 'type' => 'text'],
                        ['name' => 'style', 'label' => 'Style', 'type' => 'select', 'options' => ['primary' => 'Primary', 'ghost' => 'Ghost']],
                    ]],
                    ['name' => 'stats_band', 'label' => 'Bande de stats (signature V8 sous le texte)', 'type' => 'repeater', 'item_fields' => [
                        ['name' => 'label', 'label' => 'Label', 'type' => 'text'],
                        ['name' => 'value', 'label' => 'Valeur', 'type' => 'text'],
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // CARTES (chambres / espaces depuis vp_pieces)
            // ──────────────────────────────────────────────────────────────
            'cartes' => [
                'label' => 'Cartes chambres (lit vp_pieces)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre (h2, affiché en mode villa)', 'type' => 'textarea'],
                    ['name' => 'intro', 'label' => 'Intro (paragraphe à droite, mode villa)', 'type' => 'textarea'],
                    ['name' => 'offer', 'label' => 'Offre', 'type' => 'select', 'options' => [
                        'bb' => 'Chambres d\'hôtes (B&B) — markup .ch-room (1 section/chambre)',
                        'villa' => 'Villa entière — markup .room-card-x (grille)',
                        'both' => 'Les deux',
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // LISTE
            // ──────────────────────────────────────────────────────────────
            'liste' => [
                'label' => 'Liste (items)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'intro', 'label' => 'Intro (paragraphe à droite du titre)', 'type' => 'textarea'],
                    ['name' => 'display', 'label' => 'Affichage', 'type' => 'select', 'options' => [
                        'numbered-grid' => 'Grille numérotée 01, 02, … (V8)',
                        'simple' => 'Liste verticale simple',
                    ], 'default' => 'simple'],
                    ['name' => 'items', 'label' => 'Items (un par ligne)', 'type' => 'repeater', 'item_type' => 'string'],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // TABLEAU
            // ──────────────────────────────────────────────────────────────
            'tableau' => [
                'label' => 'Tableau (clé/valeur ou colonnes)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'intro', 'label' => 'Intro', 'type' => 'textarea'],
                    ['name' => 'display', 'label' => 'Affichage', 'type' => 'select', 'options' => [
                        'key-value' => 'Liste clé/valeur (.practical)',
                        'key-value-two-cols' => '2 colonnes clé/valeur (.espaces)',
                        'columns' => 'Tableau avec entêtes',
                    ], 'default' => 'key-value'],
                    ['name' => 'anchor_id', 'label' => 'Ancre HTML (id, ex. "infos")', 'type' => 'text', 'help' => 'Optionnel — permet les liens internes #id.'],
                    ['name' => 'rows', 'label' => 'Lignes', 'type' => 'repeater', 'item_fields' => [
                        ['name' => 'key', 'label' => 'Clé', 'type' => 'text'],
                        ['name' => 'value', 'label' => 'Valeur (mini-md autorisé)', 'type' => 'text'],
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // CTA
            // ──────────────────────────────────────────────────────────────
            'cta' => [
                'label' => 'Call to Action',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'text', 'label' => 'Texte', 'type' => 'textarea'],
                    ['name' => 'cta', 'label' => 'Bouton', 'type' => 'repeater', 'max' => 1, 'item_fields' => [
                        ['name' => 'label', 'label' => 'Libellé', 'type' => 'text'],
                        ['name' => 'url', 'label' => 'URL', 'type' => 'text'],
                        ['name' => 'style', 'label' => 'Style', 'type' => 'select', 'options' => ['primary' => 'Primary', 'ghost' => 'Ghost']],
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // AVIS
            // ──────────────────────────────────────────────────────────────
            'avis' => [
                'label' => 'Avis clients',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'intro', 'label' => 'Intro (texte à droite)', 'type' => 'textarea'],
                    ['name' => 'intro_style', 'label' => 'Style intro', 'type' => 'select', 'options' => [
                        'normal' => 'Normal (paragraphe)',
                        'placeholder' => 'Placeholder (badge avec point)',
                    ], 'default' => 'normal'],
                    ['name' => 'display', 'label' => 'Affichage', 'type' => 'select', 'options' => [
                        'testimonial' => 'Témoignages (cards V8)',
                        'cards' => 'Cards V7-style',
                    ], 'default' => 'testimonial'],
                    ['name' => 'source', 'label' => 'Source', 'type' => 'select', 'options' => [
                        'manual' => 'Manuel (items ci-dessous)',
                        'auto' => 'Auto (vp_reviews featured=1)',
                    ], 'default' => 'manual'],
                    ['name' => 'limit', 'label' => 'Limite (si source=auto)', 'type' => 'int', 'default' => 3],
                    ['name' => 'items', 'label' => 'Avis manuels (si source=manual)', 'type' => 'repeater', 'item_fields' => [
                        ['name' => 'rating', 'label' => 'Note (1-5)', 'type' => 'int', 'default' => 5],
                        ['name' => 'quote', 'label' => 'Citation', 'type' => 'textarea'],
                        ['name' => 'author', 'label' => 'Auteur', 'type' => 'text'],
                        ['name' => 'location', 'label' => 'Lieu', 'type' => 'text'],
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // FAQ
            // ──────────────────────────────────────────────────────────────
            'faq' => [
                'label' => 'FAQ (lit vp_faq)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'intro', 'label' => 'Intro', 'type' => 'textarea'],
                    ['name' => 'page_slug', 'label' => 'Slug page à filtrer dans vp_faq', 'type' => 'text', 'help' => 'Défaut : slug de la page courante.'],
                    ['name' => 'first_open', 'label' => 'Première question dépliée par défaut', 'type' => 'bool', 'default' => true],
                    ['name' => 'limit', 'label' => 'Limite (optionnel)', 'type' => 'int'],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // STATS
            // ──────────────────────────────────────────────────────────────
            'stats' => [
                'label' => 'Chiffres clés',
                'fields' => [
                    ['name' => 'display', 'label' => 'Affichage', 'type' => 'select', 'options' => [
                        'strip' => 'Bande horizontale pleine largeur (V8)',
                        'grid' => 'Grille de cartes',
                    ], 'default' => 'strip'],
                    ['name' => 'heading', 'label' => 'Titre (display=grid uniquement)', 'type' => 'textarea'],
                    ['name' => 'items', 'label' => 'Cellules', 'type' => 'repeater', 'item_fields' => [
                        ['name' => 'label', 'label' => 'Label', 'type' => 'text'],
                        ['name' => 'value', 'label' => 'Valeur', 'type' => 'text'],
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // TERRITOIRE
            // ──────────────────────────────────────────────────────────────
            'territoire' => [
                'label' => 'Territoire (lieux + distances)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'intro', 'label' => 'Intro', 'type' => 'textarea'],
                    ['name' => 'destinations', 'label' => 'Destinations', 'type' => 'repeater', 'item_fields' => [
                        ['name' => 'time', 'label' => 'Durée (ex. "8 MIN")', 'type' => 'text'],
                        ['name' => 'place', 'label' => 'Lieu (rendu en italique)', 'type' => 'text'],
                        ['name' => 'tag', 'label' => 'Étiquette (sous-titre)', 'type' => 'text'],
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // GALERIE
            // ──────────────────────────────────────────────────────────────
            'galerie' => [
                'label' => 'Galerie photos',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'layout', 'label' => 'Mise en page', 'type' => 'select', 'options' => [
                        'grid' => 'Grille', 'masonry' => 'Masonry', 'carousel' => 'Carrousel',
                    ], 'default' => 'grid'],
                    ['name' => 'image_ids', 'label' => 'Images (IDs vp_media, un par ligne)', 'type' => 'repeater', 'item_type' => 'int'],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // ARTICLES
            // ──────────────────────────────────────────────────────────────
            'articles' => [
                'label' => 'Articles (auto ou cartes manuelles)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'intro', 'label' => 'Intro', 'type' => 'textarea'],
                    ['name' => 'source', 'label' => 'Source', 'type' => 'select', 'options' => [
                        'manual' => 'Manuel (cartes ci-dessous)',
                        'auto' => 'Auto (vp_articles)',
                    ], 'default' => 'manual'],
                    ['name' => 'display', 'label' => 'Affichage', 'type' => 'select', 'options' => [
                        'grid' => 'Mosaïque (V8)', 'list' => 'Liste',
                    ], 'default' => 'grid'],
                    ['name' => 'type', 'label' => 'Type (si source=auto)', 'type' => 'select', 'options' => [
                        'journal' => 'Journal', 'itineraire' => 'Itinéraire', 'all' => 'Tous',
                    ], 'default' => 'journal'],
                    ['name' => 'limit', 'label' => 'Limite', 'type' => 'int', 'default' => 3],
                    ['name' => 'items', 'label' => 'Cartes manuelles', 'type' => 'repeater', 'item_fields' => [
                        ['name' => 'image_id', 'label' => 'Image', 'type' => 'media_id'],
                        ['name' => 'kicker', 'label' => 'Kicker (mono uppercase)', 'type' => 'text'],
                        ['name' => 'title', 'label' => 'Titre (mini-md)', 'type' => 'textarea'],
                        ['name' => 'text', 'label' => 'Texte', 'type' => 'textarea'],
                        ['name' => 'cta', 'label' => 'CTA', 'type' => 'repeater', 'max' => 1, 'item_fields' => [
                            ['name' => 'label', 'label' => 'Libellé', 'type' => 'text'],
                            ['name' => 'url', 'label' => 'URL', 'type' => 'text'],
                        ]],
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // PETIT-DEJEUNER
            // ──────────────────────────────────────────────────────────────
            'petit-dejeuner' => [
                'label' => 'Petit-déjeuner (image + liste)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'text', 'label' => 'Paragraphe', 'type' => 'textarea'],
                    ['name' => 'image_id', 'label' => 'Image à gauche', 'type' => 'media_id'],
                    ['name' => 'items', 'label' => 'Items du petit-déjeuner', 'type' => 'repeater', 'item_type' => 'string'],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // PISCINE
            // ──────────────────────────────────────────────────────────────
            'piscine' => [
                'label' => 'Piscine (image 21:9 + features)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'lede', 'label' => 'Lede', 'type' => 'textarea'],
                    ['name' => 'image_id', 'label' => 'Grande image (21:9)', 'type' => 'media_id'],
                    ['name' => 'text', 'label' => 'Texte principal', 'type' => 'textarea'],
                    ['name' => 'note', 'label' => 'Note grise (sécurité, etc.)', 'type' => 'textarea'],
                    ['name' => 'features', 'label' => 'Liste des features (un par ligne)', 'type' => 'repeater', 'item_type' => 'string'],
                    ['name' => 'anchor_id', 'label' => 'Ancre HTML (ex. "piscine")', 'type' => 'text'],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // MAPPEMONDE (V8 : laissé en HTML dur, mais le type reste défini)
            // ──────────────────────────────────────────────────────────────
            'mappemonde' => [
                'label' => 'Mappemonde (origines clients)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'intro', 'label' => 'Intro', 'type' => 'textarea'],
                    // Marqueurs à modéliser plus tard
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // FORMULA (nouveau V8 — la home)
            // ──────────────────────────────────────────────────────────────
            'formula' => [
                'label' => 'Formules (cartes B&B/Villa avec stats + CTA)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'intro', 'label' => 'Intro (texte à droite du titre)', 'type' => 'textarea'],
                    ['name' => 'formulas', 'label' => 'Cartes formules', 'type' => 'repeater', 'item_fields' => [
                        ['name' => 'label_numeral', 'label' => 'N°', 'type' => 'text'],
                        ['name' => 'label_period', 'label' => 'Période', 'type' => 'text'],
                        ['name' => 'label_tag', 'label' => 'Étiquette droite', 'type' => 'text'],
                        ['name' => 'title', 'label' => 'Titre', 'type' => 'text'],
                        ['name' => 'text', 'label' => 'Description', 'type' => 'textarea'],
                        ['name' => 'stats', 'label' => 'Stat-pills (un par ligne)', 'type' => 'repeater', 'item_type' => 'string'],
                        ['name' => 'cta', 'label' => 'CTA', 'type' => 'repeater', 'max' => 1, 'item_fields' => [
                            ['name' => 'label', 'label' => 'Libellé', 'type' => 'text'],
                            ['name' => 'url', 'label' => 'URL', 'type' => 'text'],
                            ['name' => 'style', 'label' => 'Style', 'type' => 'select', 'options' => ['primary' => 'Primary']],
                        ]],
                    ]],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // INTERIOR (nouveau V8 — villa)
            // ──────────────────────────────────────────────────────────────
            'interior' => [
                'label' => 'Intérieur (cards image + kicker + titre, sans lien)',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Titre', 'type' => 'textarea'],
                    ['name' => 'items', 'label' => 'Espaces', 'type' => 'repeater', 'item_fields' => [
                        ['name' => 'image_id', 'label' => 'Image', 'type' => 'media_id'],
                        ['name' => 'kicker', 'label' => 'Kicker (mono uppercase)', 'type' => 'text'],
                        ['name' => 'title', 'label' => 'Titre (italique serif)', 'type' => 'text'],
                        ['name' => 'text', 'label' => 'Texte', 'type' => 'textarea'],
                    ]],
                ],
            ],
        ];
    }
}
