<?php
declare(strict_types=1);

// ── Helpers ──────────────────────────────────────────────────────────────────
function countryFlag(string $code): string {
    $code = strtoupper($code);
    if (strlen($code) !== 2) return '🌐';
    $chars = str_split($code);
    return mb_chr(ord($chars[0]) + 127397) . mb_chr(ord($chars[1]) + 127397);
}

$countryNames = [
    'FR'=>'France','BE'=>'Belgique','CH'=>'Suisse','LU'=>'Luxembourg',
    'GB'=>'Royaume-Uni','DE'=>'Allemagne','NL'=>'Pays-Bas','IT'=>'Italie',
    'ES'=>'Espagne','PT'=>'Portugal','US'=>'États-Unis','CA'=>'Canada',
    'AU'=>'Australie','JP'=>'Japon','SE'=>'Suède','NO'=>'Norvège',
    'DK'=>'Danemark','FI'=>'Finlande','AT'=>'Autriche','PL'=>'Pologne',
    'CZ'=>'Tchéquie','RU'=>'Russie','CN'=>'Chine','IN'=>'Inde',
    'BR'=>'Brésil','MX'=>'Mexique','AR'=>'Argentine','ZA'=>'Afrique du Sud',
    'MA'=>'Maroc','TN'=>'Tunisie','DZ'=>'Algérie','SG'=>'Singapour',
    'IE'=>'Irlande','IL'=>'Israël','TR'=>'Turquie','GR'=>'Grèce',
    'HU'=>'Hongrie','RO'=>'Roumanie','HR'=>'Croatie','SK'=>'Slovaquie',
];

$trendHtml = function(array $t): string {
    if ($t['dir'] === 'flat' || $t['pct'] === 0) {
        return '<span class="trend trend-flat">= stable</span>';
    }
    $cls = $t['dir'] === 'up' ? 'trend-up' : 'trend-down';
    $arrow = $t['dir'] === 'up' ? '↑' : '↓';
    return "<span class=\"trend $cls\">$arrow {$t['pct']}% vs mois préc.</span>";
};
?>

<div class="page-header">
    <div>
        <h1>Statistiques</h1>
        <p>Données internes — fenêtre glissante 30 jours</p>
    </div>
</div>

<!-- ═══ KPIs ═══ -->
<div class="stats-cards stats-cards-5">

    <div class="stat-card">
        <div class="stat-number"><?= number_format((int)($current30['total'] ?? 0)) ?></div>
        <div class="stat-label">Pages vues <span class="stat-period">30 j</span></div>
        <div class="stat-trend"><?= $trendHtml($trendViews) ?></div>
        <div class="stat-meta">
            Aujourd'hui <?= number_format((int)($today['total'] ?? 0)) ?>
            · 7 j <?= number_format((int)($week['total'] ?? 0)) ?>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?= number_format((int)($current30['uniques'] ?? 0)) ?></div>
        <div class="stat-label">Visiteurs uniques <span class="stat-period">30 j</span></div>
        <div class="stat-trend"><?= $trendHtml($trendUniques) ?></div>
        <div class="stat-meta">Aujourd'hui <?= number_format((int)($today['uniques'] ?? 0)) ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?= $pagesPerVisitor ?></div>
        <div class="stat-label">Pages / visite</div>
        <div class="stat-trend">
            <?php if ($pagesPerVisitor >= 3): ?>
                <span class="badge badge-success">Bonne exploration</span>
            <?php elseif ($pagesPerVisitor >= 2): ?>
                <span class="badge badge-warning">Navigation moyenne</span>
            <?php else: ?>
                <span class="badge badge-danger">Visite courte</span>
            <?php endif; ?>
        </div>
        <div class="stat-meta">Engagement contenu</div>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?= $returningPct ?>%</div>
        <div class="stat-label">Visiteurs récurrents</div>
        <div class="stat-meta">
            <?= number_format($returningVisitors) ?> retours · <?= number_format($newVisitors) ?> nouveaux
        </div>
        <?php if ($totalVisitors30 > 0): ?>
        <div class="stat-split">
            <div class="stat-split-a" style="width:<?= $returningPct ?>%"></div>
            <div class="stat-split-b" style="width:<?= 100 - $returningPct ?>%"></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?= $contactRate ?>%</div>
        <div class="stat-label">Taux de contact</div>
        <div class="stat-meta">
            <?= number_format((int)($contactVisits['uniques'] ?? 0)) ?> visiteurs → /contact
        </div>
        <div class="stat-meta">Proxy intention réservation</div>
    </div>

</div>

<!-- ═══ Évolution 30 derniers jours ═══ -->
<div class="admin-card">
    <h2>Évolution — 30 derniers jours</h2>
    <?php $maxViews = max(array_column($chart, 'views') ?: [1]); ?>
    <div class="chart-bars-v" role="img" aria-label="Évolution sur 30 jours">
        <?php foreach ($chart as $i => $day):
            $vPct = $maxViews > 0 ? round($day['views'] / $maxViews * 100, 1) : 0;
            $uPct = $maxViews > 0 ? round($day['uniques'] / $maxViews * 100, 1) : 0;
        ?>
        <div class="chart-bar-v-col" title="<?= htmlspecialchars($day['label']) ?> · <?= $day['views'] ?> vues · <?= $day['uniques'] ?> uniques">
            <div class="chart-bar-v-stack">
                <div class="chart-bar-v-fill is-views" style="height:<?= $vPct ?>%"></div>
                <div class="chart-bar-v-fill is-uniques" style="height:<?= $uPct ?>%"></div>
            </div>
            <?php if ($i % 5 === 0): ?>
            <div class="chart-bar-v-tick"><?= htmlspecialchars($day['label']) ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="chart-legend">
        <span class="legend-item"><span class="legend-swatch is-views"></span> Pages vues</span>
        <span class="legend-item"><span class="legend-swatch is-uniques"></span> Visiteurs uniques</span>
    </div>
</div>

<!-- ═══ Heures de pointe + Jours actifs ═══ -->
<div class="grid-2-asym">

    <div class="admin-card">
        <h2>
            Heures de pointe
            <span class="card-meta">utile pour répondre aux demandes</span>
        </h2>
        <?php
        $maxHourly = max($hourly ?: [1]);
        $peakHour  = array_search($maxHourly, $hourly);
        ?>
        <div class="chart-bars-v chart-hourly" role="img" aria-label="Trafic par heure de la journée">
            <?php for ($h = 0; $h <= 23; $h++):
                $val = $hourly[$h];
                $pct = $maxHourly > 0 ? round($val / $maxHourly * 100, 1) : 0;
                $isPeak = ($h === $peakHour);
            ?>
            <div class="chart-bar-v-col" title="<?= $h ?>h — <?= $val ?> vues">
                <div class="chart-bar-v-stack">
                    <div class="chart-bar-v-fill <?= $isPeak ? 'is-peak' : 'is-views' ?>" style="height:<?= $pct ?>%"></div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        <div class="chart-axis">
            <?php foreach ([0,3,6,9,12,15,18,21] as $h): ?>
            <span><?= $h ?>h</span>
            <?php endforeach; ?>
        </div>
        <?php if ($maxHourly > 0): ?>
        <p class="card-footer-note">
            Pic de trafic : <strong><?= $peakHour ?>h–<?= $peakHour + 1 ?>h</strong>
            (<?= $maxHourly ?> vues) — disponibilité recommandée à cette heure.
        </p>
        <?php endif; ?>
    </div>

    <div class="admin-card">
        <h2>Jours actifs</h2>
        <?php
        $maxDow = max(array_column($dow, 'cnt') ?: [1]);
        $peakDow = array_reduce($dow, fn($c, $d) => ($d['cnt'] > ($c['cnt'] ?? 0)) ? $d : $c, ['label'=>'','cnt'=>0]);
        ?>
        <div class="chart-rows">
        <?php foreach ($dow as $d):
            $pct = $maxDow > 0 ? round($d['cnt'] / $maxDow * 100, 1) : 0;
            $isPeak = ($d['cnt'] === $maxDow && $maxDow > 0);
        ?>
        <div class="chart-row">
            <span class="chart-row-label"><?= $d['label'] ?></span>
            <div class="chart-row-bar"><span class="chart-row-fill <?= $isPeak ? 'is-peak' : '' ?>" style="width:<?= $pct ?>%"></span></div>
            <span class="chart-row-value"><?= $d['cnt'] ?></span>
        </div>
        <?php endforeach; ?>
        </div>
        <?php if ($maxDow > 0): ?>
        <p class="card-footer-note">Jour le plus actif : <strong><?= $peakDow['label'] ?></strong></p>
        <?php endif; ?>
    </div>

</div>

<!-- ═══ Sources + Géographie ═══ -->
<div class="grid-2">

    <div class="admin-card">
        <h2>Sources de trafic</h2>
        <?php
        $directHits  = (int)($directTraffic['cnt'] ?? 0);
        $refHits     = array_sum(array_column($topReferrers, 'hits'));
        $sourceTotal = $directHits + $refHits;
        ?>
        <?php if ($directHits > 0):
            $directPct = $sourceTotal > 0 ? round($directHits / $sourceTotal * 100) : 0;
        ?>
        <div class="chart-rows">
            <div class="chart-row">
                <span class="chart-row-label">Direct / bookmark</span>
                <div class="chart-row-bar"><span class="chart-row-fill is-info" style="width:<?= $directPct ?>%"></span></div>
                <span class="chart-row-value"><?= number_format($directHits) ?> · <?= $directPct ?>%</span>
            </div>
        </div>
        <?php endif; ?>
        <?php if (empty($topReferrers)): ?>
            <p class="text-muted mt-2">Aucune source référente détectée.</p>
        <?php else: ?>
        <table class="admin-table mt-2">
            <thead><tr><th>Source</th><th class="cell-right">Visites</th><th class="cell-right">%</th></tr></thead>
            <tbody>
            <?php foreach ($topReferrers as $r):
                $domain = parse_url($r['referrer'], PHP_URL_HOST) ?: $r['referrer'];
                $pct = $sourceTotal > 0 ? round((int)$r['hits'] / $sourceTotal * 100) : 0;
            ?>
            <tr>
                <td class="cell-truncate" title="<?= htmlspecialchars($r['referrer']) ?>"><?= htmlspecialchars($domain) ?></td>
                <td class="cell-right text-strong"><?= number_format((int)$r['hits']) ?></td>
                <td class="cell-right text-muted"><?= $pct ?>%</td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="admin-card">
        <h2>Origines géographiques</h2>
        <?php if (empty($countryStats)): ?>
            <p class="text-muted">Pas encore de données. Les prochaines visites seront géolocalisées.</p>
        <?php else: ?>
        <?php $countryTotal = array_sum(array_column($countryStats, 'cnt')); ?>
        <div class="chart-rows">
        <?php foreach ($countryStats as $cs):
            $code = strtoupper($cs['country'] ?? '');
            $pct  = $countryTotal > 0 ? round((int)$cs['cnt'] / $countryTotal * 100) : 0;
            $name = $countryNames[$code] ?? $code;
        ?>
        <div class="chart-row">
            <span class="chart-row-label"><?= countryFlag($code) ?> <?= htmlspecialchars($name) ?></span>
            <div class="chart-row-bar"><span class="chart-row-fill" style="width:<?= $pct ?>%"></span></div>
            <span class="chart-row-value"><?= number_format((int)$cs['cnt']) ?> · <?= $pct ?>%</span>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- ═══ Top pages + Articles populaires ═══ -->
<div class="grid-2">

    <div class="admin-card">
        <h2>Pages les plus vues</h2>
        <?php if (empty($topPages)): ?>
            <p class="text-muted">Aucune donnée.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead><tr><th>Page</th><th class="cell-right">Vues</th><th class="cell-right">%</th></tr></thead>
            <tbody>
            <?php foreach ($topPages as $p):
                $pct = $totalViews30 > 0 ? round((int)$p['views'] / $totalViews30 * 100) : 0;
            ?>
            <tr>
                <td class="cell-truncate" title="<?= htmlspecialchars($p['page_url']) ?>"><?= htmlspecialchars($p['page_url']) ?></td>
                <td class="cell-right text-strong"><?= number_format((int)$p['views']) ?></td>
                <td class="cell-right text-muted"><?= $pct ?>%</td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="admin-card">
        <h2>Articles populaires</h2>
        <?php if (empty($topArticles)): ?>
            <p class="text-muted">Aucune donnée.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead><tr><th>Article</th><th>Type</th><th class="cell-right">Vues</th></tr></thead>
            <tbody>
            <?php foreach ($topArticles as $a): ?>
            <tr>
                <td class="cell-truncate"><?= htmlspecialchars($a['title']) ?></td>
                <td><span class="badge badge-<?= $a['type'] === 'journal' ? 'info' : 'success' ?>"><?= htmlspecialchars($a['type']) ?></span></td>
                <td class="cell-right text-strong"><?= number_format((int)$a['views']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>

<!-- ═══ Langues + Appareils ═══ -->
<div class="grid-2">

    <div class="admin-card">
        <h2>Langues de navigation</h2>
        <?php if (empty($langStats)): ?>
            <p class="text-muted">Aucune donnée.</p>
        <?php else:
            $langTotal  = array_sum(array_column($langStats, 'cnt'));
            $langClass  = ['fr' => '', 'en' => 'is-warn', 'es' => 'is-info'];
            $langLabels = ['fr' => 'Français', 'en' => 'Anglais', 'es' => 'Espagnol'];
        ?>
        <div class="chart-rows">
        <?php foreach ($langStats as $ls):
            $pct   = $langTotal > 0 ? round((int)$ls['cnt'] / $langTotal * 100) : 0;
            $cls   = $langClass[$ls['lang']] ?? '';
            $label = $langLabels[$ls['lang']] ?? strtoupper($ls['lang']);
        ?>
        <div class="chart-row">
            <span class="chart-row-label text-strong"><?= $label ?></span>
            <div class="chart-row-bar"><span class="chart-row-fill <?= $cls ?>" style="width:<?= $pct ?>%"></span></div>
            <span class="chart-row-value"><?= number_format((int)$ls['cnt']) ?> · <?= $pct ?>%</span>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="admin-card">
        <h2>Appareils</h2>
        <?php
        $desktopPct = $deviceTotal > 0 ? round(($deviceMap['desktop'] ?? 0) / $deviceTotal * 100) : 0;
        $mobilePct  = $deviceTotal > 0 ? round(($deviceMap['mobile']  ?? 0) / $deviceTotal * 100) : 0;
        $tabletPct  = $deviceTotal > 0 ? round(($deviceMap['tablet']  ?? 0) / $deviceTotal * 100) : 0;
        $deviceInfo = [
            ['label' => 'Desktop',  'pct' => $desktopPct, 'cnt' => $deviceMap['desktop'] ?? 0, 'cls' => ''],
            ['label' => 'Mobile',   'pct' => $mobilePct,  'cnt' => $deviceMap['mobile']  ?? 0, 'cls' => 'is-warn'],
            ['label' => 'Tablette', 'pct' => $tabletPct,  'cnt' => $deviceMap['tablet']  ?? 0, 'cls' => 'is-info'],
        ];
        ?>
        <div class="chart-rows">
        <?php foreach ($deviceInfo as $di): ?>
        <div class="chart-row">
            <span class="chart-row-label text-strong"><?= $di['label'] ?></span>
            <div class="chart-row-bar"><span class="chart-row-fill <?= $di['cls'] ?>" style="width:<?= $di['pct'] ?>%"></span></div>
            <span class="chart-row-value"><?= number_format($di['cnt']) ?> · <?= $di['pct'] ?>%</span>
        </div>
        <?php endforeach; ?>
        </div>
        <p class="card-footer-note">
            <?php if ($mobilePct > 60): ?>
                Majorité mobile — vérifier le rendu sur petits écrans.
            <?php elseif ($desktopPct > 60): ?>
                Majorité desktop — audience active en journée de travail.
            <?php else: ?>
                Mix équilibré mobile / desktop.
            <?php endif; ?>
        </p>
    </div>

</div>
