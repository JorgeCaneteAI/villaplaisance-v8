<?php
declare(strict_types=1);

namespace App\Controllers\Front;

use App\Services\IcalExportService;

/**
 * Endpoint public protégé par token, expose les réservations Villa Plaisance
 * au format iCal pour abonnement dans Apple Calendar / Google Calendar.
 *
 * Route : GET /calendar.ics?token=XYZ
 *
 * Le token est stocké dans vp_settings (setting_key = 'ical_export_token').
 * Jorge gère son cycle de vie (génération, révocation) depuis l'admin.
 *
 * Renvoie :
 * - 200 + text/calendar : token valide
 * - 403                 : token absent ou invalide
 */
class CalendarIcalController
{
    public function export(): void
    {
        $token = $_GET['token'] ?? '';
        $expected = self::expectedToken();

        if ($expected === '' || !is_string($token) || !hash_equals($expected, $token)) {
            http_response_code(403);
            header('Content-Type: text/plain; charset=utf-8');
            echo "Forbidden\n";
            return;
        }

        $reservations = self::loadReservations();
        $ics = IcalExportService::buildIcs($reservations, defined('APP_URL') ? (string) APP_URL : '');

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename="villa-plaisance.ics"');
        header('Cache-Control: private, max-age=300');
        echo $ics;
    }

    /**
     * Charge le token depuis vp_settings. Renvoie '' si absent (le contrôleur
     * répondra alors 403 quoi qu'on lui passe, donc impossible d'activer
     * l'endpoint sans avoir d'abord inséré la ligne en BDD).
     */
    private static function expectedToken(): string
    {
        $row = \Database::fetchOne(
            "SELECT setting_value FROM vp_settings WHERE setting_key = 'ical_export_token' LIMIT 1"
        );
        return (string) ($row['setting_value'] ?? '');
    }

    /**
     * Récupère les réservations à exposer dans le flux. Fenêtre temporelle :
     * - 90 jours en arrière (historique récent, utile pour relancer un avis)
     * - 2 ans en avant (cale les réservations longues d'avance type Booking)
     */
    private static function loadReservations(): array
    {
        $from = (new \DateTimeImmutable('-90 days'))->format('Y-m-d');
        $to   = (new \DateTimeImmutable('+730 days'))->format('Y-m-d');
        return \Database::fetchAll(
            "SELECT id, code, nom_client, propriete, source, arrivee, depart,
                    adultes, enfants, bebes, statut, commentaire, updated_at
             FROM vp_reservations
             WHERE depart >= ? AND arrivee <= ?
             ORDER BY arrivee ASC, propriete ASC",
            [$from, $to]
        );
    }
}
