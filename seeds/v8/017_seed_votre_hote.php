<?php
declare(strict_types=1);

/**
 * V8 — Port de la page /votre-hote sur vp_sections (FR + EN + ES).
 *
 * Migration depuis le système legacy vp_host_profile : on lit chaque langue,
 * on convertit en 8 blocs vp_sections cohérents avec les autres pages portées.
 *
 * Structure générée :
 *   pos 1 : hero        (title=name, lede=subtitle, image_id=lookup photo)
 *   pos 2 : prose text-only (quote en grand italique)
 *   pos 3 : prose two-col    (01 / Qui je suis — intro)
 *   pos 4 : prose two-col    (02 / D'où je viens — origin)
 *   pos 5 : prose two-col    (03 / Ce qui me passionne — passions)
 *   pos 6 : prose two-col    (04 / Ma philosophie — philosophy)
 *   pos 7 : prose two-col    (05 / Trois détails — fun_facts)
 *   pos 8 : cta              (Échanger directement)
 *
 * Idempotent : DELETE WHERE page_slug='votre-hote' AND lang=$lang avant INSERT.
 * Saute une langue si vp_host_profile n'a pas d'entrée pour cette langue.
 * Le système legacy vp_host_profile/vp_host_blocks reste en BDD comme fallback.
 *
 * Pré-requis : vp_host_profile peuplée (seed 025_host_profile.php déjà passé).
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — Port /votre-hote sur vp_sections ===\n\n";

// Helper : résoudre un filename en vp_media.id (si possible)
$mediaId = function (string $filename): ?int {
    if ($filename === '') return null;
    $name = basename($filename);  // au cas où le path est complet
    $row = Database::fetchOne("SELECT id FROM vp_media WHERE filename = ? LIMIT 1", [$name]);
    return $row ? (int)$row['id'] : null;
};

// Labels éditoriaux par langue (les overlines/labels des sections)
$labels = [
    'fr' => [
        'qui_je_suis'   => ['num' => '01', 'text' => 'En quelques mots',    'heading' => "Qui je *suis*."],
        'origines'      => ['num' => '02', 'text' => "D'où je viens",        'heading' => "Mes *origines*."],
        'passions'      => ['num' => '03', 'text' => 'Ce qui me passionne',  'heading' => "Mes *passions*."],
        'philosophie'   => ['num' => '04', 'text' => "Ma vision de l'accueil", 'heading' => "Ma *philosophie*."],
        'fun_facts'     => ['num' => '05', 'text' => 'Fun facts',            'heading' => "Trois *détails*."],
        'cta_heading'   => "Échanger *directement*.",
        'cta_text'      => "Pour préparer votre séjour, poser une question, ou simplement parler de la Provence.",
        'cta_label'     => "Écrire à votre hôte",
    ],
    'en' => [
        'qui_je_suis'   => ['num' => '01', 'text' => 'In a few words',       'heading' => "Who I *am*."],
        'origines'      => ['num' => '02', 'text' => "Where I'm from",       'heading' => "My *roots*."],
        'passions'      => ['num' => '03', 'text' => 'What I love',          'heading' => "My *passions*."],
        'philosophie'   => ['num' => '04', 'text' => 'My take on hospitality', 'heading' => "My *philosophy*."],
        'fun_facts'     => ['num' => '05', 'text' => 'Fun facts',            'heading' => "Three *details*."],
        'cta_heading'   => "Talk *directly*.",
        'cta_text'      => "To plan your stay, ask a question, or simply talk about Provence.",
        'cta_label'     => "Write to your host",
    ],
    'es' => [
        'qui_je_suis'   => ['num' => '01', 'text' => 'En pocas palabras',     'heading' => "Quién *soy*."],
        'origines'      => ['num' => '02', 'text' => "De dónde vengo",        'heading' => "Mis *orígenes*."],
        'passions'      => ['num' => '03', 'text' => 'Lo que me apasiona',    'heading' => "Mis *pasiones*."],
        'philosophie'   => ['num' => '04', 'text' => "Mi visión de la acogida", 'heading' => "Mi *filosofía*."],
        'fun_facts'     => ['num' => '05', 'text' => 'Detalles divertidos',   'heading' => "Tres *detalles*."],
        'cta_heading'   => "Hablar *directamente*.",
        'cta_text'      => "Para preparar vuestra estancia, hacer una pregunta, o simplemente hablar de la Provenza.",
        'cta_label'     => "Escribir al anfitrión",
    ],
];

$totalInserted = 0;
foreach (['fr', 'en', 'es'] as $lang) {
    $profile = Database::fetchOne("SELECT * FROM vp_host_profile WHERE lang = ?", [$lang]);
    if (!$profile) {
        echo "  ⚠️  Pas de vp_host_profile pour lang=$lang, skip\n";
        continue;
    }

    echo "─── Langue $lang ───\n";

    // Reset des blocs pour cette page+lang
    Database::query("DELETE FROM vp_sections WHERE page_slug = 'votre-hote' AND lang = ?", [$lang]);
    echo "  → DELETE votre-hote/$lang\n";

    $L = $labels[$lang];
    $photoId = $mediaId((string)($profile['photo'] ?? ''));
    if ($photoId === null) {
        echo "  ⚠️  photo '{$profile['photo']}' non trouvée dans vp_media (hero sans image)\n";
    }

    // ── BLOC 1 — HERO ──────────────────────────────────────────────
    $heroContent = [
        'title' => (string)($profile['name'] ?? ''),
        'lede'  => (string)($profile['subtitle'] ?? ''),
    ];
    if ($photoId !== null) $heroContent['image_id'] = $photoId;

    Database::insert('vp_sections', [
        'page_slug' => 'votre-hote',
        'lang' => $lang,
        'block_type' => 'hero',
        'title' => 'Hero votre hôte',
        'content' => json_encode($heroContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'position' => 1,
        'active' => 1,
    ]);
    echo "  ✓ [1] hero\n";

    // ── BLOC 2 — QUOTE (prose text-only avec heading en quote) ─────
    if (!empty($profile['quote'])) {
        Database::insert('vp_sections', [
            'page_slug' => 'votre-hote',
            'lang' => $lang,
            'block_type' => 'prose',
            'title' => 'Citation',
            'content' => json_encode([
                'layout' => 'text-only',
                'heading' => '*' . $profile['quote'] . '*',
                'surface' => 'stone',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'position' => 2,
            'active' => 1,
        ]);
        echo "  ✓ [2] quote (prose text-only)\n";
    }

    // ── BLOCS 3-7 — Sections intro / origines / passions / philo / fun_facts ──
    $sectionMap = [
        3 => ['key' => 'intro',      'labels' => $L['qui_je_suis']],
        4 => ['key' => 'origin',     'labels' => $L['origines']],
        5 => ['key' => 'passions',   'labels' => $L['passions']],
        6 => ['key' => 'philosophy', 'labels' => $L['philosophie']],
        7 => ['key' => 'fun_facts',  'labels' => $L['fun_facts']],
    ];
    foreach ($sectionMap as $pos => $spec) {
        $text = (string)($profile[$spec['key']] ?? '');
        if (trim($text) === '') continue;

        Database::insert('vp_sections', [
            'page_slug' => 'votre-hote',
            'lang' => $lang,
            'block_type' => 'prose',
            'title' => 'Section ' . $spec['labels']['text'],
            'content' => json_encode([
                'layout' => 'two-col',
                'label_numeral' => $spec['labels']['num'],
                'label_text' => $spec['labels']['text'],
                'heading' => $spec['labels']['heading'],
                'text' => $text,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'position' => $pos,
            'active' => 1,
        ]);
        echo "  ✓ [$pos] prose ({$spec['key']})\n";
    }

    // ── BLOC 8 — CTA final ──────────────────────────────────────────
    Database::insert('vp_sections', [
        'page_slug' => 'votre-hote',
        'lang' => $lang,
        'block_type' => 'cta',
        'title' => 'CTA contact',
        'content' => json_encode([
            'heading' => $L['cta_heading'],
            'text' => $L['cta_text'],
            'surface' => 'stone',
            'cta' => [
                ['label' => $L['cta_label'], 'url' => '/contact', 'style' => 'primary'],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'position' => 8,
        'active' => 1,
    ]);
    echo "  ✓ [8] cta\n\n";

    $totalInserted += 8;
}

echo "Terminé : ~$totalInserted blocs insérés (jusqu'à 8 par langue × 3 langues).\n";
echo "💡 La page /votre-hote rend désormais depuis vp_sections.\n";
echo "💡 Édition via /admin/sections/page/votre-hote (sélecteur langue en haut).\n";
echo "💡 Le système legacy vp_host_profile reste intact en BDD — au cas où.\n";
