<?php
/**
 * Admin V8, liste des chambres & espaces (vp_pieces).
 *
 * @var array  $pieces       Pieces filtrées par lang + offer
 * @var string $langFilter   'fr'|'en'|'es'
 * @var string $offerFilter  ''|'bb'|'villa'|'both'
 * @var array  $langLabels
 * @var string $csrf
 */
$offerLabels = ['bb' => 'Chambres d\'hôtes (B&B)', 'villa' => 'Villa entière', 'both' => 'Commun'];
?>

<div class="page-header">
    <h1>Chambres &amp; Espaces</h1>
</div>

<div class="vp-pc-toolbar">
    <form method="GET" action="/admin/pieces" class="vp-pc-filters">
        <label>
            <span class="text-sm text-muted">Langue</span>
            <select name="lang" onchange="this.form.submit()">
                <?php foreach (SUPPORTED_LANGS as $l): ?>
                <option value="<?= $l ?>" <?= $langFilter === $l ? 'selected' : '' ?>><?= $langLabels[$l] ?? strtoupper($l) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span class="text-sm text-muted">Offre</span>
            <select name="offer" onchange="this.form.submit()">
                <option value="" <?= $offerFilter === '' ? 'selected' : '' ?>>Toutes</option>
                <?php foreach ($offerLabels as $k => $lbl): ?>
                <option value="<?= $k ?>" <?= $offerFilter === $k ? 'selected' : '' ?>><?= htmlspecialchars($lbl) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </form>

    <form method="POST" action="/admin/pieces/create" class="vp-pc-create">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <select name="offer" required>
            <?php foreach ($offerLabels as $k => $lbl): ?>
            <option value="<?= $k ?>"><?= htmlspecialchars($lbl) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="type" required>
            <option value="chambre">Chambre</option>
            <option value="espace">Espace</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">+ Nouveau</button>
    </form>
</div>

<?php if (empty($pieces)): ?>
<p class="text-muted mt-2">Aucune chambre ou espace pour ces filtres.</p>
<?php else: ?>

<?php
$currentOffer = '';
foreach ($pieces as $p):
    if ($p['offer'] !== $currentOffer):
        $currentOffer = $p['offer'];
?>
<h2 class="vp-pc-group-title"><?= htmlspecialchars($offerLabels[$currentOffer] ?? $currentOffer) ?></h2>
<?php endif; ?>

<?php
    $images = json_decode($p['images'] ?? '[]', true) ?: [];
    if (empty($images) && !empty($p['image'])) $images = [$p['image']];
    $firstImg = $images[0] ?? null;
    $thumbUrl = null;
    if ($firstImg) {
        $thumbName = pathinfo($firstImg, PATHINFO_FILENAME) . '.webp';
        $thumbPath = ROOT . '/public/uploads/thumb/' . $thumbName;
        $thumbUrl = file_exists($thumbPath) ? '/uploads/thumb/' . $thumbName : '/uploads/' . $firstImg;
    }
    $meta = is_string($p['meta'] ?? null) ? json_decode($p['meta'], true) : null;
    $hasMeta = is_array($meta) && !empty($meta);
?>
<div class="vp-pc-row">
    <div class="vp-pc-thumb">
        <?php if ($thumbUrl): ?>
        <img src="<?= htmlspecialchars($thumbUrl) ?>" alt="" loading="lazy" decoding="async">
        <?php else: ?>
        <div class="vp-pc-thumb-empty">-</div>
        <?php endif; ?>
    </div>
    <div class="vp-pc-info">
        <div class="vp-pc-name">
            <span class="vp-pc-pos">#<?= (int)$p['position'] ?></span>
            <strong><?= htmlspecialchars($p['name']) ?></strong>
            <span class="badge badge-info"><?= htmlspecialchars($p['type']) ?></span>
            <?php if ($hasMeta): ?><span class="badge badge-meta" title="meta JSON présent">meta</span><?php endif; ?>
        </div>
        <?php if (!empty($p['sous_titre'])): ?>
        <div class="vp-pc-tagline"><?= htmlspecialchars($p['sous_titre']) ?></div>
        <?php endif; ?>
        <div class="vp-pc-meta-info">
            <span><?= count($images) ?> image<?= count($images) > 1 ? 's' : '' ?></span>
            <?php if (!empty($p['note'])): ?> · <span class="vp-pc-note">📍 <?= htmlspecialchars($p['note']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="vp-pc-actions">
        <a href="/admin/pieces/<?= (int)$p['id'] ?>/edit" class="btn btn-sm">Éditer →</a>
        <form method="POST" action="/admin/pieces/<?= (int)$p['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cette pièce (toutes langues) ?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">✕</button>
        </form>
    </div>
</div>

<?php endforeach; ?>
<?php endif; ?>

<p class="text-sm text-muted mt-2">
    💡 La suppression retire la pièce pour <strong>toutes les langues</strong>.
    L'édition s'applique uniquement à la langue affichée (<?= htmlspecialchars($langLabels[$langFilter] ?? $langFilter) ?>).
</p>
