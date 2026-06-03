<?php
/** @var array $pages @var array $sectionCounts */
?>
<div class="page-header">
    <div>
        <h1>Pages du site</h1>
        <p>Édite les <strong>blocs de contenu</strong> (textes, images, listes) et les <strong>métadonnées SEO</strong> (titre Google, description, image partage) de chaque page.</p>
    </div>
</div>

<?php if (empty($pages)): ?>
<div class="empty-state">
    <div class="empty-icon">▦</div>
    Aucune page enregistrée dans <code class="info-mono">vp_pages</code>.
</div>
<?php else: ?>
<div class="admin-card admin-card--flush">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Page</th>
                <th>URL</th>
                <th class="cell-center">Blocs</th>
                <th class="cell-center">SEO</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page):
                $count = $sectionCounts[$page['slug']] ?? 0;
                $hasBlocks = $count > 0;
                $hasMeta = !empty($page['meta_title']) && !empty($page['meta_desc']);
                $publicUrl = '/' . ($page['slug'] === 'accueil' ? '' : $page['slug']);
            ?>
            <tr>
                <td>
                    <strong class="text-strong"><?= htmlspecialchars($page['title'] ?? $page['slug']) ?></strong>
                    <?php if (!empty($page['meta_title'])): ?>
                    <div class="text-sm text-muted"><?= htmlspecialchars(mb_strimwidth($page['meta_title'], 0, 80, '…')) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <code class="badge badge-meta"><?= htmlspecialchars($publicUrl) ?></code>
                </td>
                <td class="cell-center">
                    <?php if ($hasBlocks): ?>
                        <span class="badge badge-success"><?= $count ?></span>
                    <?php else: ?>
                        <span class="badge badge-warning">vide</span>
                    <?php endif; ?>
                </td>
                <td class="cell-center">
                    <?php if ($hasMeta): ?>
                        <span class="badge badge-success">✓</span>
                    <?php else: ?>
                        <span class="badge badge-warning">à compléter</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="/admin/sections/page/<?= htmlspecialchars($page['slug']) ?>" class="btn btn-sm btn-primary">
                            <?= $hasBlocks ? 'Éditer' : 'Ajouter' ?>
                        </a>
                        <a href="<?= htmlspecialchars($publicUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm" title="Voir la page publique">
                            <svg class="btn-icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6M10 14 21 3"/></svg>
                            Voir
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
