<?php
declare(strict_types=1);

/**
 * V8 — port des 7 chambres en lecture vp_pieces depuis cartes.php V8.
 *
 * 1. Ajoute la colonne `meta JSON` à vp_pieces (pour stocker label_a / label_b
 *    / layout, utilisés par le markup B&B `.ch-room`).
 * 2. Met à jour les 7 chambres existantes (FR uniquement pour l'instant) avec :
 *    - images : array JSON des filenames /uploads/
 *    - meta   : label_a, label_b, layout (uniquement les B&B)
 *
 * Idempotent : peut être relancé sans casser.
 * Pré-requis : seed 002 (qui crée les 2 chambres B&B + 4 chambres villa).
 */

require __DIR__ . '/../../config.php';

// 1. Ajouter la colonne meta si elle n'existe pas
$colExists = false;
try {
    $cols = Database::fetchAll("SHOW COLUMNS FROM vp_pieces LIKE 'meta'");
    $colExists = !empty($cols);
} catch (\Throwable $e) {
    echo "❌ Erreur lecture schéma vp_pieces : " . $e->getMessage() . "\n";
    exit(1);
}

if (!$colExists) {
    try {
        Database::query("ALTER TABLE vp_pieces ADD COLUMN meta JSON DEFAULT NULL AFTER images");
        echo "✅ Colonne 'meta' ajoutée à vp_pieces\n";
    } catch (\Throwable $e) {
        echo "❌ Échec ALTER TABLE : " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "ℹ️  Colonne 'meta' déjà présente, skip\n";
}

// 2. Mise à jour des 7 chambres FR avec leurs images + meta
// Format : [offer, position, images[], meta[]]
$updates = [
    // ── B&B (chambres-d-hotes) — 2 chambres avec markup .ch-room ─────────
    [
        'bb', 1,
        ['villa-plaisance-chambre-verte-01.webp', 'villa-plaisance-chambre-verte-02.webp', 'villa-plaisance-chambre-verte-03.webp'],
        ['label_a' => 'I · Première chambre de la suite', 'label_b' => 'Côté jardin', 'layout' => 'normal'],
    ],
    [
        'bb', 2,
        ['villa-plaisance-chambre-bleue-01.webp', 'villa-plaisance-chambre-bleue-02.webp', 'villa-plaisance-chambre-bleue-03.webp'],
        ['label_a' => 'II · Seconde chambre de la suite', 'label_b' => 'Chambre / mini salon de lecture', 'layout' => 'alt'],
    ],

    // ── Villa (location-villa-provence) — 4 chambres avec markup .room-card-x ─
    [
        'villa', 1,
        ['villa-plaisance-chambre-verte-01.webp'],
        null,  // markup villa n'utilise pas meta
    ],
    [
        'villa', 2,
        ['villa-plaisance-chambre-bleue-01.webp'],
        null,
    ],
    [
        'villa', 3,
        ['villa-plaisance-chambre-arche-01.webp'],
        null,
    ],
    [
        'villa', 4,
        ['villa-plaisance-chambre-annees-70-01.webp'],
        null,
    ],
];

$updated = 0;
$missing = 0;
foreach ($updates as [$offer, $pos, $images, $meta]) {
    $row = Database::fetchOne(
        "SELECT id FROM vp_pieces WHERE offer = ? AND position = ? AND lang = 'fr' LIMIT 1",
        [$offer, $pos]
    );
    if (!$row) {
        echo "⚠️  Chambre $offer/pos$pos introuvable (seed 002 pas lancé ?)\n";
        $missing++;
        continue;
    }
    $data = [
        'images' => json_encode($images, JSON_UNESCAPED_UNICODE),
    ];
    if ($meta !== null) {
        $data['meta'] = json_encode($meta, JSON_UNESCAPED_UNICODE);
    }
    Database::update('vp_pieces', $data, 'id = ?', [(int)$row['id']]);
    $updated++;
}

echo "✅ $updated chambre(s) mise(s) à jour, $missing manquante(s)\n";
