<?php
declare(strict_types=1);

/**
 * V8 — Crée la table vp_icon_mapping pour la page admin /admin/icons-lab.
 *
 * Chaque ligne associe un libellé exact (insensible casse) à un nom
 * d'icône du sprite (/public/assets/img/icons.svg). icon_name peut être
 * NULL pour signifier « volontairement sans icône » (override le fallback
 * regex de IconService::pillIcon()).
 *
 * Idempotent : CREATE TABLE IF NOT EXISTS.
 *
 * Lancement (serveur) :
 *   cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/024_icon_mapping.php
 */

require __DIR__ . '/../../config.php';

echo "=== V8 — Migration vp_icon_mapping ===\n\n";

Database::query(<<<'SQL'
CREATE TABLE IF NOT EXISTS vp_icon_mapping (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(160) NOT NULL,
    icon_name VARCHAR(40) NULL,
    source VARCHAR(40) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_label (label),
    KEY idx_source (source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
);
echo "  ✓ Table vp_icon_mapping créée (ou déjà présente).\n";

$count = Database::fetchOne("SELECT COUNT(*) AS n FROM vp_icon_mapping");
echo "\nÉtat : " . ((int)($count['n'] ?? 0)) . " mapping(s) enregistré(s).\n";
echo "Va sur /admin/icons-lab pour les éditer.\n";
