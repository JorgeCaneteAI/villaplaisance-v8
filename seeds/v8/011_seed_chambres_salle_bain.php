<?php
declare(strict_types=1);

/**
 * V8 — bloc prose layout `two-col-image-left` pour la section salle de bain
 * privative sur la page chambres-d-hotes (position 5, entre les chambres
 * pos 4 et le petit-déj pos 6).
 *
 * Résout l'ID de l'image en runtime via SELECT sur vp_media.filename.
 * Idempotent : DELETE puis INSERT.
 */

require __DIR__ . '/../../config.php';

$slug = 'chambres-d-hotes';
$lang = 'fr';
$position = 5;
$blockType = 'prose';

// Résolution de l'image_id depuis le filename
$imageId = null;
try {
    $row = Database::fetchOne(
        "SELECT id FROM vp_media WHERE filename = ? LIMIT 1",
        ['villa-plaisance-salle-de-bain-chambre-hotes-01.webp']
    );
    $imageId = $row ? (int)$row['id'] : null;
} catch (\Throwable) {}

if ($imageId === null) {
    echo "⚠️  Image 'villa-plaisance-salle-de-bain-chambre-hotes-01.webp' introuvable dans vp_media — le bloc sera inséré sans image\n";
}

Database::query(
    "DELETE FROM vp_sections WHERE page_slug = ? AND lang = ? AND position = ? AND block_type = ?",
    [$slug, $lang, $position, $blockType]
);

$content = [
    'layout' => 'two-col-image-left',
    'label_numeral' => '04',
    'label_text' => 'Salle de bain',
    'heading' => "Salle de bain\n*privative*\ndans chaque chambre.",
    'text' =>
        "Chaque chambre dispose de sa propre salle de bain privative — pas de couloir partagé, pas d'attente derrière la porte." .
        "\n\n" .
        "Produits de toilette bio, serviettes généreuses, douche à l'italienne ou baignoire selon la chambre. Sèche-cheveux, éclairage miroir, et les petites attentions qu'on aime trouver.",
];
if ($imageId !== null) {
    $content['image_id'] = $imageId;
}

Database::insert('vp_sections', [
    'page_slug' => $slug,
    'lang' => $lang,
    'block_type' => $blockType,
    'title' => 'Salle de bain privative',
    'content' => json_encode($content, JSON_UNESCAPED_UNICODE),
    'position' => $position,
    'active' => 1,
]);

echo "✅ Bloc prose 'Salle de bain' inséré sur $slug pos $position" . ($imageId ? " (image_id=$imageId)" : "") . "\n";
