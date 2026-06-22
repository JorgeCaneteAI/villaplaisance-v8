<?php
declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\BaseController;

/**
 * Page publique /avis, liste de tous les avis clients (vp_reviews status=published)
 * avec filtres par offre + JSON-LD Review/AggregateRating pour le SEO.
 */
class AvisController extends BaseController
{
    public function index(): void
    {
        $lang = \LangService::get();
        $offerFilter = $_GET['offer'] ?? 'all';
        if (!in_array($offerFilter, ['all', 'bb', 'villa', 'both'], true)) $offerFilter = 'all';

        $platformFilter = $_GET['platform'] ?? 'all';
        if (!in_array($platformFilter, ['all', 'airbnb', 'booking', 'google', 'direct'], true)) $platformFilter = 'all';

        $sort = $_GET['sort'] ?? 'recent';
        if (!in_array($sort, ['recent', 'old', 'rating_high', 'rating_low'], true)) $sort = 'recent';

        // Tri demandé. Les valeurs sont en liste blanche ci-dessus, donc leur
        // interpolation dans ORDER BY est sûre (aucune entrée utilisateur brute).
        // La note est normalisée (Booking sur 10 ramené sur 5) pour un classement juste.
        $ratingExpr = "(CASE WHEN platform = 'booking' AND rating > 5 THEN rating / 2 ELSE rating END)";
        $orderBy = match ($sort) {
            'old'         => 'review_date ASC, id ASC',
            'rating_high' => "{$ratingExpr} DESC, review_date DESC, id DESC",
            'rating_low'  => "{$ratingExpr} ASC, review_date DESC, id DESC",
            default       => 'review_date DESC, id DESC',
        };

        // Avis published, filtrés par offre et par plateforme côté SQL si demandé
        $sql = "SELECT * FROM vp_reviews WHERE status = 'published'";
        $params = [];
        if ($offerFilter !== 'all') {
            $sql .= " AND offer IN (?, 'both')";
            $params[] = $offerFilter;
        }
        if ($platformFilter !== 'all') {
            $sql .= " AND platform = ?";
            $params[] = $platformFilter;
        }
        $sql .= " ORDER BY {$orderBy}";

        $reviews = [];
        try { $reviews = \Database::fetchAll($sql, $params); } catch (\Throwable) {}

        // Stats globales (sur tous les avis published, indépendamment du filtre)
        $stats = [
            'total' => 0,
            'avg' => 0.0,
            'by_offer' => ['bb' => 0, 'villa' => 0, 'both' => 0],
            'by_platform' => [],
        ];
        try {
            $allPublished = \Database::fetchAll("SELECT offer, platform, rating FROM vp_reviews WHERE status = 'published'");
            $stats['total'] = count($allPublished);
            if ($stats['total'] > 0) {
                $sum = 0.0;
                $count = 0;
                foreach ($allPublished as $r) {
                    $rating = (float)$r['rating'];
                    // Normalise booking (sur 10) → 5
                    if ($r['platform'] === 'booking' && $rating > 5) $rating = $rating / 2;
                    $sum += $rating;
                    $count++;
                    $stats['by_offer'][$r['offer']] = ($stats['by_offer'][$r['offer']] ?? 0) + 1;
                    $stats['by_platform'][$r['platform']] = ($stats['by_platform'][$r['platform']] ?? 0) + 1;
                }
                $stats['avg'] = round($sum / $count, 1);
            }
        } catch (\Throwable) {}

        // SEO (vp_pages avis seedé en 021).
        // Signature SeoService::forPage(slug, lang, fallbackTitle, fallbackDesc), paramètres positionnels.
        $seo = \SeoService::forPage(
            'avis',
            $lang,
            'Avis clients, Villa Plaisance',
            'Découvrez les témoignages de nos hôtes, chambres d\'hôtes et villa entière à Bédarrides en Provence.'
        );

        // JSON-LD : LodgingBusiness (@id) + Breadcrumb + Reviews référencées.
        // L'aggregateRating vit DANS l'entité principale (un AggregateRating
        // top-level séparé ferait doublon avec le fallback de SeoService) et
        // chaque Review pointe l'entité par @id au lieu de la redéclarer.
        $base = APP_ENV === 'production' ? 'https://villaplaisance.fr' : APP_URL;
        $entityId = $base . '/#villa-plaisance';

        $lodging = \SeoService::lodgingBusinessJsonLd();
        $lodging['@id'] = $entityId;
        if ($stats['total'] > 0) {
            $lodging['aggregateRating'] = \SeoService::aggregateRatingJsonLd(
                (float)$stats['avg'],
                (int)$stats['total']
            );
        }

        $jsonLd = [
            $lodging,
            \SeoService::breadcrumbJsonLd([
                ['name' => t('nav.home'), 'url' => APP_URL . '/'],
                ['name' => 'Avis clients'],
            ]),
        ];
        if ($stats['total'] > 0) {
            // Échantillon des 10 meilleurs/plus récents avis en Review[]
            $sampleSize = min(10, count($reviews));
            for ($i = 0; $i < $sampleSize; $i++) {
                $r = $reviews[$i];
                $rating = (float)$r['rating'];
                if ($r['platform'] === 'booking' && $rating > 5) $rating = $rating / 2;
                $review = [
                    '@context' => 'https://schema.org',
                    '@type' => 'Review',
                    // @id + type + name : dédoublonné pour Google, résoluble
                    // même par les parseurs qui ne suivent pas les références.
                    'itemReviewed' => [
                        '@type' => 'LodgingBusiness',
                        '@id' => $entityId,
                        'name' => 'Villa Plaisance',
                    ],
                    'author' => ['@type' => 'Person', 'name' => $r['author']],
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => $rating,
                        'bestRating' => 5,
                        'worstRating' => 1,
                    ],
                    'reviewBody' => $r['content'],
                ];
                // Omise si absente ou invalide plutôt qu'émise null (même
                // règle que les breadcrumbs : Google refuse les propriétés
                // null ; strtotime false → TypeError sous strict_types).
                $reviewTs = !empty($r['review_date']) ? strtotime((string)$r['review_date']) : false;
                if ($reviewTs !== false) {
                    $review['datePublished'] = date('Y-m-d', $reviewTs);
                }
                $jsonLd[] = $review;
            }
        }

        $this->render('front/avis', compact('seo', 'reviews', 'stats', 'offerFilter', 'platformFilter', 'sort', 'jsonLd', 'lang'), 'front-proto');
    }
}
