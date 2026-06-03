<?php
declare(strict_types=1);

/**
 * V8 — Désactive le bloc "avis" position 6 sur la home (FR + EN + ES).
 *
 * Pourquoi : ce bloc seedé contenait des placeholders ('Placeholder — un mot
 * du voyageur…') affichés sur la home. Le fallback HTML de home.php
 * (section TÉMOIGNAGES) tire désormais directement vp_reviews, donc on
 * neutralise le bloc pour laisser passer le fallback.
 *
 * Idempotent : UPDATE active=0 (ne supprime pas, on peut le réactiver depuis
 * /admin/sections/page/accueil en cas de besoin).
 *
 * Lancement (sur le serveur) :
 *   cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/022_disable_home_avis_block.php
 */

require __DIR__ . '/../../config.php';

echo "=== V8 — Désactivation bloc avis sur accueil (placeholders) ===\n\n";

foreach (['fr', 'en', 'es'] as $lang) {
    $row = Database::fetchOne(
        "SELECT id, position, block_type, active FROM vp_sections
         WHERE page_slug = 'accueil' AND lang = ? AND block_type = 'avis'",
        [$lang]
    );

    if (!$row) {
        echo "  · [$lang] aucun bloc avis sur accueil — rien à faire.\n";
        continue;
    }

    if ((int)$row['active'] === 0) {
        echo "  · [$lang] bloc avis (id={$row['id']}, pos={$row['position']}) déjà désactivé.\n";
        continue;
    }

    Database::update('vp_sections', ['active' => 0], 'id = ?', [(int)$row['id']]);
    echo "  ✓ [$lang] bloc avis (id={$row['id']}, pos={$row['position']}) désactivé.\n";
}

// Vérification finale
echo "\nÉtat final des blocs avis sur accueil :\n";
$check = Database::fetchAll(
    "SELECT lang, id, position, active FROM vp_sections
     WHERE page_slug='accueil' AND block_type='avis' ORDER BY lang"
);
foreach ($check as $r) {
    $state = (int)$r['active'] === 1 ? 'ACTIF' : 'inactif';
    echo "  - [{$r['lang']}] id={$r['id']} pos={$r['position']} → $state\n";
}

echo "\nDone. La home affichera désormais les avis tirés directement de\n";
echo "vp_reviews via le fallback HTML de home.php (section TÉMOIGNAGES).\n";
