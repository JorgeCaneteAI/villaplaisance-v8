<?php
declare(strict_types=1);

/**
 * Seed V8 — Page Accueil (tous les blocs).
 *
 * Phase 2 du chantier B (port vp_sections).
 *
 * Idempotent : DELETE WHERE page_slug='accueil' AND lang='fr' avant INSERT.
 * Ne touche pas aux blocs des autres pages — quand on portera /chambres-d-hotes
 * etc., chacune aura son propre seed (002_seed_chambres.php, ...).
 *
 * ⚠️ S'exécute sur VPV8_dev (DB isolée). N'affecte pas la prod V7.
 *
 * Précondition : vp_media doit contenir les entrées référencées (lookup
 * par filename, robuste à un changement d'ID).
 *
 * Usage : cd /home/efkz3012/v2.villaplaisance.fr && php seeds/v8/001_seed_home.php
 */

define('ROOT', dirname(__DIR__, 2));
require ROOT . '/config.php';

echo "=== Seed V8 — Page Accueil ===\n\n";

// Helper de lookup vp_media par filename
$mediaId = function (string $filename): ?int {
    $row = Database::fetchOne("SELECT id FROM vp_media WHERE filename = ? LIMIT 1", [$filename]);
    return $row ? (int)$row['id'] : null;
};

$heroImageId = $mediaId('hero-piscine.webp');
if ($heroImageId === null) {
    fwrite(STDERR, "✕ vp_media manque 'hero-piscine.webp'. Annulé.\n");
    exit(1);
}
echo "  → hero-piscine.webp = vp_media.id $heroImageId\n";

// Reset des sections de la page accueil/fr (idempotence)
Database::query("DELETE FROM vp_sections WHERE page_slug = 'accueil' AND lang = 'fr'");
echo "  → DELETE accueil/fr\n";

// ========================================================================
// BLOC 1 — HERO
// ========================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'accueil',
    'lang'       => 'fr',
    'block_type' => 'hero',
    'title'      => 'Hero accueil V8',
    'content'    => json_encode([
        'title'    => "Villa\n*Plaisance*",
        'lede'     => "Une maison, deux façons d'y séjourner — chambres d'hôtes de septembre à juin, maison d'hôtes en juillet et août.",
        'image_id' => $heroImageId,
        'tags'     => [
            'Bédarrides · Vaucluse · Provence',
            "Triangle d'Or",
        ],
        'ctas' => [
            ['label' => "Sept → Juin · Chambres d'hôtes", 'url' => '/chambres-d-hotes',         'style' => 'primary'],
            ['label' => "Juil → Août · Maison d'hôtes",   'url' => '/location-villa-provence', 'style' => 'ghost'],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 1,
    'active'     => 1,
]);
echo "  ✓ [1] Hero\n";

// ========================================================================
// BLOC 2 — INTRO « Une maison de charme en Provence » (prose two-col)
// ========================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'accueil',
    'lang'       => 'fr',
    'block_type' => 'prose',
    'title'      => 'Intro — La maison',
    'content'    => json_encode([
        'label_numeral' => '01',
        'label_text'    => 'La maison',
        'heading'       => "Une maison\nde *charme*\nen Provence.",
        'text'          => "Nichée au cœur du Triangle d'Or provençal, Villa Plaisance est une maison de charme à Bédarrides — à 15 min d'Avignon, 8 min de Châteauneuf-du-Pape, 18 min d'Orange."
                          . "\n\n"
                          . "De septembre à juin : chambres d'hôtes B&B avec petit-déjeuner maison et piscine partagée. En juillet–août : la villa entière (4 chambres, 10 personnes, piscine privée 12 × 6 m) en toute autonomie."
                          . "\n\n"
                          . "Le lieu est calme, le village vivant, la campagne à pied, le TGV à quinze minutes.",
        'layout'        => 'two-col',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 2,
    'active'     => 1,
]);
echo "  ✓ [2] Intro (prose two-col)\n";

// ========================================================================
// BLOC 3 — DEUX FORMULES (nouveau type V8 : formula)
// ========================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'accueil',
    'lang'       => 'fr',
    'block_type' => 'formula',
    'title'      => 'Deux formules — B&B + Villa',
    'content'    => json_encode([
        'label_numeral' => '02',
        'label_text'    => 'Deux formules',
        'heading'       => "Deux façons\nde *séjourner*,\nune seule maison.",
        'intro'         => "La maison vit deux saisons — choisissez celle qui vous va.",
        'surface'       => 'stone',
        'formulas' => [
            [
                'label_numeral' => '01',
                'label_period'  => 'Sept → Juin',
                'label_tag'     => "Chez l'habitant",
                'title'         => "Chambres d'hôtes",
                'text'          => "Vous séjournez chez l'habitant. Deux chambres communicantes et climatisées avec salle de bain privée vous sont strictement dédiées. Le petit-déjeuner est inclus : produits locaux, confitures maison, fruits du jardin. Piscine partagée, conseils personnalisés et accueil chaleureux. Suite communicante idéale pour les familles (1 à 5 personnes).",
                'stats'         => ['1 – 5 pers.', 'Petit-déj inclus', 'Piscine partagée'],
                'cta'           => ['label' => "Découvrir les chambres d'hôtes", 'url' => '/chambres-d-hotes'],
            ],
            [
                'label_numeral' => '02',
                'label_period'  => 'Juil & Août',
                'label_tag'     => 'Vous seuls',
                'title'         => 'La Villa en exclusivité',
                'text'          => "Vous séjournez seuls et disposez de la villa et des extérieurs en exclusivité. 4 chambres, 2 salles de bain, cuisine entièrement équipée, piscine privée clôturée 12 × 6 m et jardin sous les oliviers. Jusqu'à 10 personnes en totale autonomie — votre maison en Provence, sans vis-à-vis.",
                'stats'         => ["Jusqu'à 10 pers.", 'Piscine privée 12 × 6', 'Samedi → samedi'],
                'cta'           => ['label' => 'Découvrir la villa entière', 'url' => '/location-villa-provence'],
            ],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 3,
    'active'     => 1,
]);
echo "  ✓ [3] Formules (formula)\n";

// ========================================================================
// BLOC 4 — TRIANGLE D'OR (territoire two-col)
// ========================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'accueil',
    'lang'       => 'fr',
    'block_type' => 'territoire',
    'title'      => "Triangle d'Or — destinations",
    'content'    => json_encode([
        'label_numeral' => '03',
        'label_text'    => 'Où nous sommes',
        'heading'       => "Au cœur du\n*Triangle d'Or*.",
        'intro'         => "Toute la région rayonne depuis Bédarrides — Avignon au sud, Orange au nord, Châteauneuf à l'ouest, le Mont Ventoux droit devant.",
        'destinations' => [
            ['time' => '8 MIN',  'place' => 'Châteauneuf-du-Pape',    'tag' => 'Vignes'],
            ['time' => '15 MIN', 'place' => 'Avignon',                'tag' => 'Gare TGV'],
            ['time' => '18 MIN', 'place' => 'Orange',                 'tag' => 'Théâtre antique'],
            ['time' => '25 MIN', 'place' => "L'Isle-sur-la-Sorgue",   'tag' => 'Marché brocante'],
            ['time' => '30 MIN', 'place' => 'Pont du Gard',           'tag' => 'Patrimoine romain'],
            ['time' => '35 MIN', 'place' => 'Vaison-la-Romaine',      'tag' => 'Antiques & marché'],
            ['time' => '42 MIN', 'place' => 'Gordes',                 'tag' => 'Village perché'],
            ['time' => '45 MIN', 'place' => 'Les Baux-de-Provence',   'tag' => 'Carrières de Lumières'],
            ['time' => '45 MIN', 'place' => 'Mont Ventoux',           'tag' => 'La route, la vue'],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 4,
    'active'     => 1,
]);
echo "  ✓ [4] Triangle d'Or (territoire)\n";

// ========================================================================
// BLOC 5 (numéral 05) — TÉMOIGNAGES (avis display=testimonial, placeholders)
// NB. La numérotation visuelle saute le 04 (mappemonde) qui reste en HTML
// dur pour cette phase. À harmoniser quand on portera la mappemonde.
// ========================================================================
Database::insert('vp_sections', [
    'page_slug'  => 'accueil',
    'lang'       => 'fr',
    'block_type' => 'avis',
    'title'      => 'Témoignages (placeholders)',
    'content'    => json_encode([
        'label_numeral' => '05',
        'label_text'    => "Ce qu'on en dit",
        'heading'       => "Quelques *mots*\nlaissés au départ.",
        'intro'         => "Témoignages d'exemple — à remplacer par vos vrais avis",
        'intro_style'   => 'placeholder',
        'display'       => 'testimonial',
        'source'        => 'manual',
        'items' => [
            [
                'rating'   => 5,
                'quote'    => "« *Placeholder* — un mot du voyageur sur la chambre, le petit-déjeuner, l'accueil. Deux ou trois phrases au maximum. »",
                'author'   => 'Visiteur',
                'location' => 'ville',
            ],
            [
                'rating'   => 5,
                'quote'    => "« *Placeholder* — un mot sur la villa entière, la piscine, la cuisine, le calme. Témoignage à venir. »",
                'author'   => 'Visiteur',
                'location' => 'ville',
            ],
            [
                'rating'   => 5,
                'quote'    => "« *Placeholder* — un mot sur la région, les conseils, la disponibilité des hôtes. »",
                'author'   => 'Visiteur',
                'location' => 'ville',
            ],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 6,  // position 5 réservée à mappemonde quand on la portera
    'active'     => 1,
]);
echo "  ✓ [6] Témoignages (avis testimonial)\n";

// ========================================================================
// BLOC 7 — DU JOURNAL (articles display=grid manual, 2 cartes mosaïque)
// Images : on cherche dans vp_media par filename (peut être null si absent)
// ========================================================================
$journalImg1 = $mediaId('villa-plaisance-vignes-provence-01.webp');
$journalImg2 = $mediaId('villa-plaisance-vp-itini-elisa-02-pont-du-gard.webp');
if ($journalImg1 === null) echo "  ⚠ image journal #1 absente de vp_media (sera sans image)\n";
if ($journalImg2 === null) echo "  ⚠ image journal #2 absente de vp_media (sera sans image)\n";

Database::insert('vp_sections', [
    'page_slug'  => 'accueil',
    'lang'       => 'fr',
    'block_type' => 'articles',
    'title'      => 'Du journal — teasers',
    'content'    => json_encode([
        'label_numeral' => '06',
        'label_text'    => 'Du journal',
        'heading'       => "Ce qu'on *écrit*,\nce qu'on conseille.",
        'intro'         => "Deux rubriques — articles autour du tourisme, et la sélection sur place.",
        'surface'       => 'sage-light',
        'source'        => 'manual',
        'display'       => 'grid',
        'items' => [
            [
                'image_id' => $journalImg1,
                'kicker'   => 'Journal · Tourisme',
                'title'    => "Voyager *autrement*\nen Provence.",
                'text'     => "Cinq façons de regarder la région — provence contemporaine, voyager autrement, hôtes & hôteliers, territoire & transition, l'art de séjourner.",
                'cta'      => ['label' => 'Lire le journal', 'url' => '/journal'],
            ],
            [
                'image_id' => $journalImg2,
                'kicker'   => 'Journal · Que faire sur place',
                'title'    => "Sur place,\ntout est *là*.",
                'text'     => "Sites à visiter, tables, commerces, activités avec les enfants — la sélection de la maison.",
                'cta'      => ['label' => 'Voir la sélection', 'url' => '/itineraire'],
            ],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'position'   => 7,
    'active'     => 1,
]);
echo "  ✓ [7] Journal (articles grid manual)\n";

// ========================================================================
// Vérification finale
// ========================================================================
$check = Database::fetchAll(
    "SELECT id, block_type, position, active FROM vp_sections WHERE page_slug='accueil' AND lang='fr' ORDER BY position"
);
echo "\nÉtat vp_sections (accueil/fr) :\n";
foreach ($check as $row) {
    echo "  - id={$row['id']} type={$row['block_type']} pos={$row['position']} active={$row['active']}\n";
}
echo "\nDone.\n";
