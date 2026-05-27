<?php
/** @var array $pages @var array $sectionCounts */
?>
<div class="page-header">
    <h1>Pages du site</h1>
</div>

<p class="text-muted mb-2" style="max-width: 65ch;">
    Pour chaque page du site, tu peux éditer ses <strong>blocs de contenu</strong> (textes, images, listes…) et ses <strong>métadonnées SEO</strong> (titre Google, description, image partage Facebook).
</p>

<?php if (empty($pages)): ?>
<div class="mail-empty">
    Aucune page enregistrée dans <code>vp_pages</code>.
</div>
<?php else: ?>
<div class="admin-card" style="padding: 0; overflow: hidden;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Page</th>
                <th>URL</th>
                <th class="text-center">Blocs</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page):
                $count = $sectionCounts[$page['slug']] ?? 0;
                $hasBlocks = $count > 0;
            ?>
            <tr>
                <td>
                    <strong style="font-family: var(--font-display); font-size: 1.05rem; color: var(--ink-900);">
                        <?= htmlspecialchars($page['title'] ?? $page['slug']) ?>
                    </strong>
                    <?php if (!empty($page['meta_title'])): ?>
                    <div class="text-sm text-muted" style="margin-top: 2px;"><?= htmlspecialchars(mb_strimwidth($page['meta_title'], 0, 70, '…')) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <code class="text-sm" style="background: var(--linen-100); padding: 2px 8px; border-radius: 3px;">/<?= htmlspecialchars($page['slug']) ?></code>
                </td>
                <td class="text-center">
                    <?php if ($hasBlocks): ?>
                    <span class="badge badge-success"><?= $count ?> bloc<?= $count > 1 ? 's' : '' ?></span>
                    <?php else: ?>
                    <span class="badge badge-warning">vide</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/admin/sections/page/<?= htmlspecialchars($page['slug']) ?>" class="btn btn-primary btn-sm">
                        <?= $hasBlocks ? 'Éditer les blocs' : 'Ajouter des blocs' ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
