<?php
declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class VillaController extends BaseController
{
    public function index(): void
    {
        $lang = \LangService::get();
        $seo = \SeoService::forPage('location-villa-provence', $lang,
            'Location villa Provence — Villa Plaisance, Bédarrides',
            '4 chambres, piscine privée 12×6m, jardin provençal. Villa entière en exclusivité juillet-août, jusqu\'à 10 personnes, entre Avignon et Orange.'
        );

        $jsonLd = [
            \SeoService::vacationRentalJsonLd(),
            \SeoService::breadcrumbJsonLd([
                ['name' => t('nav.home'), 'url' => APP_URL . '/'],
                ['name' => t('nav.villa')],
            ]),
        ];

        $faqs = [];
        try {
            $faqs = \Database::fetchAll(
                "SELECT question, answer FROM vp_faq WHERE page_slug = 'location-villa-provence' AND lang = ? AND active = 1 ORDER BY position",
                [$lang]
            );
        } catch (\Throwable) {}
        if (!empty($faqs)) {
            $jsonLd[] = \SeoService::faqJsonLd($faqs);
        }

        $featuredReviews = [];
        try {
            $featuredReviews = \Database::fetchAll(
                "SELECT author, origin, content, platform, offer, rating
                 FROM vp_reviews
                 WHERE status = 'published' AND offer IN ('villa', 'both') AND home_carousel = 1
                 ORDER BY review_date DESC
                 LIMIT 4"
            );
        } catch (\Throwable) {}

        $this->render('front/villa', compact('seo', 'jsonLd', 'lang', 'faqs', 'featuredReviews'));
    }
}
