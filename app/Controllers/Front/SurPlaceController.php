<?php
declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class SurPlaceController extends BaseController
{
    /**
     * Note : la page liste publique /sur-place a été supprimée le 2026-05-23.
     * Son contenu est désormais servi par ItineraryController::index sur
     * /itineraire (lien "Que faire" de la nav). Le router redirige 301
     * /sur-place → /itineraire.
     *
     * Cette classe ne garde que show() pour les pages détaillées des
     * articles vp_articles type='sur-place' : URL /sur-place/{slug}.
     */
    public function show(string $slug): void
    {
        $lang = \LangService::get();

        $article = null;
        try {
            $article = \Database::fetchOne(
                "SELECT * FROM vp_articles WHERE slug = ? AND lang = ? AND status = 'published'",
                [$slug, $lang]
            );
        } catch (\Throwable) {}

        if (!$article) {
            http_response_code(404);
            $seo = \SeoService::forPage('404', $lang, '404, Fiche introuvable', '');
            $jsonLd = [];
            $this->render('front/404', compact('seo', 'jsonLd', 'lang'), 'front-proto');
            return;
        }

        $seo = [
            'title' => $article['meta_title'] ?: $article['title'],
            'description' => $article['meta_desc'] ?: ($article['excerpt'] ?? ''),
            'canonical' => \SeoService::canonical('sur-place/' . $slug, $lang),
            'og' => [
                'title' => $article['meta_title'] ?: $article['title'],
                'description' => $article['meta_desc'] ?: ($article['excerpt'] ?? ''),
                'image' => $article['og_image'] ?: (APP_URL . '/assets/img/og-default.jpg'),
                'url' => \SeoService::canonical('sur-place/' . $slug, $lang),
                'type' => 'article',
                'locale' => \SeoService::locale($lang),
            ],
            'hreflang' => \SeoService::hreflang('sur-place/' . $slug),
        ];

        $jsonLd = [
            \SeoService::blogPostingJsonLd($article),
            \SeoService::breadcrumbJsonLd([
                ['name' => t('nav.home'), 'url' => APP_URL . '/'],
                ['name' => t('nav.surplace'), 'url' => APP_URL . '/sur-place/'],
                ['name' => $article['title']],
            ]),
        ];

        $contentBlocks = json_decode($article['content'] ?? '[]', true) ?: [];

        $this->render('front/article', compact('seo', 'article', 'contentBlocks', 'jsonLd', 'lang'), 'front-proto');
    }
}
