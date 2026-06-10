<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Génération de PDF calendriers mensuels et annuels via FPDF.
 * Portage fidèle de export_pdf.py (reportlab) de l'app Flask legacy.
 * A4 paysage, bandes colorées par source, calcul dynamique des cellules.
 *
 * Note encoding : FPDF 1.x ne supporte pas UTF-8 nativement. Les polices
 * DejaVuSans vendorées sont compilées en cp1252 ; tout texte passé aux
 * primitives FPDF est converti UTF-8→CP1252 via self::enc().
 */
class CalendarPdfService
{
    private const EXPORTS_DIR = ROOT . '/public/uploads/exports';
    private const FPDF_PATH = ROOT . '/app/Services/lib/fpdf/fpdf.php';
    private const FONT_DIR = ROOT . '/app/Services/lib/fpdf/font';

    public static function exportMonth(int $year, int $month): string
    {
        self::ensureFpdfLoaded();
        @mkdir(self::EXPORTS_DIR, 0755, true);

        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        self::registerFonts($pdf);
        $pdf->SetTitle(self::enc("Reservations " . ReservationConstants::MOIS_FR[$month] . " $year"));
        $pdf->AddPage();
        self::drawMonth($pdf, $year, $month);

        $path = self::EXPORTS_DIR . "/reservations_{$year}_" . sprintf('%02d', $month) . ".pdf";
        $pdf->Output('F', $path);
        return $path;
    }

    public static function exportYear(int $year): string
    {
        self::ensureFpdfLoaded();
        @mkdir(self::EXPORTS_DIR, 0755, true);

        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        self::registerFonts($pdf);
        $pdf->SetTitle(self::enc("Villa Plaisance $year"));

        // PDF annuel = vue villa uniquement (VP-BB + VP-ETE cumulés), pas
        // d'Avignon. Reproduit la sémantique slots morning/full/evening
        // de la vue annuelle web filtrée.
        for ($m = 1; $m <= 12; $m++) {
            $pdf->AddPage();
            self::drawMonthVilla($pdf, $year, $m);
        }

        $path = self::EXPORTS_DIR . "/villa_plaisance_{$year}.pdf";
        $pdf->Output('F', $path);
        return $path;
    }

    private static function ensureFpdfLoaded(): void
    {
        if (!defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', self::FONT_DIR . '/');
        }
        if (!class_exists('\\FPDF')) {
            require_once self::FPDF_PATH;
        }
    }

    private static function registerFonts(\FPDF $pdf): void
    {
        $pdf->AddFont('DejaVuSans', '', 'DejaVuSans.php');
        $pdf->AddFont('DejaVuSans', 'B', 'DejaVuSans-Bold.php');
    }

    /**
     * Convertit UTF-8 → CP1252 pour FPDF 1.x (polices cp1252).
     * //TRANSLIT remplace les glyphes hors cp1252 (ex: ●, €) par un équivalent.
     */
    private static function enc(string $s): string
    {
        $out = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s);
        return $out === false ? $s : $out;
    }

    private static function hexToRgb(string $hex): array
    {
        $h = ltrim($hex, '#');
        return [hexdec(substr($h, 0, 2)), hexdec(substr($h, 2, 2)), hexdec(substr($h, 4, 2))];
    }

    private static function truncate(\FPDF $pdf, string $text, float $maxWidth): string
    {
        while ($text !== '' && $pdf->GetStringWidth($text) > $maxWidth) {
            $text = mb_substr($text, 0, -1);
        }
        return $text;
    }

    /**
     * Rendu d'un mois pour le PDF mensuel global : reproduit la vue web
     * (index.php), 3 lanes par cellule (VP-BB / VP-ETE / AV-ANN), slots
     * morning/full/evening calés sur 46% / 20% / 34% comme la vue web.
     * Date d'arrivée/départ dans les demi-bandeaux.
     */
    private static function drawMonth(\FPDF $pdf, int $year, int $month): void
    {
        $W = 297.0;           // A4 landscape mm
        $H = 210.0;
        $MARGIN = 12.0;
        $usableW = $W - 2 * $MARGIN;
        $usableH = $H - 2 * $MARGIN;

        $data = ReservationService::buildCalendarData($year, $month);
        $weeks = $data['weeks'];
        $resaByDay = $data['resa_by_day'];
        $nWeeks = count($weeks);

        $TITLE_H = 20.0;
        $HEADER_H = 7.0;
        $LEGEND_H = 8.0;
        $CELL_H = ($usableH - $TITLE_H - $HEADER_H - $LEGEND_H) / max(1, $nWeeks);
        $CELL_W = $usableW / 7.0;

        // ── TITRE ──
        [$r, $g, $b] = self::hexToRgb('#2C2C2A');
        $pdf->SetFillColor($r, $g, $b);
        $pdf->Rect($MARGIN, $MARGIN, $usableW, $TITLE_H, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('DejaVuSans', 'B', 16);
        $pdf->SetXY($MARGIN + 6, $MARGIN + 5);
        $titre = 'RÉSERVATIONS, ' . mb_strtoupper(ReservationConstants::MOIS_FR[$month], 'UTF-8') . ' ' . $year;
        $pdf->Cell($usableW - 12, 10, self::enc($titre));
        $pdf->SetFont('DejaVuSans', '', 8);
        $pdf->SetXY($W - $MARGIN - 80, $MARGIN + 8);
        $pdf->Cell(76, 5, self::enc('Villa Plaisance & Studio Avignon'), 0, 0, 'R');

        // ── EN-TÊTES JOURS ──
        $headerY = $MARGIN + $TITLE_H;
        foreach (ReservationConstants::JOURS_FR as $i => $jour) {
            $x = $MARGIN + $i * $CELL_W;
            [$rh, $gh, $bh] = self::hexToRgb($i >= 5 ? '#3d3d3a' : '#2C2C2A');
            $pdf->SetFillColor($rh, $gh, $bh);
            $pdf->Rect($x, $headerY, $CELL_W, $HEADER_H, 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('DejaVuSans', 'B', 8);
            $pdf->SetXY($x, $headerY + 1);
            $pdf->Cell($CELL_W, $HEADER_H - 2, self::enc($jour), 0, 0, 'C');
        }

        // ── CELLULES ──
        $LANES = ['VP-BB' => 0, 'VP-ETE' => 1, 'AV-ANN' => 2];
        $N_LANES = 3;
        $BAND_H   = 4.5;  // hauteur d'une lane
        $LANE_GAP = 0.7;
        $SEP_H    = 1.5;  // séparateur entre semaines
        $DAY_NUM_H = 4.5; // hauteur réservée au numéro de jour
        $SLOT_MORNING_W = $CELL_W * 0.46;
        $SLOT_GUTTER_W  = $CELL_W * 0.20;
        $SLOT_EVENING_W = $CELL_W * 0.34;

        foreach ($weeks as $weekIdx => $week) {
            $cellY = $MARGIN + $TITLE_H + $HEADER_H + $weekIdx * $CELL_H;
            foreach ($week as $dayIdx => $day) {
                $cellX = $MARGIN + $dayIdx * $CELL_W;
                $isCurrent = (int) $day->format('n') === $month;
                $isWeekEnd   = ($dayIdx === 6);

                // Fond cellule
                $bgHex = !$isCurrent ? '#f5f5f5' : ($dayIdx >= 5 ? '#fafaf8' : '#ffffff');
                [$rb, $gb, $bb] = self::hexToRgb($bgHex);
                $pdf->SetFillColor($rb, $gb, $bb);
                $pdf->Rect($cellX, $cellY, $CELL_W, $CELL_H, 'F');

                // Séparateur épais en bas (sauf dernière rangée)
                if ($weekIdx < $nWeeks - 1) {
                    [$rs, $gs, $bs] = self::hexToRgb('#e5e5e0');
                    $pdf->SetFillColor($rs, $gs, $bs);
                    $pdf->Rect($cellX, $cellY + $CELL_H - $SEP_H, $CELL_W, $SEP_H, 'F');
                }
                // Bordure droite cellule (sauf dim)
                if (!$isWeekEnd) {
                    $pdf->SetDrawColor(221, 221, 221);
                    $pdf->SetLineWidth(0.2);
                    $pdf->Line($cellX + $CELL_W, $cellY, $cellX + $CELL_W, $cellY + $CELL_H);
                }

                // Numéro du jour
                $grey = $isCurrent ? 34 : 187;
                $pdf->SetTextColor($grey, $grey, $grey);
                $pdf->SetFont('DejaVuSans', 'B', 8);
                $pdf->SetXY($cellX + 2, $cellY + 1);
                $pdf->Cell(10, 4, (string) (int) $day->format('j'));

                $key = $day->format('Y-m-d');
                if (!$isCurrent || !isset($resaByDay[$key])) continue;

                // Range les résas par lane (= propriété). Ordre fixe pour
                // que l'œil suive horizontalement chaque propriété.
                $byLane = [0 => [], 1 => [], 2 => []];
                foreach ($resaByDay[$key] as $r) {
                    $l = $LANES[$r['propriete'] ?? ''] ?? null;
                    if ($l === null) continue;
                    $byLane[$l][] = $r;
                }

                // Empile les 3 lanes, chacune avec son slot morning/full/evening.
                for ($lane = 0; $lane < $N_LANES; $lane++) {
                    $bandY = $cellY + $DAY_NUM_H + $lane * ($BAND_H + $LANE_GAP);

                    // Classement des résas de la lane
                    $morn = $evn = $full = null;
                    foreach ($byLane[$lane] as $r) {
                        if (!empty($r['is_departure']))    $morn = $r;
                        elseif (!empty($r['is_arrival']))  $evn  = $r;
                        else                                $full = $r;
                    }

                    if ($full) {
                        // Bandeau pleine cellule, pas de padding latéral
                        // → continuité visuelle entre cellules adjacentes.
                        [$rc, $gc, $bc] = self::hexToRgb($full['couleur']['bg']);
                        $pdf->SetFillColor($rc, $gc, $bc);
                        $pdf->Rect($cellX, $bandY, $CELL_W, $BAND_H, 'F');
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->SetFont('DejaVuSans', 'B', 6);
                        $pdf->SetXY($cellX + 1.2, $bandY + 0.7);
                        $line = self::enc($full['code'] . ' · ' . $full['nom_client']);
                        $pdf->Cell($CELL_W - 2.4, $BAND_H - 1.5, self::truncate($pdf, $line, $CELL_W - 2.4));
                    } else {
                        if ($morn) {
                            // Slot morning, demi-cellule gauche (0 → 46%), date alignée à droite (côté gouttière)
                            [$rc, $gc, $bc] = self::hexToRgb($morn['couleur']['bg']);
                            $pdf->SetFillColor($rc, $gc, $bc);
                            $pdf->Rect($cellX, $bandY, $SLOT_MORNING_W, $BAND_H, 'F');
                            $pdf->SetTextColor(255, 255, 255);
                            $pdf->SetFont('DejaVuSans', 'B', 6);
                            $dateStr = (new \DateTimeImmutable($morn['depart']))->format('d/m');
                            $pdf->SetXY($cellX, $bandY + 0.7);
                            $pdf->Cell($SLOT_MORNING_W - 1.2, $BAND_H - 1.5, self::enc($dateStr), 0, 0, 'R');
                        }
                        if ($evn) {
                            // Slot evening, demi-cellule droite (66 → 100%), date alignée à gauche (côté gouttière)
                            [$rc, $gc, $bc] = self::hexToRgb($evn['couleur']['bg']);
                            $pdf->SetFillColor($rc, $gc, $bc);
                            $evX = $cellX + $SLOT_MORNING_W + $SLOT_GUTTER_W;
                            $pdf->Rect($evX, $bandY, $SLOT_EVENING_W, $BAND_H, 'F');
                            $pdf->SetTextColor(255, 255, 255);
                            $pdf->SetFont('DejaVuSans', 'B', 6);
                            $dateStr = (new \DateTimeImmutable($evn['arrivee']))->format('d/m');
                            $pdf->SetXY($evX + 1.2, $bandY + 0.7);
                            $pdf->Cell($SLOT_EVENING_W - 1.8, $BAND_H - 1.5, self::enc($dateStr), 0, 0, 'L');
                        }
                    }
                }
            }
        }

        // ── LÉGENDE ──
        $legY = $H - $MARGIN - 6;
        $legX = $MARGIN;
        foreach (ReservationConstants::SOURCES as $src => $c) {
            [$rl, $gl, $bl] = self::hexToRgb($c['bg']);
            $pdf->SetFillColor($rl, $gl, $bl);
            $pdf->Rect($legX, $legY, 22, 5, 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('DejaVuSans', 'B', 7);
            $pdf->SetXY($legX, $legY + 0.5);
            $pdf->Cell(22, 4, self::enc('● ' . $src), 0, 0, 'C');
            $legX += 24;
        }
        // Légende complémentaire arrivée/départ
        $pdf->SetTextColor(110, 110, 110);
        $pdf->SetFont('DejaVuSans', '', 6.5);
        $pdf->SetXY($MARGIN, $legY + 5.5);
        $pdf->Cell($usableW, 3, self::enc('Demi-cellule gauche = départ matin (avant 11h) · Demi-cellule droite = arrivée soir (après 16h)'), 0, 0, 'L');
    }

    /**
     * Rendu d'un mois pour le PDF annuel "Villa" : reproduit la vue web
     * annuelle filtrée villa (VP-BB + VP-ETE cumulés sur 1 lane, AV-ANN
     * exclu). Slots morning/full/evening calés sur 46% / 20% / 34%,
     * date d'arrivée/départ dans les demi-bandeaux, séparateur épais
     * entre rangées de semaines.
     */
    private static function drawMonthVilla(\FPDF $pdf, int $year, int $month): void
    {
        $W = 297.0;
        $H = 210.0;
        $MARGIN = 12.0;
        $usableW = $W - 2 * $MARGIN;
        $usableH = $H - 2 * $MARGIN;

        $data = ReservationService::buildCalendarData($year, $month);
        $weeks = $data['weeks'];
        $resaByDay = $data['resa_by_day'];
        $nWeeks = count($weeks);

        $TITLE_H = 20.0;
        $HEADER_H = 7.0;
        $LEGEND_H = 8.0;
        $CELL_H = ($usableH - $TITLE_H - $HEADER_H - $LEGEND_H) / max(1, $nWeeks);
        $CELL_W = $usableW / 7.0;

        // ── TITRE ──
        [$r, $g, $b] = self::hexToRgb('#2C2C2A');
        $pdf->SetFillColor($r, $g, $b);
        $pdf->Rect($MARGIN, $MARGIN, $usableW, $TITLE_H, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('DejaVuSans', 'B', 16);
        $pdf->SetXY($MARGIN + 6, $MARGIN + 5);
        $titre = 'VILLA PLAISANCE, ' . mb_strtoupper(ReservationConstants::MOIS_FR[$month], 'UTF-8') . ' ' . $year;
        $pdf->Cell($usableW - 12, 10, self::enc($titre));
        $pdf->SetFont('DejaVuSans', '', 8);
        $pdf->SetXY($W - $MARGIN - 80, $MARGIN + 8);
        $pdf->Cell(76, 5, self::enc('Chambres & Maison entière'), 0, 0, 'R');

        // ── EN-TÊTES JOURS ──
        $headerY = $MARGIN + $TITLE_H;
        foreach (ReservationConstants::JOURS_FR as $i => $jour) {
            $x = $MARGIN + $i * $CELL_W;
            [$rh, $gh, $bh] = self::hexToRgb($i >= 5 ? '#3d3d3a' : '#2C2C2A');
            $pdf->SetFillColor($rh, $gh, $bh);
            $pdf->Rect($x, $headerY, $CELL_W, $HEADER_H, 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('DejaVuSans', 'B', 8);
            $pdf->SetXY($x, $headerY + 1);
            $pdf->Cell($CELL_W, $HEADER_H - 2, self::enc($jour), 0, 0, 'C');
        }

        // ── CELLULES ──
        $SLOT_MORNING_W = $CELL_W * 0.46;
        $SLOT_GUTTER_W  = $CELL_W * 0.20;
        $SLOT_EVENING_W = $CELL_W * 0.34;
        $BAND_H = 6.0;
        $SEP_H  = 1.5; // séparateur horizontal entre semaines

        foreach ($weeks as $weekIdx => $week) {
            $cellY = $MARGIN + $TITLE_H + $HEADER_H + $weekIdx * $CELL_H;
            foreach ($week as $dayIdx => $day) {
                $cellX = $MARGIN + $dayIdx * $CELL_W;
                $isCurrent = (int) $day->format('n') === $month;
                $isWeekStart = ($dayIdx === 0);
                $isWeekEnd   = ($dayIdx === 6);

                // Fond cellule
                $bgHex = !$isCurrent ? '#f5f5f5' : ($dayIdx >= 5 ? '#fafaf8' : '#ffffff');
                [$rb, $gb, $bb] = self::hexToRgb($bgHex);
                $pdf->SetFillColor($rb, $gb, $bb);
                $pdf->Rect($cellX, $cellY, $CELL_W, $CELL_H, 'F');

                // Séparateur épais en bas (sauf dernière rangée)
                if ($weekIdx < $nWeeks - 1) {
                    [$rs, $gs, $bs] = self::hexToRgb('#e5e5e0');
                    $pdf->SetFillColor($rs, $gs, $bs);
                    $pdf->Rect($cellX, $cellY + $CELL_H - $SEP_H, $CELL_W, $SEP_H, 'F');
                }
                // Bordure droite cellule (sauf dim)
                if (!$isWeekEnd) {
                    $pdf->SetDrawColor(221, 221, 221);
                    $pdf->SetLineWidth(0.2);
                    $pdf->Line($cellX + $CELL_W, $cellY, $cellX + $CELL_W, $cellY + $CELL_H);
                }

                // Numéro du jour
                $grey = $isCurrent ? 34 : 187;
                $pdf->SetTextColor($grey, $grey, $grey);
                $pdf->SetFont('DejaVuSans', 'B', 8);
                $pdf->SetXY($cellX + 2, $cellY + 1);
                $pdf->Cell(10, 4, (string) (int) $day->format('j'));

                // Rendu de LA résa (lane villa unique)
                $key = $day->format('Y-m-d');
                if (!isset($resaByDay[$key])) continue;

                // Filtre : VP-BB + VP-ETE uniquement (AV-ANN exclu).
                $villa = array_filter($resaByDay[$key], fn($r) =>
                    in_array($r['propriete'] ?? '', ['VP-BB', 'VP-ETE'], true)
                );
                if (empty($villa)) continue;

                // Tri par type de slot (max 1 de chaque dans une lane)
                $arrivee = $depart = $full = null;
                foreach ($villa as $r) {
                    if (!empty($r['is_departure']))    $depart  = $r;
                    elseif (!empty($r['is_arrival']))  $arrivee = $r;
                    else                                $full    = $r;
                }

                $bandY = $cellY + 6;
                // Coin arrondi simulé : pas dispo en FPDF, on garde droit.

                if ($full) {
                    // Bandeau pleine cellule, AUCUN padding latéral → continuité
                    // visuelle entre cellules adjacentes d'une même résa.
                    [$rc, $gc, $bc] = self::hexToRgb($full['couleur']['bg']);
                    $pdf->SetFillColor($rc, $gc, $bc);
                    $pdf->Rect($cellX, $bandY, $CELL_W, $BAND_H, 'F');
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->SetFont('DejaVuSans', 'B', 6.5);
                    $pdf->SetXY($cellX + 1.5, $bandY + 1);
                    $line = self::enc($full['code'] . ' · ' . $full['nom_client']);
                    $pdf->Cell($CELL_W - 3, $BAND_H - 2, self::truncate($pdf, $line, $CELL_W - 3));
                } else {
                    // Slot morning (départ matin, 0 → 46%), pas de padding gauche
                    if ($depart) {
                        [$rc, $gc, $bc] = self::hexToRgb($depart['couleur']['bg']);
                        $pdf->SetFillColor($rc, $gc, $bc);
                        $pdf->Rect($cellX, $bandY, $SLOT_MORNING_W, $BAND_H, 'F');
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->SetFont('DejaVuSans', 'B', 6.5);
                        $dateStr = (new \DateTimeImmutable($depart['depart']))->format('d/m');
                        $pdf->SetXY($cellX, $bandY + 1);
                        // Aligné à droite (côté gouttière)
                        $pdf->Cell($SLOT_MORNING_W - 1.5, $BAND_H - 2, self::enc($dateStr), 0, 0, 'R');
                    }
                    // Slot evening (arrivée soir, 66 → 100%), pas de padding droit
                    if ($arrivee) {
                        [$rc, $gc, $bc] = self::hexToRgb($arrivee['couleur']['bg']);
                        $pdf->SetFillColor($rc, $gc, $bc);
                        $evX = $cellX + $SLOT_MORNING_W + $SLOT_GUTTER_W;
                        $pdf->Rect($evX, $bandY, $SLOT_EVENING_W, $BAND_H, 'F');
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->SetFont('DejaVuSans', 'B', 6.5);
                        $dateStr = (new \DateTimeImmutable($arrivee['arrivee']))->format('d/m');
                        $pdf->SetXY($evX + 1.5, $bandY + 1);
                        // Aligné à gauche (côté gouttière)
                        $pdf->Cell($SLOT_EVENING_W - 2, $BAND_H - 2, self::enc($dateStr), 0, 0, 'L');
                    }
                }
            }
        }

        // ── LÉGENDE (sources, sans Avignon qui n'est pas dans ce PDF) ──
        $legY = $H - $MARGIN - 6;
        $legX = $MARGIN;
        foreach (ReservationConstants::SOURCES as $src => $c) {
            [$rl, $gl, $bl] = self::hexToRgb($c['bg']);
            $pdf->SetFillColor($rl, $gl, $bl);
            $pdf->Rect($legX, $legY, 22, 5, 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('DejaVuSans', 'B', 7);
            $pdf->SetXY($legX, $legY + 0.5);
            $pdf->Cell(22, 4, self::enc('● ' . $src), 0, 0, 'C');
            $legX += 24;
        }
    }
}
