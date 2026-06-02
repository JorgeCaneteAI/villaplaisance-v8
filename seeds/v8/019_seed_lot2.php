<?php
declare(strict_types=1);

/**
 * V8 — Lot 2 : hero éditable sur Espaces extérieurs et Itinéraire.
 * Pattern identique au lot 1.
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — Lot 2 (Espaces extérieurs, Itinéraire) ===\n\n";

$pages = [
    'espaces-exterieurs' => [
        'fr' => [
            'title' => "Dehors, ici,\nc'est encore *chez vous*.",
            'lede'  => "Le jardin de Villa Plaisance est un prolongement naturel de la maison — 1 500 m² de verdure, oliviers centenaires, lavandes, rosiers anciens, herbes aromatiques.",
            'tags'  => ['Les extérieurs', '1 500 m² de jardin'],
        ],
        'en' => [
            'title' => "Outside, here,\nis still *your home*.",
            'lede'  => "The garden of Villa Plaisance is a natural extension of the house — 1,500 m² of green, century-old olive trees, lavender, old roses and aromatic herbs.",
            'tags'  => ['The outdoors', '1,500 m² garden'],
        ],
        'es' => [
            'title' => "Fuera, aquí,\nseguís en *vuestra casa*.",
            'lede'  => "El jardín de Villa Plaisance es una prolongación natural de la casa — 1 500 m² de verdor, olivos centenarios, lavandas, rosales antiguos, hierbas aromáticas.",
            'tags'  => ['Los exteriores', '1 500 m² de jardín'],
        ],
    ],
    'itineraire' => [
        'fr' => [
            'title' => "Sur place,\ntout est *là*.",
            'lede'  => "Sites à visiter, tables et restaurants, commerces, activités avec les enfants — ce qu'on vous indiquerait nous-mêmes au petit-déjeuner.",
            'tags'  => ['Journal · 05 / Que faire sur place', 'La sélection de la maison'],
        ],
        'en' => [
            'title' => "On site,\neverything is *there*.",
            'lede'  => "Sites to visit, tables and restaurants, shops, things to do with children — what we'd point to ourselves over breakfast.",
            'tags'  => ['Journal · 05 / What to do nearby', "The house's pick"],
        ],
        'es' => [
            'title' => "En el sitio,\ntodo está *ahí*.",
            'lede'  => "Lugares por visitar, mesas y restaurantes, comercios, actividades con niños — lo que os indicaríamos nosotros mismos al desayuno.",
            'tags'  => ['Diario · 05 / Qué hacer cerca', 'La selección de la casa'],
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
