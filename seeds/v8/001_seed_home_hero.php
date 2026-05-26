<?php
declare(strict_types=1);

/**
 * Seed V8 — Bloc hero de la page Accueil.
 *
 * Phase 2 du chantier B (port vp_sections).
 * Premier bloc câblé depuis la BDD côté V8.
 *
 * Précondition : vp_media doit contenir une entrée pour hero-piscine.webp
 * (ID 172 au 2026-05-26, vérifier sinon adapter ci-dessous).
 *
 * Idempotent : TRUNCATE vp_sections avant l'insert.
 * ⚠️ Cette table est isolée côté VPV8_dev, n'affecte pas la prod V7.
 *
 * Usage : cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/001_seed_home_hero.php
 */

define('ROOT', dirname(__DIR__, 2));
require ROOT . '/config.php';

echo "=== Seed V8 — Home Hero ===\n\n";

// 1) Résolution de l'image hero dans vp_media (par filename, robuste à un changement d'ID)
$imageRow = Database::fetchOne(
    "SELECT id FROM vp_media WHERE filename = ? LIMIT 1",
    ['hero-piscine.webp']
);
if (!$imageRow) {
    fwrite(STDERR, "✕ vp_media n'a pas d'entrée pour 'hero-piscine.webp'. Annulé.\n");
    exit(1);
}
$imageId = (int)$imageRow['id'];
echo "  → hero-piscine.webp = vp_media.id $imageId\n";

// 2) TRUNCATE vp_sections (sécurisé : on est sur VPV8_dev, séparée de la prod)
Database::query("TRUNCATE TABLE vp_sections");
echo "  → vp_sections vidée\n";

// 3) INSERT du bloc hero pour la home (FR)
$content = json_encode([
    'title'    => "Villa\n*Plaisance*",
    'lede'     => "Une maison, deux façons d'y séjourner — chambres d'hôtes de septembre à juin, maison d'hôtes en juillet et août.",
    'image_id' => $imageId,
    'tags'     => [
        'Bédarrides · Vaucluse · Provence',
        "Triangle d'Or",
    ],
    'ctas' => [
        [
            'label' => "Sept → Juin · Chambres d'hôtes",
            'url'   => '/chambres-d-hotes',
            'style' => 'primary',
        ],
        [
            'label' => 'Juil → Août · Maison d\'hôtes',
            'url'   => '/location-villa-provence',
            'style' => 'ghost',
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

Database::insert('vp_sections', [
    'page_slug'  => 'accueil',
    'lang'       => 'fr',
    'block_type' => 'hero',
    'title'      => 'Hero accueil V8',
    'content'    => $content,
    'position'   => 1,
    'active'     => 1,
]);

echo "  ✓ Bloc hero inséré (page_slug=accueil, lang=fr, position=1)\n\n";

// 4) Vérif
$check = Database::fetchAll("SELECT id, block_type, position, active FROM vp_sections WHERE page_slug = 'accueil' AND lang = 'fr' ORDER BY position");
echo "État vp_sections (accueil/fr) :\n";
foreach ($check as $row) {
    echo "  - id={$row['id']} type={$row['block_type']} pos={$row['position']} active={$row['active']}\n";
}
echo "\nDone.\n";
