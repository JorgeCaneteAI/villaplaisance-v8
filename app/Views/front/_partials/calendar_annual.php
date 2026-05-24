<?php
declare(strict_types=1);

/**
 * Partial : Style A = vue annuelle (12 mois consécutifs).
 *
 * Variables optionnelles :
 *  @var int $cal_start_year  défaut : année courante
 *  @var int $cal_start_month défaut : mois courant
 *
 * Logique sémantique alignée sur le design :
 *  - Juillet et août → variant 'villa' (terra, VP-ETE)
 *  - Tous les autres mois → variant 'bnb' (linen, VP-BB)
 * (la logique additive VP-BB↔VP-ETE du service rend les jours cohérents
 * dans les deux cas, seule la teinte change)
 *
 * Source design : docs/design-refs/2026-05-24-calendriers.html (Style A).
 */

$cal_start_year  = $cal_start_year  ?? (int) date('Y');
$cal_start_month = $cal_start_month ?? (int) date('n');

$cursor = new \DateTimeImmutable(sprintf('%04d-%02d-01', $cal_start_year, $cal_start_month));
?>
<div class="annual-grid">
<?php for ($i = 0; $i < 12; $i++):
    $cal_year  = (int) $cursor->format('Y');
    $cal_month = (int) $cursor->format('n');

    if (in_array($cal_month, [7, 8], true)) {
        $cal_propriete = 'VP-ETE';
        $cal_variant   = 'villa';
    } else {
        $cal_propriete = 'VP-BB';
        $cal_variant   = 'bnb';
    }

    include __DIR__ . '/calendar_month.php';
    $cursor = $cursor->modify('+1 month');
endfor; ?>
</div>
