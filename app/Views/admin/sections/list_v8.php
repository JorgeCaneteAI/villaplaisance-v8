<?php
/**
 * Liste V8 des blocs d'une page.
 *
 * @var array  $sections        Sections de la langue courante
 * @var array  $sectionsByLang  Sections groupées par lang (badges présence)
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

$langLabels = ['fr' => '🇫🇷 Français', 'en' => '🇬🇧 English', 'es' => '🇪🇸 Español'];
$publicUrl = '/' . ($page_slug === 'accueil' ? '' : $page_slug);

// Pour chaque position, savoir quelles langues ont un bloc
$presenceByPos = [];
foreach ($langs as $l) {
    foreach ($sectionsByLang[$l] ?? [] as $s) {
        $presenceByPos[(int)$s['position']][$l] = true;
    }
}
?>

<div class="page-header">
    <div>
        <h1><?= htmlspecialchars($pageTitle !== '' ? $pageTitle : $page_slug) ?></h1>
        <p>Blocs de contenu de la page <code class="info-mono"><?= htmlspecialchars($publicUrl) ?></code>. Glisse-dépose pour réordonner.</p>
    </div>
    <div class="btn-group">
        <a href="<?= htmlspecialchars($publicUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6M10 14 21 3"/></svg>
            Voir la page
        </a>
        <a href="/admin/pages" class="btn btn-sm">← Toutes les pages</a>
    </div>
</div>

<!-- Toolbar : sélecteur de langue + URL publique -->
<div class="filter-toolbar mb-2">
    <form method="GET" action="/admin/sections/page/<?= htmlspecialchars($page_slug) ?>" class="filter-toolbar-left">
        <label class="filter-label">
            <span>Langue affichée</span>
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
    <div class="filter-toolbar-right">
        <span class="text-muted text-sm">URL publique :</span>
        <a href="<?= htmlspecialchars($publicUrl) ?>" target="_blank" rel="noopener" class="info-mono"><?= htmlspecialchars($publicUrl) ?></a>
    </div>
</div>

<?php if (empty($sections)): ?>
<div class="empty-state">
    <div class="empty-icon">▦</div>
    <strong>Aucun bloc dans la langue <?= htmlspecialchars(strtoupper($currentLang)) ?>.</strong>
    <p class="text-muted text-sm" style="max-width: 50ch;">
        Cette page s'affiche encore depuis le HTML en dur (fallback). Pour la porter sur le système de blocs, ajoute un bloc ci-dessous.
    </p>
</div>
<?php else: ?>
<div class="admin-card admin-card--flush mb-2">
    <table class="admin-table admin-table--blocks">
        <thead>
            <tr>
                <th class="col-pos">Pos.</th>
                <th>Bloc</th>
                <th>Type</th>
                <th class="cell-center">Langues</th>
                <th class="cell-center">Actif</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sections as $section):
                $typeLabel = $blockTypes[$section['block_type']] ?? $section['block_type'];
                $title = $section['title'] ?: '(sans titre admin)';
                $isActive = (int)$section['active'] === 1;
            ?>
            <tr data-section-id="<?= (int)$section['id'] ?>" draggable="true" class="<?= $isActive ? '' : 'row-muted' ?> row-draggable">
                <td class="col-pos">
                    <span class="section-position"><?= sprintf('%02d', (int)$section['position']) ?></span>
                </td>
                <td>
                    <strong class="text-strong"><?= htmlspecialchars($title) ?></strong>
                </td>
                <td>
                    <code class="badge badge-meta info-mono"><?= htmlspecialchars($section['block_type']) ?></code>
                    <div class="text-sm text-muted"><?= htmlspecialchars($typeLabel) ?></div>
                </td>
                <td class="cell-center">
                    <div class="lang-pills">
                        <?php foreach ($langs as $l):
                            $present = $presenceByPos[(int)$section['position']][$l] ?? false;
                        ?>
                        <span class="lang-pill <?= $present ? 'is-present' : 'is-missing' ?>" title="<?= $l ?> <?= $present ? 'présent' : 'manquant' ?>"><?= $l ?></span>
                        <?php endforeach; ?>
                    </div>
                </td>
                <td class="cell-center">
                    <?php if ($isActive): ?>
                        <span class="badge badge-success">Actif</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Caché</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="/admin/sections/<?= (int)$section['id'] ?>/edit" class="btn btn-sm btn-primary">Éditer</a>
                        <form method="POST" action="/admin/sections/<?= (int)$section['id'] ?>/duplicate" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <button type="submit" class="btn btn-sm" title="Dupliquer ce bloc juste en dessous">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                Dupliquer
                            </button>
                        </form>
                        <form method="POST" action="/admin/sections/<?= (int)$section['id'] ?>/toggle" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <button type="submit" class="btn btn-sm" title="<?= $isActive ? 'Cacher ce bloc' : 'Rendre actif' ?>">
                                <?php if ($isActive): ?>
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                                    Cacher
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Activer
                                <?php endif; ?>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Création d'un nouveau bloc -->
<div class="admin-card">
    <h2>Nouveau bloc</h2>
    <form method="POST" action="/admin/sections/create" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="page_slug" value="<?= htmlspecialchars($page_slug) ?>">

        <div class="form-group form-group--type">
            <label for="new-block-type">Type de bloc</label>
            <select id="new-block-type" name="block_type" required>
                <?php foreach ($blockTypes as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group form-group--title">
            <label for="new-block-title">Titre admin <span class="text-muted text-sm">(interne, optionnel)</span></label>
            <input type="text" id="new-block-title" name="title" placeholder="ex. « Hero accueil V8 »">
        </div>
        <div class="form-group form-group--submit">
            <button type="submit" class="btn btn-primary">Créer (FR + EN + ES)</button>
        </div>
    </form>
    <p class="text-sm text-muted mt-1">
        Crée 3 entrées vides (FR/EN/ES) à la dernière position. Tu remplis le contenu via « Éditer » après.
    </p>
</div>

<script>
(function () {
    'use strict';
    const tbody = document.querySelector('.admin-table--blocks tbody');
    if (!tbody) return;

    const csrf = <?= json_encode($csrf) ?>;
    let dragRow = null;

    tbody.addEventListener('dragstart', (e) => {
        const tr = e.target.closest('tr[data-section-id]');
        if (!tr) return;
        dragRow = tr;
        tr.classList.add('is-dragging');
        e.dataTransfer.effectAllowed = 'move';
    });

    tbody.addEventListener('dragend', () => {
        if (dragRow) {
            dragRow.classList.remove('is-dragging');
            dragRow = null;
        }
        Array.from(tbody.querySelectorAll('tr[data-section-id]')).forEach((tr, i) => {
            const cell = tr.querySelector('.section-position');
            if (cell) cell.textContent = String(i + 1).padStart(2, '0');
        });
    });

    tbody.addEventListener('dragover', (e) => {
        e.preventDefault();
        const tr = e.target.closest('tr[data-section-id]');
        if (!tr || tr === dragRow) return;
        const rect = tr.getBoundingClientRect();
        const after = (e.clientY - rect.top) > rect.height / 2;
        tr.parentNode.insertBefore(dragRow, after ? tr.nextSibling : tr);
    });

    tbody.addEventListener('drop', (e) => {
        e.preventDefault();
        const order = Array.from(tbody.querySelectorAll('tr[data-section-id]'))
            .map(tr => parseInt(tr.dataset.sectionId, 10));
        fetch('/admin/sections/reorder', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ order }),
        })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) alert('Erreur de réordonnancement : ' + (data.error || 'inconnu'));
        })
        .catch(() => alert('Erreur réseau lors du réordonnancement'));
    });
})();
</script>
