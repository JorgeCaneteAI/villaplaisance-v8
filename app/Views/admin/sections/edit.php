<?php
/**
 * Vue d'édition d'un bloc V8.
 * Génère un formulaire structuré à partir de BlockFieldsService::fieldsFor($type)
 * au lieu d'exposer le JSON brut.
 *
 * @var array $section
 * @var array $blockTypes
 * @var string $csrf
 */
$blockType = $section['block_type'] ?? 'prose';
$fields    = BlockFieldsService::fieldsFor($blockType);
$content   = json_decode($section['content'] ?? '{}', true) ?: [];
$publicUrl = '/' . ($section['page_slug'] === 'accueil' ? '' : $section['page_slug']);
$typeLabel = $blockTypes[$blockType] ?? $blockType;
$langLabels = ['fr' => 'Français', 'en' => 'English', 'es' => 'Español'];
?>

<div class="page-header">
    <div>
        <h1>Éditer un bloc</h1>
        <p>
            Page <a href="/admin/sections/page/<?= htmlspecialchars($section['page_slug']) ?>" class="text-strong"><?= htmlspecialchars($section['page_slug']) ?></a>
            · Langue <strong><?= htmlspecialchars($langLabels[$section['lang']] ?? strtoupper($section['lang'])) ?></strong>
            · Position <span class="info-mono"><?= sprintf('%02d', (int)$section['position']) ?></span>
            · Type <code class="badge badge-meta info-mono"><?= htmlspecialchars($blockType) ?></code>
        </p>
    </div>
    <div class="btn-group">
        <a href="<?= htmlspecialchars($publicUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6M10 14 21 3"/></svg>
            Voir la page
        </a>
        <a href="/admin/sections/page/<?= htmlspecialchars($section['page_slug']) ?>?lang=<?= htmlspecialchars($section['lang']) ?>" class="btn btn-sm">← Retour</a>
    </div>
</div>

<form method="POST" action="/admin/sections/<?= (int)$section['id'] ?>/save" class="admin-form-shell">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
    <input type="hidden" name="block_type" value="<?= htmlspecialchars($blockType) ?>">

    <!-- Méta du bloc -->
    <section class="admin-card">
        <h2>Métadonnées du bloc</h2>
        <div class="form-row">
            <div class="form-group">
                <label for="title">Titre admin</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>" placeholder="ex. Hero accueil V8">
                <p class="form-hint">Libellé interne pour repérer ce bloc dans la liste. Pas visible côté visiteur.</p>
            </div>
            <div class="form-group">
                <label for="block_type_disabled">Type</label>
                <input type="text" id="block_type_disabled" value="<?= htmlspecialchars($typeLabel) ?>" disabled>
                <p class="form-hint">Le type ne se change pas (champs JSON incompatibles entre types).</p>
            </div>
        </div>
    </section>

    <!-- Contenu du bloc -->
    <section class="admin-card">
        <h2>Contenu du bloc</h2>
        <?php foreach ($fields as $field): ?>
            <?php BlockFormRenderer::renderField($field, $content); ?>
        <?php endforeach; ?>
    </section>

    <!-- Activation + Submit -->
    <section class="admin-card form-card-actions">
        <label class="form-toggle">
            <input type="hidden" name="active" value="0">
            <input type="checkbox" name="active" value="1" <?= $section['active'] ? 'checked' : '' ?>>
            <span class="form-toggle-label">
                <strong>Bloc actif</strong>
                <span class="text-muted text-sm">Visible côté visiteur</span>
            </span>
        </label>
        <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
    </section>
</form>

<!-- MediaPicker (modal partagé) -->
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
