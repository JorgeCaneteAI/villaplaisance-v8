<?php
/**
 * Vue : calendrier annuel, 12 mois empilés verticalement,
 * chaque mois rendu avec la même logique que la vue mensuelle
 * (lanes par propriété + slots départ/gouttière/arrivée).
 * @var int $year
 * @var array $mois_data
 * @var \DateTimeImmutable $today
 * @var int $prev_year
 * @var int $next_year
 * @var ?string $propriete Filtre propriété : 'villa' (VP-BB+VP-ETE cumulés) | 'avignon' (AV-ANN) | null (toutes)
 */
use App\Services\ReservationConstants;

/* Renderer de bandeau partagé entre les 12 mois, identique à la vue mois */
$renderResa = function (array $r, bool $isCurrentMonth, string $slot = 'full'): void {
    $isAvignon = ($r['propriete'] ?? '') === 'AV-ANN';
    $bg  = $isAvignon ? '#7E57C2' : $r['couleur']['bg'];
    $txt = $isAvignon ? '#ffffff' : $r['couleur']['text'];

    $cls = ['resa', 'resa--' . $slot];
    if (!$isCurrentMonth) $cls[] = 'resa--outside';

    $slotLabel = match ($slot) {
        'morning' => 'Départ matin · ',
        'evening' => 'Arrivée soir · ',
        default   => '',
    };
    $title = ($isAvignon ? 'AVIGNON · ' . ($r['source'] ?? '') . ', ' : '')
           . (!$isCurrentMonth ? '(hors mois) ' : '')
           . $slotLabel
           . ($r['code'] ?? '') . ' · ' . ($r['nom_client'] ?? '')
           . (!empty($r['commentaire']) ? ', ' . $r['commentaire'] : '');
    ?>
    <a href="/admin/calendrier/saisie/<?= (int) $r['id'] ?>"
       class="<?= implode(' ', $cls) ?>"
       title="<?= htmlspecialchars($title) ?>"
       style="background: <?= htmlspecialchars($bg) ?>; color: <?= htmlspecialchars($txt) ?>;">
        <?php if ($slot === 'full'): ?>
            <strong><?= htmlspecialchars($r['code']) ?></strong>
            <span class="resa-sep">·</span>
            <?= htmlspecialchars($r['nom_client']) ?>
        <?php elseif ($slot === 'evening'): ?>
            <span class="resa-date"><?= (new \DateTimeImmutable($r['arrivee']))->format('d/m') ?></span>
        <?php elseif ($slot === 'morning'): ?>
            <span class="resa-date"><?= (new \DateTimeImmutable($r['depart']))->format('d/m') ?></span>
        <?php endif; ?>
    </a>
    <?php
};
?>

<div class="calendrier annee<?= $propriete ? ' is-filtered' : '' ?>">
    <header class="calendrier__nav">
        <a href="/admin/calendrier/annee/<?= $prev_year ?>" class="btn">&larr; <?= $prev_year ?></a>
        <h1><?= $year ?></h1>
        <a href="/admin/calendrier/annee/<?= $next_year ?>" class="btn"><?= $next_year ?> &rarr;</a>
    </header>

    <div class="calendrier__toolbar">
        <a href="/admin/calendrier/<?= $year ?>/<?= (int) $today->format('n') ?>" class="btn btn-primary">Vue mensuelle</a>
        <a href="/admin/calendrier/liste?mois=<?= $year ?>" class="btn">Liste</a>
        <a href="/admin/calendrier/export/pdf/annee/<?= $year ?>" class="btn">PDF année</a>
    </div>

    <div class="calendrier__filter" role="group" aria-label="Filtrer par propriété">
        <?php
        $filters = [
            null      => 'Tous',
            'villa'   => 'Villa',
            'avignon' => 'Avignon',
        ];
        foreach ($filters as $val => $label):
            $href = '/admin/calendrier/annee/' . $year . ($val ? '?propriete=' . $val : '');
            $isActive = ($propriete === $val);
        ?>
            <a href="<?= htmlspecialchars($href) ?>"
               class="btn filter-pill<?= $isActive ? ' is-active' : '' ?>"
               <?= $isActive ? 'aria-current="page"' : '' ?>>
                <?= htmlspecialchars($label) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="annee-stack">
        <?php foreach ($mois_data as $m):
            $currentMonth = (int) $m['month'];
        ?>
        <section class="annee-mois" id="mois-<?= $currentMonth ?>">
            <header class="annee-mois-head">
                <a href="/admin/calendrier/<?= $year ?>/<?= $currentMonth ?>" class="annee-mois-title">
                    <span class="annee-mois-num"><?= sprintf('%02d', $currentMonth) ?></span>
                    <span class="annee-mois-nom"><?= htmlspecialchars($m['nom']) ?></span>
                </a>
            </header>

            <table class="calendrier__grid">
                <thead>
                    <tr>
                        <?php foreach (ReservationConstants::JOURS_FR as $jour): ?>
                            <th><?= $jour ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($m['weeks'] as $week): ?>
                        <tr>
                            <?php foreach ($week as $day):
                                $isCurrent = (int) $day->format('n') === $currentMonth;
                                $key = $day->format('Y-m-d');
                                $isToday = $day->format('Y-m-d') === $today->format('Y-m-d');
                                $classes = ['cell', $isCurrent ? 'current' : 'outside'];
                                if ($isToday) $classes[] = 'today';

                                // Mapping propriété → index de lane selon le filtre actif :
                                //  - villa   : VP-BB et VP-ETE sur la même lane (maison cumulée)
                                //  - avignon : AV-ANN seule
                                //  - null    : 3 lanes (comportement historique)
                                $LANES = match ($propriete) {
                                    'villa'   => ['VP-BB' => 0, 'VP-ETE' => 0],
                                    'avignon' => ['AV-ANN' => 0],
                                    default   => ['VP-BB' => 0, 'VP-ETE' => 1, 'AV-ANN' => 2],
                                };
                                $laneCount = max($LANES) + 1;
                                $byLane = array_fill(0, $laneCount, []);
                                foreach ($m['resa_by_day'][$key] ?? [] as $r) {
                                    $l = $LANES[$r['propriete']] ?? null;
                                    if ($l === null) continue;
                                    $byLane[$l][] = $r;
                                }
                            ?>
                            <td class="<?= implode(' ', $classes) ?>">
                                <div class="day-num"><?= (int) $day->format('j') ?></div>
                                <div class="lanes lanes--<?= $laneCount ?>">
                                    <?php for ($lane = 0; $lane < $laneCount; $lane++):
                                        // Même logique que la vue mensuelle (cf. index.php).
                                        $morn = $evn = $full = null;
                                        foreach ($byLane[$lane] as $r) {
                                            if (!empty($r['is_departure']))    $morn = $r;
                                            elseif (!empty($r['is_arrival']))  $evn  = $r;
                                            else                                $full = $r;
                                        }
                                    ?>
                                    <div class="lane lane--<?= $lane ?>">
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
        </section>
        <?php endforeach; ?>
    </div>
</div>
