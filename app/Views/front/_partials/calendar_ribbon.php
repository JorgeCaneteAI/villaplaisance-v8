<?php
declare(strict_types=1);

/**
 * Partial : Style C = ruban saisonnier (vue d'ensemble année).
 *
 * Aucune variable requise. Affiche un résumé fixe avec 5 segments dont les
 * labels et les couleurs sémantiques sont alignés sur le design :
 *
 *   Mai · Juin    → bnb
 *   Juillet       → villa
 *   Août          → villa
 *   Sept. → Déc.  → bnb
 *   Jan. → Avril  → bnb
 *
 * Pour chaque segment on calcule l'occupation réelle (jours bookés / jours
 * futurs total) et on choisit le statut affiché :
 *   - >= 80 % bookés → « Complet » (overlay .ribbon-full)
 *   - 20-80 %        → « Quelques dates »
 *   - < 20 %         → « Disponible »
 *
 * Les segments entièrement dans le passé sont masqués.
 * Source design : docs/design-refs/2026-05-24-calendriers.html (Style C).
 */

use App\Services\PublicAvailabilityService;

$today        = new \DateTimeImmutable('today');
$currentYear  = (int) $today->format('Y');
$currentMonth = (int) $today->format('n');

$segments = [
    ['label' => 'Mai · Juin',    'months' => [5, 6],          'intent' => 'bnb'],
    ['label' => 'Juillet',       'months' => [7],             'intent' => 'villa'],
    ['label' => 'Août',          'months' => [8],             'intent' => 'villa'],
    ['label' => 'Sept. → Déc.',  'months' => [9, 10, 11, 12], 'intent' => 'bnb'],
    ['label' => 'Jan. → Avril',  'months' => [1, 2, 3, 4],    'intent' => 'bnb'],
];

$computed = [];
foreach ($segments as $seg) {
    $propriete = $seg['intent'] === 'villa' ? 'VP-ETE' : 'VP-BB';
    $bookedDays = 0;
    $totalDays  = 0;

    foreach ($seg['months'] as $m) {
        // Année cible : si le mois est passé dans l'année courante, on regarde l'an prochain.
        $y = ($m < $currentMonth) ? $currentYear + 1 : $currentYear;
        $grid = PublicAvailabilityService::getMonthGrid($propriete, $y, $m);
        foreach ($grid as $date => $status) {
            if (new \DateTimeImmutable($date) < $today) continue; // ignore jours passés
            $totalDays++;
            if ($status === 'booked') $bookedDays++;
        }
    }

    if ($totalDays === 0) continue; // segment entièrement passé → on saute

    $ratio = $bookedDays / $totalDays;
    $full  = $ratio >= 0.8;
    $statusLabel = $full
        ? 'Complet'
        : ($ratio >= 0.2 ? 'Quelques dates' : 'Disponible');

    $computed[] = [
        'label'       => $seg['label'],
        'status'      => $statusLabel,
        'intent'      => $seg['intent'],
        'full'        => $full,
        'flex'        => $totalDays, // largeur proportionnelle au nombre de jours réels
    ];
}
?>
<div class="season-ribbon">
<?php foreach ($computed as $seg):
    $cls = 'ribbon-seg ribbon-' . $seg['intent'];
    if ($seg['full']) $cls .= ' ribbon-full';
?>
    <div class="<?= $cls ?>" style="flex: <?= $seg['flex'] ?>;">
        <div class="ribbon-label"><?= htmlspecialchars($seg['label']) ?></div>
        <div class="ribbon-status"><?= htmlspecialchars($seg['status']) ?></div>
    </div>
<?php endforeach; ?>
</div>
