<?php
/**
 * Liste V8 des blocs d'une page.
 *
 * Tableau des blocs de la langue courante, sélecteur de langue, badges
 * présence/absence par langue, lien Éditer vers le formulaire dédié V8.
 *
 * @var array  $sections        Sections de la langue courante
 * @var array  $sectionsByLang  Sections groupées par lang (pour les badges)
 * @var string $currentLang     'fr'|'en'|'es'
 * @var array  $langs           SUPPORTED_LANGS
 * @var string $page_slug
 * @var array  $blockTypes      Libellés via BlockService::getBlockTypes()
 * @var string $csrf
 */

$pageTitle = '';
try {
    $row = Database::fetchOne("SELECT title FROM vp_pages WHERE slug = ? AND lang = 'fr' LIMIT 1", [$page_slug]);
    $pageTitle = $row['title'] ?? '';
} catch (\Throwable) {}

$langLabels = ['fr' => "🇫🇷 Français", 'en' => "🇬🇧 English", 'es' => "🇪🇸 Español"];

// Pour chaque position, savoir quelles langues ont un bloc à cette position
$presenceByPos = [];
foreach ($langs as $l) {
    foreach ($sectionsByLang[$l] ?? [] as $s) {
        $presenceByPos[(int)$s['position']][$l] = true;
    }
}
?>
<div class="page-header">
    <h1><?= htmlspecialchars($pageTitle !== '' ? $pageTitle : $page_slug) ?></h1>
    <a href="/admin/pages" class="btn">← Toutes les pages</a>
</div>

<div class="vp-pc-toolbar" style="margin-bottom: 1rem;">
    <form method="GET" action="/admin/sections/page/<?= htmlspecialchars($page_slug) ?>" class="vp-pc-filters">
        <label>
            <span class="text-sm text-muted">Langue affichée</span>
            <select name="lang" onchange="this.form.submit()">
                <?php foreach ($langs as $l):
                    $count = count($sectionsByLang[$l] ?? []);
                ?>
                <option value="<?= $l ?>" <?= $currentLang === $l ? 'selected' : '' ?>>
                    <?= htmlspecialchars($langLabels[$l] ?? strtoupper($l)) ?> · <?= $count ?> bloc<?= $count > 1 ? 's' : '' ?>
                </option>
                <?php endforeach; ?>
            </select>
        </label>
    </form>
    <p class="text-sm text-muted" style="margin: 0;">
        URL publique : <a href="/<?= htmlspecialchars($page_slug === 'accueil' ? '' : $page_slug) ?>" target="_blank" style="color: var(--terra-500);">/<?= htmlspecialchars($page_slug) ?></a>
    </p>
</div>

<?php if (empty($sections)): ?>
<div class="mail-empty">
    <div class="mail-empty-icon">▦</div>
    Cette page n'a pas encore de bloc en base de données. Elle s'affiche depuis le HTML en dur (« fallback »).
    <br><br>
    <p class="text-sm text-muted" style="max-width: 50ch; margin: 0 auto;">
        Pour porter cette page sur le système de blocs, il faut créer un seed PHP (cf. <code>seeds/v8/*</code>) ou ajouter les blocs un par un via « Nouveau bloc » ci-dessous.
    </p>
</div>
<?php else: ?>
<div class="admin-card" style="padding: 0; overflow: hidden;">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 56px;">Pos.</th>
                <th>Bloc</th>
                <th>Type</th>
                <th class="text-center">Langues</th>
                <th class="text-center">Actif</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sections as $section):
                $typeLabel = $blockTypes[$section['block_type']] ?? $section['block_type'];
                $title = $section['title'] ?: '(sans titre admin)';
                $isActive = (int)$section['active'] === 1;
            ?>
            <tr style="<?= !$isActive ? 'opacity: 0.55;' : '' ?>">
                <td style="font-variant-numeric: tabular-nums; font-weight: 600; color: var(--stone-500);">
                    <?= sprintf('%02d', (int)$section['position']) ?>
                </td>
                <td>
                    <strong style="font-family: var(--font-display); font-size: 1.05rem; color: var(--ink-900);"><?= htmlspecialchars($title) ?></strong>
                </td>
                <td>
                    <span style="background: var(--linen-100); padding: 2px 8px; border-radius: 3px; font-family: var(--font-mono); font-size: 0.78rem;"><?= htmlspecialchars($section['block_type']) ?></span>
                    <div class="text-sm text-muted" style="margin-top: 2px;"><?= htmlspecialchars($typeLabel) ?></div>
                </td>
                <td class="text-center">
                    <?php foreach ($langs as $l):
                        $present = $presenceByPos[(int)$section['position']][$l] ?? false;
                    ?>
                    <span title="<?= $l ?> <?= $present ? 'présent' : 'manquant' ?>" style="
                        display: inline-block;
                        font-family: var(--font-mono);
                        font-size: 0.7rem;
                        font-weight: 600;
                        padding: 2px 6px;
                        margin: 0 1px;
                        border-radius: 3px;
                        text-transform: uppercase;
                        background: <?= $present ? 'var(--sage-200)' : 'var(--linen-200)' ?>;
                        color: <?= $present ? 'var(--sage-700)' : 'var(--stone-500)' ?>;
                        <?= $present ? '' : 'text-decoration: line-through;' ?>
                    "><?= $l ?></span>
                    <?php endforeach; ?>
                </td>
                <td class="text-center">
                    <?php if ($isActive): ?>
                        <span class="badge badge-success">Actif</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Caché</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="/admin/sections/<?= (int)$section['id'] ?>/edit" class="btn btn-primary btn-sm">Éditer</a>
                        <form method="POST" action="/admin/sections/<?= (int)$section['id'] ?>/toggle" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <button type="submit" class="btn btn-sm" title="<?= $isActive ? 'Cacher ce bloc' : 'Rendre actif' ?>">
                                <?= $isActive ? '◌ Cacher' : '✓ Activer' ?>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<p class="text-sm text-muted" style="margin-top: 12px;">
    ⚠ Réordonnancement par glisser-déposer arrivera en Session 2. Pour l'instant, l'ordre vient des seeds PHP.
</p>
<?php endif; ?>
