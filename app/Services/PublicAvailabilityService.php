<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Lecture publique des disponibilités pour le front (v2.villaplaisance.fr).
 *
 * Différences avec ReservationService::buildCalendarData (qui sert l'admin) :
 *  - sort uniquement open/booked par jour (aucune info client, source, code)
 *  - applique la logique additive VP-BB ↔ VP-ETE : une résa sur l'une marque
 *    l'autre comme bloquée (même maison physique)
 *  - exclut AV-ANN (Studio Avignon, hors villaplaisance.fr)
 *  - exclut les résas statut = 'Annulée'
 *
 * Convention de jour bloqué : [arrivee, depart) — le jour de départ est libre
 * (un nouveau check-in est possible le soir). Cohérent avec Airbnb/Booking.
 */
class PublicAvailabilityService
{
    public const PROPRIETES_PUBLIQUES = ['VP-BB', 'VP-ETE'];

    /**
     * Retourne la grille d'un mois pour une propriété donnée.
     *
     * @param string $propriete 'VP-BB' (chambres) ou 'VP-ETE' (villa entière)
     * @param int    $year      ex: 2026
     * @param int    $month     1..12
     * @return array<string,string> ['YYYY-MM-DD' => 'open' | 'booked'] couvrant
     *                              uniquement les jours du mois demandé.
     */
    public static function getMonthGrid(string $propriete, int $year, int $month): array
    {
        if (!in_array($propriete, self::PROPRIETES_PUBLIQUES, true)) {
            throw new \InvalidArgumentException(
                "propriete must be one of: " . implode(', ', self::PROPRIETES_PUBLIQUES)
            );
        }

        $firstDay = sprintf('%04d-%02d-01', $year, $month);
        $lastDay  = date('Y-m-t', strtotime($firstDay));

        // Logique additive : on récupère TOUTES les résas qui chevauchent le mois
        // sur VP-BB ou VP-ETE, peu importe la propriété demandée. Une résa villa
        // entière bloque les chambres et vice-versa (même maison physique).
        $reservations = \Database::fetchAll(
            "SELECT arrivee, depart FROM vp_reservations
             WHERE propriete IN ('VP-BB', 'VP-ETE')
               AND statut != 'Annulée'
               AND arrivee <= ?
               AND depart > ?",
            [$lastDay, $firstDay]
        );

        // Initialise tous les jours du mois à 'open'
        $grid = [];
        $cursor = new \DateTimeImmutable($firstDay);
        $end    = new \DateTimeImmutable($lastDay);
        while ($cursor <= $end) {
            $grid[$cursor->format('Y-m-d')] = 'open';
            $cursor = $cursor->modify('+1 day');
        }

        // Marque les jours occupés. Jour de départ EXCLU (check-in possible le soir).
        foreach ($reservations as $r) {
            $start = new \DateTimeImmutable($r['arrivee']);
            $stop  = new \DateTimeImmutable($r['depart']);
            $d = $start;
            while ($d < $stop) {
                $key = $d->format('Y-m-d');
                if (isset($grid[$key])) {
                    $grid[$key] = 'booked';
                }
                $d = $d->modify('+1 day');
            }
        }

        return $grid;
    }
}
