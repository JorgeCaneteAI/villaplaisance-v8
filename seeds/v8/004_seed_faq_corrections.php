<?php
declare(strict_types=1);

/**
 * Seed V8 — Corrections éditoriales sur vp_faq.
 *
 * 1. Supprime toutes les mentions de "tarif"/"rate"/"tarifa" non conformes
 *    à la règle produit "aucun tarif sur le site" (FR + EN + ES).
 * 2. Ajoute une nouvelle FAQ "Faut-il apporter ses serviettes de piscine ?"
 *    juste après la FAQ sur la piscine (FR + EN + ES) sur la page
 *    chambres-d-hotes.
 *
 * Idempotent :
 *  - Les UPDATE str_replace sont sans effet si déjà appliqués.
 *  - Les INSERT serviettes sont précédés d'une détection FR — si déjà
 *    présentes, on ne décale rien et on n'insère rien.
 *
 * Usage : cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/004_seed_faq_corrections.php
 */

define('ROOT', dirname(__DIR__, 2));
require ROOT . '/config.php';

echo "=== Seed V8 — Corrections FAQ + ajout serviettes piscine ===\n\n";

// ============================================================================
// 1. UPDATE des FAQ avec mention de tarif
// ============================================================================
$replacements = [
    // id => [ ['before' => '...', 'after' => '...'], ... ]
    4  => [['Oui, le petit-déjeuner maison est inclus dans le tarif B&B.', 'Oui, le petit-déjeuner maison est inclus dans tout séjour en chambres d\'hôtes.']],
    26 => [['Yes, homemade breakfast is included in the B&B rate.', 'Yes, homemade breakfast is included with every B&B stay.']],
    27 => [['Sí, el desayuno casero está incluido en la tarifa B&B.', 'Sí, el desayuno casero está incluido en cualquier estancia en habitaciones de huéspedes.']],
    17 => [
        ['pour un tarif préférentiel. Réservation directe = meilleur tarif garanti.', 'pour bénéficier de nos meilleures conditions. La réservation directe reste toujours la plus avantageuse.'],
    ],
    40 => [
        ['for a preferential rate. Direct booking = best rate guaranteed.', 'to benefit from our best conditions. Direct booking remains the most advantageous option.'],
    ],
    41 => [
        // Sans le contenu exact ES, on tente plusieurs variantes plausibles
        ['para una tarifa preferencial. Reserva directa = mejor tarifa garantizada.', 'para beneficiaros de nuestras mejores condiciones. La reserva directa siempre es la opción más ventajosa.'],
        ['para una tarifa preferencial', 'para beneficiaros de nuestras mejores condiciones'],
        ['mejor tarifa garantizada', 'siempre la opción más ventajosa'],
    ],
    18 => [['Le ménage de fin de séjour est inclus dans le tarif.', 'Le ménage de fin de séjour est inclus dans le séjour.']],
    19 => [
        ['des tarifs long séjour. Réservation directe = meilleur tarif garanti.', 'nos meilleures conditions pour les longs séjours. La réservation directe reste toujours la plus avantageuse.'],
    ],
    56 => [
        ['or a multi-week discount.', 'or our best long-stay conditions.'],
        ['multi-week discount', 'best long-stay conditions'],
    ],
];

foreach ($replacements as $id => $patches) {
    $row = Database::fetchOne("SELECT answer FROM vp_faq WHERE id = ?", [$id]);
    if (!$row) {
        echo "  ⚠ id=$id introuvable, ignoré\n";
        continue;
    }
    $before = (string)$row['answer'];
    $after = $before;
    foreach ($patches as $patch) {
        $after = str_replace($patch[0], $patch[1], $after);
    }
    if ($after === $before) {
        echo "  ⏭ id=$id : aucun match (probablement déjà corrigé)\n";
        continue;
    }
    Database::update('vp_faq', ['answer' => $after], 'id = ?', [$id]);
    echo "  ✓ id=$id corrigé\n";
}

// ============================================================================
// 2. Ajout FAQ "Serviettes piscine"
// ============================================================================
$slug      = 'chambres-d-hotes';
$markerFr  = 'Faut-il apporter ses serviettes de piscine ?';
$insertAt  = 10;  // position cible : juste après la FAQ piscine (id=16, position 9)

// Détection idempotente
$exists = Database::fetchOne(
    "SELECT COUNT(*) AS c FROM vp_faq WHERE page_slug = ? AND lang = 'fr' AND question = ?",
    [$slug, $markerFr]
);
if ((int)($exists['c'] ?? 0) > 0) {
    echo "\n  ⏭ FAQ serviettes piscine déjà présente, pas d'INSERT.\n";
} else {
    // Décaler les FAQ qui suivent la position cible (10+ → +1)
    Database::query("UPDATE vp_faq SET position = position + 1 WHERE page_slug = '$slug' AND position >= $insertAt");
    echo "\n  → positions >= $insertAt décalées de +1\n";

    $towels = [
        [
            'lang' => 'fr',
            'question' => 'Faut-il apporter ses serviettes de piscine ?',
            'answer'   => "Oui, nous vous invitons à prévoir vos propres serviettes pour la piscine et le jardin — les serviettes mises à disposition dans la salle de bain sont réservées à un usage intérieur. Si vous préférez voyager léger, des serviettes de piscine peuvent être louées sur place auprès de Villa Plaisance.",
        ],
        [
            'lang' => 'en',
            'question' => 'Do we need to bring our own pool towels?',
            'answer'   => "Yes — please bring your own towels for the pool and garden. The bathroom towels are for indoor use only. If you'd rather travel light, pool towels can be rented on site from Villa Plaisance.",
        ],
        [
            'lang' => 'es',
            'question' => '¿Hay que traer toallas de piscina?',
            'answer'   => "Sí, os pedimos que traigáis vuestras propias toallas para la piscina y el jardín — las toallas del baño están reservadas a un uso interior. Si preferís viajar ligeros, podéis alquilar toallas de piscina in situ en Villa Plaisance.",
        ],
    ];
    foreach ($towels as $row) {
        Database::insert('vp_faq', [
            'page_slug' => $slug,
            'lang'      => $row['lang'],
            'question'  => $row['question'],
            'answer'    => $row['answer'],
            'position'  => $insertAt,
            'active'    => 1,
        ]);
        echo "  ✓ [{$row['lang']}/pos=$insertAt] " . mb_strimwidth($row['question'], 0, 60, '…') . "\n";
    }
}

// ============================================================================
// 3. Audit final : aucune mention de tarif/rate/tarifa ne devrait subsister
// ============================================================================
$residus = Database::fetchAll(
    "SELECT id, lang, LEFT(question, 50) AS q FROM vp_faq
     WHERE (answer REGEXP '(tarif|prix| tarifa b\\\\&b|preferential rate|best rate guaranteed|multi-week discount)'
         OR question REGEXP '(tarif|prix)')"
);
echo "\nRésidus de mentions tarif après corrections : " . count($residus) . "\n";
foreach ($residus as $r) {
    echo "  ! id={$r['id']} [{$r['lang']}] {$r['q']}\n";
}

echo "\nDone.\n";
