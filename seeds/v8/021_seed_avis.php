<?php
declare(strict_types=1);

/**
 * V8 — Page /avis : entrée vp_pages (FR/EN/ES) + hero éditable (FR/EN/ES).
 *
 * Page dédiée aux témoignages clients, montée le 2026-06-02.
 * - Contenu dynamique : liste des avis vient de vp_reviews (status='published')
 * - Hero éditable via vp_sections (page_slug='avis', pos 1)
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — Page /avis (vp_pages + hero vp_sections) ===\n\n";

// 1. vp_pages
$pages = [
    'fr' => [
        'title' => 'Avis clients',
        'meta_title' => 'Avis clients · Villa Plaisance',
        'meta_desc' => "Découvrez les témoignages de nos hôtes — Airbnb, Booking, Google. Chambres d'hôtes et villa entière à Bédarrides, au cœur du Triangle d'Or provençal.",
    ],
    'en' => [
        'title' => 'Guest reviews',
        'meta_title' => 'Guest reviews · Villa Plaisance',
        'meta_desc' => "Read what our guests say — Airbnb, Booking, Google reviews. B&B and whole-villa rental in Bédarrides, at the heart of Provence's Golden Triangle.",
    ],
    'es' => [
        'title' => 'Opiniones de huéspedes',
        'meta_title' => 'Opiniones · Villa Plaisance',
        'meta_desc' => "Descubrid los testimonios de nuestros huéspedes — Airbnb, Booking, Google. Habitaciones de huéspedes y casa entera en Bédarrides, en el corazón del Triángulo de Oro provenzal.",
    ],
];
foreach ($pages as $lang => $data) {
    $existing = Database::fetchOne("SELECT id FROM vp_pages WHERE slug = ? AND lang = ?", ['avis', $lang]);
    if ($existing) {
        Database::update('vp_pages', ['title' => $data['title'], 'meta_title' => $data['meta_title'], 'meta_desc' => $data['meta_desc'], 'active' => 1], 'id = ?', [(int)$existing['id']]);
        echo "  ↻ vp_pages avis/$lang mis à jour\n";
    } else {
        Database::insert('vp_pages', ['slug' => 'avis', 'lang' => $lang, 'title' => $data['title'], 'meta_title' => $data['meta_title'], 'meta_desc' => $data['meta_desc'], 'active' => 1]);
        echo "  ✓ vp_pages avis/$lang créé\n";
    }
}

// 2. Hero éditable
$heroes = [
    'fr' => [
        'title' => "Ce qu'ils en *disent*.",
        'lede'  => "Les mots laissés par nos hôtes au fil des saisons — Airbnb, Booking, Google et nos propres carnets de séjour.",
        'tags'  => ['07 · Avis clients', 'Témoignages vérifiés'],
    ],
    'en' => [
        'title' => "What they *say*.",
        'lede'  => "Words left by our guests over the seasons — Airbnb, Booking, Google, and our own guest book.",
        'tags'  => ['07 · Guest reviews', 'Verified testimonials'],
    ],
    'es' => [
        'title' => "Lo que *dicen*.",
        'lede'  => "Las palabras dejadas por nuestros huéspedes a lo largo de las temporadas — Airbnb, Booking, Google y nuestros propios libros de visitas.",
        'tags'  => ['07 · Opiniones', 'Testimonios verificados'],
    ],
];
foreach ($heroes as $lang => $data) {
    Database::query("DELETE FROM vp_sections WHERE page_slug = 'avis' AND lang = ? AND position = 1", [$lang]);
    Database::insert('vp_sections', [
        'page_slug'  => 'avis',
        'lang'       => $lang,
        'block_type' => 'hero',
        'title'      => 'Hero avis',
        'content'    => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'position'   => 1,
        'active'     => 1,
    ]);
    echo "  ✓ hero avis/$lang inséré\n";
}

echo "\nDone.\n";
echo "💡 Page accessible : https://v2.villaplaisance.fr/avis\n";
echo "💡 Édition : /admin/sections/page/avis\n";
