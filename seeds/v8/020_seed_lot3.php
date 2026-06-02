<?php
declare(strict_types=1);

/**
 * V8 — Lot 3 : hero éditable sur les 3 pages légales (mentions, politique,
 * plan-du-site). Le texte juridique reste en HTML dur côté vue.
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — Lot 3 (Mentions légales, Politique, Plan du site) ===\n\n";

$pages = [
    'mentions-legales' => [
        'fr' => [
            'title' => "Mentions *légales*.",
            'lede'  => "L'éditeur du site, l'hébergement, la propriété intellectuelle, les crédits photo et la responsabilité — tout ce qu'il faut savoir réuni sur une page.",
            'tags'  => ['Légal', 'Villa Plaisance'],
        ],
        'en' => [
            'title' => "Legal *notice*.",
            'lede'  => "The legal owner of this site, hosting details, intellectual property, photo credits and liability — all you need to know in a single page.",
            'tags'  => ['Legal', 'Villa Plaisance'],
        ],
        'es' => [
            'title' => "Aviso *legal*.",
            'lede'  => "El editor del sitio, el alojamiento, la propiedad intelectual, los créditos fotográficos y la responsabilidad — todo lo necesario reunido en una página.",
            'tags'  => ['Legal', 'Villa Plaisance'],
        ],
    ],
    'politique-confidentialite' => [
        'fr' => [
            'title' => "Politique de *confidentialité*.",
            'lede'  => "Quelles données on collecte, pourquoi, combien de temps on les garde, et vos droits selon le RGPD. Aucun suivi tant que vous ne l'avez pas explicitement accepté.",
            'tags'  => ['Légal', 'RGPD'],
        ],
        'en' => [
            'title' => "Privacy *policy*.",
            'lede'  => "What data we collect, why, how long we keep it, and your rights under GDPR. No tracking unless you explicitly accept it.",
            'tags'  => ['Legal', 'GDPR'],
        ],
        'es' => [
            'title' => "Política de *privacidad*.",
            'lede'  => "Qué datos recopilamos, por qué, cuánto tiempo los conservamos, y vuestros derechos según el RGPD. Ningún seguimiento mientras no lo aceptéis explícitamente.",
            'tags'  => ['Legal', 'RGPD'],
        ],
    ],
    'plan-du-site' => [
        'fr' => [
            'title' => "Plan du *site*.",
            'lede'  => "Une vue d'ensemble de toutes les pages publiques. Si quelque chose manque, écrivez-nous, on l'ajoutera.",
            'tags'  => ['Sitemap', 'Villa Plaisance'],
        ],
        'en' => [
            'title' => "Site *map*.",
            'lede'  => "A bird's-eye view of every public page. If something's missing, write to us — we'll add it.",
            'tags'  => ['Sitemap', 'Villa Plaisance'],
        ],
        'es' => [
            'title' => "Mapa del *sitio*.",
            'lede'  => "Una vista general de todas las páginas públicas. Si falta algo, escribidnos y lo añadiremos.",
            'tags'  => ['Sitemap', 'Villa Plaisance'],
        ],
    ],
];

foreach ($pages as $slug => $langs) {
    echo "─── $slug ───\n";
    foreach ($langs as $lang => $data) {
        Database::query("DELETE FROM vp_sections WHERE page_slug = ? AND lang = ? AND position = 1", [$slug, $lang]);
        Database::insert('vp_sections', [
            'page_slug'  => $slug,
            'lang'       => $lang,
            'block_type' => 'hero',
            'title'      => 'Hero ' . $slug,
            'content'    => json_encode([
                'title' => $data['title'],
                'lede'  => $data['lede'],
                'tags'  => $data['tags'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'position'   => 1,
            'active'     => 1,
        ]);
        echo "  ✓ $slug/$lang hero\n";
    }
    echo "\n";
}

echo "Done.\n";
