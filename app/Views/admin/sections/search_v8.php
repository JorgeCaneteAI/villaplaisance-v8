<?php
/**
 * Admin V8 — recherche globale dans tous les blocs vp_sections.
 *
 * @var string $q
 * @var array  $results  Résultats du LIKE %q% sur title + content + page_slug + block_type
 * @var array  $pages    Liste des page_slugs distincts (pour la navigation)
 * @var array  $blockTypes
 * @var string $csrf
 */

$highlight = function (string $text, string $needle, int $context = 80): string {
    if ($needle === '') return htmlspecialchars($text);
    $pos = mb_stripos($text, $needle);
    if ($pos === false) return htmlspecialchars(mb_strimwidth($text, 0, 160, '…'));
    $start = max(0, $pos - $context);
    $excerpt = mb_substr($text, $start, $context * 2);
    if ($start > 0) $excerpt = '…' . $excerpt;
    if (mb_strlen($text) > $start + $context * 2) $excerpt .= '…';
    // Highlight insensible à la casse
    $highlighted = preg_replace(
        '/(' . preg_quote(htmlspecialchars($needle), '/') . ')/iu',
        '<mark style="background:#fff3a0; padding:0 2px; border-radius:2px;">$1</mark>',
        htmlspecialchars($excerpt)
    );
    return $highlighted;
};

$langLabels = ['fr' => '🇫🇷', 'en' => '🇬🇧', 'es' => '🇪🇸'];
?>

<div class="page-header">
    <h1>Recherche dans les blocs</h1>
</div>

<div class="admin-card" style="margin-bottom: 1rem;">
    <form method="GET" action="/admin/sections" style="display: flex; gap: 0.5rem;">
        <input type="search" name="q" value="<?= htmlspecialchars($q) ?>"
               placeholder="Rechercher dans titre, contenu, page, type de bloc…"
               autofocus
               style="flex: 1; padding: 0.6rem 0.9rem; border: 1px solid var(--admin-border); border-radius: var(--admin-radius); font: inherit; font-size: 1rem;">
        <button type="submit" class="btn btn-primary">Rechercher</button>
        <?php if ($q !== ''): ?>
        <a href="/admin/sections" class="btn">Effacer</a>
        <?php endif; ?>
    </form>
    <p class="text-sm text-muted" style="margin-top: 0.5rem;">
        Cherche dans le titre admin, le contenu JSON, le slug de page et le type de bloc, toutes langues confondues.
    </p>
</div>

<?php if ($q === ''): ?>
<div class="admin-card">
    <h2 style="font-size: 1.05rem; margin: 0 0 12px; font-family: var(--font-display); font-weight: 500;">Accès rapide aux pages</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.5rem;">
        <?php foreach ($pages as $page): ?>
        <a href="/admin/sections/page/<?= htmlspecialchars($page['slug']) ?>" class="btn">
            <?= htmlspecialchars($page['slug']) ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php else: ?>

<p class="text-muted" style="margin-bottom: 0.75rem;">
    <?= count($results) ?> résultat<?= count($results) > 1 ? 's' : '' ?> pour « <strong><?= htmlspecialchars($q) ?></strong> »
    <?= count($results) >= 200 ? ' (limité à 200)' : '' ?>
</p>

<?php if (empty($results)): ?>
<div class="admin-card">
    <p class="text-muted text-center" style="padding: 2rem 0;">Aucun bloc ne correspond à cette recherche.</p>
</div>
<?php else: ?>

<div class="admin-card" style="padding: 0; overflow: hidden;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Page</th>
                <th>Lang</th>
                <th>Type</th>
                <th>Titre admin</th>
                <th>Extrait</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $r): ?>
            <tr style="<?= !$r['active'] ? 'opacity: 0.6;' : '' ?>">
                <td>
                    <a href="/admin/sections/page/<?= htmlspecialchars($r['page_slug']) ?>?lang=<?= htmlspecialchars($r['lang']) ?>" style="font-family: var(--font-mono); font-size: 0.82rem; color: var(--sage-700);">
                        <?= htmlspecialchars($r['page_slug']) ?>
                    </a>
                </td>
                <td style="text-align: center; font-size: 1.1rem;"><?= $langLabels[$r['lang']] ?? strtoupper($r['lang']) ?></td>
                <td>
                    <span style="background: var(--linen-100); padding: 2px 8px; border-radius: 3px; font-family: var(--font-mono); font-size: 0.78rem;"><?= htmlspecialchars($r['block_type']) ?></span>
                    <span style="display:block; font-size: 0.78rem; color: var(--stone-500); margin-top: 2px;">pos <?= (int)$r['position'] ?></span>
                </td>
                <td><?= $highlight((string)($r['title'] ?? ''), $q) ?></td>
                <td style="max-width: 380px; font-size: 0.85rem; color: var(--stone-600);">
                    <?= $highlight((string)$r['content'], $q) ?>
                </td>
                <td>
                    <a href="/admin/sections/<?= (int)$r['id'] ?>/edit" class="btn btn-primary btn-sm">Éditer →</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php endif; ?>
