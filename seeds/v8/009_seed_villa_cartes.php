<?php
declare(strict_types=1);

/**
 * V8 — insère le bloc `cartes` offer=villa en position 3 sur la page location-villa-provence.
 * Rend la grille des 4 chambres villa (Verte / Bleue / Arche / 70) lues depuis vp_pieces.
 * Idempotent : DELETE puis INSERT.
 */

require __DIR__ . '/../../config.php';

$slug = 'location-villa-provence';
$lang = 'fr';
$position = 3;
$blockType = 'cartes';

Database::query(
    "DELETE FROM vp_sections WHERE page_slug = ? AND lang = ? AND position = ? AND block_type = ?",
    [$slug, $lang, $position, $blockType]
);

$content = [
    'offer' => 'villa',
    'heading' => "Quatre chambres,\nquatre *univers*.",
    'intro' => "Chaque chambre a sa personnalité — les livres, une arche, le jardin, les seventies. Aucune ne manque ni d'ombre ni de lumière.",
    'label_numeral' => '01',
    'label_text' => 'Les chambres',
];

Database::insert('vp_sections', [
    'page_slug' => $slug,
    'lang' => $lang,
    'block_type' => $blockType,
    'title' => 'Cartes 4 chambres villa',
    'content' => json_encode($content, JSON_UNESCAPED_UNICODE),
    'position' => $position,
    'active' => 1,
]);

echo "✅ Bloc cartes (offer=villa) inséré sur $slug pos $position\n";
