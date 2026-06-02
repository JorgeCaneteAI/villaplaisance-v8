#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Génère les miniatures WebP (400px max, qualité 70) pour tous les médias
 * de vp_media qui n'en ont pas encore. Idempotent (skip si la thumb existe).
 *
 * À lancer une fois après le déploiement de la feature thumbnails.
 *   php bin/generate_thumbs.php           # tous les médias
 *   php bin/generate_thumbs.php --force   # regénère même si déjà existante
 *   php bin/generate_thumbs.php --folder=chambres  # filtre par dossier
 *
 * Code de sortie : 0 si tout OK, 1 si au moins une erreur.
 */

require __DIR__ . '/../config.php';

// Parsing args
$force = in_array('--force', $argv, true);
$folderFilter = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--folder=')) {
        $folderFilter = substr($arg, strlen('--folder='));
    }
}

// Récupération des médias
$sql = "SELECT id, filename, folder FROM vp_media";
$params = [];
if ($folderFilter !== null) {
    $sql .= " WHERE folder = ?";
    $params[] = $folderFilter;
}
$sql .= " ORDER BY id";

$medias = Database::fetchAll($sql, $params);
$total = count($medias);

printf("[%s] %d média(s) à traiter%s%s\n",
    date('Y-m-d H:i:s'),
    $total,
    $folderFilter !== null ? " (dossier='$folderFilter')" : '',
    $force ? ' [--force]' : ''
);

$generated = 0;
$skipped = 0;
$missing = 0;
$errors = 0;

foreach ($medias as $m) {
    $filename = (string)($m['filename'] ?? '');
    if ($filename === '') continue;

    $source = ROOT . '/public/uploads/' . $filename;
    $thumb = \App\Controllers\Admin\MediaController::thumbAbsolutePath($filename);

    if (!file_exists($source)) {
        fwrite(STDERR, "  MISSING source #{$m['id']} : $filename\n");
        $missing++;
        continue;
    }

    if (!$force && file_exists($thumb)) {
        $skipped++;
        continue;
    }

    if ($force && file_exists($thumb)) {
        @unlink($thumb);
    }

    $ok = \App\Controllers\Admin\MediaController::generateThumb($source, $thumb);
    if ($ok) {
        $generated++;
        // log allégé : 1 ligne tous les 20
        if ($generated % 20 === 0) {
            printf("  … %d généré(s)\n", $generated);
        }
    } else {
        fwrite(STDERR, "  ERR generate #{$m['id']} : $filename\n");
        $errors++;
    }
}

printf("[%s] Terminé — généré: %d, skip: %d, source manquante: %d, erreurs: %d\n",
    date('Y-m-d H:i:s'),
    $generated, $skipped, $missing, $errors
);

exit($errors === 0 ? 0 : 1);
