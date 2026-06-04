<?php
/**
 * Admin V8, liste des FAQ (vp_faq).
 *
 * @var array  $faqs
 * @var array  $slugs        Liste des page_slugs distinctes
 * @var string $langFilter
 * @var string $slugFilter
 * @var array  $langLabels
 * @var string $csrf
 */
?>

<div class="page-header">
    <h1>FAQ</h1>
</div>

<div class="vp-pc-toolbar">
    <form method="GET" action="/admin/faq" class="vp-pc-filters">
        <label>
            <span class="text-sm text-muted">Langue</span>
            <select name="lang" onchange="this.form.submit()">
                <?php foreach (SUPPORTED_LANGS as $l): ?>
                <option value="<?= $l ?>" <?= $langFilter === $l ? 'selected' : '' ?>><?= $langLabels[$l] ?? strtoupper($l) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span class="text-sm text-muted">Page</span>
            <select name="page_slug" onchange="this.form.submit()">
                <option value="" <?= $slugFilter === '' ? 'selected' : '' ?>>Toutes</option>
                <?php foreach ($slugs as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>" <?= $slugFilter === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </form>

    <form method="POST" action="/admin/faq/create" class="vp-pc-create">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="text" name="page_slug" placeholder="page_slug (ex. chambres-d-hotes)" required style="padding: 0.4rem 0.6rem; border: 1px solid var(--admin-border); border-radius: var(--admin-radius);">
        <button type="submit" class="btn btn-primary btn-sm">+ Nouvelle FAQ</button>
    </form>
</div>

<?php if (empty($faqs)): ?>
<p class="text-muted mt-2">Aucune FAQ pour ces filtres.</p>
<?php else: ?>

<?php
$currentSlug = '';
foreach ($faqs as $f):
    if ($f['page_slug'] !== $currentSlug):
        $currentSlug = $f['page_slug'];
?>
<h2 class="vp-pc-group-title"><?= htmlspecialchars($currentSlug) ?></h2>
<?php endif; ?>

<div class="vp-pc-row" style="grid-template-columns: 40px 1fr auto;">
    <div class="vp-pc-thumb" style="width: 40px; height: 40px; background: transparent;">
        <span class="vp-pc-pos" style="display:flex; align-items:center; justify-content:center; height: 100%; font-size: 0.9rem;">#<?= (int)$f['position'] ?></span>
    </div>
    <div class="vp-pc-info">
        <div class="vp-pc-name">
            <strong style="font-family: var(--font-sans); font-size: 0.95rem; font-weight: 500;"><?= htmlspecialchars($f['question'] ?: '(question vide)') ?></strong>
            <?php if (!$f['active']): ?><span class="badge" style="background: var(--linen-200); color: var(--stone-600);">masquée</span><?php endif; ?>
        </div>
        <?php if (!empty($f['answer'])): ?>
        <div class="vp-pc-tagline" style="overflow:hidden; text-overflow:ellipsis; max-height: 1.5rem;"><?= htmlspecialchars(mb_substr($f['answer'], 0, 140)) ?><?= mb_strlen($f['answer']) > 140 ? '…' : '' ?></div>
        <?php else: ?>
        <div class="vp-pc-tagline" style="font-style: italic; color: var(--stone-500);">(réponse vide)</div>
        <?php endif; ?>
    </div>
    <div class="vp-pc-actions">
        <form method="POST" action="/admin/faq/<?= (int)$f['id'] ?>/toggle" style="display:inline">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-sm" title="<?= $f['active'] ? 'Masquer' : 'Afficher' ?>"><?= $f['active'] ? '👁' : '🚫' ?></button>
        </form>
        <a href="/admin/faq/<?= (int)$f['id'] ?>/edit" class="btn btn-sm">Éditer →</a>
        <form method="POST" action="/admin/faq/<?= (int)$f['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cette FAQ (toutes langues) ?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">✕</button>
        </form>
    </div>
</div>

<?php endforeach; ?>
<?php endif; ?>

<p class="text-sm text-muted mt-2">
    💡 La suppression et la création s'appliquent à <strong>toutes les langues</strong>. L'édition est par langue.
</p>
