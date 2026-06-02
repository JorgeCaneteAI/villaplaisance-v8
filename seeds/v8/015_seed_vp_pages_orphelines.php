<?php
declare(strict_types=1);

/**
 * V8 — crée les entrées vp_pages manquantes pour /disponibilites, /itineraire,
 * /votre-hote (FR + EN + ES). Meta basiques mais cohérentes — à affiner via
 * /admin/pages quand Jorge fait l'audit SEO.
 *
 * Idempotent : INSERT ... ON DUPLICATE KEY UPDATE via la contrainte UNIQUE
 * (slug, lang) du schéma vp_pages.
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — vp_pages orphelines (disponibilites, itineraire, votre-hote) ===\n\n";

$pages = [
    // /disponibilites
    [
        'slug' => 'disponibilites',
        'fr' => [
            'title'     => 'Disponibilités',
            'meta_title'=> 'Disponibilités · Villa Plaisance',
            'meta_desc' => "Consultez en temps réel les dates disponibles à la Villa Plaisance — chambres d'hôtes (sept-juin) et villa entière (juil-août) à Bédarrides en Provence.",
        ],
        'en' => [
            'title'     => 'Availability',
            'meta_title'=> 'Availability · Villa Plaisance',
            'meta_desc' => "Check available dates in real time at Villa Plaisance — B&B (Sept-June) and whole villa (July-August) in Bédarrides, Provence.",
        ],
        'es' => [
            'title'     => 'Disponibilidad',
            'meta_title'=> 'Disponibilidad · Villa Plaisance',
            'meta_desc' => "Consulta las fechas disponibles en Villa Plaisance — habitaciones de huéspedes (sept-junio) y casa entera (jul-agosto) en Bédarrides, Provenza.",
        ],
    ],
    // /itineraire
    [
        'slug' => 'itineraire',
        'fr' => [
            'title'     => 'Que faire sur place',
            'meta_title'=> 'Que faire en Provence · Villa Plaisance',
            'meta_desc' => "Carnet d'itinéraires autour de la Villa Plaisance : Châteauneuf-du-Pape, Pont du Gard, Mont Ventoux, marchés provençaux et adresses sélectionnées par vos hôtes.",
        ],
        'en' => [
            'title'     => 'What to do nearby',
            'meta_title'=> 'What to do in Provence · Villa Plaisance',
            'meta_desc' => "Itineraries around Villa Plaisance — Châteauneuf-du-Pape, Pont du Gard, Mont Ventoux, Provençal markets and a curated selection by your hosts.",
        ],
        'es' => [
            'title'     => 'Qué hacer cerca',
            'meta_title'=> 'Qué hacer en Provenza · Villa Plaisance',
            'meta_desc' => "Itinerarios alrededor de Villa Plaisance — Châteauneuf-du-Pape, Pont du Gard, Mont Ventoux, mercados provenzales y una selección de direcciones por vuestros anfitriones.",
        ],
    ],
    // /votre-hote
    [
        'slug' => 'votre-hote',
        'fr' => [
            'title'     => 'Votre hôte',
            'meta_title'=> 'Jorge Canete, votre hôte · Villa Plaisance',
            'meta_desc' => "Découvrez Jorge Canete, votre hôte à la Villa Plaisance, maison de charme à Bédarrides au cœur du Triangle d'Or provençal.",
        ],
        'en' => [
            'title'     => 'Your host',
            'meta_title'=> 'Jorge Canete, your host · Villa Plaisance',
            'meta_desc' => "Meet Jorge Canete, your host at Villa Plaisance — a maison de charme in Bédarrides, at the heart of Provence's Golden Triangle.",
        ],
        'es' => [
            'title'     => 'Vuestro anfitrión',
            'meta_title'=> 'Jorge Canete, vuestro anfitrión · Villa Plaisance',
            'meta_desc' => "Conoce a Jorge Canete, vuestro anfitrión en Villa Plaisance — una casa de encanto en Bédarrides, en el corazón del Triángulo de Oro provenzal.",
        ],
    ],
];

$inserted = 0;
$updated = 0;
foreach ($pages as $page) {
    $slug = $page['slug'];
    foreach (['fr', 'en', 'es'] as $lang) {
        $data = $page[$lang];
        // Check existence (la contrainte UNIQUE est sur (slug, lang))
        $existing = Database::fetchOne("SELECT id FROM vp_pages WHERE slug = ? AND lang = ?", [$slug, $lang]);
        if ($existing) {
            Database::update('vp_pages', [
                'title'      => $data['title'],
                'meta_title' => $data['meta_title'],
                'meta_desc'  => $data['meta_desc'],
                'active'     => 1,
            ], 'id = ?', [(int)$existing['id']]);
            $updated++;
            echo "  ↻ $slug/$lang mis à jour\n";
        } else {
            Database::insert('vp_pages', [
                'slug'       => $slug,
                'lang'       => $lang,
                'title'      => $data['title'],
                'meta_title' => $data['meta_title'],
                'meta_desc'  => $data['meta_desc'],
                'active'     => 1,
            ]);
            $inserted++;
            echo "  ✓ $slug/$lang créé\n";
        }
    }
}

echo "\nTerminé : $inserted créés, $updated mis à jour.\n";
echo "💡 Pour affiner les meta : /admin/pages → édition de la page concernée.\n";
