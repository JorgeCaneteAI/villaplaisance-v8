<?php
/**
 * Admin V8 — édition d'une chambre/espace vp_pieces.
 *
 * @var array  $piece
 * @var string $csrf
 *
 * Réutilise le MediaPicker partagé (avec mode 'filename') pour le repeater images.
 */
$images = is_string($piece['images'] ?? null) ? (json_decode($piece['images'], true) ?: []) : [];
if (empty($images) && !empty($piece['image'])) $images = [$piece['image']];

$meta = is_string($piece['meta'] ?? null) ? (json_decode($piece['meta'], true) ?: []) : [];
$labelA = $meta['label_a'] ?? '';
$labelB = $meta['label_b'] ?? '';
$layout = $meta['layout'] ?? '';

$offerLabels = ['bb' => "Chambres d'hôtes (B&B)", 'villa' => 'Villa entière', 'both' => 'Commun'];

// Helper : URL miniature ou image originale en fallback
$thumbUrl = function (string $filename): string {
    if ($filename === '') return '';
    $thumbName = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
    $thumbPath = ROOT . '/public/uploads/thumb/' . $thumbName;
    return file_exists($thumbPath) ? '/uploads/thumb/' . $thumbName : '/uploads/' . $filename;
};
?>

<div class="page-header">
    <h1>Éditer une pièce</h1>
    <a href="/admin/pieces" class="btn">← Retour à la liste</a>
</div>

<p class="text-muted mb-2">
    ID : <code><?= (int)$piece['id'] ?></code> ·
    Langue : <strong><?= htmlspecialchars($piece['lang']) ?></strong> ·
    Offre : <strong><?= htmlspecialchars($offerLabels[$piece['offer']] ?? $piece['offer']) ?></strong> ·
    Position : <?= (int)$piece['position'] ?>
</p>

<form method="POST" action="/admin/pieces/<?= (int)$piece['id'] ?>/save" class="admin-card">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

    <h2 style="font-size: 1.1rem; margin: 0 0 16px; font-family: var(--font-display); font-weight: 500;">Identité</h2>

    <div class="form-row">
        <div class="form-group">
            <label for="name">Nom (h2 affiché en italique)</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($piece['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="sous_titre">Tagline (sous-titre court — affiché en uppercase sur B&B)</label>
            <input type="text" id="sous_titre" name="sous_titre" value="<?= htmlspecialchars($piece['sous_titre'] ?? '') ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="offer">Offre</label>
            <select id="offer" name="offer">
                <?php foreach ($offerLabels as $k => $lbl): ?>
                <option value="<?= $k ?>" <?= $piece['offer'] === $k ? 'selected' : '' ?>><?= htmlspecialchars($lbl) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type">
                <option value="chambre" <?= $piece['type'] === 'chambre' ? 'selected' : '' ?>>Chambre</option>
                <option value="espace" <?= $piece['type'] === 'espace' ? 'selected' : '' ?>>Espace</option>
            </select>
        </div>
    </div>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--admin-border);">

    <h2 style="font-size: 1.1rem; margin: 0 0 16px; font-family: var(--font-display); font-weight: 500;">Contenu éditorial</h2>

    <div class="form-group">
        <label for="description">Description (paragraphe long affiché en body-lg)</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($piece['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="equip">Pills d'équipement (séparés par virgule, ex. <code>Lit 160×200, Vue jardin, Wifi</code>)</label>
        <input type="text" id="equip" name="equip" value="<?= htmlspecialchars($piece['equip'] ?? '') ?>">
        <p class="text-sm text-muted" style="margin-top: 4px;">Pour un pill 2-lignes : utilise <code>|</code>, ex. <code>2 lits 90×200|jumelables</code>.</p>
    </div>

    <div class="form-group">
        <label for="note">Pill spécial (affiché en <code>.pill.solid</code> à la fin sur le markup villa)</label>
        <input type="text" id="note" name="note" value="<?= htmlspecialchars($piece['note'] ?? '') ?>" placeholder="ex. « Rez-de-chaussée », « Accès direct jardin »">
    </div>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--admin-border);">

    <h2 style="font-size: 1.1rem; margin: 0 0 16px; font-family: var(--font-display); font-weight: 500;">Images</h2>

    <p class="text-sm text-muted">
        Format <strong>WebP</strong> uniquement. Pour le markup B&B : 1 grande + 2 petites (3 images). Pour le markup villa : 1 seule image suffit.
    </p>

    <div class="vp-rp" data-name-prefix="images" data-id-prefix="img" data-item-kind="filename">
        <div class="vp-rp-items">
            <?php foreach ($images as $i => $filename):
                $url = $thumbUrl($filename);
                $inputId = 'img-' . $i;
            ?>
            <div class="vp-rp-item" data-index="<?= $i ?>">
                <div class="vp-rp-item-handle"><span class="vp-rp-item-num"><?= $i + 1 ?></span></div>
                <div class="vp-rp-item-body">
                    <div class="vp-mp-row">
                        <input type="text" id="<?= $inputId ?>" name="images[<?= $i ?>]" value="<?= htmlspecialchars($filename) ?>" data-mp-mode="filename" placeholder="(filename .webp)" style="flex:1; min-width: 220px;">
                        <button type="button" class="vp-mp-trigger" data-target="<?= $inputId ?>">Choisir…</button>
                        <img class="vp-mp-preview" data-for="<?= $inputId ?>" src="<?= htmlspecialchars($url) ?>" alt="" <?= $url ? '' : 'hidden' ?>>
                        <span class="vp-mp-preview-label" data-for="<?= $inputId ?>"><?= htmlspecialchars($filename) ?></span>
                    </div>
                </div>
                <div class="vp-rp-item-actions">
                    <button type="button" class="vp-rp-up" title="Monter">↑</button>
                    <button type="button" class="vp-rp-down" title="Descendre">↓</button>
                    <button type="button" class="vp-rp-remove" title="Supprimer">×</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <template class="vp-rp-tpl">
            <div class="vp-rp-item" data-index="__INDEX__">
                <div class="vp-rp-item-handle"><span class="vp-rp-item-num">#</span></div>
                <div class="vp-rp-item-body">
                    <div class="vp-mp-row">
                        <input type="text" id="img-__INDEX__" name="images[__INDEX__]" value="" data-mp-mode="filename" placeholder="(filename .webp)" style="flex:1; min-width: 220px;">
                        <button type="button" class="vp-mp-trigger" data-target="img-__INDEX__">Choisir…</button>
                        <img class="vp-mp-preview" data-for="img-__INDEX__" src="" alt="" hidden>
                        <span class="vp-mp-preview-label" data-for="img-__INDEX__"></span>
                    </div>
                </div>
                <div class="vp-rp-item-actions">
                    <button type="button" class="vp-rp-up" title="Monter">↑</button>
                    <button type="button" class="vp-rp-down" title="Descendre">↓</button>
                    <button type="button" class="vp-rp-remove" title="Supprimer">×</button>
                </div>
            </div>
        </template>
        <button type="button" class="vp-rp-add">+ Ajouter une image</button>
    </div>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--admin-border);">

    <h2 style="font-size: 1.1rem; margin: 0 0 16px; font-family: var(--font-display); font-weight: 500;">
        Meta (markup B&B uniquement)
    </h2>
    <p class="text-sm text-muted">
        Ces 3 champs ne servent que sur le markup B&B (<code>.ch-room</code>). Sur le markup villa, ils sont ignorés.
    </p>

    <div class="form-row">
        <div class="form-group">
            <label for="meta_label_a">Label A (au-dessus du tagline)</label>
            <input type="text" id="meta_label_a" name="meta_label_a" value="<?= htmlspecialchars($labelA) ?>" placeholder="ex. « I · Première chambre de la suite »">
        </div>
        <div class="form-group">
            <label for="meta_label_b">Label B (à droite du label A)</label>
            <input type="text" id="meta_label_b" name="meta_label_b" value="<?= htmlspecialchars($labelB) ?>" placeholder="ex. « Côté jardin »">
        </div>
    </div>

    <div class="form-group">
        <label for="meta_layout">Mise en page de la section</label>
        <select id="meta_layout" name="meta_layout">
            <option value="" <?= $layout === '' ? 'selected' : '' ?>>(auto : alternance selon position)</option>
            <option value="normal" <?= $layout === 'normal' ? 'selected' : '' ?>>Normal — image à droite</option>
            <option value="alt" <?= $layout === 'alt' ? 'selected' : '' ?>>Alt — image à gauche (fond stone)</option>
        </select>
    </div>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--admin-border);">

    <input type="hidden" name="image" value="<?= htmlspecialchars($piece['image'] ?? '') ?>">

    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="/admin/pieces" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary" style="margin-left: auto;">Enregistrer</button>
    </div>
</form>

<!-- MediaPicker modal partagé (réutilisé depuis sections/edit) -->
<div id="vp-media-picker" class="vp-mp-backdrop" hidden>
    <div class="vp-mp-modal" role="dialog" aria-modal="true" aria-labelledby="vp-mp-title">
        <header class="vp-mp-head">
            <h2 id="vp-mp-title">Choisir un média</h2>
            <button type="button" class="vp-mp-close" aria-label="Fermer">×</button>
        </header>
        <div class="vp-mp-toolbar">
            <input type="search" class="vp-mp-search" placeholder="Rechercher (nom, alt, titre, tags)…">
            <select class="vp-mp-folder">
                <option value="">Tous les dossiers</option>
            </select>
            <a href="/admin/media" target="_blank" rel="noopener" class="vp-mp-upload">+ Uploader →</a>
        </div>
        <div class="vp-mp-grid" aria-live="polite">
            <p class="vp-mp-empty">Chargement…</p>
        </div>
        <footer class="vp-mp-foot">
            <span class="vp-mp-count"></span>
            <button type="button" class="vp-mp-cancel">Annuler</button>
        </footer>
    </div>
</div>

<script src="/assets/js/admin-section-edit.js?v=<?= filemtime(ROOT . '/public/assets/js/admin-section-edit.js') ?>" defer></script>
