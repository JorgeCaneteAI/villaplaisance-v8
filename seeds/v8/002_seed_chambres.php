<?php
declare(strict_types=1);

/**
 * Seed V8 — Page Chambres d'hôtes (premiers blocs).
 *
 * Phase 3 du chantier B — port progressif des autres pages V8.
 *
 * Idempotent par page : DELETE WHERE page_slug='chambres-d-hotes' AND lang='fr'.
 *
 * ⚠️ Le widget « Disponibilités » (ruban saisonnier) entre le hero et l'intro
 * reste un include direct dans la vue (`_partials/calendar_ribbon.php`) lu via
 * PublicAvailabilityService. Il n'est PAS un bloc vp_sections.
 *
 * Usage : cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/002_seed_chambres.php
 */

define('ROOT', dirname(__DIR__, 2));
require ROOT . '/config.php';

echo "=== Seed V8 — Page Chambres d'hôtes ===\n\n";

// Reset des sections de la page chambres/fr (idempotence)
Database::query("DELETE FROM vp_sections WHERE page_slug = 'chambres-d-hotes' AND lang = 'fr'");
echo "  → DELETE chambres-d-hotes/fr\n";

// ========================================================================
// BLOC 1 — HERO (sans image fond, page-hero standard)
// ========================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'chambres-d-hotes',
    'lang'       => 'fr',
    'block_type' => 'hero',
    'title'      => 'Hero chambres',
    'content'    => json_encode([
        'title' => "Une *suite*,\ndeux chambres,\nun petit-déjeuner.",
        'lede'  => "De septembre à juin, nous accueillons nos hôtes à Villa Plaisance dans deux chambres de charme — petit-déjeuner maison, piscine partagée, jardins ombragés.",
        'tags'  => [
            '01 · Chambres d\'hôtes · Septembre → juin',
            'Réponse dans la journée',
        ],
        'ctas' => [
            ['label' => 'Demander un séjour', 'url' => '/contact',  'style' => 'primary'],
            ['label' => 'Infos pratiques',    'url' => '#infos',    'style' => 'ghost'],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 1,
    'active'     => 1,
]);
echo "  ✓ [1] Hero\n";

// ========================================================================
// BLOC 2 — INTRO (prose two-col + stats_band)
// ========================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'chambres-d-hotes',
    'lang'       => 'fr',
    'block_type' => 'prose',
    'title'      => 'Intro chambres B&B',
    'content'    => json_encode([
        'label_numeral' => '01',
        'label_text'    => "Les chambres d'hôtes",
        'heading'       => "Chambres\nd'hôtes *B&B*\nà Bédarrides.",
        'text'          => "À Villa Plaisance, nous accueillons nos hôtes de septembre à juin dans une suite privée formée de deux chambres communicantes, pensées pour le confort et l'authenticité provençale."
                          . "\n\n"
                          . "Une seule entrée, une seule réservation — les deux chambres se louent ensemble, pour une à cinq personnes. Petit-déjeuner maison, piscine partagée, jardins ombragés, à 15 min d'Avignon et 8 min de Châteauneuf-du-Pape.",
        'layout'        => 'two-col',
        'stats_band' => [
            ['label' => 'Capacité',       'value' => '1 — 5 pers.'],
            ['label' => 'Petit-déjeuner', 'value' => 'Inclus'],
            ['label' => 'Piscine',        'value' => 'Partagée'],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 2,
    'active'     => 1,
]);
echo "  ✓ [2] Intro (prose two-col + stats_band)\n";

// ========================================================================
// BLOC 6 (numéral 05) — PETIT-DÉJEUNER
// NB. positions 3/4/5 réservées aux blocs HTML dur de la page :
//   - 3 = How it works · Suite explainer
//   - 4 = Chambre Verte
//   - 5 = Chambre Bleue + Salle de bain
// On les portera plus tard, en même temps que la refonte cartes ↔ vp_pieces.
// ========================================================================
$mediaId = function (string $filename): ?int {
    $row = Database::fetchOne("SELECT id FROM vp_media WHERE filename = ? LIMIT 1", [$filename]);
    return $row ? (int)$row['id'] : null;
};
$breakfastImg = $mediaId('villa-plaisance-petit-dejeuner-brioche-01.webp')
             ?? $mediaId('petit-dejeuner.webp');
if ($breakfastImg === null) echo "  ⚠ image petit-déjeuner absente de vp_media (bloc sans image)\n";
else                        echo "  → image petit-déjeuner = vp_media.id $breakfastImg\n";

Database::insert('vp_sections', [
    'page_slug'  => 'chambres-d-hotes',
    'lang'       => 'fr',
    'block_type' => 'petit-dejeuner',
    'title'      => 'Petit-déjeuner inclus',
    'content'    => json_encode([
        'label_numeral' => '05',
        'label_text'    => 'Petit-déjeuner',
        'heading'       => "Petit-déjeuner\n*maison*, inclus.",
        'text'          => "Chaque matin, de 7h30 à 10h, en terrasse ou en véranda selon la saison.",
        'image_id'      => $breakfastImg,
        'surface'       => 'sage-light',
        'items' => [
            'Confitures artisanales (figues, abricots, lavande-miel)',
            'Viennoiseries',
            'Pain de boulanger',
            'Fromages provençaux',
            'Charcuterie régionale',
            'Fruits frais de saison',
            "Jus d'orange pressé",
            'Café, thé, tisanes bio',
            'Miel',
            'Yaourt maison',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 6,
    'active'     => 1,
]);
echo "  ✓ [6] Petit-déjeuner\n";

// ========================================================================
// BLOC 7 — ÉQUIPEMENTS (liste display=numbered-grid)
// ========================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'chambres-d-hotes',
    'lang'       => 'fr',
    'block_type' => 'liste',
    'title'      => 'Équipements & services inclus',
    'content'    => json_encode([
        'label_numeral' => '06',
        'label_text'    => 'Inclus',
        'heading'       => "Équipements\n& *services* inclus.",
        'intro'         => "Ce qu'on remet avec la clé — rien d'extravagant, juste ce qui rend un séjour simple.",
        'display'       => 'numbered-grid',
        'items' => [
            'Literie premium (draps 100 % coton percale)',
            'Salle de bain privative avec produits de toilette bio',
            'Climatisation réversible dans chaque chambre',
            'Wifi haut débit gratuit',
            'Télévision écran plat',
            'Piscine partagée 12 × 6 m (mai à octobre)',
            'Jardin provençal et terrasses ombragées',
            'Parking privé gratuit',
            'Arrivée autonome (boîte à clé)',
            'Conseils et recommandations locales personnalisés',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 7,
    'active'     => 1,
]);
echo "  ✓ [7] Équipements (liste numbered-grid)\n";

// ========================================================================
// BLOC 8 — INFOS PRATIQUES (tableau display=key-value, ancre #infos)
// ========================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'chambres-d-hotes',
    'lang'       => 'fr',
    'block_type' => 'tableau',
    'title'      => 'Infos pratiques',
    'content'    => json_encode([
        'label_numeral' => '07',
        'label_text'    => 'Infos pratiques',
        'heading'       => "Tout ce qu'il faut *savoir*.",
        'intro'         => "Dates, capacité, horaires, ce qui est inclus — l'essentiel d'un coup d'œil.",
        'surface'       => 'stone',
        'anchor_id'     => 'infos',
        'display'       => 'key-value',
        'rows' => [
            ['key' => 'Période',         'value' => '**De septembre à juin**'],
            ['key' => 'Arrivée',         'value' => 'À partir de 17h'],
            ['key' => 'Départ',          'value' => 'Avant 11h'],
            ['key' => 'Séjour minimum',  'value' => '**2 nuits** (haute saison : 3 nuits)'],
            ['key' => 'Capacité',        'value' => '**1 à 5 personnes** (suite communicante)'],
            ['key' => 'Petit-déjeuner',  'value' => 'Inclus · servi de 7h30 à 10h'],
            ['key' => 'Piscine',         'value' => 'Partagée · 12 × 6 m · mai à octobre'],
            ['key' => 'Animaux',         'value' => 'Non acceptés'],
            ['key' => 'Fumeur',          'value' => 'Non-fumeur'],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 8,
    'active'     => 1,
]);
echo "  ✓ [8] Infos pratiques (tableau key-value)\n";

// ========================================================================
// Vérification finale
// ========================================================================
$check = Database::fetchAll(
    "SELECT id, block_type, position, active FROM vp_sections WHERE page_slug='chambres-d-hotes' AND lang='fr' ORDER BY position"
);
echo "\nÉtat vp_sections (chambres-d-hotes/fr) :\n";
foreach ($check as $row) {
    echo "  - id={$row['id']} type={$row['block_type']} pos={$row['position']} active={$row['active']}\n";
}
echo "\nDone.\n";
