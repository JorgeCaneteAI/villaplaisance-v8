<?php
declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(): void
    {
        $lang = \LangService::get();

        $seo = \SeoService::forPage('accueil', $lang,
            'Villa Plaisance — Chambres d\'hôtes et villa de charme à Bédarrides, Provence',
            'Chambres d\'hôtes de septembre à juin, villa entière en juillet-août. Piscine privée, 4 chambres, entre Avignon et Orange. Bédarrides, Vaucluse.'
        );

        $jsonLd = [\SeoService::lodgingBusinessJsonLd()];

        $faqs = [];
        try {
            $faqs = \Database::fetchAll(
                "SELECT question, answer FROM vp_faq WHERE page_slug = 'accueil' AND lang = ? AND active = 1 ORDER BY position",
                [$lang]
            );
        } catch (\Throwable) {}
        if (!empty($faqs)) {
            $jsonLd[] = \SeoService::faqJsonLd($faqs);
        }

        $recentArticles = [];
        try {
            $recentArticles = \Database::fetchAll(
                "SELECT slug, title, excerpt, category, cover_image, published_at
                 FROM vp_articles
                 WHERE type = 'journal' AND status = 'published' AND lang = ?
                 ORDER BY published_at DESC
                 LIMIT 3",
                [$lang]
            );
        } catch (\Throwable) {}

        $featuredReviews = [];
        try {
            $featuredReviews = \Database::fetchAll(
                "SELECT author, origin, content, platform, offer, rating
                 FROM vp_reviews
                 WHERE status = 'published' AND home_carousel = 1
                 ORDER BY review_date DESC
                 LIMIT 4"
            );
        } catch (\Throwable) {}

        $allReviews = [];
        try {
            $allReviews = \Database::fetchAll(
                "SELECT rating FROM vp_reviews WHERE status = 'published' AND featured = 1"
            );
        } catch (\Throwable) {}
        if (!empty($allReviews)) {
            $avgRating = array_sum(array_column($allReviews, 'rating')) / count($allReviews);
            $jsonLd[0]['aggregateRating'] = \SeoService::aggregateRatingJsonLd($avgRating, count($allReviews));
        }

        $guestOrigins = [];
        try {
            $guestOrigins = \Database::fetchAll(
                "SELECT DISTINCT origin FROM vp_reviews WHERE status = 'published' AND origin IS NOT NULL AND origin <> ''"
            );
        } catch (\Throwable) {}
        $guestOriginsCount = count($guestOrigins);

        $this->render('front/home', compact(
            'seo', 'jsonLd', 'lang',
            'faqs', 'recentArticles', 'featuredReviews', 'guestOriginsCount'
        ));
    }

    public function notFound(): void
    {
        http_response_code(404);
        $lang = \LangService::get();
        $seo = \SeoService::forPage('404', $lang, '404 — Page introuvable', 'Cette page n\'existe pas.');
        $jsonLd = [];
        $this->render('front/404', compact('seo', 'jsonLd', 'lang'));
    }
}
