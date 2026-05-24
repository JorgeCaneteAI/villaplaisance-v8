<?php
declare(strict_types=1);

/**
 * Partial : Style B = 3 mois côte à côte (mois courant + 2 suivants).
 *
 * Variables attendues :
 *  @var string $cal_propriete   'VP-BB' ou 'VP-ETE'
 *  @var string $cal_variant     'bnb' (défaut) ou 'villa'
 *
 * Wraps 3 inclusions de calendar_month.php dans une .focus-grid.
 * Source de design : docs/design-refs/2026-05-24-calendriers.html (Style B).
 */

$cal_variant = $cal_variant ?? 'bnb';

$now = new \DateTimeImmutable('first day of this month');
?>
<div class="focus-grid">
<?php for ($offset = 0; $offset < 3; $offset++):
    $m = $now->modify("+{$offset} month");
    $cal_year  = (int) $m->format('Y');
    $cal_month = (int) $m->format('n');
    include __DIR__ . '/calendar_month.php';
endfor; ?>
</div>
