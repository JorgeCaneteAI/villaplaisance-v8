<?php
declare(strict_types=1);

/**
 * bin/audit_seo_pages.php
 *
 * Liste les entrées vp_pages dont meta_title ou meta_desc sont vides
 * ou trop courtes, et qui demandent une saisie via l'admin
 * (/admin/sections ou directement /admin/pages selon l'écran admin).
 *
 * Critères Google 2026 (souples) :
 *   - meta_title : 30-65 caractères idéal (sinon Google le réécrit)
 *   - meta_desc  : 50-160 caractères idéal (sinon Google le réécrit)
 *
 * Lancement :
 *   cd /home/efkz3012/v2.villaplaisance.fr && php bin/audit_seo_pages.php
 *   ou en local :
 *   cd "/Users/jorgecanete/Documents/C.L.A.U.D.E/villaplaisance/Site Internet/v8" && php bin/audit_seo_pages.php
 */

require __DIR__ . '/../config.php';

echo "=== Audit SEO meta vp_pages ===\n\n";

$pages = [];
try {
    $pages = Database::fetchAll(
        "SELECT slug, lang, meta_title, meta_desc FROM vp_pages ORDER BY slug, lang"
    );
} catch (\Throwable $e) {
    echo "ERREUR : impossible de lire vp_pages — " . $e->getMessage() . "\n";
    exit(1);
}

if (empty($pages)) {
    echo "Table vp_pages vide ou inaccessible.\n";
    exit(0);
}

$missing = [];
$short   = [];
$tooLong = [];
$okCount = 0;

foreach ($pages as $p) {
    $slug = $p['slug'];
    $lang = $p['lang'];
    $key = "$slug [$lang]";
    $issues = [];

    $title = trim((string) ($p['meta_title'] ?? ''));
    $desc  = trim((string) ($p['meta_desc']  ?? ''));

    if ($title === '') {
        $issues[] = 'meta_title vide';
    } elseif (mb_strlen($title, 'UTF-8') < 30) {
        $issues[] = sprintf('meta_title court (%d chars, idéal 30-65)', mb_strlen($title, 'UTF-8'));
    } elseif (mb_strlen($title, 'UTF-8') > 65) {
        $issues[] = sprintf('meta_title long (%d chars, idéal 30-65)', mb_strlen($title, 'UTF-8'));
    }

    if ($desc === '') {
        $issues[] = 'meta_desc vide';
    } elseif (mb_strlen($desc, 'UTF-8') < 50) {
        $issues[] = sprintf('meta_desc court (%d chars, idéal 50-160)', mb_strlen($desc, 'UTF-8'));
    } elseif (mb_strlen($desc, 'UTF-8') > 160) {
        $issues[] = sprintf('meta_desc long (%d chars, idéal 50-160)', mb_strlen($desc, 'UTF-8'));
    }

    if (empty($issues)) {
        $okCount++;
        continue;
    }

    if (in_array('meta_title vide', $issues, true) || in_array('meta_desc vide', $issues, true)) {
        $missing[$key] = $issues;
    } else {
        $short[$key] = $issues;
    }
}

printf("Total entrées : %d\n", count($pages));
printf("OK            : %d\n", $okCount);
printf("Vides         : %d (à créer via admin)\n", count($missing));
printf("Hors fourchette: %d (à raccourcir/allonger)\n\n", count($short));

if (!empty($missing)) {
    echo "—— Vides ——\n";
    foreach ($missing as $key => $issues) {
        echo "  · $key\n";
        foreach ($issues as $i) echo "      $i\n";
    }
    echo "\n";
}

if (!empty($short)) {
    echo "—— Hors fourchette ——\n";
    foreach ($short as $key => $issues) {
        echo "  · $key\n";
        foreach ($issues as $i) echo "      $i\n";
    }
    echo "\n";
}

echo "Édite via : /admin/sections (bloc hero de la page) ou /admin/pages si disponible.\n";
echo "Note : si Google trouve la meta trop courte/longue, il la réécrit lui-même.\n";
