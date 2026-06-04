<?php /** @var array $logs */ ?>
<div class="page-header">
    <h1>Logs de synchronisation iCal</h1>
    <a href="/admin/calendrier" class="btn">← Retour au calendrier</a>
</div>

<p style="font-size: 0.85rem; color: var(--stone-600); margin-bottom: 1rem;">
    Les 50 dernières exécutions du sync iCal (déclenchements automatiques par le cron toutes les 30 min + manuels depuis l'admin).
</p>

<div class="admin-card" style="padding: 0; overflow: hidden;">
    <table class="table-logs">
        <thead>
            <tr>
                <th>Début</th><th>Fin</th><th class="num" title="Créées">+</th><th class="num" title="Mises à jour">~</th><th class="num" title="Supprimées">−</th><th>Trigger</th><th>Erreurs</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr class="<?= $log['errors'] ? 'row-error' : '' ?>">
                    <td><?= htmlspecialchars($log['started_at']) ?></td>
                    <td><?= htmlspecialchars($log['ended_at'] ?? '-') ?></td>
                    <td class="num"><?= (int) $log['created'] ?></td>
                    <td class="num"><?= (int) $log['updated'] ?></td>
                    <td class="num"><?= (int) $log['deleted'] ?></td>
                    <td><span class="log-trigger log-trigger-<?= htmlspecialchars($log['triggered_by']) ?>"><?= htmlspecialchars($log['triggered_by']) ?></span></td>
                    <td><pre><?= htmlspecialchars($log['errors'] ?? '') ?></pre></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($logs)): ?>
                <tr><td colspan="7" class="empty">Aucun log pour le moment.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.table-logs { width: 100%; border-collapse: collapse; font-size: 13px; background: #fff; }
.table-logs th, .table-logs td { padding: 10px 12px; text-align: left; border-bottom: 1px solid var(--admin-border); vertical-align: top; }
.table-logs th { background: var(--olive-900); color: #fff; font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; }
.table-logs tr:last-child td { border-bottom: none; }
.table-logs tr:hover { background: var(--linen-100); }
.table-logs .num { text-align: right; font-variant-numeric: tabular-nums; font-weight: 600; color: var(--ink-700); }
.table-logs pre { white-space: pre-wrap; font-size: 11px; margin: 0; font-family: var(--font-mono); color: var(--admin-error); }
.table-logs .row-error { background: color-mix(in oklab, var(--admin-error) 5%, #fff); }
.table-logs .row-error:hover { background: color-mix(in oklab, var(--admin-error) 8%, #fff); }
.table-logs .empty { text-align: center; padding: 32px; color: var(--stone-500); font-style: italic; }
.log-trigger { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
.log-trigger-cron { background: var(--sage-200); color: var(--sage-700); }
.log-trigger-manual { background: var(--linen-200); color: var(--ink-700); }
</style>
