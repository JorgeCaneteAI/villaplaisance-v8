<?php
declare(strict_types=1);

class SeoService
{
    public static function forPage(string $slug, string $lang, string $fallbackTitle = '', string $fallbackDesc = ''): array
    {
        // Try to get SEO data from database
        $seo = null;
        try {
            $seo = Database::fetchOne(
                "SELECT * FROM vp_pages WHERE slug = ? AND lang = ?",
                [$slug, $lang]
            );
        } catch (\Throwable $e) {
            // DB not available yet, use fallbacks
        }

        $title = $seo['meta_title'] ?? $fallbackTitle;
        $description = $seo['meta_desc'] ?? $fallbackDesc;
        $canonical = self::canonical($slug, $lang);

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og' => [
                'title' => $seo['og_title'] ?? $title,
                'description' => $seo['og_desc'] ?? $description,
                'image' => $seo['og_image'] ?? APP_URL . '/assets/img/og-default.jpg',
                'url' => $canonical,
                'type' => 'website',
                'locale' => self::locale($lang),
            ],
            'hreflang' => self::hreflang($slug),
            'jsonld' => $seo['jsonld'] ?? null,
        ];
    }

    public static function canonical(string $slug, string $lang): string
    {
        $base = APP_ENV === 'production' ? 'https://villaplaisance.fr' : APP_URL;
        if ($slug === 'accueil' && $lang === 'fr') {
            return $base . '/';
        }
        return $base . LangService::url($slug, $lang);
    }

    public static function hreflang(string $page): array
    {
        $links = [];
        $base = APP_ENV === 'production' ? 'https://villaplaisance.fr' : APP_URL;
        foreach (SUPPORTED_LANGS as $lang) {
            $url = $base . LangService::url($page, $lang);
            $links[] = ['lang' => $lang, 'url' => $url];
        }
        // x-default = fr
        $links[] = ['lang' => 'x-default', 'url' => $base . LangService::url($page, 'fr')];
        return $links;
    }

    public static function locale(string $lang): string
    {
        // OpenGraph locale codes. Cible EN = international (US format), pas UK
        // spécifique (le public anglophone de Villa Plaisance touche US/CA/AU/IE
        // autant que UK). ES = ES (Espagne), public hispanique principal.
        return match ($lang) {
            'fr' => 'fr_FR',
            'en' => 'en_US',
            'es' => 'es_ES',
            default => 'fr_FR',
        };
    }

    public static function lodgingBusinessJsonLd(): array
    {
        $base = APP_ENV === 'production' ? 'https://villaplaisance.fr' : APP_URL;
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LodgingBusiness',
            // Identifiant stable pour Google (lève le critique "identifier manquant"
            // remonté par l'inspection GSC sur VacationRental/BedAndBreakfast).
            'identifier' => $base . '#villa-plaisance-bedarrides',
            'name' => 'Villa Plaisance',
            'description' => 'Chambres d\'hôtes et villa de charme à Bédarrides, au cœur du Triangle d\'Or provençal.',
            'url' => $base,
            // Format E.164 (international) recommandé par Schema.org.
            'telephone' => '+33490330049',
            'email' => 'contact@villaplaisance.fr',
            // L'adresse rue n'est pas publiée (transmise après confirmation de
            // séjour, cf. /contact). On expose commune + CP + région + pays,
            // suffisant pour les fiches locales Google et les LLMs.
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Bédarrides',
                'postalCode' => '84370',
                'addressRegion' => 'Vaucluse',
                'addressCountry' => 'FR',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                // String pour éviter la sérialisation IEEE 754 verbeuse de
                // serialize_precision sur certaines configs PHP (o2switch).
                // Schema.org accepte Number OR Text pour GeoCoordinates.
                'latitude' => '44.0386',
                'longitude' => '4.8972',
            ],
            // Array de 8+ images (Google demande >= 8 pour la fiche hébergement
            // riche). On pioche dans les uploads existants : façade + piscine +
            // chambres + intérieurs + petit-déj. Note absolue.
            'image' => [
                $base . '/uploads/hero-piscine.webp',
                $base . '/uploads/villa-plaisance-chambre-verte-01.webp',
                $base . '/uploads/villa-plaisance-chambre-bleue-01.webp',
                $base . '/uploads/villa-plaisance-chambre-arche-01.webp',
                $base . '/uploads/villa-plaisance-chambre-annees-70-01.webp',
                $base . '/uploads/villa-plaisance-salon-salle-a-manger-01.webp',
                $base . '/uploads/villa-plaisance-cuisine-equipee-01.webp',
                $base . '/uploads/villa-plaisance-petit-dejeuner-brioche-01.webp',
            ],
            'priceRange' => '€€',
            // Pas de starRating : c'est un classement officiel (étoiles hôtelières),
            // pas une note d'avis. L'auto-déclarer expose à un warning GSC.
            // Note moyenne agrégée depuis Google Business Profile (5.0 / 11 avis).
            // À synchroniser quand le nombre d'avis évolue.
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => '5.0',
                'reviewCount' => 11,
                'bestRating' => '5',
                'worstRating' => '1',
            ],
        ];
    }

    /**
     * Liste des Room enrichies pour containsPlace.
     *
     * Google n'accepte QUE des Room (pas de Place générique) dans le containsPlace
     * d'un VacationRental/BedAndBreakfast. Chaque Room doit déclarer au minimum
     * occupancy (critique) + bed/amenityFeature/numberOfBedrooms/
     * numberOfBathroomsTotal (warnings) pour passer l'inspection GSC.
     *
     * Les espaces communs (cuisine, salon, piscine) sont retirés car non valides
     * comme containsPlace et déjà couverts par amenityFeature au niveau parent.
     */
    public static function containsPlaces(): array
    {
        $mkRoom = static function (string $name, string $desc, string $bedType, int $numBeds, int $maxOccupancy, array $features): array {
            return [
                '@type' => 'Room',
                'name' => $name,
                'description' => $desc,
                'occupancy' => [
                    '@type' => 'QuantitativeValue',
                    // Google attend `value` (scalaire) plutôt que `maxValue`
                    // (range) pour la capacité d'une chambre. On garde aussi
                    // maxValue pour la rétrocompatibilité Schema.org.
                    'value' => $maxOccupancy,
                    'maxValue' => $maxOccupancy,
                ],
                'bed' => [
                    '@type' => 'BedDetails',
                    'typeOfBed' => $bedType,
                    'numberOfBeds' => $numBeds,
                ],
                'numberOfBedrooms' => 1,
                'numberOfBathroomsTotal' => 1,
                'amenityFeature' => array_map(static fn($f) => [
                    '@type' => 'LocationFeatureSpecification',
                    'name' => $f,
                    'value' => true,
                ], $features),
            ];
        };
        return [
            $mkRoom(
                'Chambre Verte',
                'Lit 160×200, vue jardin, climatisation, salle de bain privative.',
                'Queen', 1, 2,
                ['Climatisation', 'Vue jardin', 'Salle de bain privative', 'TV', 'WiFi']
            ),
            $mkRoom(
                'Chambre Bleue',
                'Deux lits 90×200 jumelables en 180, bibliothèque 300 livres, climatisation.',
                'Twin', 2, 3,
                ['Climatisation', 'Bibliothèque 300 livres', 'Clic-clac', 'WiFi']
            ),
            $mkRoom(
                'Chambre Arche',
                'Lit 140×180 sous arche bleu nuit, bibliothèques sol-plafond, accès jardin.',
                'Double', 1, 2,
                ['Climatisation', 'Accès jardin', 'Bibliothèques sol-plafond', 'WiFi']
            ),
            $mkRoom(
                'Chambre 70',
                'Grand lit double, mobilier vintage années 70, accès direct jardin.',
                'Double', 1, 2,
                ['Climatisation', 'Accès direct jardin', 'Mobilier vintage', 'WiFi']
            ),
        ];
    }

    public static function bedAndBreakfastJsonLd(): array
    {
        $base = self::lodgingBusinessJsonLd();
        $base['@type'] = 'BedAndBreakfast';
        // additionalType retiré : 'schema.org/Accommodation' refusé comme
        // énumération invalide par GSC.
        // Points d'ancrage croisés pour confirmer l'entité à Google et aux
        // LLMs. Airbnb (chambres) + Booking (chambres) + Google Business.
        $base['sameAs'] = [
            'https://www.airbnb.fr/h/villaplaisance',
            'https://www.booking.com/hotel/fr/villa-plaisance-bedarrides',
            'https://maps.app.goo.gl/awAia9UD5BMeiXF26',
        ];
        // BedAndBreakfast = uniquement les 2 chambres B&B (Verte + Bleue),
        // chacune enrichie d'occupancy, bed, amenityFeature, etc.
        $base['containsPlace'] = array_slice(self::containsPlaces(), 0, 2);
        $base['amenityFeature'] = [
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Piscine partagée', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Petit-déjeuner inclus', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Climatisation', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'WiFi gratuit', 'value' => true],
        ];
        return $base;
    }

    public static function vacationRentalJsonLd(): array
    {
        $base = self::lodgingBusinessJsonLd();
        $base['@type'] = 'VacationRental';
        // additionalType retiré : 'schema.org/Accommodation' refusé comme
        // énumération invalide par GSC.
        // Airbnb (villa entière) + Google Business (même fiche que B&B).
        // Pas de Booking : ne commercialise que les chambres.
        $base['sameAs'] = [
            'https://www.airbnb.fr/h/villaplaisance-bedarrides',
            'https://maps.app.goo.gl/awAia9UD5BMeiXF26',
        ];
        // VacationRental = les 4 chambres villa (Verte, Bleue, Arche, 70),
        // chacune enrichie. Les espaces communs (cuisine, salon, piscine)
        // ne peuvent pas être dans containsPlace (Google les rejette comme
        // type invalide), ils restent couverts par amenityFeature ci-dessous.
        $base['containsPlace'] = self::containsPlaces();
        $base['numberOfRooms'] = 4;
        $base['occupancy'] = [
            '@type' => 'QuantitativeValue',
            'maxValue' => 10,
        ];
        $base['amenityFeature'] = [
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Piscine privée 12×6m', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Cuisine équipée', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Jardin provençal', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Climatisation', 'value' => true],
        ];
        return $base;
    }

    public static function faqJsonLd(array $faqs): array
    {
        $items = [];
        foreach ($faqs as $faq) {
            $items[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $items,
        ];
    }

    public static function blogPostingJsonLd(array $article): array
    {
        $base = APP_ENV === 'production' ? 'https://villaplaisance.fr' : APP_URL;

        // Image en URL absolue obligatoire (un nom de fichier nu est
        // irrésoluble par Google → rich results article inéligibles).
        $image = !empty($article['cover_image'])
            ? ImageService::url($article['cover_image'])
            : '/assets/img/og-default.jpg';
        if (!str_starts_with($image, 'http')) {
            $image = $base . $image;
        }

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $article['title'],
            'description' => $article['excerpt'] ?? '',
            // Auteur humain identifié plutôt qu'Organization : signal
            // E-E-A-T, et les moteurs IA citent davantage les contenus
            // avec auteur nommé. L'URL ancre l'entité sur /votre-hote.
            'author' => [
                '@type' => 'Person',
                'name' => 'Jorge Cañete',
                'url' => $base . '/votre-hote',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Villa Plaisance',
                'url' => $base,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $base . '/assets/img/logo-proto.png',
                ],
            ],
            'image' => $image,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $base . '/journal/' . ($article['slug'] ?? ''),
            ],
        ];

        // Dates en ISO 8601 (le format SQL "Y-m-d H:i:s" est rejeté).
        // Omises proprement si absentes plutôt qu'émises vides.
        $published = self::isoDate($article['published_at'] ?? $article['created_at'] ?? null);
        $modified = self::isoDate($article['updated_at'] ?? $article['created_at'] ?? null);
        if ($published !== null) {
            $jsonLd['datePublished'] = $published;
        }
        if ($modified !== null) {
            $jsonLd['dateModified'] = $modified;
        }

        return $jsonLd;
    }

    private static function isoDate(?string $sqlDate): ?string
    {
        if ($sqlDate === null || $sqlDate === '') {
            return null;
        }
        $ts = strtotime($sqlDate);
        return $ts === false ? null : date('c', $ts);
    }

    public static function breadcrumbJsonLd(array $items): array
    {
        $list = [];
        foreach ($items as $i => $item) {
            $listItem = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
            ];
            // Google accepte d'omettre `item` sur le dernier élément (page
            // courante), mais refuse `item: null` (warning GSC). On omet
            // proprement quand l'URL n'est pas fournie.
            if (!empty($item['url'])) {
                $listItem['item'] = $item['url'];
            }
            $list[] = $listItem;
        }
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    public static function aggregateRatingJsonLd(float $rating, int $count): array
    {
        // Clamp dans [0;5] pour ne jamais dépasser bestRating (rejeté par Google).
        $rating = max(0.0, min(5.0, $rating));
        // Format anglais (point), Schema.org n'accepte pas la virgule décimale FR.
        return [
            '@type' => 'AggregateRating',
            'ratingValue' => number_format($rating, 1, '.', ''),
            'reviewCount' => $count,
            'bestRating' => '5',
            'worstRating' => '1',
        ];
    }
}
