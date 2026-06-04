<?php
/**
 * @var string $csrf
 * @var array  $labels
 * @var array  $dbMapping
 * @var array  $autoMatches
 * @var array  $availableIcons
 * @var int    $totalLabels
 * @var int    $mappedCount
 * @var int    $autoOnlyCount
 */

$sourceLabels = [
    'pill_chambre' => 'Pill équipement',
    'pill_solid'   => 'Pill solide',
    'sous_titre'   => 'Sous-titre',
    'contact'      => 'Contact',
    'reviews'      => 'Avis',
];

$lucideCatalog   = \IconService::LUCIDE_CATALOG;
$availableLucide = \IconService::availableLucide();
$totalIcons      = count($availableIcons) + count($availableLucide);

function vp_icon_preview(?string $name, int $size = 18): string {
    if ($name === null || $name === '') {
        return '<span class="icon-empty">-</span>';
    }
    return \IconService::svg($name, $size, 'icon-preview');
}
?>

<div class="page-header">
    <div>
        <h1>Icônes, Lab</h1>
        <p>Audit + override de la correspondance libellé → icône. Tu peux choisir une icône <strong>custom (sprite Villa)</strong> ou <strong>Lucide</strong> (préfixe <code class="info-mono">lc-</code>). Le mapping admin prend toujours le pas sur le match auto par mots-clés.</p>
    </div>
</div>

<!-- ═══ Stats ═══ -->
<div class="stats-cards stats-cards-4 mb-2">
    <div class="stat-card">
        <div class="stat-number"><?= (int) $totalLabels ?></div>
        <div class="stat-label">Libellés détectés</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= (int) $mappedCount ?></div>
        <div class="stat-label">Override admin</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= (int) $autoOnlyCount ?></div>
        <div class="stat-label">Auto seulement</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $totalIcons ?></div>
        <div class="stat-label">Icônes disponibles</div>
        <div class="stat-meta"><?= count($availableLucide) ?> Lucide · <?= count($availableIcons) ?> custom</div>
    </div>
</div>

<!-- ═══ Audit de mapping ═══ -->
<section class="admin-card admin-card--flush mb-3">
    <div class="icons-section-head">
        <h2>Mapping libellé → icône</h2>
        <span class="text-muted text-sm"><?= $totalLabels ?> libellés détectés dans le site</span>
    </div>
    <form method="POST" action="/admin/icons-lab/save">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Source</th>
                    <th class="cell-center">#</th>
                    <th class="cell-center">Auto</th>
                    <th class="cell-center">Final</th>
                    <th>Choix admin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($labels as $row):
                    $label = $row['label'];
                    $source = $row['source'];
                    $count = $row['count'];
                    $key = mb_strtolower($label, 'UTF-8');
                    $autoMatch = $autoMatches[$label] ?? null;
                    $hasOverride = array_key_exists($key, $dbMapping);
                    $overrideValue = $hasOverride ? $dbMapping[$key] : null;
                    $final = $hasOverride ? ($overrideValue !== '' ? $overrideValue : null) : $autoMatch;

                    $selectVal = 'auto';
                    if ($hasOverride) {
                        $selectVal = $overrideValue === '' ? 'none' : $overrideValue;
                    }
                ?>
                <tr>
                    <td class="text-strong"><?= htmlspecialchars($label) ?></td>
                    <td><span class="badge badge-meta"><?= htmlspecialchars($sourceLabels[$source] ?? $source) ?></span></td>
                    <td class="cell-center text-muted text-sm"><?= (int) $count ?></td>
                    <td class="cell-center"><?= vp_icon_preview($autoMatch) ?></td>
                    <td class="cell-center"><?= vp_icon_preview($final) ?></td>
                    <td>
                        <select name="mapping[<?= htmlspecialchars($label) ?>]">
                            <option value="auto" <?= $selectVal === 'auto' ? 'selected' : '' ?>>
                                Auto<?= $autoMatch ? " → $autoMatch" : ' (rien)' ?>
                            </option>
                            <option value="none" <?= $selectVal === 'none' ? 'selected' : '' ?>>(forcer : aucune)</option>
                            <optgroup label="Sprite Villa Plaisance">
                                <?php foreach ($availableIcons as $icon): ?>
                                    <option value="<?= htmlspecialchars($icon) ?>" <?= $selectVal === $icon ? 'selected' : '' ?>><?= htmlspecialchars($icon) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                            <?php foreach ($lucideCatalog as $cat): ?>
                            <optgroup label="Lucide · <?= htmlspecialchars($cat['label']) ?>">
                                <?php foreach ($cat['icons'] as $icon):
                                    $value = 'lc-' . $icon;
                                ?>
                                    <option value="<?= htmlspecialchars($value) ?>" <?= $selectVal === $value ? 'selected' : '' ?>><?= htmlspecialchars($icon) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="form-card-actions">
            <span class="text-muted text-sm">« Auto » revient au match par mots-clés. « (forcer : aucune) » masque l'icône même si le regex aurait matché.</span>
            <button type="submit" class="btn btn-primary">Enregistrer les overrides</button>
        </div>
    </form>
</section>

<!-- ═══ Bibliothèque Lucide (chips de catégorie + recherche) ═══ -->
<section class="admin-card mb-2">
    <div class="icons-section-head">
        <h2>Bibliothèque Lucide tourisme</h2>
        <span class="text-muted text-sm"><?= count($availableLucide) ?> icônes · <?= count($lucideCatalog) ?> catégories · ISC license <a href="https://lucide.dev/license" target="_blank" rel="noopener">lucide.dev</a></span>
    </div>

    <p class="form-hint">Pour utiliser dans un libellé : copie le nom (préfixe <code class="info-mono">lc-</code> ajouté automatiquement) et colle-le dans le champ « Choix admin » ci-dessus.</p>

    <div class="icons-toolbar">
        <div class="media-search">
            <svg class="media-search-icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" id="lucide-search" placeholder="Filtrer (ex : lit, wifi, sun…)" autocomplete="off">
        </div>
        <div class="icons-category-chips" id="lucide-category-chips">
            <button type="button" class="icons-chip is-active" data-cat="all">Toutes</button>
            <?php foreach ($lucideCatalog as $catKey => $cat): ?>
            <button type="button" class="icons-chip" data-cat="<?= htmlspecialchars($catKey) ?>">
                <?= htmlspecialchars($cat['label']) ?>
                <span class="icons-chip-count"><?= count($cat['icons']) ?></span>
            </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="lucide-categories">
        <?php foreach ($lucideCatalog as $catKey => $cat): ?>
        <div class="icons-cat-block" data-cat="<?= htmlspecialchars($catKey) ?>">
            <div class="icons-cat-title"><?= htmlspecialchars($cat['label']) ?></div>
            <div class="icons-grid">
                <?php foreach ($cat['icons'] as $icon):
                    $fullName = 'lc-' . $icon;
                ?>
                <button type="button" class="icon-card" data-name="<?= htmlspecialchars($fullName) ?>" data-search="<?= htmlspecialchars($icon) ?>" title="Cliquer pour copier le nom">
                    <?= \IconService::svg($fullName, 28) ?>
                    <span class="icon-name"><?= htmlspecialchars($icon) ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ═══ Sprite Villa Plaisance ═══ -->
<section class="admin-card">
    <div class="icons-section-head">
        <h2>Sprite Villa Plaisance</h2>
        <span class="text-muted text-sm"><?= count($availableIcons) ?> icônes thématiques (vignoble, aqueduc, plateformes avis…)</span>
    </div>
    <p class="form-hint">Préfixe d'usage : nom seul, sans <code class="info-mono">lc-</code>.</p>
    <div class="icons-grid">
        <?php foreach ($availableIcons as $icon): ?>
        <button type="button" class="icon-card" data-name="<?= htmlspecialchars($icon) ?>" data-search="<?= htmlspecialchars($icon) ?>" title="Cliquer pour copier le nom">
            <?= \IconService::svg($icon, 28) ?>
            <span class="icon-name"><?= htmlspecialchars($icon) ?></span>
        </button>
        <?php endforeach; ?>
    </div>
</section>

<!-- Toast (feedback copie) -->
<div id="copy-toast" class="copy-toast" hidden></div>

<script>
(function () {
    'use strict';
    const search = document.getElementById('lucide-search');
    const chips  = document.querySelectorAll('#lucide-category-chips .icons-chip');
    const blocks = document.querySelectorAll('#lucide-categories .icons-cat-block');
    const toast  = document.getElementById('copy-toast');

    let currentCat = 'all';

    function applyFilters() {
        const q = (search?.value || '').trim().toLowerCase();
        blocks.forEach(block => {
            const catMatch = currentCat === 'all' || block.dataset.cat === currentCat;
            let visibleCount = 0;
            block.querySelectorAll('.icon-card').forEach(card => {
                const name = (card.dataset.search || '').toLowerCase();
                const match = catMatch && (q === '' || name.includes(q));
                card.hidden = !match;
                if (match) visibleCount++;
            });
            block.hidden = visibleCount === 0;
        });
    }

    search?.addEventListener('input', applyFilters);

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('is-active'));
            chip.classList.add('is-active');
            currentCat = chip.dataset.cat;
            applyFilters();
        });
    });

    // Copie au clic + toast
    function showToast(msg) {
        if (!toast) return;
        toast.textContent = msg;
        toast.hidden = false;
        toast.classList.add('is-visible');
        clearTimeout(showToast._t);
        showToast._t = setTimeout(() => {
            toast.classList.remove('is-visible');
            setTimeout(() => { toast.hidden = true; }, 200);
        }, 1500);
    }

    document.querySelectorAll('.icon-card').forEach(card => {
        card.addEventListener('click', async (e) => {
            e.preventDefault();
            const name = card.dataset.name || '';
            try {
                await navigator.clipboard.writeText(name);
                showToast('Copié : ' + name);
                card.classList.add('is-copied');
                setTimeout(() => card.classList.remove('is-copied'), 600);
            } catch (err) {
                showToast('Erreur de copie');
            }
        });
    });
})();
</script>
