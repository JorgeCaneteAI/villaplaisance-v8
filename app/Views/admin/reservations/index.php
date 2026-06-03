<?php
/**
 * Vue : calendrier mensuel des réservations.
 * @var int $year
 * @var int $month
 * @var string $mois_nom
 * @var array $weeks
 * @var array $resa_by_day
 * @var array $couleurs
 * @var \DateTimeImmutable $today
 * @var int $prev_year @var int $prev_month @var int $next_year @var int $next_month
 */
use App\Services\ReservationConstants;
?>
<div class="calendrier">
    <header class="calendrier__nav">
        <a href="/admin/calendrier/<?= $prev_year ?>/<?= $prev_month ?>" class="btn">&larr; <?= ReservationConstants::MOIS_FR[$prev_month] ?></a>
        <h1><?= htmlspecialchars($mois_nom) ?> <?= $year ?></h1>
        <a href="/admin/calendrier/<?= $next_year ?>/<?= $next_month ?>" class="btn"><?= ReservationConstants::MOIS_FR[$next_month] ?> &rarr;</a>
    </header>

    <div class="calendrier__toolbar">
        <a href="/admin/calendrier/saisie" class="btn btn-primary">+ Nouvelle résa</a>
        <a href="/admin/calendrier/liste" class="btn">Liste</a>
        <a href="/admin/calendrier/annee/<?= $year ?>" class="btn">Vue annuelle</a>
        <a href="/admin/calendrier/print/<?= $year ?>/<?= $month ?>" class="btn">Imprimer</a>
        <a href="/admin/calendrier/export/pdf/<?= $year ?>/<?= $month ?>" class="btn">PDF</a>
    </div>

    <?php
    $csrf = $_SESSION['csrf_token'] ?? ($_SESSION['csrf_token'] = bin2hex(random_bytes(32)));
    $fmtAge = function(float $m): string {
        if ($m < 60) return round($m) . ' min';
        if ($m < 1440) return round($m / 60, 1) . ' h';
        return round($m / 1440) . ' j';
    };
    $widgetClass = 'is-unknown';
    $widgetIcon  = '◌';
    $widgetTitle = 'Synchronisation iCal';
    $widgetStatus = 'Aucune synchronisation enregistrée pour le moment.';
    if (!empty($last_sync_at)) {
        $syncAge = max(0, (time() - strtotime($last_sync_at)) / 60);
        $when = date('d/m/Y à H:i', strtotime($last_sync_at));
        if ((int) $last_sync_ok === 0) {
            $widgetClass = 'is-error'; $widgetIcon = '✕';
            $widgetTitle = 'Dernière sync en erreur';
            $widgetStatus = 'Il y a <strong>' . $fmtAge($syncAge) . '</strong> · ' . $when;
        } elseif ($syncAge < 60) {
            $widgetClass = ''; $widgetIcon = '✓';
            $widgetTitle = 'Calendriers à jour';
            $widgetStatus = 'Dernière sync il y a <strong>' . $fmtAge($syncAge) . '</strong> · ' . $when;
        } elseif ($syncAge < 1440) {
            $widgetClass = 'is-warn'; $widgetIcon = '⌛';
            $widgetTitle = 'Sync ancienne';
            $widgetStatus = 'Dernière sync il y a <strong>' . $fmtAge($syncAge) . '</strong> · ' . $when;
        } else {
            $widgetClass = 'is-error'; $widgetIcon = '!';
            $widgetTitle = 'Sync trop ancienne';
            $widgetStatus = 'Dernière sync il y a <strong>' . $fmtAge($syncAge) . '</strong> · ' . $when;
        }
    }
    ?>
    <div class="sync-widget <?= $widgetClass ?>">
        <div class="sync-widget-icon" aria-hidden="true"><?= $widgetIcon ?></div>
        <div class="sync-widget-info">
            <div class="sync-widget-title"><?= htmlspecialchars($widgetTitle) ?></div>
            <div class="sync-widget-status"><?= $widgetStatus ?></div>
            <span class="sync-widget-note">Sync automatique toutes les 30 minutes (Airbnb + Booking).</span>
        </div>
        <div class="sync-widget-actions">
            <form method="post" action="/admin/calendrier/sync">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <button type="submit" class="btn btn-primary">Sync maintenant</button>
            </form>
            <a href="/admin/calendrier/logs" class="btn">Voir les logs</a>
        </div>
    </div>

    <table class="calendrier__grid">
        <thead>
            <tr>
                <?php foreach (ReservationConstants::JOURS_FR as $jour): ?>
                    <th><?= $jour ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($weeks as $week): ?>
                <tr>
                    <?php foreach ($week as $day): ?>
                        <?php
                        $isCurrent = (int) $day->format('n') === $month;
                        $key = $day->format('Y-m-d');
                        $isToday = $day->format('Y-m-d') === $today->format('Y-m-d');
                        $classes = ['cell', $isCurrent ? 'current' : 'outside'];
                        if ($isToday) $classes[] = 'today';
                        ?>
                        <td class="<?= implode(' ', $classes) ?>">
                            <div class="day-num"><?= (int) $day->format('j') ?></div>
                            <?php if ($isCurrent && isset($resa_by_day[$key])): ?>
                                <?php foreach ($resa_by_day[$key] as $r): ?>
                                    <a href="/admin/calendrier/saisie/<?= (int) $r['id'] ?>"
                                       class="resa"
                                       title="<?= htmlspecialchars($r['commentaire'] ?? '') ?>"
                                       style="background: <?= htmlspecialchars($r['couleur']['bg']) ?>; color: <?= htmlspecialchars($r['couleur']['text']) ?>;">
                                        <strong><?= htmlspecialchars($r['code']) ?></strong>
                                        &middot; <?= htmlspecialchars($r['nom_client']) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php /* Styles déplacés dans /assets/css/admin.css (section 17.3) */ ?>
