<?php
use App\Services\ReservationConstants;
/** @var int $year @var int $month @var string $mois_nom @var array $weeks @var array $resa_by_day */
/** @var array $couleurs @var \DateTimeImmutable $today */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Réservations, <?= htmlspecialchars($mois_nom) ?> <?= $year ?></title>
<style>
@page { size: A4 landscape; margin: 8mm; }
* { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; color-adjust: exact; }
body { font-family: Helvetica, Arial, sans-serif; font-size: 10pt; margin: 0; padding: 0; color: #222; }
h1 { background: #2C2C2A; color: #fff; padding: 8px 12px; margin: 0 0 8px; font-size: 14pt; font-weight: 700; }

table.cal { width: 100%; border-collapse: collapse; table-layout: fixed; }
table.cal th {
    background: #2C2C2A; color: #fff; padding: 5px 4px;
    font-size: 8.5pt; font-weight: 700; text-align: center;
    letter-spacing: 0.5px;
}
table.cal th.weekend { background: #3d3d3a; }
table.cal td {
    border: 0.5px solid #cfcfcf;
    vertical-align: top;
    height: 26mm;
    padding: 3px 0 6px 0;
    overflow: hidden;
    position: relative;
}
table.cal td.outside { background: #f5f5f5; color: #bbb; }
table.cal td.weekend { background: #fafaf8; }
table.cal td.today { background: #eef0ff; }

.day-num { font-weight: 700; font-size: 9pt; padding: 0 5px 2px; }
table.cal td.outside .day-num { color: #bbb; }

/* Lanes empilées : 3 lignes fixes (VP-BB / VP-ETE / AV-ANN).
   Même logique que la vue web, dimensions adaptées au format A4 paysage. */
.lanes {
    display: grid;
    grid-template-rows: repeat(3, 18px);
    gap: 2px;
}
.lane {
    display: grid;
    /* Mêmes proportions que la vue web :
       46% matin (départ avant 11h) | 20% gouttière | 34% soir (arrivée après 16h). */
    grid-template-columns: 46% 20% 34%;
    min-height: 18px;
}
.slot { min-height: 18px; overflow: hidden; }
.slot--day { background: transparent; }

.resa {
    display: flex;
    align-items: center;
    gap: 3px;
    padding: 0 5px;
    height: 18px;
    font-size: 7.5pt;
    font-weight: 600;
    text-decoration: none;
    line-height: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-variant-numeric: tabular-nums;
}
.resa--full {
    grid-column: 1 / -1;
}
.resa--outside { opacity: 0.45; filter: saturate(0.6); }

.resa-sep { opacity: 0.6; padding: 0 1px; }

/* Slots étroits : matin (départ) aligné à droite, soir (arrivée) à gauche,
   les deux convergent vers la gouttière centrale. */
.slot--morning .resa { justify-content: flex-end; border-radius: 0 2px 2px 0; padding: 0 4px; }
.slot--evening .resa { justify-content: flex-start; border-radius: 2px 0 0 2px; padding: 0 4px; }

/* Radius en fin de semaine (lundi gauche, dimanche droit) pour fermer les segments. */
table.cal td:first-child .resa--full { border-top-left-radius: 2px; border-bottom-left-radius: 2px; }
table.cal td:last-child .resa--full { border-top-right-radius: 2px; border-bottom-right-radius: 2px; }
table.cal td:first-child .slot--morning .resa,
table.cal td:first-child .slot--evening .resa { border-top-left-radius: 2px; border-bottom-left-radius: 2px; }
table.cal td:last-child .slot--morning .resa,
table.cal td:last-child .slot--evening .resa { border-top-right-radius: 2px; border-bottom-right-radius: 2px; }

.legend {
    margin-top: 8px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
    font-size: 8pt;
}
.legend .item {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 3px 10px; border-radius: 3px; font-weight: 600;
}
.legend .arrivee-depart {
    margin-left: auto;
    color: #666;
    font-size: 7.5pt;
    font-style: italic;
}

.actions { margin-bottom: 10px; }
.actions button, .actions a {
    padding: 6px 12px; font-size: 11pt;
    background: #2C2C2A; color: #fff;
    border: none; border-radius: 4px;
    cursor: pointer; text-decoration: none; display: inline-block;
}
.actions a { background: #888; }
@media print {
    .actions { display: none; }
    body { font-size: 9pt; }
}
</style>
</head>
<body>
<h1>RÉSERVATIONS, <?= htmlspecialchars(strtoupper($mois_nom)) ?> <?= $year ?></h1>

<div class="actions">
    <button onclick="window.print()">Imprimer (Cmd+P)</button>
    <a href="/admin/calendrier/<?= $year ?>/<?= $month ?>">Retour</a>
</div>

<?php
/**
 * Rendu d'une résa dans un slot du calendrier print.
 * Aligné sur la vue web (index.php), même sémantique :
 *   - 'full'    : nuit pleine, code + nom client
 *   - 'morning' : jour de départ, demi-cellule gauche, date dd/mm
 *   - 'evening' : jour d'arrivée, demi-cellule droite, date dd/mm
 */
$renderResa = function (array $r, bool $isCurrentMonth, string $slot = 'full'): void {
    $bg  = $r['couleur']['bg']   ?? '#888';
    $txt = $r['couleur']['text'] ?? '#fff';
    $cls = ['resa', 'resa--' . $slot];
    if (!$isCurrentMonth) $cls[] = 'resa--outside';
    ?>
    <span class="<?= implode(' ', $cls) ?>"
          style="background: <?= htmlspecialchars($bg) ?>; color: <?= htmlspecialchars($txt) ?>;">
        <?php if ($slot === 'full'): ?>
            <strong><?= htmlspecialchars($r['code']) ?></strong>
            <span class="resa-sep">·</span>
            <?= htmlspecialchars($r['nom_client']) ?>
        <?php elseif ($slot === 'evening'): ?>
            <?= (new \DateTimeImmutable($r['arrivee']))->format('d/m') ?>
        <?php elseif ($slot === 'morning'): ?>
            <?= (new \DateTimeImmutable($r['depart']))->format('d/m') ?>
        <?php endif; ?>
    </span>
    <?php
};
?>

<table class="cal">
    <thead>
        <tr>
            <?php foreach (ReservationConstants::JOURS_FR as $i => $jour): ?>
                <th class="<?= $i >= 5 ? 'weekend' : '' ?>"><?= htmlspecialchars($jour) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($weeks as $week): ?>
            <tr>
                <?php foreach ($week as $i => $day): ?>
                    <?php
                    $isCurrent = (int) $day->format('n') === $month;
                    $isWeekend = $i >= 5;
                    $isToday   = $day->format('Y-m-d') === $today->format('Y-m-d');
                    $key = $day->format('Y-m-d');

                    $classes = [];
                    if (!$isCurrent) $classes[] = 'outside';
                    elseif ($isWeekend) $classes[] = 'weekend';
                    if ($isToday) $classes[] = 'today';

                    // Ranger les résas du jour par lane (= propriété), même ordre
                    // que la vue web pour la cohérence visuelle horizontale.
                    $LANES = ['VP-BB' => 0, 'VP-ETE' => 1, 'AV-ANN' => 2];
                    $byLane = [0 => [], 1 => [], 2 => []];
                    foreach ($resa_by_day[$key] ?? [] as $r) {
                        $l = $LANES[$r['propriete']] ?? null;
                        if ($l === null) continue;
                        $byLane[$l][] = $r;
                    }
                    ?>
                    <td class="<?= implode(' ', $classes) ?>">
                        <div class="day-num"><?= (int) $day->format('j') ?></div>
                        <div class="lanes">
                            <?php for ($lane = 0; $lane < 3; $lane++):
                                // Classer les résas de la lane en 3 cas distincts :
                                // - is_departure → slot morning (matin, départ)
                                // - is_arrival   → slot evening (soir, arrivée)
                                // - sinon        → slot full (nuit pleine)
                                $morn = $evn = $full = null;
                                foreach ($byLane[$lane] as $r) {
                                    if (!empty($r['is_departure']))    $morn = $r;
                                    elseif (!empty($r['is_arrival']))  $evn  = $r;
                                    else                                $full = $r;
                                }
                            ?>
                            <div class="lane">
                                <?php if ($full): ?>
                                    <?php $renderResa($full, $isCurrent, 'full'); ?>
                                <?php elseif ($morn || $evn): ?>
                                    <div class="slot slot--morning">
                                        <?php if ($morn) $renderResa($morn, $isCurrent, 'morning'); ?>
                                    </div>
                                    <div class="slot slot--day"></div>
                                    <div class="slot slot--evening">
                                        <?php if ($evn) $renderResa($evn, $isCurrent, 'evening'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="legend">
    <?php foreach ($couleurs as $src => $c): ?>
        <span class="item" style="background: <?= htmlspecialchars($c['bg']) ?>; color: <?= htmlspecialchars($c['text']) ?>;">
            ● <?= htmlspecialchars($src) ?>
        </span>
    <?php endforeach; ?>
    <span class="arrivee-depart">
        Demi-cellule gauche = départ matin (avant 11h) · Demi-cellule droite = arrivée soir (après 16h)
    </span>
</div>
</body>
</html>
