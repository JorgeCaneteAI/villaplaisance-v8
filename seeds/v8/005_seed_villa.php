<?php
declare(strict_types=1);

/**
 * Seed V8 — Page Maison d'hôtes (location-villa-provence).
 *
 * Phase 3.6 — port des blocs faciles.
 *
 * Positions :
 *   1 = hero
 *   2 = stats strip (key stats)
 *   3 = RÉSERVÉ aux 4 chambres (chantier vp_pieces, plus tard)
 *   4 = intérieur (cuisine + salon)
 *   5 = piscine
 *   6 = les espaces (tableau key-value-two-cols)
 *   7 = infos pratiques (tableau key-value avec ancre #infos)
 *   8 = FAQ (lit vp_faq via page_slug='location-villa-provence')
 *
 * Idempotent par page : DELETE WHERE page_slug='location-villa-provence' AND lang='fr'.
 *
 * Usage : cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/005_seed_villa.php
 */

define('ROOT', dirname(__DIR__, 2));
require ROOT . '/config.php';

echo "=== Seed V8 — Page Maison d'hôtes ===\n\n";

// Lookup vp_media par filename
$mediaId = function (string $filename): ?int {
    $row = Database::fetchOne("SELECT id FROM vp_media WHERE filename = ? LIMIT 1", [$filename]);
    return $row ? (int)$row['id'] : null;
};
$cuisineImg = $mediaId('villa-plaisance-cuisine-equipee-01.webp');
$salonImg   = $mediaId('villa-plaisance-salon-salle-a-manger-01.webp');
$piscineImg = $mediaId('villa-plaisance-piscine-privee-05.webp');

foreach ([
    ['cuisine',  $cuisineImg],
    ['salon',    $salonImg],
    ['piscine',  $piscineImg],
] as [$name, $id]) {
    if ($id === null) echo "  ⚠ image '$name' absente de vp_media (bloc sans image)\n";
    else              echo "  → image '$name' = vp_media.id $id\n";
}

// Reset des sections page villa
Database::query("DELETE FROM vp_sections WHERE page_slug = 'location-villa-provence' AND lang = 'fr'");
echo "  → DELETE location-villa-provence/fr\n";

// ============================================================================
// 1. HERO
// ============================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'location-villa-provence',
    'lang'       => 'fr',
    'block_type' => 'hero',
    'title'      => 'Hero villa',
    'content'    => json_encode([
        'title' => "La villa *entière*,\nen toute autonomie.",
        'lede'  => "En juillet et en août, Villa Plaisance ouvre ses portes en location complète — quatre chambres, dix personnes, piscine privée exclusive, cuisine équipée et terrasses face aux vignes. Hors saison, la maison entière peut être louée sur demande.",
        'tags'  => [
            "02 · Maison d'hôtes · Juillet – août",
            'Réponse dans la journée',
        ],
        'ctas' => [
            ['label' => 'Demander un séjour', 'url' => '/contact', 'style' => 'primary'],
            ['label' => 'Infos pratiques',    'url' => '#infos',   'style' => 'ghost'],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 1,
    'active'     => 1,
]);
echo "  ✓ [1] Hero\n";

// ============================================================================
// 2. KEY STATS STRIP
// ============================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'location-villa-provence',
    'lang'       => 'fr',
    'block_type' => 'stats',
    'title'      => 'Key stats strip',
    'content'    => json_encode([
        'display' => 'strip',
        'items' => [
            ['label' => 'Capacité',         'value' => '10 pers.'],
            ['label' => 'Chambres',         'value' => '4'],
            ['label' => 'Piscine privée',   'value' => '12 × 6 m'],
            ['label' => 'Séjour minimum',   'value' => '4 nuits'],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 2,
    'active'     => 1,
]);
echo "  ✓ [2] Key stats strip\n";

// ============================================================================
// 4. INTÉRIEUR : cuisine + salon
// ============================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'location-villa-provence',
    'lang'       => 'fr',
    'block_type' => 'interior',
    'title'      => 'Intérieur — cuisine + salon',
    'content'    => json_encode([
        'label_numeral' => '02',
        'label_text'    => "L'intérieur",
        'heading'       => "Une *cuisine*,\nun *salon*, une longue table.",
        'surface'       => 'stone',
        'items' => [
            [
                'image_id' => $cuisineImg,
                'kicker'   => 'CUISINE · UN ESPACE TOUT-COMPRIS',
                'title'    => 'Entièrement équipée',
                'text'     => 'Piano gaz, lave-vaisselle, réfrigérateur XXL, four, micro-ondes — et tout ce qu\'il faut pour cuisiner à dix.',
            ],
            [
                'image_id' => $salonImg,
                'kicker'   => 'SALON · SALLE À MANGER · LA CONVIVIALITÉ EN TOUTE SIMPLICITÉ',
                'title'    => 'Climatisé, clair, long',
                'text'     => "Grand salon et salle à manger climatisés, facile à vivre. Une longue table où dix personnes tiennent sans qu'on se gêne du coude.",
            ],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 4,
    'active'     => 1,
]);
echo "  ✓ [4] Intérieur (interior)\n";

// ============================================================================
// 5. PISCINE
// ============================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'location-villa-provence',
    'lang'       => 'fr',
    'block_type' => 'piscine',
    'title'      => 'Piscine privée 12 × 6',
    'content'    => json_encode([
        'label_numeral' => '03',
        'label_text'    => 'La piscine',
        'heading'       => "Piscine privée, *12 × 6*.\nPour vous seuls.",
        'lede'          => "Exclusivement réservée à votre groupe, 24h/24. Aucune autre famille ou locataire n'y a accès pendant votre séjour.",
        'image_id'      => $piscineImg,
        'text'          => "Transats, parasols, table de jardin, salon extérieur et douche solaire — et l'option de chauffer la piscine si vous préférez vous baigner dès début juillet.",
        'note'          => "La piscine est clôturée, conformément à la réglementation. Les enfants restent à portée d'œil.",
        'features' => [
            '12 × 6 m, clôturée',
            'Exclusive · 24h/24',
            'Transats & parasols',
            'Table de jardin & salon extérieur',
            'Douche solaire',
            'Chauffage sur demande',
        ],
        'anchor_id'     => 'piscine',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 5,
    'active'     => 1,
]);
echo "  ✓ [5] Piscine\n";

// ============================================================================
// 6. LES ESPACES (tableau key-value-two-cols)
// ============================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'location-villa-provence',
    'lang'       => 'fr',
    'block_type' => 'tableau',
    'title'      => 'Les espaces — inventaire de la maison',
    'content'    => json_encode([
        'label_numeral' => '04',
        'label_text'    => 'Les espaces',
        'heading'       => "Tout ce que la *maison* contient.",
        'intro'         => "L'inventaire complet de la maison — chambres, salles de bain, cuisine, terrasse, jardin, parking. De quoi préparer une longue semaine sans surprise.",
        'surface'       => 'stone',
        'display'       => 'key-value-two-cols',
        'rows' => [
            ['key' => 'Suite parentale',   'value' => 'Lit king 180 × 200 + salle de bain privée + dressing + vue jardin'],
            ['key' => 'Chambre Bleue',     'value' => 'Lit double queen 160 × 200'],
            ['key' => 'Chambre Arche',     'value' => 'Lit double 160 × 200'],
            ['key' => 'Chambre Années 70', 'value' => '2 lits simples 90 × 200 (jumelables)'],
            ['key' => 'Salles de bain',    'value' => '2 salles de bain complètes + 3 WC indépendants'],
            ['key' => 'Salon · S. à manger','value' => 'Grand salon et salle à manger climatisés'],
            ['key' => 'Cuisine',           'value' => 'Piano gaz, lave-vaisselle, réfrigérateur XXL, four, micro-ondes'],
            ['key' => 'Terrasse couverte', 'value' => '40 m² avec salon de jardin 12 places'],
            ['key' => 'Jardin',            'value' => 'Provençal · oliviers, BBQ charbon, terrain de pétanque'],
            ['key' => 'Buanderie',         'value' => 'Lave-linge et sèche-linge'],
            ['key' => 'Connectivité',      'value' => 'Wifi haut débit (fibre) + TV streaming dans chaque pièce de vie'],
            ['key' => 'Parking',           'value' => 'Parking fermé 2 véhicules · lit bébé & chaise haute sur demande'],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 6,
    'active'     => 1,
]);
echo "  ✓ [6] Les espaces (tableau key-value-two-cols)\n";

// ============================================================================
// 7. INFOS PRATIQUES (tableau key-value avec ancre #infos)
// ============================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'location-villa-provence',
    'lang'       => 'fr',
    'block_type' => 'tableau',
    'title'      => 'Infos pratiques villa',
    'content'    => json_encode([
        'label_numeral' => '05',
        'label_text'    => 'Infos pratiques',
        'heading'       => "Tout ce qu'il faut *savoir*.",
        'intro'         => "Dates, capacité, horaires, ce qui est inclus — l'essentiel d'un coup d'œil, avant que vous nous écriviez.",
        'anchor_id'     => 'infos',
        'display'       => 'key-value',
        'rows' => [
            ['key' => 'Période',         'value' => '**Juillet & août**'],
            ['key' => 'Hors saison',     'value' => 'Sur demande, conditions spécifiques'],
            ['key' => 'Arrivée',         'value' => 'À partir de 17h'],
            ['key' => 'Départ',          'value' => 'Avant 10h'],
            ['key' => 'Séjour minimum',  'value' => '**4 nuits**'],
            ['key' => 'Capacité',        'value' => '**10 personnes** max · 4 chambres'],
            ['key' => 'Piscine',         'value' => 'Privée exclusive · 12 × 6 m · chauffage en option'],
            ['key' => 'Ménage',          'value' => 'Fin de séjour inclus · intermédiaire en option'],
            ['key' => 'Animaux',         'value' => 'Non acceptés'],
            ['key' => 'Fumeur',          'value' => 'Non-fumeur'],
            ['key' => 'Linge',           'value' => 'Draps et serviettes fournis'],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 7,
    'active'     => 1,
]);
echo "  ✓ [7] Infos pratiques\n";

// ============================================================================
// 8. FAQ
// ============================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'location-villa-provence',
    'lang'       => 'fr',
    'block_type' => 'faq',
    'title'      => 'FAQ villa',
    'content'    => json_encode([
        'label_numeral' => '06',
        'label_text'    => 'Questions fréquentes',
        'heading'       => "Villa entière,\nles *questions* qui reviennent.",
        'page_slug'     => 'location-villa-provence',
        'surface'       => 'stone',
        'first_open'    => false,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 8,
    'active'     => 1,
]);
echo "  ✓ [8] FAQ (lit vp_faq)\n";

// ============================================================================
// Vérification finale
// ============================================================================
$check = Database::fetchAll(
    "SELECT id, block_type, position FROM vp_sections WHERE page_slug='location-villa-provence' AND lang='fr' ORDER BY position"
);
echo "\nÉtat vp_sections (location-villa-provence/fr) :\n";
foreach ($check as $row) {
    echo "  - id={$row['id']} type={$row['block_type']} pos={$row['position']}\n";
}
echo "\nDone.\n";
