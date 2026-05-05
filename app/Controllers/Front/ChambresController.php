<?php
declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class ChambresController extends BaseController
{
    public function index(): void
    {
        $lang = \LangService::get();
        $seo = \SeoService::forPage('chambres-d-hotes', $lang,
            'Chambres d\'hôtes à Bédarrides — Villa Plaisance, Provence',
            'Deux chambres climatisées avec petit-déjeuner maison et piscine partagée. De septembre à juin, entre Avignon et Orange.'
        );

        $jsonLd = [
            \SeoService::bedAndBreakfastJsonLd(),
            \SeoService::breadcrumbJsonLd([
                ['name' => t('nav.home'), 'url' => APP_URL . '/'],
                ['name' => t('nav.chambres')],
            ]),
        ];

        $faqs = [];
        try {
            $faqs = \Database::fetchAll(
                "SELECT question, answer FROM vp_faq WHERE page_slug = 'chambres-d-hotes' AND lang = ? AND active = 1 ORDER BY position",
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
                 WHERE status = 'published' AND offer IN ('bb', 'both') AND home_carousel = 1
                 ORDER BY review_date DESC
                 LIMIT 4"
            );
        } catch (\Throwable) {}

        $this->render('front/chambres', compact('seo', 'jsonLd', 'lang', 'faqs', 'featuredReviews'));
    }
}
