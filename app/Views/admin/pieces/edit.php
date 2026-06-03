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
$langLabels  = ['fr' => 'Français', 'en' => 'English', 'es' => 'Español'];

// URL miniature ou image originale en fallback
$thumbUrl = function (string $filename): string {
    if ($filename === '') return '';
    $thumbName = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
    $thumbPath = ROOT . '/public/uploads/thumb/' . $thumbName;
    return file_exists($thumbPath) ? '/uploads/thumb/' . $thumbName : '/uploads/' . $filename;
};

// Rendu d'un item du repeater images — utilisé pour items existants ET template
$renderImageItem = function (string $filename, $index) use ($thumbUrl) {
    $url = $thumbUrl($filename);
    $inputId = 'img-' . $index;
    $num = is_int($index) ? ($index + 1) : '#';
    ?>
    <div class="vp-rp-item piece-image-item" data-index="<?= htmlspecialchars((string)$index) ?>">
        <div class="vp-rp-item-handle"><span class="vp-rp-item-num"><?= $num ?></span></div>
        <div class="vp-rp-item-body piece-image-body">
            <?php if ($url): ?>
                <img class="vp-mp-preview piece-image-thumb" data-for="<?= $inputId ?>" src="<?= htmlspecialchars($url) ?>" alt="">
            <?php else: ?>
                <div class="vp-mp-preview vp-mp-preview--empty piece-image-thumb" data-for="<?= $inputId ?>">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
            <?php endif; ?>
            <div class="piece-image-meta">
                <input type="text" id="<?= $inputId ?>" name="images[<?= htmlspecialchars((string)$index) ?>]" value="<?= htmlspecialchars($filename) ?>" data-mp-mode="filename" placeholder="nom-du-fichier.webp" class="piece-image-input">
                <button type="button" class="btn btn-sm vp-mp-trigger" data-target="<?= $inputId ?>">Choisir…</button>
            </div>
        </div>
        <div class="vp-rp-item-actions">
            <button type="button" class="vp-rp-up" title="Monter">↑</button>
            <button type="button" class="vp-rp-down" title="Descendre">↓</button>
            <button type="button" class="vp-rp-remove" title="Supprimer">×</button>
        </div>
    </div>
    <?php
};
?>

<div class="page-header">
    <div>
        <h1>Éditer une pièce</h1>
        <p>
            ID <span class="info-mono">#<?= (int)$piece['id'] ?></span>
            · Langue <strong><?= htmlspecialchars($langLabels[$piece['lang']] ?? strtoupper($piece['lang'])) ?></strong>
            · Offre <strong><?= htmlspecialchars($offerLabels[$piece['offer']] ?? $piece['offer']) ?></strong>
            · Position <span class="info-mono"><?= sprintf('%02d', (int)$piece['position']) ?></span>
        </p>
    </div>
    <a href="/admin/pieces" class="btn btn-sm">← Toutes les pièces</a>
</div>

<form method="POST" action="/admin/pieces/<?= (int)$piece['id'] ?>/save" class="admin-form-shell">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
    <input type="hidden" name="image" value="<?= htmlspecialchars($piece['image'] ?? '') ?>">

    <!-- Identité -->
    <section class="admin-card">
        <h2>Identité</h2>
        <div class="form-row">
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($piece['name']) ?>" required>
                <p class="form-hint">H2 affiché en italique sur le front.</p>
            </div>
            <div class="form-group">
                <label for="sous_titre">Tagline</label>
                <input type="text" id="sous_titre" name="sous_titre" value="<?= htmlspecialchars($piece['sous_titre'] ?? '') ?>">
                <p class="form-hint">Sous-titre court, affiché en uppercase sur B&amp;B.</p>
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
    </section>

    <!-- Contenu éditorial -->
    <section class="admin-card">
        <h2>Contenu éditorial</h2>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($piece['description'] ?? '') ?></textarea>
            <p class="form-hint">Paragraphe long affiché en body-lg.</p>
        </div>
        <div class="form-group">
            <label for="equip">Pills d'équipement</label>
            <input type="text" id="equip" name="equip" value="<?= htmlspecialchars($piece['equip'] ?? '') ?>" placeholder="Lit 160×200, Vue jardin, Wifi">
            <p class="form-hint">Séparés par une virgule. Pour un pill 2-lignes : utilise <code class="info-mono">|</code>, ex. <code class="info-mono">2 lits 90×200|jumelables</code>.</p>
        </div>
        <div class="form-group">
            <label for="note">Pill spécial</label>
            <input type="text" id="note" name="note" value="<?= htmlspecialchars($piece['note'] ?? '') ?>" placeholder="ex. « Rez-de-chaussée », « Accès direct jardin »">
            <p class="form-hint">Affiché en pill solide à la fin sur le markup villa.</p>
        </div>
    </section>

    <!-- Images -->
    <section class="admin-card">
        <h2>Images</h2>
        <p class="form-hint mb-2">Format <strong>WebP</strong> uniquement. Markup B&amp;B : 1 grande + 2 petites (3 images). Markup villa : 1 seule image suffit.</p>

        <div class="vp-rp" data-name-prefix="images" data-id-prefix="img" data-item-kind="filename">
            <div class="vp-rp-items">
                <?php foreach ($images as $i => $filename): ?>
                    <?php $renderImageItem((string)$filename, (int)$i); ?>
                <?php endforeach; ?>
            </div>
            <template class="vp-rp-tpl">
                <?php $renderImageItem('', '__INDEX__'); ?>
            </template>
            <div class="vp-rp-footer">
                <button type="button" class="btn btn-sm vp-rp-add">+ Ajouter une image</button>
            </div>
        </div>
    </section>

    <!-- Meta B&B -->
    <section class="admin-card">
        <h2>Meta <span class="card-meta">markup B&amp;B uniquement</span></h2>
        <p class="form-hint mb-2">Ces 3 champs ne servent que sur le markup B&amp;B (<code class="info-mono">.ch-room</code>). Sur le markup villa, ils sont ignorés.</p>

        <div class="form-row">
            <div class="form-group">
                <label for="meta_label_a">Label A</label>
                <input type="text" id="meta_label_a" name="meta_label_a" value="<?= htmlspecialchars($labelA) ?>" placeholder="I · Première chambre de la suite">
                <p class="form-hint">Au-dessus du tagline.</p>
            </div>
            <div class="form-group">
                <label for="meta_label_b">Label B</label>
                <input type="text" id="meta_label_b" name="meta_label_b" value="<?= htmlspecialchars($labelB) ?>" placeholder="Côté jardin">
                <p class="form-hint">À droite du label A.</p>
            </div>
        </div>

        <div class="form-group">
            <label for="meta_layout">Mise en page de la section</label>
            <select id="meta_layout" name="meta_layout">
                <option value="" <?= $layout === '' ? 'selected' : '' ?>>Auto — alternance selon position</option>
                <option value="normal" <?= $layout === 'normal' ? 'selected' : '' ?>>Normal — image à droite</option>
                <option value="alt" <?= $layout === 'alt' ? 'selected' : '' ?>>Alt — image à gauche (fond stone)</option>
            </select>
        </div>
    </section>

    <!-- Submit -->
    <section class="admin-card form-card-actions">
        <a href="/admin/pieces" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
    </section>
</form>

<!-- MediaPicker modal partagé -->
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
            <button type="button" class="vp-mp-cancel btn btn-sm">Annuler</button>
        </footer>
    </div>
</div>

<script src="/assets/js/admin-section-edit.js?v=<?= filemtime(ROOT . '/public/assets/js/admin-section-edit.js') ?>" defer></script>
