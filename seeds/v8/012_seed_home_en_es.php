<?php
declare(strict_types=1);

/**
 * V8 — Traductions EN + ES de la page accueil.
 *
 * Source EN : attributs data-en="..." extraits de app/Views/front/home.php
 * (traductions originales Jorge).
 * Source ES : traductions proposées par l'assistant, à valider/corriger par
 * Jorge via /admin/sections (ou en éditant ce seed directement).
 *
 * Idempotent : DELETE WHERE page_slug='accueil' AND lang IN ('en', 'es') avant INSERT.
 * Ne touche jamais aux blocs FR (seed 001).
 *
 * Précondition : seed 001_seed_home.php a tourné (vp_media OK).
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — Traductions Accueil EN + ES ===\n\n";

// Helper lookup vp_media par filename (résout les image_id en runtime)
$mediaId = function (string $filename): ?int {
    $row = Database::fetchOne("SELECT id FROM vp_media WHERE filename = ? LIMIT 1", [$filename]);
    return $row ? (int)$row['id'] : null;
};

$heroImageId = $mediaId('hero-piscine.webp');
$journalImg1 = $mediaId('villa-plaisance-vignes-provence-01.webp');
$journalImg2 = $mediaId('villa-plaisance-vp-itini-elisa-02-pont-du-gard.webp');

// ============================================================================
// Définition des blocs traduits — array indexé par position
// Chaque entrée : { block_type, title, en: [...content...], es: [...content...] }
// ============================================================================
$blocks = [
    1 => [
        'block_type' => 'hero',
        'title_en' => 'Hero — home',
        'title_es' => 'Hero — inicio',
        'en' => [
            'title'    => "Villa\n*Plaisance*",
            'lede'     => "One house, two ways to stay — B&B from September to June, the whole villa in July and August.",
            'image_id' => $heroImageId,
            'tags'     => [
                'Bédarrides · Vaucluse · Provence',
                'Golden Triangle',
            ],
            'ctas' => [
                ['label' => "Sept → June · B&B",                 'url' => '/chambres-d-hotes',         'style' => 'primary'],
                ['label' => "July → August · Whole villa",       'url' => '/location-villa-provence', 'style' => 'ghost'],
            ],
        ],
        'es' => [
            'title'    => "Villa\n*Plaisance*",
            'lede'     => "Una casa, dos maneras de quedarse — habitaciones de huéspedes de septiembre a junio, casa entera en julio y agosto.",
            'image_id' => $heroImageId,
            'tags'     => [
                'Bédarrides · Vaucluse · Provenza',
                'Triángulo de Oro',
            ],
            'ctas' => [
                ['label' => "Sept → Junio · Habitaciones de huéspedes",  'url' => '/chambres-d-hotes',         'style' => 'primary'],
                ['label' => "Julio → Agosto · Casa entera",              'url' => '/location-villa-provence', 'style' => 'ghost'],
            ],
        ],
    ],

    2 => [
        'block_type' => 'prose',
        'title_en' => 'Intro — The house',
        'title_es' => 'Intro — La casa',
        'en' => [
            'label_numeral' => '01',
            'label_text'    => 'The house',
            'heading'       => "A *charming*\nhouse\nin Provence.",
            'text'          =>
                "Nestled in the heart of Provence's Golden Triangle, Villa Plaisance is a maison de charme in Bédarrides — 15 min from Avignon, 8 min from Châteauneuf-du-Pape, 18 min from Orange." .
                "\n\n" .
                "September to June: a B&B with homemade breakfast and shared pool. July to August: the whole villa (4 bedrooms, 10 guests, private 12 × 6 m pool) in full autonomy." .
                "\n\n" .
                "The place is calm, the village alive, the countryside starts at the doorstep, the TGV is fifteen minutes away.",
            'layout'        => 'two-col',
        ],
        'es' => [
            'label_numeral' => '01',
            'label_text'    => 'La casa',
            'heading'       => "Una casa\nde *encanto*\nen Provenza.",
            'text'          =>
                "Enclavada en el corazón del Triángulo de Oro provenzal, Villa Plaisance es una casa de encanto en Bédarrides — a 15 min de Aviñón, 8 min de Châteauneuf-du-Pape, 18 min de Orange." .
                "\n\n" .
                "De septiembre a junio: habitaciones de huéspedes B&B con desayuno casero y piscina compartida. En julio–agosto: la casa entera (4 habitaciones, 10 personas, piscina privada 12 × 6 m) en total autonomía." .
                "\n\n" .
                "El lugar es tranquilo, el pueblo vive, el campo empieza al salir, el TGV está a quince minutos.",
            'layout'        => 'two-col',
        ],
    ],

    3 => [
        'block_type' => 'formula',
        'title_en' => 'Two formulas — B&B + Villa',
        'title_es' => 'Dos fórmulas — B&B + Villa',
        'en' => [
            'label_numeral' => '02',
            'label_text'    => 'Two formulas',
            'heading'       => "Two ways\nto *stay*,\none house.",
            'intro'         => "The house lives in two seasons — pick the one that suits you.",
            'surface'       => 'stone',
            'formulas' => [
                [
                    'label_numeral' => '01',
                    'label_period'  => 'Sept → June',
                    'label_tag'     => "At the host's place",
                    'title'         => "B&B",
                    'text'          => "You stay at our place. Two communicating, air-conditioned bedrooms with a private bathroom are strictly dedicated to you. Breakfast is included — local produce, homemade jams, fruit from the garden. Shared pool, personalised advice and a warm welcome. A communicating suite ideal for families (1 to 5 guests).",
                    'stats'         => ['1 – 5 guests', 'Breakfast included', 'Shared pool'],
                    'cta'           => ['label' => "Discover the B&B", 'url' => '/chambres-d-hotes'],
                ],
                [
                    'label_numeral' => '02',
                    'label_period'  => 'July & August',
                    'label_tag'     => 'On your own',
                    'title'         => 'Whole villa',
                    'text'          => "You stay on your own and have the villa and the outdoors exclusively. 4 bedrooms, 2 bathrooms, fully equipped kitchen, fenced private 12 × 6 m pool and garden under the olive trees. Up to 10 guests in total autonomy — your home in Provence, no neighbours.",
                    'stats'         => ['Up to 10 guests', 'Private 12 × 6 m pool', 'Sat → Sat'],
                    'cta'           => ['label' => 'Discover the whole villa', 'url' => '/location-villa-provence'],
                ],
            ],
        ],
        'es' => [
            'label_numeral' => '02',
            'label_text'    => 'Dos fórmulas',
            'heading'       => "Dos maneras\nde *quedarse*,\nuna sola casa.",
            'intro'         => "La casa vive dos temporadas — elige la que más te conviene.",
            'surface'       => 'stone',
            'formulas' => [
                [
                    'label_numeral' => '01',
                    'label_period'  => 'Sept → Junio',
                    'label_tag'     => 'En casa del anfitrión',
                    'title'         => 'Habitaciones de huéspedes',
                    'text'          => "Te alojas en casa del anfitrión. Dos habitaciones comunicantes y climatizadas con baño privado, estrictamente dedicadas a ti. El desayuno está incluido: productos locales, mermeladas caseras, fruta del jardín. Piscina compartida, consejos personalizados y una acogida cálida. Suite comunicante ideal para familias (1 a 5 personas).",
                    'stats'         => ['1 – 5 pers.', 'Desayuno incluido', 'Piscina compartida'],
                    'cta'           => ['label' => 'Descubrir las habitaciones', 'url' => '/chambres-d-hotes'],
                ],
                [
                    'label_numeral' => '02',
                    'label_period'  => 'Julio & Agosto',
                    'label_tag'     => 'Vosotros solos',
                    'title'         => 'La Villa en exclusiva',
                    'text'          => "Os alojáis solos y disponéis de la casa y los exteriores en exclusiva. 4 habitaciones, 2 baños, cocina totalmente equipada, piscina privada vallada de 12 × 6 m y jardín bajo los olivos. Hasta 10 personas en total autonomía — vuestra casa en Provenza, sin vecinos a la vista.",
                    'stats'         => ['Hasta 10 pers.', 'Piscina privada 12 × 6', 'Sábado → sábado'],
                    'cta'           => ['label' => 'Descubrir la casa entera', 'url' => '/location-villa-provence'],
                ],
            ],
        ],
    ],

    4 => [
        'block_type' => 'territoire',
        'title_en' => "Golden Triangle — destinations",
        'title_es' => "Triángulo de Oro — destinos",
        'en' => [
            'label_numeral' => '03',
            'label_text'    => 'Where we are',
            'heading'       => "At the heart of\nthe *Golden Triangle*.",
            'intro'         => "The whole region radiates from Bédarrides — Avignon to the south, Orange to the north, Châteauneuf to the west, the Mont Ventoux straight ahead.",
            'destinations' => [
                ['time' => '8 MIN',  'place' => 'Châteauneuf-du-Pape',  'tag' => 'Vines'],
                ['time' => '15 MIN', 'place' => 'Avignon',              'tag' => 'TGV station'],
                ['time' => '18 MIN', 'place' => 'Orange',               'tag' => 'Roman theatre'],
                ['time' => '25 MIN', 'place' => "L'Isle-sur-la-Sorgue", 'tag' => 'Antiques market'],
                ['time' => '30 MIN', 'place' => 'Pont du Gard',         'tag' => 'Roman heritage'],
                ['time' => '35 MIN', 'place' => 'Vaison-la-Romaine',    'tag' => 'Ruins & market'],
                ['time' => '42 MIN', 'place' => 'Gordes',               'tag' => 'Perched village'],
                ['time' => '45 MIN', 'place' => 'Les Baux-de-Provence', 'tag' => 'Carrières de Lumières'],
                ['time' => '45 MIN', 'place' => 'Mont Ventoux',         'tag' => 'The road, the view'],
            ],
        ],
        'es' => [
            'label_numeral' => '03',
            'label_text'    => 'Dónde estamos',
            'heading'       => "En el corazón del\n*Triángulo de Oro*.",
            'intro'         => "Toda la región irradia desde Bédarrides — Aviñón al sur, Orange al norte, Châteauneuf al oeste, el Mont Ventoux justo enfrente.",
            'destinations' => [
                ['time' => '8 MIN',  'place' => 'Châteauneuf-du-Pape',  'tag' => 'Viñas'],
                ['time' => '15 MIN', 'place' => 'Aviñón',               'tag' => 'Estación TGV'],
                ['time' => '18 MIN', 'place' => 'Orange',               'tag' => 'Teatro romano'],
                ['time' => '25 MIN', 'place' => "L'Isle-sur-la-Sorgue", 'tag' => 'Mercado de antigüedades'],
                ['time' => '30 MIN', 'place' => 'Pont du Gard',         'tag' => 'Patrimonio romano'],
                ['time' => '35 MIN', 'place' => 'Vaison-la-Romaine',    'tag' => 'Ruinas y mercado'],
                ['time' => '42 MIN', 'place' => 'Gordes',               'tag' => 'Pueblo encaramado'],
                ['time' => '45 MIN', 'place' => 'Les Baux-de-Provence', 'tag' => 'Carrières de Lumières'],
                ['time' => '45 MIN', 'place' => 'Mont Ventoux',         'tag' => 'La carretera, la vista'],
            ],
        ],
    ],

    6 => [
        'block_type' => 'avis',
        'title_en' => 'Testimonials (placeholders)',
        'title_es' => 'Testimonios (placeholders)',
        'en' => [
            'label_numeral' => '05',
            'label_text'    => 'What guests say',
            'heading'       => "A few *words*\nleft on departure.",
            'intro'         => "Example testimonials — to be replaced by your real reviews",
            'intro_style'   => 'placeholder',
            'display'       => 'testimonial',
            'source'        => 'manual',
            'items' => [
                [
                    'rating'   => 5,
                    'quote'    => "« *Placeholder* — a word from the traveller about the room, breakfast, the welcome. Two or three sentences at most. »",
                    'author'   => 'Guest',
                    'location' => 'city',
                ],
                [
                    'rating'   => 5,
                    'quote'    => "« *Placeholder* — a word about the whole villa, the pool, the kitchen, the quiet. Testimonial to come. »",
                    'author'   => 'Guest',
                    'location' => 'city',
                ],
                [
                    'rating'   => 5,
                    'quote'    => "« *Placeholder* — a word about the region, the tips, the availability of the hosts. »",
                    'author'   => 'Guest',
                    'location' => 'city',
                ],
            ],
        ],
        'es' => [
            'label_numeral' => '05',
            'label_text'    => 'Lo que dicen',
            'heading'       => "Algunas *palabras*\ndejadas al partir.",
            'intro'         => "Testimonios de ejemplo — a sustituir por vuestras opiniones reales",
            'intro_style'   => 'placeholder',
            'display'       => 'testimonial',
            'source'        => 'manual',
            'items' => [
                [
                    'rating'   => 5,
                    'quote'    => "« *Placeholder* — una palabra del viajero sobre la habitación, el desayuno, la acogida. Dos o tres frases como mucho. »",
                    'author'   => 'Huésped',
                    'location' => 'ciudad',
                ],
                [
                    'rating'   => 5,
                    'quote'    => "« *Placeholder* — una palabra sobre la casa entera, la piscina, la cocina, la tranquilidad. Testimonio por venir. »",
                    'author'   => 'Huésped',
                    'location' => 'ciudad',
                ],
                [
                    'rating'   => 5,
                    'quote'    => "« *Placeholder* — una palabra sobre la región, los consejos, la disponibilidad de los anfitriones. »",
                    'author'   => 'Huésped',
                    'location' => 'ciudad',
                ],
            ],
        ],
    ],

    7 => [
        'block_type' => 'articles',
        'title_en' => 'From the journal — teasers',
        'title_es' => 'Del diario — teasers',
        'en' => [
            'label_numeral' => '06',
            'label_text'    => 'From the journal',
            'heading'       => "What we *write*,\nwhat we suggest.",
            'intro'         => "Two sections — tourism essays, and what to do nearby.",
            'surface'       => 'sage-light',
            'source'        => 'manual',
            'display'       => 'grid',
            'items' => [
                [
                    'image_id' => $journalImg1,
                    'kicker'   => 'Journal · Tourism',
                    'title'    => "Travelling\n*differently*\nin Provence.",
                    'text'     => "Five ways of looking at the region — Provence contemporaine, voyager autrement, hosts & hoteliers, land & transition, the art of staying.",
                    'cta'      => ['label' => 'Read the journal', 'url' => '/journal'],
                ],
                [
                    'image_id' => $journalImg2,
                    'kicker'   => 'Journal · What to do nearby',
                    'title'    => "On site,\neverything\nis *there*.",
                    'text'     => "Sites to visit, tables, shops, things to do with children — the house's pick.",
                    'cta'      => ['label' => 'See the selection', 'url' => '/itineraire'],
                ],
            ],
        ],
        'es' => [
            'label_numeral' => '06',
            'label_text'    => 'Del diario',
            'heading'       => "Lo que *escribimos*,\nlo que aconsejamos.",
            'intro'         => "Dos secciones — artículos sobre el turismo, y la selección en el sitio.",
            'surface'       => 'sage-light',
            'source'        => 'manual',
            'display'       => 'grid',
            'items' => [
                [
                    'image_id' => $journalImg1,
                    'kicker'   => 'Diario · Turismo',
                    'title'    => "Viajar\n*de otro modo*\nen Provenza.",
                    'text'     => "Cinco maneras de mirar la región — Provenza contemporánea, viajar de otro modo, anfitriones y hoteleros, territorio y transición, el arte de alojarse.",
                    'cta'      => ['label' => 'Leer el diario', 'url' => '/journal'],
                ],
                [
                    'image_id' => $journalImg2,
                    'kicker'   => 'Diario · Qué hacer en el sitio',
                    'title'    => "En el sitio,\ntodo está\n*ahí*.",
                    'text'     => "Lugares por visitar, mesas, comercios, actividades con niños — la selección de la casa.",
                    'cta'      => ['label' => 'Ver la selección', 'url' => '/itineraire'],
                ],
            ],
        ],
    ],
];

// ============================================================================
// Insertion : DELETE + INSERT pour chaque langue
// ============================================================================
foreach (['en', 'es'] as $lang) {
    Database::query(
        "DELETE FROM vp_sections WHERE page_slug = 'accueil' AND lang = ?",
        [$lang]
    );
    echo "  → DELETE accueil/$lang\n";

    foreach ($blocks as $position => $block) {
        $titleKey = 'title_' . $lang;
        Database::insert('vp_sections', [
            'page_slug'  => 'accueil',
            'lang'       => $lang,
            'block_type' => $block['block_type'],
            'title'      => $block[$titleKey] ?? $block['block_type'],
            'content'    => json_encode($block[$lang], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'position'   => $position,
            'active'     => 1,
        ]);
        echo "  ✓ [$position] {$block['block_type']} ($lang)\n";
    }
    echo "\n";
}

// ============================================================================
// Vérification finale
// ============================================================================
$check = Database::fetchAll(
    "SELECT lang, block_type, position FROM vp_sections WHERE page_slug='accueil' AND lang IN ('en','es') ORDER BY lang, position"
);
echo "État vp_sections (accueil/en+es) :\n";
foreach ($check as $row) {
    echo "  - [{$row['lang']}] type={$row['block_type']} pos={$row['position']}\n";
}
echo "\nDone.\n";
