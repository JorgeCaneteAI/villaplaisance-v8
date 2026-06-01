<?php
/**
 * Vue d'édition d'un bloc V8.
 *
 * Génère un formulaire structuré à partir de BlockFieldsService::fieldsFor($type)
 * au lieu d'exposer le JSON brut. Pour les types complexes (repeater, media_id),
 * fallback temporaire en textarea JSON tant qu'on n'a pas le JS de la Session 2.
 *
 * @var array $section
 * @var array $blockTypes  (liste des types via BlockService::getBlockTypes())
 * @var string $csrf
 */
$blockType = $section['block_type'] ?? 'prose';
$fields = BlockFieldsService::fieldsFor($blockType);
$content = json_decode($section['content'] ?? '{}', true) ?: [];
?>

<div class="page-header">
    <h1>Éditer un bloc</h1>
    <a href="/admin/sections/page/<?= htmlspecialchars($section['page_slug']) ?>" class="btn">← Retour</a>
</div>

<p class="text-muted mb-2">
    Page : <code><?= htmlspecialchars($section['page_slug']) ?></code> · Langue : <strong><?= htmlspecialchars($section['lang']) ?></strong> · Position : <?= (int)$section['position'] ?>
</p>

<form method="POST" action="/admin/sections/<?= (int)$section['id'] ?>/save" class="admin-card">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

    <div class="form-row">
        <div class="form-group">
            <label for="title">Titre (admin)</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
            <p class="text-sm text-muted" style="margin-top: 4px;">Libellé interne pour t'aider à reconnaître ce bloc dans la liste. Pas visible côté visiteur.</p>
        </div>
        <div class="form-group">
            <label for="block_type">Type de bloc</label>
            <select id="block_type" name="block_type" disabled>
                <?php foreach ($blockTypes as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>" <?= $blockType === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="block_type" value="<?= htmlspecialchars($blockType) ?>">
            <p class="text-sm text-muted" style="margin-top: 4px;">Le type ne se change pas en édition (les champs JSON sont incompatibles entre types).</p>
        </div>
    </div>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--admin-border);">

    <h2 style="font-size: 1.1rem; margin: 0 0 16px; font-family: var(--font-display); font-weight: 500;">
        Contenu du bloc
    </h2>

    <?php foreach ($fields as $field): ?>
        <?php BlockFormRenderer::renderField($field, $content); ?>
    <?php endforeach; ?>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--admin-border);">

    <div style="display: flex; align-items: center; gap: 16px;">
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
            <input type="hidden" name="active" value="0">
            <input type="checkbox" name="active" value="1" <?= $section['active'] ? 'checked' : '' ?>>
            <span>Bloc actif (visible côté visiteur)</span>
        </label>

        <button type="submit" class="btn btn-primary" style="margin-left: auto;">Enregistrer</button>
    </div>
</form>

<!-- MediaPicker (Phase 4 Session 2 / L1) — modal partagé pour tous les champs media_id -->
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
