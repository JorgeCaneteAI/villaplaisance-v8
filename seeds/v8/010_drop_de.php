<?php
declare(strict_types=1);

/**
 * V8 — Drop la colonne vp_media.alt_de.
 *
 * Décision Jorge 2026-06-02 : pas d'allemand sur le site. On retire la
 * seule colonne *_de qui restait en DB (les autres tables n'en avaient
 * pas) et toutes les refs code (MediaController, media/edit.php,
 * ImageService, SeoService, llms.php).
 *
 * Idempotent : skip si la colonne n'existe déjà plus.
 */

require __DIR__ . '/../../config.php';

$cols = [];
try {
    $cols = Database::fetchAll("SHOW COLUMNS FROM vp_media LIKE 'alt_de'");
} catch (\Throwable $e) {
    echo "❌ Erreur lecture schéma vp_media : " . $e->getMessage() . "\n";
    exit(1);
}

if (empty($cols)) {
    echo "ℹ️  Colonne vp_media.alt_de déjà absente, skip\n";
    exit(0);
}

try {
    Database::query("ALTER TABLE vp_media DROP COLUMN alt_de");
    echo "✅ Colonne vp_media.alt_de supprimée\n";
} catch (\Throwable $e) {
    echo "❌ Échec DROP COLUMN : " . $e->getMessage() . "\n";
    exit(1);
}
