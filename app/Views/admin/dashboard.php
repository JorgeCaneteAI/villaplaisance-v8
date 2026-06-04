<?php
/* ──────────────────────────────────────────────────────────────────────────
   Dashboard, vitrine de la refonte UI V2 (2026-06-03)
   Aucune logique métier ici, juste de l'affichage des données préparées par
   DashboardController::index().
   ────────────────────────────────────────────────────────────────────────── */
?>
<div class="page-header">
    <div>
        <h1>Tableau de bord</h1>
        <p>Vue d’ensemble du site Villa Plaisance</p>
    </div>
</div>

<!-- ═══ Cartes de stats ═══ -->
<div class="stats-cards stats-cards-6">
    <a href="/admin/articles" class="stat-card stat-card-link">
        <div class="stat-number"><?= $stats['articles_published'] ?></div>
        <div class="stat-label">Articles publiés</div>
        <div class="stat-meta"><?= $stats['articles_draft'] ?> brouillon<?= $stats['articles_draft'] > 1 ? 's' : '' ?></div>
    </a>
    <a href="/admin/pages" class="stat-card stat-card-link">
        <div class="stat-number"><?= $stats['pages'] ?></div>
        <div class="stat-label">Pages CMS</div>
    </a>
    <a href="/admin/sections" class="stat-card stat-card-link" title="Recherche full-text dans tous les blocs">
        <div class="stat-number"><?= $stats['sections'] ?></div>
        <div class="stat-label">Blocs de contenu</div>
        <div class="stat-meta">recherche full-text</div>
    </a>
    <a href="/admin/messages" class="stat-card stat-card-link">
        <div class="stat-number"><?= $stats['messages_unread'] ?></div>
        <div class="stat-label">Messages non lus</div>
        <div class="stat-meta"><?= $stats['messages'] ?> total</div>
    </a>
    <a href="/admin/avis" class="stat-card stat-card-link">
        <div class="stat-number"><?= $stats['reviews'] ?></div>
        <div class="stat-label">Avis clients</div>
    </a>
    <a href="/admin/media" class="stat-card stat-card-link">
        <div class="stat-number"><?= $stats['media'] ?></div>
        <div class="stat-label">Médias</div>
    </a>
</div>

<!-- ═══ Grid Santé SEO + Traductions ═══ -->
<div class="grid-2">

    <!-- Santé SEO -->
    <div class="admin-card">
        <h2>
            <svg class="card-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            Santé SEO
        </h2>
        <div class="dash-seo-grid">
            <?php
            $seoBars = [
                ['label' => 'Meta title + desc',  'pct' => $seo['meta_pct'],  'count' => $seo['articles_with_meta']  . '/' . $stats['articles_total'] . ' articles'],
                ['label' => 'Image de couverture','pct' => $seo['cover_pct'], 'count' => $seo['articles_with_cover'] . '/' . $stats['articles_total'] . ' articles'],
                ['label' => 'Description GSO',    'pct' => $seo['gso_pct'],   'count' => $seo['articles_with_gso']   . '/' . $stats['articles_total'] . ' articles'],
                ['label' => 'Pages avec meta',    'pct' => $stats['pages'] > 0 ? round($seo['pages_with_meta'] / $stats['pages'] * 100) : 0, 'count' => $seo['pages_with_meta'] . '/' . $stats['pages'] . ' pages'],
            ];
            foreach ($seoBars as $bar):
                $level = $bar['pct'] >= 80 ? 'is-ok' : ($bar['pct'] >= 50 ? 'is-warn' : 'is-bad');
            ?>
            <div class="dash-seo-item">
                <div class="dash-seo-info">
                    <span><?= $bar['label'] ?></span>
                    <strong><?= $bar['pct'] ?>%</strong>
                </div>
                <div class="dash-seo-bar">
                    <div class="dash-seo-fill <?= $level ?>" style="width:<?= $bar['pct'] ?>%;"></div>
                </div>
                <div class="dash-seo-count"><?= $bar['count'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Traductions -->
    <div class="admin-card">
        <h2>
            <svg class="card-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            Couverture traductions
        </h2>
        <?php foreach (['en' => 'Anglais', 'es' => 'Espagnol'] as $code => $label): ?>
        <div class="dash-lang-block">
            <div class="dash-lang-header"><?= strtoupper($code) ?>, <?= $label ?></div>

            <div class="dash-lang-row">
                <span class="dash-lang-tag">Articles</span>
                <div class="dash-seo-bar">
                    <div class="dash-seo-fill <?= ($translations[$code]['pct'] ?? 0) >= 80 ? 'is-ok' : 'is-warn' ?>" style="width:<?= $translations[$code]['pct'] ?? 0 ?>%;"></div>
                </div>
                <span class="dash-lang-pct"><?= $translations[$code]['pct'] ?? 0 ?>%</span>
                <span class="dash-lang-count"><?= $translations[$code]['filled'] ?? 0 ?>/<?= $translations[$code]['total'] ?? 0 ?></span>
            </div>

            <div class="dash-lang-row">
                <span class="dash-lang-tag">Blocs</span>
                <div class="dash-seo-bar">
                    <div class="dash-seo-fill <?= ($translations[$code . '_sections']['pct'] ?? 0) >= 80 ? 'is-ok' : 'is-warn' ?>" style="width:<?= $translations[$code . '_sections']['pct'] ?? 0 ?>%;"></div>
                </div>
                <span class="dash-lang-pct"><?= $translations[$code . '_sections']['pct'] ?? 0 ?>%</span>
                <span class="dash-lang-count"><?= $translations[$code . '_sections']['filled'] ?? 0 ?>/<?= $translations[$code . '_sections']['total'] ?? 0 ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ═══ Grid Redirections + Fichiers SEO ═══ -->
<div class="grid-2-asym">

    <!-- Redirections 301 -->
    <div class="admin-card">
        <h2>
            <svg class="card-icon" viewBox="0 0 24 24"><path d="M3 7v6a4 4 0 0 0 4 4h14"/><path d="m18 13 3-3-3-3"/></svg>
            Redirections
            <a href="/admin/redirects" class="card-action">Gérer</a>
        </h2>
        <?php if (empty($redirects)): ?>
            <p class="text-muted">Aucune redirection configurée.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ancienne URL</th>
                    <th>Nouvelle URL</th>
                    <th>Code</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($redirects as $r): ?>
                <tr<?= $r['active'] ? '' : ' class="row-muted"' ?>>
                    <td class="text-mono"><?= htmlspecialchars($r['url_from']) ?></td>
                    <td class="text-mono"><?= htmlspecialchars($r['url_to']) ?></td>
                    <td><span class="badge badge-info"><?= $r['status_code'] ?></span></td>
                    <td>
                        <?php if ($r['active']): ?>
                            <span class="badge badge-success">Actif</span>
                        <?php else: ?>
                            <span class="badge">Inactif</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Fichiers SEO -->
    <div class="admin-card">
        <h2>
            <svg class="card-icon" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            Fichiers SEO
            <a href="/admin/seo-files" class="card-action">Gérer</a>
        </h2>
        <div class="dash-file-list">
            <?php foreach ($seoFiles as $name => $info): ?>
            <div class="dash-file-row">
                <div class="dash-file-name">
                    <?php if ($info['exists']): ?>
                        <svg class="dash-check is-ok" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg class="dash-check is-bad" viewBox="0 0 24 24"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                    <?php endif; ?>
                    <code><?= $name ?></code>
                </div>
                <div class="dash-file-meta">
                    <span class="badge badge-<?= $info['type'] === 'Éditable' ? 'info' : ($info['type'] === 'Auto-généré' ? 'success' : 'meta') ?>"><?= $info['type'] ?></span>
                    <?php if ($info['url']): ?>
                        <a href="<?= $info['url'] ?>" target="_blank" rel="noopener" class="btn btn-sm">Voir</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="dash-section-divider">
            <h3>Contenu additionnel</h3>
            <div class="dash-file-row">
                <div class="dash-file-name">
                    <svg class="dash-check is-ok" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>FAQ structurées</span>
                </div>
                <strong class="text-strong"><?= $stats['faq'] ?></strong>
            </div>
            <div class="dash-file-row">
                <div class="dash-file-name">
                    <svg class="dash-check is-ok" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Pièces / blocs</span>
                </div>
                <strong class="text-strong"><?= $stats['pieces'] ?></strong>
            </div>
        </div>
    </div>
</div>

<!-- ═══ Articles à compléter ═══ -->
<?php if (!empty($articlesAttention)): ?>
<div class="admin-card">
    <h2>
        <svg class="card-icon" viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
        Articles à compléter
        <span class="card-meta"><?= count($articlesAttention) ?> article<?= count($articlesAttention) > 1 ? 's' : '' ?></span>
    </h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Type</th>
                <th>Meta title</th>
                <th>Meta desc</th>
                <th>Cover</th>
                <th>GSO</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articlesAttention as $art): ?>
            <tr>
                <td class="text-strong"><?= htmlspecialchars($art['title']) ?></td>
                <td><span class="badge badge-meta"><?= $art['type'] ?></span></td>
                <?php foreach (['meta_title', 'meta_desc', 'cover_image', 'gso_desc'] as $field): ?>
                <td class="cell-center">
                    <?php if (!empty($art[$field])): ?>
                        <svg class="dash-check is-ok" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg class="dash-check is-bad" viewBox="0 0 24 24"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                    <?php endif; ?>
                </td>
                <?php endforeach; ?>
                <td><a href="/admin/articles/edit/<?= $art['id'] ?>" class="btn btn-sm btn-primary">Compléter</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- ═══ Derniers messages ═══ -->
<div class="admin-card">
    <h2>
        <svg class="card-icon" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
        Derniers messages
        <?php if ($stats['messages_unread'] > 0): ?>
            <span class="badge badge-warning"><?= $stats['messages_unread'] ?> non lu<?= $stats['messages_unread'] > 1 ? 's' : '' ?></span>
        <?php endif; ?>
        <a href="/admin/messages" class="card-action">Tout voir</a>
    </h2>
    <?php if (empty($recentMessages)): ?>
        <p class="text-muted">Aucun message pour le moment.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>De</th>
                <th>Email</th>
                <th>Sujet</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentMessages as $msg): ?>
            <tr>
                <td class="text-strong"><a href="/admin/messages/<?= $msg['id'] ?>"><?= htmlspecialchars($msg['name']) ?></a></td>
                <td class="text-muted"><?= htmlspecialchars($msg['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($msg['subject'] ?: '(sans sujet)') ?></td>
                <td class="text-muted"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></td>
                <td>
                    <?php if (empty($msg['read_at'])): ?>
                        <span class="badge badge-warning">Non lu</span>
                    <?php else: ?>
                        <span class="badge">Lu</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
