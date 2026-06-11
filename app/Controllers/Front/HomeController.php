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
            'Villa Plaisance, Chambres d\'hôtes et villa de charme à Bédarrides, Provence',
            'Chambres d\'hôtes de septembre à juin, villa entière en juillet-août. Piscine privée, 4 chambres, entre Avignon et Orange. Bédarrides, Vaucluse.'
        );

        // JSON-LD : la maison offre 2 modèles distincts (BedAndBreakfast
        // sept-juin + VacationRental juillet-août). On n'émet ici que le
        // BedAndBreakfast (catégorie principale, exigences raisonnables).
        // Le VacationRental est conservé sur /location-villa-provence
        // (VillaController) où il est contextuellement pertinent, mais
        // il est volontairement retiré de la home : son rich result Google
        // « Vacation rental » exige des champs très spécifiques (prix par
        // nuit dynamiques, dates de dispo, photos par chambre) destinés
        // aux chaînes hôtelières connectées à Google Hotels. Pas notre
        // cible. Sans ce rich result précis, l'entité reste reconnue via
        // BedAndBreakfast + LocalBusiness + Organisation + AggregateRating.
        $jsonLd = [
            \SeoService::bedAndBreakfastJsonLd(),
        ];

        // Add FAQ JSON-LD
        $faqs = [];
        try {
            $faqs = \Database::fetchAll(
                "SELECT question, answer FROM vp_faq WHERE page_slug = 'accueil' AND lang = ? AND active = 1",
                [$lang]
            );
        } catch (\Throwable) {}
        if (!empty($faqs)) {
            $jsonLd[] = \SeoService::faqJsonLd($faqs);
        }

        // L'aggregateRating est porté par bedAndBreakfastJsonLd() depuis la
        // source unique (tous les avis publiés, cf. SeoService::
        // aggregateRatingFromReviews). On ne le surcharge plus ici : l'ancien
        // calcul featured-only donnait une 3e valeur (5.0/34) incohérente avec
        // /avis (4.9/104) pour la même entité @id.

        // Layout 'front-proto' = design Claude (Cormorant Garamond + style-proto.css).
        $this->render('front/home', compact('seo', 'jsonLd', 'lang'), 'front-proto');
    }

    public function notFound(): void
    {
        http_response_code(404);
        $lang = \LangService::get();
        $seo = \SeoService::forPage('404', $lang, '404, Page introuvable', 'Cette page n\'existe pas.');
        $jsonLd = [];
        $this->render('front/404', compact('seo', 'jsonLd', 'lang'), 'front-proto');
    }
}
