<?php
declare(strict_types=1);

/**
 * V8 — Traductions EN + ES de la page chambres-d-hotes.
 *
 * Source EN : data-en extraits de app/Views/front/chambres.php (origin Jorge).
 * Source ES : proposé par l'assistant, à valider via /admin/sections/page/chambres-d-hotes?lang=es
 *
 * Blocs traités : 1 (hero), 2 (prose intro), 4 (cartes B&B), 5 (salle de bain prose),
 *                 6 (petit-déj), 7 (liste équipements), 8 (tableau infos), 9 (faq).
 *
 * Idempotent : DELETE EN+ES avant INSERT. Ne touche pas FR (seeds 002, 008, 011).
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — Traductions Chambres EN + ES ===\n\n";

$mediaId = function (string $filename): ?int {
    $row = Database::fetchOne("SELECT id FROM vp_media WHERE filename = ? LIMIT 1", [$filename]);
    return $row ? (int)$row['id'] : null;
};
$breakfastImg = $mediaId('villa-plaisance-petit-dejeuner-brioche-01.webp') ?? $mediaId('petit-dejeuner.webp');
$bathImg = $mediaId('villa-plaisance-salle-de-bain-chambre-hotes-01.webp');

$blocks = [
    1 => [
        'block_type' => 'hero',
        'title_en' => 'Hero — B&B',
        'title_es' => 'Hero — habitaciones',
        'en' => [
            'title' => "A *suite*,\ntwo bedrooms,\nbreakfast included.",
            'lede'  => "From September to June, we welcome guests at Villa Plaisance in two charming bedrooms — homemade breakfast, shared pool, shaded gardens.",
            'tags'  => ["01 · B&B rooms · September → June", "Reply within the day"],
            'ctas'  => [
                ['label' => 'Enquire about a stay', 'url' => '/contact', 'style' => 'primary'],
                ['label' => 'Practical info',       'url' => '#infos',   'style' => 'ghost'],
            ],
        ],
        'es' => [
            'title' => "Una *suite*,\ndos habitaciones,\nun desayuno.",
            'lede'  => "De septiembre a junio, recibimos a nuestros huéspedes en Villa Plaisance en dos habitaciones con encanto — desayuno casero, piscina compartida, jardines a la sombra.",
            'tags'  => ["01 · Habitaciones de huéspedes · Septiembre → junio", "Respuesta en el día"],
            'ctas'  => [
                ['label' => 'Solicitar una estancia', 'url' => '/contact', 'style' => 'primary'],
                ['label' => 'Información práctica',   'url' => '#infos',   'style' => 'ghost'],
            ],
        ],
    ],

    2 => [
        'block_type' => 'prose',
        'title_en' => 'Intro — B&B',
        'title_es' => 'Intro — habitaciones',
        'en' => [
            'label_numeral' => '01',
            'label_text'    => 'The B&B',
            'heading'       => "B&B rooms\nin *Bédarrides*.",
            'text'          =>
                "At Villa Plaisance, we welcome guests from September to June in a private suite of two communicating bedrooms, designed for comfort and Provençal authenticity." .
                "\n\n" .
                "One entrance, one booking — the two rooms are rented as a single unit, for one to five guests. Homemade breakfast, shared pool, shaded gardens, 15 min from Avignon and 8 min from Châteauneuf-du-Pape.",
            'layout' => 'two-col',
            'stats_band' => [
                ['label' => 'Capacity',  'value' => '1 — 5 guests'],
                ['label' => 'Breakfast', 'value' => 'Included'],
                ['label' => 'Pool',      'value' => 'Shared'],
            ],
        ],
        'es' => [
            'label_numeral' => '01',
            'label_text'    => 'Las habitaciones',
            'heading'       => "Habitaciones\nde huéspedes\nen *Bédarrides*.",
            'text'          =>
                "En Villa Plaisance, recibimos a nuestros huéspedes de septiembre a junio en una suite privada formada por dos habitaciones comunicantes, pensadas para el confort y la autenticidad provenzal." .
                "\n\n" .
                "Una sola entrada, una sola reserva — las dos habitaciones se alquilan juntas, para una a cinco personas. Desayuno casero, piscina compartida, jardines a la sombra, a 15 min de Aviñón y 8 min de Châteauneuf-du-Pape.",
            'layout' => 'two-col',
            'stats_band' => [
                ['label' => 'Capacidad', 'value' => '1 — 5 pers.'],
                ['label' => 'Desayuno',  'value' => 'Incluido'],
                ['label' => 'Piscina',   'value' => 'Compartida'],
            ],
        ],
    ],

    4 => [
        'block_type' => 'cartes',
        'title_en' => 'B&B rooms (Verte + Bleue)',
        'title_es' => 'Habitaciones (Verde + Azul)',
        // Cartes B&B n'a pas de heading/intro — juste offer.
        // Le contenu visible (name, sous_titre, description, equip) vient de vp_pieces.
        'en' => ['offer' => 'bb'],
        'es' => ['offer' => 'bb'],
    ],

    5 => [
        'block_type' => 'prose',
        'title_en' => 'Private bathroom',
        'title_es' => 'Baño privado',
        'en' => [
            'layout' => 'two-col-image-left',
            'label_numeral' => '04',
            'label_text'    => 'Bath',
            'heading'       => "A *private*\nbathroom\nin each room.",
            'text'          =>
                "Each bedroom has its own private bathroom — no shared corridor, no waiting at the door." .
                "\n\n" .
                "Organic toiletries, generous towels, a walk-in shower or a tub — depending on the room. Hairdryer, mirror lighting, and the small things you'd want to find.",
            'image_id' => $bathImg,
        ],
        'es' => [
            'layout' => 'two-col-image-left',
            'label_numeral' => '04',
            'label_text'    => 'Baño',
            'heading'       => "Un baño\n*privado*\nen cada habitación.",
            'text'          =>
                "Cada habitación dispone de su propio baño privado — sin pasillo compartido, sin esperar detrás de la puerta." .
                "\n\n" .
                "Productos de aseo bio, toallas generosas, ducha a la italiana o bañera según la habitación. Secador, iluminación del espejo, y los pequeños detalles que se aprecian.",
            'image_id' => $bathImg,
        ],
    ],

    6 => [
        'block_type' => 'petit-dejeuner',
        'title_en' => 'Breakfast included',
        'title_es' => 'Desayuno incluido',
        'en' => [
            'label_numeral' => '05',
            'label_text'    => 'Breakfast',
            'heading'       => "*Homemade*\nbreakfast, included.",
            'text'          => "Every morning, from 7:30 to 10 am, on the terrace or in the veranda depending on the season.",
            'image_id'      => $breakfastImg,
            'surface'       => 'sage-light',
            'items' => [
                'House-made jams (fig, apricot, lavender-honey)',
                'Pastries',
                'Bread from the baker',
                'Provençal cheeses',
                'Regional charcuterie',
                'Fresh seasonal fruit',
                'Fresh-squeezed orange juice',
                'Coffee, tea, organic infusions',
                'Local honey',
                'Homemade yoghurt',
            ],
        ],
        'es' => [
            'label_numeral' => '05',
            'label_text'    => 'Desayuno',
            'heading'       => "Desayuno\n*casero*, incluido.",
            'text'          => "Cada mañana, de 7:30 a 10 h, en la terraza o en la galería según la estación.",
            'image_id'      => $breakfastImg,
            'surface'       => 'sage-light',
            'items' => [
                'Mermeladas artesanales (higo, albaricoque, lavanda-miel)',
                'Bollería',
                'Pan del panadero',
                'Quesos provenzales',
                'Embutidos regionales',
                'Frutas frescas de temporada',
                'Zumo de naranja recién exprimido',
                'Café, té, infusiones bio',
                'Miel local',
                'Yogur casero',
            ],
        ],
    ],

    7 => [
        'block_type' => 'liste',
        'title_en' => 'Equipment & services',
        'title_es' => 'Equipamiento y servicios',
        'en' => [
            'label_numeral' => '06',
            'label_text'    => 'Included',
            'heading'       => "Equipment\n& *services* included.",
            'intro'         => "The list of what we hand over with the key — nothing fancy, just what makes a stay easy.",
            'display'       => 'numbered-grid',
            'items' => [
                'Premium bedding (100% percale cotton sheets)',
                'Private bathroom with organic toiletries',
                'Reversible A/C in every bedroom',
                'Free high-speed wifi',
                'Flat-screen television',
                'Shared 12 × 6 m pool (May to October)',
                'Provençal garden and shaded terraces',
                'Free private parking',
                'Self-check-in (key box)',
                'Personalised local advice and recommendations',
            ],
        ],
        'es' => [
            'label_numeral' => '06',
            'label_text'    => 'Incluido',
            'heading'       => "Equipamiento\n& *servicios* incluidos.",
            'intro'         => "Lo que entregamos con la llave — nada extravagante, solo lo que hace que una estancia sea sencilla.",
            'display'       => 'numbered-grid',
            'items' => [
                'Ropa de cama premium (sábanas 100% percal de algodón)',
                'Baño privado con productos de aseo bio',
                'Aire acondicionado reversible en cada habitación',
                'Wifi de alta velocidad gratuito',
                'Televisión de pantalla plana',
                'Piscina compartida 12 × 6 m (mayo a octubre)',
                'Jardín provenzal y terrazas a la sombra',
                'Aparcamiento privado gratuito',
                'Entrada autónoma (caja de llaves)',
                'Consejos y recomendaciones locales personalizados',
            ],
        ],
    ],

    8 => [
        'block_type' => 'tableau',
        'title_en' => 'Practical info',
        'title_es' => 'Información práctica',
        'en' => [
            'label_numeral' => '07',
            'label_text'    => 'Practical info',
            'heading'       => "Everything you need to *know*.",
            'intro'         => "Dates, capacity, arrival times, what's included — the essentials at a glance.",
            'surface'       => 'stone',
            'anchor_id'     => 'infos',
            'display'       => 'key-value',
            'rows' => [
                ['key' => 'Period',        'value' => '**From September to June**'],
                ['key' => 'Arrival',       'value' => 'From 5 pm'],
                ['key' => 'Departure',     'value' => 'Before 11 am'],
                ['key' => 'Minimum stay',  'value' => '**2 nights** (high season: 3 nights)'],
                ['key' => 'Capacity',      'value' => '**1 to 5 guests** (connecting suite)'],
                ['key' => 'Breakfast',     'value' => 'Included · served 7:30 → 10 am'],
                ['key' => 'Pool',          'value' => 'Shared · 12 × 6 m · May to October'],
                ['key' => 'Animals',       'value' => 'Not accepted'],
                ['key' => 'Smoking',       'value' => 'Non-smoking'],
            ],
        ],
        'es' => [
            'label_numeral' => '07',
            'label_text'    => 'Información práctica',
            'heading'       => "Todo lo que hay que *saber*.",
            'intro'         => "Fechas, capacidad, horarios, lo que está incluido — lo esencial de un vistazo.",
            'surface'       => 'stone',
            'anchor_id'     => 'infos',
            'display'       => 'key-value',
            'rows' => [
                ['key' => 'Período',         'value' => '**De septiembre a junio**'],
                ['key' => 'Llegada',         'value' => 'A partir de las 17 h'],
                ['key' => 'Salida',          'value' => 'Antes de las 11 h'],
                ['key' => 'Estancia mínima', 'value' => '**2 noches** (temporada alta: 3 noches)'],
                ['key' => 'Capacidad',       'value' => '**1 a 5 personas** (suite comunicante)'],
                ['key' => 'Desayuno',        'value' => 'Incluido · servido de 7:30 a 10 h'],
                ['key' => 'Piscina',         'value' => 'Compartida · 12 × 6 m · mayo a octubre'],
                ['key' => 'Animales',        'value' => 'No admitidos'],
                ['key' => 'Fumar',           'value' => 'No fumadores'],
            ],
        ],
    ],

    9 => [
        'block_type' => 'faq',
        'title_en' => 'FAQ',
        'title_es' => 'Preguntas frecuentes',
        'en' => [
            'label_numeral' => '08',
            'label_text'    => 'Frequently asked',
            'heading'       => "The *questions*\nthat keep coming up.",
            'page_slug'     => 'chambres-d-hotes',
            'first_open'    => true,
        ],
        'es' => [
            'label_numeral' => '08',
            'label_text'    => 'Preguntas frecuentes',
            'heading'       => "Las *preguntas*\nque más se repiten.",
            'page_slug'     => 'chambres-d-hotes',
            'first_open'    => true,
        ],
    ],
];

foreach (['en', 'es'] as $lang) {
    Database::query("DELETE FROM vp_sections WHERE page_slug = 'chambres-d-hotes' AND lang = ?", [$lang]);
    echo "  → DELETE chambres-d-hotes/$lang\n";

    foreach ($blocks as $position => $block) {
        $titleKey = 'title_' . $lang;
        Database::insert('vp_sections', [
            'page_slug'  => 'chambres-d-hotes',
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

echo "Done.\n";
