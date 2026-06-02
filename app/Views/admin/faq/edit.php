<?php
/**
 * Admin V8 — édition d'une FAQ vp_faq (1 langue à la fois).
 *
 * @var array  $faq
 * @var string $csrf
 */
?>

<div class="page-header">
    <h1>Éditer une FAQ</h1>
    <a href="/admin/faq?page_slug=<?= urlencode($faq['page_slug']) ?>&lang=<?= urlencode($faq['lang']) ?>" class="btn">← Retour à la liste</a>
</div>

<p class="text-muted mb-2">
    Page : <code><?= htmlspecialchars($faq['page_slug']) ?></code> ·
    Langue : <strong><?= htmlspecialchars($faq['lang']) ?></strong> ·
    Position : <?= (int)$faq['position'] ?>
</p>

<form method="POST" action="/admin/faq/<?= (int)$faq['id'] ?>/save" class="admin-card">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

    <div class="form-group">
        <label for="question">Question</label>
        <input type="text" id="question" name="question" value="<?= htmlspecialchars($faq['question'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="answer">Réponse</label>
        <textarea id="answer" name="answer" rows="6" required><?= htmlspecialchars($faq['answer'] ?? '') ?></textarea>
        <p class="text-sm text-muted" style="margin-top: 4px;">Texte brut multi-paragraphes. Pas de markdown ici (rendu en <code>nl2br</code> côté front).</p>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="position">Position (ordre d'affichage)</label>
            <input type="number" id="position" name="position" value="<?= (int)$faq['position'] ?>" min="1" style="max-width: 120px;">
        </div>
        <div class="form-group">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; margin-top: 28px;">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" <?= $faq['active'] ? 'checked' : '' ?>>
                <span>FAQ active (visible côté visiteur)</span>
            </label>
        </div>
    </div>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--admin-border);">

    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="/admin/faq?page_slug=<?= urlencode($faq['page_slug']) ?>&lang=<?= urlencode($faq['lang']) ?>" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary" style="margin-left: auto;">Enregistrer</button>
    </div>
</form>
