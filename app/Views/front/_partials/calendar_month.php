<?php
declare(strict_types=1);

/**
 * Partial : un mois de calendrier public (Style A / brique de base).
 *
 * Variables attendues :
 *  @var string $cal_propriete   'VP-BB' ou 'VP-ETE'
 *  @var int    $cal_year        ex: 2026
 *  @var int    $cal_month       1..12
 *  @var string $cal_variant     'bnb' (défaut) ou 'villa' — applique .is-villa
 *
 * Source de design : docs/design-refs/2026-05-24-calendriers.html
 * Source de données : App\Services\PublicAvailabilityService::getMonthGrid()
 *
 * On reproduit exactement les classes du design (.cal-month, .cal-grid,
 * .cal-cell.cal-open/cal-booked/cal-empty), à charge pour le CSS de styler.
 */

use App\Services\PublicAvailabilityService;

$cal_variant = $cal_variant ?? 'bnb';

// Labels mois alignés sur le fichier de design (mois longs raccourcis).
$moisLabels = [
    1 => 'Jan.', 2 => 'Févr.', 3 => 'Mars', 4 => 'Avril',
    5 => 'Mai',  6 => 'Juin',  7 => 'Juil.', 8 => 'Août',
    9 => 'Sept.', 10 => 'Oct.', 11 => 'Nov.', 12 => 'Déc.',
];

$grid = PublicAvailabilityService::getMonthGrid($cal_propriete, $cal_year, $cal_month);

$firstDay   = new \DateTimeImmutable(sprintf('%04d-%02d-01', $cal_year, $cal_month));
$daysInMonth = (int) $firstDay->format('t');
// Offset Lun=0, Mar=1, ..., Dim=6 — pour insérer les cellules vides avant le 1er.
$leadingEmpty = ((int) $firstDay->format('N')) - 1;

$tag = $cal_variant === 'villa' ? "Villa entière" : "Chambres d'hôtes";
$monthClass = 'cal-month' . ($cal_variant === 'villa' ? ' is-villa' : '');
?>
<div class="<?= $monthClass ?>">
    <div class="cal-month-head">
        <span class="cal-month-name"><?= $moisLabels[$cal_month] ?> <?= $cal_year ?></span>
        <span class="cal-month-tag"><?= $tag ?></span>
    </div>
    <div class="cal-dow">
        <span>L</span><span>M</span><span>M</span><span>J</span><span>V</span><span>S</span><span>D</span>
    </div>
    <div class="cal-grid">
<?php /* Variables nommées explicitement pour ne pas piétiner le $i d'un
       partial parent qui inclurait ce fichier dans une boucle (cf.
       calendar_annual.php → boucle infinie corrigée le 2026-05-24). */ ?>
<?php for ($leadingIdx = 0; $leadingIdx < $leadingEmpty; $leadingIdx++): ?>
        <span class="cal-cell cal-empty"></span>
<?php endfor; ?>
<?php for ($day = 1; $day <= $daysInMonth; $day++):
    $key = sprintf('%04d-%02d-%02d', $cal_year, $cal_month, $day);
    $status = $grid[$key] ?? 'open';
    $cls = $status === 'booked' ? 'cal-booked' : 'cal-open';
?>
        <span class="cal-cell <?= $cls ?>"><?= $day ?></span>
<?php endfor; ?>
    </div>
</div>
