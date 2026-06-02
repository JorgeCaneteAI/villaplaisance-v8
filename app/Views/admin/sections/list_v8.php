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
            <tr data-section-id="<?= (int)$section['id'] ?>" draggable="true" style="<?= !$isActive ? 'opacity: 0.55;' : '' ?> cursor: grab;">
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
                        <form method="POST" action="/admin/sections/<?= (int)$section['id'] ?>/duplicate" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <button type="submit" class="btn btn-sm" title="Dupliquer ce bloc juste en dessous">⎘ Dupliquer</button>
                        </form>
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
    💡 Glisse-dépose une ligne pour réordonner. L'ordre est sauvegardé automatiquement.
</p>

<!-- Mini-formulaire « Nouveau bloc » (C) -->
<div class="admin-card" style="margin-top: 1.5rem;">
    <h2 style="font-size: 1.05rem; margin: 0 0 12px; font-family: var(--font-display); font-weight: 500;">+ Nouveau bloc sur cette page</h2>
    <form method="POST" action="/admin/sections/create" style="display: flex; gap: 0.75rem; align-items: end; flex-wrap: wrap;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="page_slug" value="<?= htmlspecialchars($page_slug) ?>">
        <div class="form-group" style="margin: 0; min-width: 220px;">
            <label for="new-block-type" class="text-sm">Type</label>
            <select id="new-block-type" name="block_type" required>
                <?php foreach ($blockTypes as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin: 0; flex: 1; min-width: 240px;">
            <label for="new-block-title" class="text-sm">Titre admin (interne, optionnel)</label>
            <input type="text" id="new-block-title" name="title" placeholder="ex. « Hero accueil V8 »">
        </div>
        <button type="submit" class="btn btn-primary">Créer (FR + EN + ES)</button>
    </form>
    <p class="text-sm text-muted" style="margin-top: 8px;">
        Crée 3 entrées vp_sections vides (FR/EN/ES) à la dernière position. Tu remplis le contenu via « Éditer » après création.
    </p>
</div>
<?php endif; ?>

<script>
(function () {
    'use strict';
    const tbody = document.querySelector('.admin-table tbody');
    if (!tbody) return;

    const csrf = <?= json_encode($csrf) ?>;
    let dragRow = null;

    tbody.addEventListener('dragstart', (e) => {
        const tr = e.target.closest('tr[data-section-id]');
        if (!tr) return;
        dragRow = tr;
        tr.style.opacity = '0.4';
        e.dataTransfer.effectAllowed = 'move';
    });

    tbody.addEventListener('dragend', () => {
        if (dragRow) {
            dragRow.style.opacity = '';
            dragRow = null;
        }
        // Re-séquence les pastilles "Pos." visuellement
        Array.from(tbody.querySelectorAll('tr[data-section-id]')).forEach((tr, i) => {
            const posCell = tr.querySelector('td:first-child');
            if (posCell) posCell.textContent = String(i + 1).padStart(2, '0');
        });
    });

    tbody.addEventListener('dragover', (e) => {
        e.preventDefault();
        const tr = e.target.closest('tr[data-section-id]');
        if (!tr || tr === dragRow) return;
        const rect = tr.getBoundingClientRect();
        const after = (e.clientY - rect.top) > rect.height / 2;
        if (after) {
            tr.parentNode.insertBefore(dragRow, tr.nextSibling);
        } else {
            tr.parentNode.insertBefore(dragRow, tr);
        }
    });

    tbody.addEventListener('drop', (e) => {
        e.preventDefault();
        // Récupère le nouvel ordre + envoie au serveur
        const order = Array.from(tbody.querySelectorAll('tr[data-section-id]'))
            .map(tr => parseInt(tr.dataset.sectionId, 10));
        fetch('/admin/sections/reorder', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrf,
            },
            body: JSON.stringify({ order }),
        })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) {
                console.error('Reorder failed', data);
                alert('Erreur de réordonnancement : ' + (data.error || 'inconnu'));
            }
        })
        .catch(err => {
            console.error('Reorder error', err);
            alert('Erreur réseau lors du réordonnancement');
        });
    });
})();
</script>
