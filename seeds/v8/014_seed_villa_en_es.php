<?php
declare(strict_types=1);

/**
 * V8 — Traductions EN + ES de la page location-villa-provence.
 *
 * Blocs : 1 (hero), 2 (stats), 3 (cartes villa — chambres), 4 (interior),
 *         5 (piscine), 6 (tableau espaces), 7 (tableau infos pratiques), 8 (faq).
 *
 * Idempotent : DELETE EN+ES avant INSERT. Ne touche pas FR (seeds 005, 009).
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — Traductions Villa EN + ES ===\n\n";

$mediaId = function (string $filename): ?int {
    $row = Database::fetchOne("SELECT id FROM vp_media WHERE filename = ? LIMIT 1", [$filename]);
    return $row ? (int)$row['id'] : null;
};
$cuisineImg = $mediaId('villa-plaisance-cuisine-equipee-01.webp');
$salonImg   = $mediaId('villa-plaisance-salon-salle-a-manger-01.webp');
$piscineImg = $mediaId('villa-plaisance-piscine-privee-05.webp');

$blocks = [
    1 => [
        'block_type' => 'hero',
        'title_en' => 'Hero — whole villa',
        'title_es' => 'Hero — casa entera',
        'en' => [
            'title' => "The *whole* villa,\nyours alone.",
            'lede'  => "In July and August, Villa Plaisance opens its doors as a whole-house rental — four bedrooms, ten guests, an exclusive private pool, fitted kitchen and terraces facing the vines. Off-season, the whole house can be rented on request.",
            'tags'  => ["02 · Whole villa · July – August", "Reply within the day"],
            'ctas'  => [
                ['label' => 'Enquire about a week', 'url' => '/contact', 'style' => 'primary'],
                ['label' => 'Practical info',       'url' => '#infos',   'style' => 'ghost'],
            ],
        ],
        'es' => [
            'title' => "La villa *entera*,\nen total autonomía.",
            'lede'  => "En julio y agosto, Villa Plaisance abre sus puertas en alquiler completo — cuatro habitaciones, diez personas, piscina privada exclusiva, cocina equipada y terrazas frente a las viñas. Fuera de temporada, la casa entera puede alquilarse bajo solicitud.",
            'tags'  => ["02 · Casa entera · Julio – agosto", "Respuesta en el día"],
            'ctas'  => [
                ['label' => 'Solicitar una semana',  'url' => '/contact', 'style' => 'primary'],
                ['label' => 'Información práctica',  'url' => '#infos',   'style' => 'ghost'],
            ],
        ],
    ],

    2 => [
        'block_type' => 'stats',
        'title_en' => 'Key stats strip',
        'title_es' => 'Cifras clave',
        'en' => [
            'display' => 'strip',
            'items' => [
                ['label' => 'Capacity',      'value' => '10 guests'],
                ['label' => 'Bedrooms',      'value' => '4'],
                ['label' => 'Private pool',  'value' => '12 × 6 m'],
                ['label' => 'Minimum stay',  'value' => '4 nights'],
            ],
        ],
        'es' => [
            'display' => 'strip',
            'items' => [
                ['label' => 'Capacidad',       'value' => '10 pers.'],
                ['label' => 'Habitaciones',    'value' => '4'],
                ['label' => 'Piscina privada', 'value' => '12 × 6 m'],
                ['label' => 'Estancia mínima', 'value' => '4 noches'],
            ],
        ],
    ],

    3 => [
        'block_type' => 'cartes',
        'title_en' => 'Cards — 4 villa bedrooms',
        'title_es' => 'Tarjetas — 4 habitaciones',
        'en' => [
            'offer' => 'villa',
            'heading'       => "Four bedrooms,\nfour *worlds*.",
            'intro'         => "Each room has a personality of its own — books, an arch, the garden, the seventies. None of them lacks for shade or light.",
            'label_numeral' => '01',
            'label_text'    => 'The bedrooms',
        ],
        'es' => [
            'offer' => 'villa',
            'heading'       => "Cuatro habitaciones,\ncuatro *universos*.",
            'intro'         => "Cada habitación tiene su propia personalidad — los libros, un arco, el jardín, los setenta. Ninguna carece de sombra ni de luz.",
            'label_numeral' => '01',
            'label_text'    => 'Las habitaciones',
        ],
    ],

    4 => [
        'block_type' => 'interior',
        'title_en' => 'Interior — kitchen + lounge',
        'title_es' => 'Interior — cocina + salón',
        'en' => [
            'label_numeral' => '02',
            'label_text'    => 'Inside',
            'heading'       => "A *kitchen*,\na *lounge*, a long table.",
            'surface'       => 'stone',
            'items' => [
                [
                    'image_id' => $cuisineImg,
                    'kicker'   => 'KITCHEN · AN ALL-IN-ONE SPACE',
                    'title'    => 'Fully equipped',
                    'text'     => "Gas range, dishwasher, XXL fridge, oven, microwave — and everything you need to cook for ten.",
                ],
                [
                    'image_id' => $salonImg,
                    'kicker'   => 'LIVING · DINING · CONVIVIALITY, SIMPLY',
                    'title'    => 'Air-conditioned, light, long',
                    'text'     => "A large living/dining room — air-conditioned, easy to live in. A long table that ten people can sit at without anyone elbowing anyone else.",
                ],
            ],
        ],
        'es' => [
            'label_numeral' => '02',
            'label_text'    => 'El interior',
            'heading'       => "Una *cocina*,\nun *salón*, una mesa larga.",
            'surface'       => 'stone',
            'items' => [
                [
                    'image_id' => $cuisineImg,
                    'kicker'   => 'COCINA · UN ESPACIO TODO EN UNO',
                    'title'    => 'Totalmente equipada',
                    'text'     => "Cocina de gas, lavavajillas, frigorífico XXL, horno, microondas — y todo lo necesario para cocinar para diez.",
                ],
                [
                    'image_id' => $salonImg,
                    'kicker'   => 'SALÓN · COMEDOR · CONVIVIALIDAD CON SENCILLEZ',
                    'title'    => 'Climatizado, claro, largo',
                    'text'     => "Un gran salón y comedor climatizado, fácil de vivir. Una mesa larga donde diez personas caben sin codearse.",
                ],
            ],
        ],
    ],

    5 => [
        'block_type' => 'piscine',
        'title_en' => 'Private pool',
        'title_es' => 'Piscina privada',
        'en' => [
            'label_numeral' => '03',
            'label_text'    => 'The pool',
            'heading'       => "Private pool, *12 × 6*.\nYours alone.",
            'lede'          => "Exclusively yours, 24/7. No other family or tenant has access during your stay.",
            'image_id'      => $piscineImg,
            'text'          => "Sunbeds, parasols, garden table, an outdoor lounge and a solar shower — and the option to have the pool heated if you'd like an early-July dip.",
            'note'          => "The pool is fenced, as required by law. Children stay close.",
            'features' => [
                '12 × 6 m, fenced',
                'Exclusive · 24/7',
                'Sunbeds & parasols',
                'Garden table & outdoor lounge',
                'Solar shower',
                'Heating on request',
            ],
            'anchor_id'     => 'piscine',
        ],
        'es' => [
            'label_numeral' => '03',
            'label_text'    => 'La piscina',
            'heading'       => "Piscina privada, *12 × 6*.\nPara vosotros solos.",
            'lede'          => "Exclusivamente reservada a vuestro grupo, 24/7. Ninguna otra familia o inquilino tiene acceso durante vuestra estancia.",
            'image_id'      => $piscineImg,
            'text'          => "Tumbonas, parasoles, mesa de jardín, salón exterior y ducha solar — y la opción de calentar la piscina si preferís bañaros desde principios de julio.",
            'note'          => "La piscina está vallada, conforme a la normativa. Los niños permanecen al alcance de la vista.",
            'features' => [
                '12 × 6 m, vallada',
                'Exclusiva · 24/7',
                'Tumbonas y parasoles',
                'Mesa de jardín y salón exterior',
                'Ducha solar',
                'Calefacción bajo solicitud',
            ],
            'anchor_id'     => 'piscine',
        ],
    ],

    6 => [
        'block_type' => 'tableau',
        'title_en' => 'The spaces — house inventory',
        'title_es' => 'Los espacios — inventario',
        'en' => [
            'label_numeral' => '04',
            'label_text'    => 'The spaces',
            'heading'       => "Everything the *house* contains.",
            'intro'         => "The complete inventory of the house — bedrooms, bathrooms, kitchen, terrace, garden, parking. So you can plan a long week without surprises.",
            'surface'       => 'stone',
            'display'       => 'key-value-two-cols',
            'rows' => [
                ['key' => 'Master suite',    'value' => 'King bed 180×200 + private bathroom + dressing + garden view'],
                ['key' => 'Blue bedroom',    'value' => 'Queen double bed 160×200'],
                ['key' => 'Arch bedroom',    'value' => 'Double bed 160×200'],
                ['key' => '70s bedroom',     'value' => '2 single beds 90×200, joinable'],
                ['key' => 'Bathrooms',       'value' => '2 full bathrooms + 3 independent WCs'],
                ['key' => 'Living / dining', 'value' => 'Large air-conditioned living and dining room'],
                ['key' => 'Kitchen',         'value' => 'Gas range, dishwasher, XXL fridge, oven, microwave'],
                ['key' => 'Covered terrace', 'value' => '40 m² with garden lounge — seats 12'],
                ['key' => 'Garden',          'value' => 'Provençal — olive trees, charcoal BBQ, pétanque court'],
                ['key' => 'Laundry',         'value' => 'Washer and dryer'],
                ['key' => 'Connectivity',    'value' => 'High-speed fibre wifi + streaming TV in every living space'],
                ['key' => 'Parking',         'value' => 'Closed parking for 2 vehicles · cot & high chair on request'],
            ],
        ],
        'es' => [
            'label_numeral' => '04',
            'label_text'    => 'Los espacios',
            'heading'       => "Todo lo que contiene la *casa*.",
            'intro'         => "El inventario completo de la casa — habitaciones, baños, cocina, terraza, jardín, aparcamiento. Lo necesario para preparar una larga semana sin sorpresas.",
            'surface'       => 'stone',
            'display'       => 'key-value-two-cols',
            'rows' => [
                ['key' => 'Suite principal',     'value' => 'Cama king 180×200 + baño privado + vestidor + vista jardín'],
                ['key' => 'Habitación Azul',     'value' => 'Cama doble queen 160×200'],
                ['key' => 'Habitación Arco',     'value' => 'Cama doble 160×200'],
                ['key' => 'Habitación Años 70',  'value' => '2 camas individuales 90×200, unibles'],
                ['key' => 'Baños',               'value' => '2 baños completos + 3 WC independientes'],
                ['key' => 'Salón · Comedor',     'value' => 'Gran salón y comedor climatizado'],
                ['key' => 'Cocina',              'value' => 'Cocina de gas, lavavajillas, frigorífico XXL, horno, microondas'],
                ['key' => 'Terraza cubierta',    'value' => '40 m² con salón de jardín para 12 personas'],
                ['key' => 'Jardín',              'value' => 'Provenzal — olivos, BBQ de carbón, cancha de petanca'],
                ['key' => 'Lavandería',          'value' => 'Lavadora y secadora'],
                ['key' => 'Conectividad',        'value' => 'Wifi de fibra de alta velocidad + TV streaming en cada espacio de vida'],
                ['key' => 'Aparcamiento',        'value' => 'Aparcamiento cerrado para 2 vehículos · cuna y trona bajo solicitud'],
            ],
        ],
    ],

    7 => [
        'block_type' => 'tableau',
        'title_en' => 'Practical info',
        'title_es' => 'Información práctica',
        'en' => [
            'label_numeral' => '05',
            'label_text'    => 'Practical info',
            'heading'       => "Everything you need to *know*.",
            'intro'         => "Dates, capacity, arrival times, what's included — the essentials at a glance, before you write to us.",
            'anchor_id'     => 'infos',
            'display'       => 'key-value',
            'rows' => [
                ['key' => 'Period',         'value' => '**July & August**'],
                ['key' => 'Off-season',     'value' => 'On request, specific conditions'],
                ['key' => 'Arrival',        'value' => 'From 5 pm'],
                ['key' => 'Departure',      'value' => 'Before 10 am'],
                ['key' => 'Minimum stay',   'value' => '**4 nights**'],
                ['key' => 'Capacity',       'value' => '**10 guests** max · 4 bedrooms'],
                ['key' => 'Pool',           'value' => 'Exclusive private · 12 × 6 m · heating optional'],
                ['key' => 'Cleaning',       'value' => 'End-of-stay included · mid-stay optional'],
                ['key' => 'Animals',        'value' => 'Not accepted'],
                ['key' => 'Smoking',        'value' => 'Non-smoking'],
                ['key' => 'Linens',         'value' => 'Sheets and towels provided'],
            ],
        ],
        'es' => [
            'label_numeral' => '05',
            'label_text'    => 'Información práctica',
            'heading'       => "Todo lo que hay que *saber*.",
            'intro'         => "Fechas, capacidad, horarios, lo que está incluido — lo esencial de un vistazo, antes de escribirnos.",
            'anchor_id'     => 'infos',
            'display'       => 'key-value',
            'rows' => [
                ['key' => 'Período',           'value' => '**Julio & agosto**'],
                ['key' => 'Fuera de temporada','value' => 'Bajo solicitud, condiciones específicas'],
                ['key' => 'Llegada',           'value' => 'A partir de las 17 h'],
                ['key' => 'Salida',            'value' => 'Antes de las 10 h'],
                ['key' => 'Estancia mínima',   'value' => '**4 noches**'],
                ['key' => 'Capacidad',         'value' => '**10 personas** máx · 4 habitaciones'],
                ['key' => 'Piscina',           'value' => 'Privada exclusiva · 12 × 6 m · calefacción opcional'],
                ['key' => 'Limpieza',          'value' => 'Final de estancia incluida · intermedia opcional'],
                ['key' => 'Animales',          'value' => 'No admitidos'],
                ['key' => 'Fumar',             'value' => 'No fumadores'],
                ['key' => 'Ropa de cama',      'value' => 'Sábanas y toallas proporcionadas'],
            ],
        ],
    ],

    8 => [
        'block_type' => 'faq',
        'title_en' => 'FAQ',
        'title_es' => 'Preguntas frecuentes',
        'en' => [
            'label_numeral' => '06',
            'label_text'    => 'Frequently asked',
            'heading'       => "Whole villa,\nthe *questions* that come back.",
            'page_slug'     => 'location-villa-provence',
            'surface'       => 'stone',
            'first_open'    => false,
        ],
        'es' => [
            'label_numeral' => '06',
            'label_text'    => 'Preguntas frecuentes',
            'heading'       => "Casa entera,\nlas *preguntas* que se repiten.",
            'page_slug'     => 'location-villa-provence',
            'surface'       => 'stone',
            'first_open'    => false,
        ],
    ],
];

foreach (['en', 'es'] as $lang) {
    Database::query("DELETE FROM vp_sections WHERE page_slug = 'location-villa-provence' AND lang = ?", [$lang]);
    echo "  → DELETE location-villa-provence/$lang\n";

    foreach ($blocks as $position => $block) {
        $titleKey = 'title_' . $lang;
        Database::insert('vp_sections', [
            'page_slug'  => 'location-villa-provence',
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
