<?php
declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\BaseController;

/**
 * Page publique « Disponibilités », vue annuelle 12 mois.
 *
 * Pas de données spécifiques à récupérer ici : les partials calendrier
 * (calendar_annual → calendar_month) appellent eux-mêmes
 * PublicAvailabilityService pour lire vp_reservations.
 */
class DisponibilitesController extends BaseController
{
    public function index(): void
    {
        $lang = \LangService::get();

        $seo = \SeoService::forPage(
            'disponibilites',
            $lang,
            'Disponibilités, Villa Plaisance',
            "Calendrier sur 12 mois des disponibilités de Villa Plaisance, "
            . "chambres d'hôtes et villa entière à Bédarrides. "
            . "Synchronisé avec Airbnb et Booking."
        );

        $jsonLd = [
            \SeoService::lodgingBusinessJsonLd(),
            \SeoService::breadcrumbJsonLd([
                ['name' => t('nav.home'), 'url' => APP_URL . '/'],
                ['name' => 'Disponibilités'],
            ]),
        ];

        $this->render(
            'front/disponibilites',
            compact('seo', 'jsonLd', 'lang'),
            'front-proto'
        );
    }
}
