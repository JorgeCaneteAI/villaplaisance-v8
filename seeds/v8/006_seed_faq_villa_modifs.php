<?php
declare(strict_types=1);

/**
 * Seed V8 — Modifications éditoriales FAQ villa entière.
 *
 * Décision Jorge 2026-05-27 :
 *  1. Plus de "samedi → samedi" : on accepte n'importe quel jour d'arrivée.
 *  2. Séjour minimum : 4 nuits (au lieu de 7).
 *  3. Hors juillet/août : possible sur demande, conditions spécifiques.
 *
 * Actions :
 *  - UPDATE FAQ "durée minimum de location" (FR/EN/ES) : remplacer 7 nuits/sam-sam par 4 nuits.
 *  - INSERT nouvelle FAQ "Peut-on louer hors juillet/août ?" (FR/EN/ES).
 *
 * Idempotent : détection par marker FR de la nouvelle FAQ.
 *
 * Usage : cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/006_seed_faq_villa_modifs.php
 */

define('ROOT', dirname(__DIR__, 2));
require ROOT . '/config.php';

echo "=== Seed V8 — Modifs FAQ villa (4 nuits + hors saison) ===\n\n";

$slug = 'location-villa-provence';

// ============================================================================
// 1. UPDATE FAQ "durée minimum" — remplacement par 4 nuits sans sam→sam
// ============================================================================
$updates = [
    'fr' => [
        'match_question' => 'Quelle est la durée minimum de location ?',
        'new_answer'     => "Le séjour minimum est de 4 nuits, à n'importe quel jour d'arrivée. En haute saison (juillet–août), nous privilégions les séjours d'une semaine, mais des séjours plus courts restent possibles selon nos disponibilités.",
    ],
    'en' => [
        'match_question' => 'What is the minimum stay?',
        'new_answer'     => "The minimum stay is 4 nights, with any day of arrival. In high season (July–August), we prefer week-long stays, but shorter ones remain possible depending on availability.",
    ],
    'es' => [
        // On essaie plusieurs variantes plausibles pour le match ES
        'match_question_alts' => [
            '¿Cuál es la duración mínima de alquiler?',
            '¿Cuál es la estancia mínima?',
        ],
        'new_answer'     => "La estancia mínima es de 4 noches, con cualquier día de llegada. En temporada alta (julio–agosto), preferimos las estancias de una semana, pero también son posibles estancias más cortas según disponibilidad.",
    ],
];

foreach ($updates as $lang => $cfg) {
    $candidates = $cfg['match_question_alts'] ?? [$cfg['match_question']];
    $found = null;
    foreach ($candidates as $q) {
        $row = Database::fetchOne(
            "SELECT id FROM vp_faq WHERE page_slug = ? AND lang = ? AND question = ? LIMIT 1",
            [$slug, $lang, $q]
        );
        if ($row) { $found = $row; break; }
    }
    if (!$found) {
        echo "  ⚠ [$lang] FAQ durée minimum introuvable, ignorée\n";
        continue;
    }
    Database::update('vp_faq', ['answer' => $cfg['new_answer']], 'id = ?', [(int)$found['id']]);
    echo "  ✓ [$lang] FAQ durée minimum (id={$found['id']}) mise à jour\n";
}

// ============================================================================
// 2. INSERT nouvelle FAQ "Peut-on louer hors juillet/août ?"
// ============================================================================
$markerFr = "Peut-on louer la villa entière hors juillet et août ?";

$exists = Database::fetchOne(
    "SELECT COUNT(*) AS c FROM vp_faq WHERE page_slug = ? AND lang = 'fr' AND question = ?",
    [$slug, $markerFr]
);
if ((int)($exists['c'] ?? 0) > 0) {
    echo "\n  ⏭ FAQ 'hors juillet/août' déjà présente, pas d'INSERT.\n";
} else {
    // Récupérer la position max actuelle pour insérer en fin
    $maxPos = Database::fetchOne(
        "SELECT COALESCE(MAX(position), 0) AS p FROM vp_faq WHERE page_slug = ?",
        [$slug]
    );
    $nextPos = ((int)$maxPos['p']) + 1;

    $rows = [
        [
            'lang'     => 'fr',
            'question' => "Peut-on louer la villa entière hors juillet et août ?",
            'answer'   => "Oui, c'est possible sur demande. La villa entière reste principalement réservée à juillet et août, mais des locations hors saison peuvent être étudiées au cas par cas — durée, période, services inclus. Les conditions sont alors spécifiques et établies sur demande. N'hésitez pas à nous écrire pour en discuter.",
        ],
        [
            'lang'     => 'en',
            'question' => "Can we rent the whole villa outside July and August?",
            'answer'   => "Yes, on request. The whole-villa rental is mainly available in July and August, but off-season stays can be considered on a case-by-case basis — duration, period, included services. Conditions are then specific and set on request. Feel free to write to us to discuss.",
        ],
        [
            'lang'     => 'es',
            'question' => "¿Se puede alquilar la villa entera fuera de julio y agosto?",
            'answer'   => "Sí, bajo solicitud. El alquiler de la villa entera está disponible principalmente en julio y agosto, pero podemos estudiar estancias fuera de temporada caso por caso — duración, periodo, servicios incluidos. Las condiciones son entonces específicas y se establecen bajo solicitud. No dudéis en escribirnos para hablarlo.",
        ],
    ];

    foreach ($rows as $row) {
        Database::insert('vp_faq', [
            'page_slug' => $slug,
            'lang'      => $row['lang'],
            'question'  => $row['question'],
            'answer'    => $row['answer'],
            'position'  => $nextPos,
            'active'    => 1,
        ]);
        echo "  ✓ [{$row['lang']}/pos=$nextPos] " . mb_strimwidth($row['question'], 0, 60, '…') . "\n";
    }
}

echo "\nDone.\n";
