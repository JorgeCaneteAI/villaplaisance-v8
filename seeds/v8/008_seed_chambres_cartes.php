<?php
declare(strict_types=1);

/**
 * V8 — insère le bloc `cartes` offer=bb en position 4 sur la page chambres-d-hotes.
 * Rend les 2 chambres B&B (Verte + Bleue) lues depuis vp_pieces via le partial v8.
 * Idempotent : DELETE puis INSERT.
 */

require __DIR__ . '/../../config.php';

$slug = 'chambres-d-hotes';
$lang = 'fr';
$position = 4;
$blockType = 'cartes';

Database::query(
    "DELETE FROM vp_sections WHERE page_slug = ? AND lang = ? AND position = ? AND block_type = ?",
    [$slug, $lang, $position, $blockType]
);

$content = [
    'offer' => 'bb',
    // Mode B&B n'affiche pas heading/intro/label_* (1 section par chambre, pas de wrapper).
    // Les labels A/B et le layout viennent de vp_pieces.meta côté partial.
];

Database::insert('vp_sections', [
    'page_slug' => $slug,
    'lang' => $lang,
    'block_type' => $blockType,
    'title' => 'Chambres B&B (Verte + Bleue)',
    'content' => json_encode($content, JSON_UNESCAPED_UNICODE),
    'position' => $position,
    'active' => 1,
]);

echo "✅ Bloc cartes (offer=bb) inséré sur $slug pos $position\n";
