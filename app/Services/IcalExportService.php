<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Génération d'un flux iCal (RFC 5545) exposant les réservations Villa Plaisance
 * pour abonnement dans Apple Calendar / Google Calendar / Thunderbird.
 *
 * Format date-only par séjour (DTSTART/DTEND avec VALUE=DATE) :
 * - DTEND est EXCLUSIVE conformément à RFC 5545. Une résa du 14 au 21 (départ
 *   le 21 au matin) émet DTSTART=20260714, DTEND=20260721 → affichée 14→20.
 * - Convention identique à Airbnb/Booking, pas de surprise dans Apple Calendar.
 *
 * Titre VEVENT (SUMMARY) = même format que la vue admin :
 *   "[CODE] NOM CLIENT · Source · N pers"
 *
 * Description = bloc multi-lignes avec code, propriété, source, dates, occupants,
 * lien direct vers la fiche admin.
 */
class IcalExportService
{
    private const PRODID = '-//Villa Plaisance//Reservations//FR';
    private const CALNAME = 'Villa Plaisance — Réservations';
    private const CALDESC = 'Réservations chambres d\'hôtes (sept-juin), villa entière (juil-août) et studio Avignon.';
    private const TZID = 'Europe/Paris';

    /**
     * Construit le contenu complet d'un fichier .ics à partir d'un tableau de
     * réservations (rows de vp_reservations brutes + label propriete).
     */
    public static function buildIcs(array $reservations, string $appUrl): string
    {
        $now = gmdate('Ymd\THis\Z');
        $lines = [];
        $lines[] = 'BEGIN:VCALENDAR';
        $lines[] = 'VERSION:2.0';
        $lines[] = 'PRODID:' . self::PRODID;
        $lines[] = 'CALSCALE:GREGORIAN';
        $lines[] = 'METHOD:PUBLISH';
        $lines[] = 'X-WR-CALNAME:' . self::esc(self::CALNAME);
        $lines[] = 'X-WR-CALDESC:' . self::esc(self::CALDESC);
        $lines[] = 'X-WR-TIMEZONE:' . self::TZID;

        foreach ($reservations as $r) {
            $event = self::buildEvent($r, $now, $appUrl);
            if ($event !== null) {
                $lines = array_merge($lines, $event);
            }
        }

        $lines[] = 'END:VCALENDAR';

        // RFC 5545 : lignes wrap à 75 octets, séparateur CRLF.
        $folded = array_map([self::class, 'fold'], $lines);
        return implode("\r\n", $folded) . "\r\n";
    }

    /**
     * Construit un VEVENT pour une résa. Renvoie un tableau de lignes (sans
     * pliage, ce sera fait en sortie) ou null si la résa doit être ignorée.
     */
    private static function buildEvent(array $r, string $now, string $appUrl): ?array
    {
        // Ignore les résa annulées : pas la peine de spammer Apple Calendar.
        if (($r['statut'] ?? '') === 'Annulée') {
            return null;
        }

        $code     = (string) ($r['code'] ?? '');
        $nom      = trim((string) ($r['nom_client'] ?? ''));
        $prop     = (string) ($r['propriete'] ?? '');
        $source   = (string) ($r['source'] ?? '');
        $arrivee  = (string) ($r['arrivee'] ?? '');
        $depart   = (string) ($r['depart'] ?? '');
        $adultes  = (int) ($r['adultes'] ?? 0);
        $enfants  = (int) ($r['enfants'] ?? 0);
        $bebes    = (int) ($r['bebes'] ?? 0);
        $statut   = (string) ($r['statut'] ?? '');
        $updated  = (string) ($r['updated_at'] ?? '');
        $id       = (int) ($r['id'] ?? 0);

        if ($arrivee === '' || $depart === '' || $id === 0) {
            return null;
        }

        // SUMMARY = format admin court : "CODE · Nom · Source · N pers"
        $personnes = self::sumPersonnes($adultes, $enfants, $bebes);
        $summary = $code !== '' ? $code : "RESA-{$id}";
        if ($nom !== '') $summary .= ' · ' . $nom;
        $summary .= ' · ' . $source;
        if ($personnes > 0) $summary .= ' · ' . $personnes . ' pers';

        // DESCRIPTION : bloc multi-lignes avec tous les détails utiles.
        $proprieteLabel = self::proprieteLabel($prop);
        $desc = [];
        $desc[] = 'Code : ' . $summary;
        $desc[] = 'Propriété : ' . $proprieteLabel . ' (' . $prop . ')';
        $desc[] = 'Source : ' . $source;
        $desc[] = 'Arrivée : ' . self::frenchDate($arrivee);
        $desc[] = 'Départ : ' . self::frenchDate($depart);
        if ($personnes > 0) {
            $detail = $adultes . ' adulte' . ($adultes > 1 ? 's' : '');
            if ($enfants > 0) $detail .= ', ' . $enfants . ' enfant' . ($enfants > 1 ? 's' : '');
            if ($bebes > 0)   $detail .= ', ' . $bebes . ' bébé' . ($bebes > 1 ? 's' : '');
            $desc[] = 'Composition : ' . $detail;
        }
        if (!empty($r['commentaire'])) {
            $desc[] = '';
            $desc[] = 'Commentaire : ' . $r['commentaire'];
        }
        $desc[] = '';
        $desc[] = 'Fiche admin : ' . rtrim($appUrl, '/') . '/admin/reservations/saisie/' . $id;
        $descStr = implode("\\n", array_map([self::class, 'esc'], $desc));

        $uid = 'resa-' . $id . '@villaplaisance.fr';
        $dtstart = str_replace('-', '', $arrivee);
        $dtend   = str_replace('-', '', $depart);

        // Couleur Apple Calendar par source (X-APPLE-CALENDAR-COLOR n'est pas
        // standard mais reconnu par Apple Calendar et iCal sur macOS).
        $couleur = self::sourceColor($source);

        $lines = [];
        $lines[] = 'BEGIN:VEVENT';
        $lines[] = 'UID:' . $uid;
        $lines[] = 'DTSTAMP:' . $now;
        $lines[] = 'DTSTART;VALUE=DATE:' . $dtstart;
        $lines[] = 'DTEND;VALUE=DATE:' . $dtend;
        $lines[] = 'SUMMARY:' . self::esc($summary);
        $lines[] = 'DESCRIPTION:' . $descStr;
        $lines[] = 'LOCATION:' . self::esc($proprieteLabel . ', Bédarrides');
        $lines[] = 'STATUS:' . ($statut === 'Option' ? 'TENTATIVE' : 'CONFIRMED');
        $lines[] = 'TRANSP:OPAQUE';
        if ($updated !== '') {
            $ts = strtotime($updated);
            if ($ts !== false) {
                $lines[] = 'LAST-MODIFIED:' . gmdate('Ymd\THis\Z', $ts);
            }
        }
        if ($couleur !== null) {
            $lines[] = 'COLOR:' . $couleur;
        }
        $lines[] = 'END:VEVENT';

        return $lines;
    }

    /**
     * Échappement RFC 5545 pour les valeurs de propriété TEXT.
     * Ordre important : backslash d'abord, sinon double-échappement.
     */
    private static function esc(string $s): string
    {
        $s = str_replace('\\', '\\\\', $s);
        $s = str_replace([';', ',', "\r\n", "\n", "\r"], ['\\;', '\\,', '\\n', '\\n', '\\n'], $s);
        return $s;
    }

    /**
     * RFC 5545 : pli les lignes longues à 75 octets, continuation préfixée d'un
     * espace. mb_strcut par caractère pour préserver l'UTF-8 multi-byte.
     */
    private static function fold(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }
        $out = '';
        $rest = $line;
        $first = true;
        while ($rest !== '') {
            $chunk = mb_strcut($rest, 0, 73, 'UTF-8');
            if ($chunk === '') break;
            $out .= ($first ? '' : "\r\n ") . $chunk;
            $rest = (string) mb_strcut($rest, strlen($chunk), null, 'UTF-8');
            $first = false;
        }
        return $out;
    }

    private static function sumPersonnes(int $adultes, int $enfants, int $bebes): int
    {
        return $adultes + $enfants + $bebes;
    }

    private static function proprieteLabel(string $code): string
    {
        return match ($code) {
            'VP-BB'  => 'Chambres d\'hôtes',
            'VP-ETE' => 'Villa entière été',
            'AV-ANN' => 'Studio Avignon',
            default  => $code,
        };
    }

    private static function frenchDate(string $isoDate): string
    {
        $ts = strtotime($isoDate);
        return $ts === false ? $isoDate : date('d/m/Y', $ts);
    }

    /**
     * Couleur CSS par source pour COLOR (RFC 7986). Apple Calendar n'affiche
     * pas toujours la couleur par évènement (souvent par calendrier abonné),
     * mais Google Calendar et certains clients le respectent.
     */
    private static function sourceColor(string $source): ?string
    {
        return match ($source) {
            'Airbnb'  => '#ff5a5f',
            'Booking' => '#003580',
            'Direct'  => '#34a853',
            'Privée'  => '#888888',
            'Absence' => '#222222',
            default   => null,
        };
    }
}
