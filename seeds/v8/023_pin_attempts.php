<?php
declare(strict_types=1);

/**
 * V8 — Ajoute pin_attempts + pin_locked_until à vp_users.
 *
 * Pourquoi : le compteur de tentatives PIN vivait dans $_SESSION, donc
 * réinitialisable en effaçant le cookie navigateur — bypass du lockout.
 * On le stocke côté serveur, par utilisateur.
 *
 * Idempotent : vérifie l'existence des colonnes avant ALTER.
 *
 * Lancement (serveur) :
 *   cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/023_pin_attempts.php
 *
 * Lancement (local) :
 *   cd "/Users/jorgecanete/Documents/C.L.A.U.D.E/villaplaisance/Site Internet/v8" && \
 *   php seeds/v8/023_pin_attempts.php
 */

require __DIR__ . '/../../config.php';

echo "=== V8 — Migration pin_attempts + pin_locked_until ===\n\n";

$dbName = $_ENV['DB_NAME'] ?? '';

$existing = Database::fetchAll(
    "SELECT COLUMN_NAME FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'vp_users'
       AND COLUMN_NAME IN ('pin_attempts', 'pin_locked_until')",
    [$dbName]
);
$existingNames = array_column($existing, 'COLUMN_NAME');

if (!in_array('pin_attempts', $existingNames, true)) {
    Database::query("ALTER TABLE vp_users ADD COLUMN pin_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER pin");
    echo "  ✓ Colonne `pin_attempts` ajoutée.\n";
} else {
    echo "  · Colonne `pin_attempts` déjà présente.\n";
}

if (!in_array('pin_locked_until', $existingNames, true)) {
    Database::query("ALTER TABLE vp_users ADD COLUMN pin_locked_until DATETIME NULL DEFAULT NULL AFTER pin_attempts");
    echo "  ✓ Colonne `pin_locked_until` ajoutée.\n";
} else {
    echo "  · Colonne `pin_locked_until` déjà présente.\n";
}

// Reset les compteurs au cas où d'anciennes valeurs traîneraient.
Database::query("UPDATE vp_users SET pin_attempts = 0, pin_locked_until = NULL");
echo "  ✓ Compteurs réinitialisés.\n";

echo "\nDone. Le compteur de tentatives PIN est désormais en DB,\n";
echo "le verrou de 15 min est appliqué côté serveur (non bypassable).\n";
