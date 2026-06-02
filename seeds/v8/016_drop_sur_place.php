<?php
declare(strict_types=1);

/**
 * V8 — Supprime la page obsolète /sur-place de vp_pages et nettoie tout
 * bloc vp_sections orphelin sur ce slug. La page redirige 301 vers
 * /itineraire depuis le 23 mai (cf. CLAUDE.md projet, dette ouverte).
 *
 * Idempotent : si la page n'existe déjà plus, le script l'affiche et sort.
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — Suppression /sur-place (obsolète) ===\n\n";

// 1. vp_pages
$pages = Database::fetchAll("SELECT id, lang FROM vp_pages WHERE slug = ?", ['sur-place']);
if (empty($pages)) {
    echo "  ℹ️  Aucune entrée vp_pages pour 'sur-place', skip\n";
} else {
    Database::query("DELETE FROM vp_pages WHERE slug = ?", ['sur-place']);
    echo "  ✓ " . count($pages) . " entrée(s) vp_pages supprimée(s) (langues : "
       . implode(', ', array_column($pages, 'lang')) . ")\n";
}

// 2. vp_sections orphelins (si jamais)
$sections = Database::fetchAll("SELECT COUNT(*) AS n FROM vp_sections WHERE page_slug = ?", ['sur-place']);
$nSections = (int)($sections[0]['n'] ?? 0);
if ($nSections === 0) {
    echo "  ℹ️  Aucun bloc vp_sections orphelin, skip\n";
} else {
    Database::query("DELETE FROM vp_sections WHERE page_slug = ?", ['sur-place']);
    echo "  ✓ $nSections bloc(s) vp_sections supprimé(s)\n";
}

// 3. vp_faq orphelins (si une FAQ avait été attachée)
$faqs = Database::fetchAll("SELECT COUNT(*) AS n FROM vp_faq WHERE page_slug = ?", ['sur-place']);
$nFaqs = (int)($faqs[0]['n'] ?? 0);
if ($nFaqs === 0) {
    echo "  ℹ️  Aucune FAQ orpheline, skip\n";
} else {
    Database::query("DELETE FROM vp_faq WHERE page_slug = ?", ['sur-place']);
    echo "  ✓ $nFaqs FAQ supprimée(s)\n";
}

echo "\nDone. /sur-place est supprimée. La redirection 301 → /itineraire reste\n";
echo "active côté serveur (cf. .htaccess ou logique de routage).\n";
