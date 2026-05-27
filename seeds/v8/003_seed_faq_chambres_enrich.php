<?php
declare(strict_types=1);

/**
 * Seed V8 — Enrichissement FAQ chambres-d-hotes.
 *
 * Ajoute 2 questions explicatives manquantes en tête de la FAQ chambres :
 *   1. Pourquoi les chambres ne se louent-elles qu'ensemble ?
 *   2. À deux, quelle chambre sera préparée ?
 *
 * Ces 2 questions existaient en HTML dur sur /chambres-d-hotes mais pas
 * en BDD (vp_faq). On les ajoute pour ne pas perdre l'éditorial en
 * basculant le bloc FAQ vers vp_sections + vp_faq.
 *
 * Idempotent : si les 2 questions cibles existent déjà (détection par
 * question exacte FR), le script ne refait pas le UPDATE de décalage
 * et ré-INSERT.
 *
 * ⚠️ Important : la question #2 contenait à l'origine un tarif
 * (« 30 € par nuit »). On le remplace par « cela est disponible en
 * option payante » (règle produit : aucun tarif sur le site).
 *
 * Usage : cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/003_seed_faq_chambres_enrich.php
 */

define('ROOT', dirname(__DIR__, 2));
require ROOT . '/config.php';

echo "=== Seed V8 — Enrichissement FAQ chambres-d-hotes ===\n\n";

$slug = 'chambres-d-hotes';
$markerFr1 = "Pourquoi les chambres ne se louent-elles qu'ensemble ?";

// Détection idempotente
$exists = Database::fetchOne(
    "SELECT COUNT(*) AS c FROM vp_faq WHERE page_slug = ? AND lang = 'fr' AND question = ?",
    [$slug, $markerFr1]
);
if ((int)($exists['c'] ?? 0) > 0) {
    echo "  ⏭ Questions déjà présentes (idempotence). Aucune modification.\n";
    exit(0);
}

// 1. Décaler les FAQ existantes de +2
$ok = Database::query(
    "UPDATE vp_faq SET position = position + 2 WHERE page_slug = '$slug'"
);
echo "  → positions des FAQ existantes décalées de +2\n";

// 2. INSERT des 2 nouvelles questions × 3 langues
$rows = [
    // ── Question 1 — Pourquoi ensemble ──
    [
        'lang' => 'fr', 'position' => 1,
        'question' => "Pourquoi les chambres ne se louent-elles qu'ensemble ?",
        'answer'   => "Les deux chambres communiquent par une porte intérieure et partagent une seule entrée. Les louer séparément reviendrait à faire se croiser deux groupes sur le même seuil — nous préférons éviter. Votre groupe a la suite pour lui seul, que vous soyez un ou cinq.",
    ],
    [
        'lang' => 'en', 'position' => 1,
        'question' => "Why are the rooms only rented together?",
        'answer'   => "The two rooms communicate through an inner door and share a single entrance. Renting them separately would mean two parties crossing each other on the same threshold — so we don't. Your group has the suite to itself, whether you're one or five.",
    ],
    [
        'lang' => 'es', 'position' => 1,
        'question' => "¿Por qué las habitaciones solo se alquilan juntas?",
        'answer'   => "Las dos habitaciones se comunican por una puerta interior y comparten una sola entrada. Alquilarlas por separado supondría que dos grupos se cruzaran en el mismo umbral — preferimos evitarlo. Vuestro grupo dispone de la suite en exclusiva, seáis uno o cinco.",
    ],
    // ── Question 2 — À deux, quelle chambre ──
    [
        'lang' => 'fr', 'position' => 2,
        'question' => "À deux, quelle chambre sera préparée ?",
        'answer'   => "Celle que vous préférez — dites-le nous à la réservation. La Chambre Verte (lit double 160×200) ou la Chambre Bleue (deux lits simples, jumelables en 180). L'autre reste accessible en salon de lecture. Si vous souhaitez que les deux chambres soient préparées pour avoir chacun la sienne, cela est disponible en option payante.",
    ],
    [
        'lang' => 'en', 'position' => 2,
        'question' => "As a couple, which room will be prepared?",
        'answer'   => "Whichever you prefer — tell us when you book. The Chambre Verte (double 160×200) or the Chambre Bleue (two singles, joinable into a 180). The other room stays accessible as a reading lounge. If you'd like both rooms prepared so you can each have your own, this is available as a paid option.",
    ],
    [
        'lang' => 'es', 'position' => 2,
        'question' => "Si venís en pareja, ¿qué habitación se prepara?",
        'answer'   => "La que prefiráis — decídnoslo al reservar. La Chambre Verte (cama doble 160×200) o la Chambre Bleue (dos camas individuales, unibles en 180). La otra queda accesible como salón de lectura. Si queréis tener ambas habitaciones preparadas para dormir cada uno en la suya, está disponible como opción de pago.",
    ],
];

foreach ($rows as $row) {
    Database::insert('vp_faq', [
        'page_slug' => $slug,
        'lang'      => $row['lang'],
        'question'  => $row['question'],
        'answer'    => $row['answer'],
        'position'  => $row['position'],
        'active'    => 1,
    ]);
    echo "  ✓ [{$row['lang']}/pos={$row['position']}] " . mb_strimwidth($row['question'], 0, 60, '…') . "\n";
}

// Vérif finale
$final = Database::fetchAll(
    "SELECT lang, COUNT(*) AS n FROM vp_faq WHERE page_slug = ? GROUP BY lang ORDER BY lang",
    [$slug]
);
echo "\nÉtat final vp_faq chambres-d-hotes :\n";
foreach ($final as $row) {
    echo "  - lang={$row['lang']} : {$row['n']} FAQ\n";
}
echo "\nDone.\n";
