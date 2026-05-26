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
