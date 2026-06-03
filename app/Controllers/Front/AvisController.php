<?php
declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\BaseController;

/**
 * Page publique /avis — liste de tous les avis clients (vp_reviews status=published)
 * avec filtres par offre + JSON-LD Review/AggregateRating pour le SEO.
 */
class AvisController extends BaseController
{
    public function index(): void
    {
        $lang = \LangService::get();
        $offerFilter = $_GET['offer'] ?? 'all';
        if (!in_array($offerFilter, ['all', 'bb', 'villa', 'both'], true)) $offerFilter = 'all';

        // Tous les avis published, filtrés par offre côté SQL si demandé
        $sql = "SELECT * FROM vp_reviews WHERE status = 'published'";
        $params = [];
        if ($offerFilter !== 'all') {
            $sql .= " AND offer IN (?, 'both')";
            $params[] = $offerFilter;
        }
        $sql .= " ORDER BY featured DESC, review_date DESC, id DESC";

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
        // Signature SeoService::forPage(slug, lang, fallbackTitle, fallbackDesc) — paramètres positionnels.
        $seo = \SeoService::forPage(
            'avis',
            $lang,
            'Avis clients, Villa Plaisance',
            'Découvrez les témoignages de nos hôtes, chambres d\'hôtes et villa entière à Bédarrides en Provence.'
        );

        // JSON-LD : LodgingBusiness + AggregateRating + array de Reviews
        $jsonLd = [\SeoService::lodgingBusinessJsonLd()];
        if ($stats['total'] > 0) {
            $jsonLd[] = [
                '@context' => 'https://schema.org',
                '@type' => 'AggregateRating',
                'itemReviewed' => [
                    '@type' => 'LodgingBusiness',
                    'name' => 'Villa Plaisance',
                ],
                'ratingValue' => $stats['avg'],
                'reviewCount' => $stats['total'],
                'bestRating' => 5,
                'worstRating' => 1,
            ];
            // Échantillon des 10 meilleurs/plus récents avis en Review[]
            $sampleSize = min(10, count($reviews));
            for ($i = 0; $i < $sampleSize; $i++) {
                $r = $reviews[$i];
                $rating = (float)$r['rating'];
                if ($r['platform'] === 'booking' && $rating > 5) $rating = $rating / 2;
                $jsonLd[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'Review',
                    'itemReviewed' => ['@type' => 'LodgingBusiness', 'name' => 'Villa Plaisance'],
                    'author' => ['@type' => 'Person', 'name' => $r['author']],
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => $rating,
                        'bestRating' => 5,
                        'worstRating' => 1,
                    ],
                    'reviewBody' => $r['content'],
                    'datePublished' => $r['review_date'] ?? null,
                ];
            }
        }

        $this->render('front/avis', compact('seo', 'reviews', 'stats', 'offerFilter', 'jsonLd', 'lang'), 'front-proto');
    }
}
