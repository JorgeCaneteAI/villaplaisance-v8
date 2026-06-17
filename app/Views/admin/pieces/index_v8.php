<?php
/**
 * Admin V8, chambres & espaces (vp_pieces) — édition en place, 3 langues
 * côte à côte, cases d'offre. Les images restent gérées via la page d'édition
 * dédiée (non multilingues).
 *
 * @var array  $groups       "offer:position" => ['offer','position','type','ref'=>row,'langs'=>['fr'=>,'en'=>,'es'=>]]
 * @var string $offerFilter  ''|'bb'|'villa'|'both'
 * @var array  $langLabels
 * @var string $csrf
 */
$offerLabels = ['bb' => "Chambres d'hôtes (B&B)", 'villa' => 'Villa entière', 'both' => 'Commun'];
$langShort   = ['fr' => '🇫🇷 Français', 'en' => '🇬🇧 English', 'es' => '🇪🇸 Español'];

$thumbUrl = function (?string $filename): ?string {
    if (!$filename) return null;
    $thumbName = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
    $thumbPath = ROOT . '/public/uploads/thumb/' . $thumbName;
    return file_exists($thumbPath) ? '/uploads/thumb/' . $thumbName : '/uploads/' . $filename;
};
?>

<div class="page-header">
    <h1>Chambres &amp; Espaces</h1>
</div>

<div class="vp-pc-toolbar">
    <form method="GET" action="/admin/pieces" class="vp-pc-filters">
        <label>
            <span class="text-sm text-muted">Offre</span>
            <select name="offer" onchange="this.form.requestSubmit()">
                <option value="" <?= $offerFilter === '' ? 'selected' : '' ?>>Toutes</option>
                <?php foreach ($offerLabels as $k => $lbl): ?>
                <option value="<?= $k ?>" <?= $offerFilter === $k ? 'selected' : '' ?>><?= htmlspecialchars($lbl) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <span class="text-sm text-muted">Les 3 langues sont éditables sur cette page.</span>
    </form>

    <form method="POST" action="/admin/pieces/create" class="vp-pc-create">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <button type="submit" class="btn btn-primary btn-sm">+ Ajouter une pièce</button>
    </form>
</div>

<?php if (empty($groups)): ?>
<p class="text-muted mt-2">Aucune chambre ou espace pour ce filtre.</p>
<?php else: ?>

<?php
$currentOffer = '';
foreach ($groups as $g):
    $ref = $g['ref'];
    if ($g['offer'] !== $currentOffer):
        $currentOffer = $g['offer'];
?>
<h2 class="vp-pc-group-title"><?= htmlspecialchars($offerLabels[$currentOffer] ?? $currentOffer) ?></h2>
<?php endif; ?>

<?php
    $images = json_decode($ref['images'] ?? '[]', true) ?: [];
    if (empty($images) && !empty($ref['image'])) $images = [$ref['image']];
    $thumb = $thumbUrl($images[0] ?? null);
    $refMeta = is_string($ref['meta'] ?? null) ? (json_decode($ref['meta'], true) ?: []) : [];
    $hasMeta = !empty($refMeta);
    $layout  = $refMeta['layout'] ?? '';
    $offer   = $g['offer'];
    $idFr    = (int)($g['langs']['fr']['id'] ?? $ref['id']);
?>
<div class="vp-pc-row piece-card" data-piece>
    <div class="vp-pc-thumb">
        <?php if ($thumb): ?>
        <img src="<?= htmlspecialchars($thumb) ?>" alt="" loading="lazy" decoding="async">
        <?php else: ?>
        <div class="vp-pc-thumb-empty">-</div>
        <?php endif; ?>
    </div>
    <div class="vp-pc-info">
        <div class="vp-pc-name">
            <span class="vp-pc-pos">#<?= $g['position'] ?></span>
            <strong><?= htmlspecialchars($ref['name'] ?: '(sans nom)') ?></strong>
            <span class="badge badge-info"><?= htmlspecialchars($g['type']) ?></span>
            <?php if ($offer === 'bb' || $offer === 'both'): ?><span class="badge piece-badge-offer">B&amp;B</span><?php endif; ?>
            <?php if ($offer === 'villa' || $offer === 'both'): ?><span class="badge piece-badge-offer">Villa</span><?php endif; ?>
            <?php if ($hasMeta): ?><span class="badge badge-meta" title="meta JSON présent">meta</span><?php endif; ?>
        </div>
        <?php if (!empty($ref['sous_titre'])): ?>
        <div class="vp-pc-tagline"><?= htmlspecialchars($ref['sous_titre']) ?></div>
        <?php endif; ?>
        <div class="vp-pc-meta-info">
            <span><?= count($images) ?> image<?= count($images) > 1 ? 's' : '' ?></span>
            <?php if (!empty($ref['note'])): ?> · <span class="vp-pc-note">📍 <?= htmlspecialchars($ref['note']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="vp-pc-actions">
        <button type="button" class="btn btn-sm piece-toggle" aria-expanded="false">Modifier ▾</button>
        <form method="POST" action="/admin/pieces/<?= $idFr ?>/delete" class="inline-form" onsubmit="return confirm('Supprimer cette pièce (toutes langues) ?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">✕</button>
        </form>
    </div>

    <form method="POST" action="/admin/pieces/<?= $idFr ?>/save-group" class="piece-editor" data-ajax-save hidden>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <?php foreach (SUPPORTED_LANGS as $l): if (isset($g['langs'][$l])): ?>
        <input type="hidden" name="ids[<?= $l ?>]" value="<?= (int)$g['langs'][$l]['id'] ?>">
        <?php endif; endforeach; ?>

        <div class="piece-editor-shared">
            <div class="piece-offers">
                <span class="piece-shared-label">Offres</span>
                <label class="piece-check"><input type="checkbox" name="offer_bb" value="1" <?= ($offer === 'bb' || $offer === 'both') ? 'checked' : '' ?>> B&amp;B</label>
                <label class="piece-check"><input type="checkbox" name="offer_villa" value="1" <?= ($offer === 'villa' || $offer === 'both') ? 'checked' : '' ?>> Villa</label>
            </div>
            <label class="piece-shared-field">
                <span class="piece-shared-label">Type</span>
                <select name="type">
                    <option value="chambre" <?= $g['type'] === 'chambre' ? 'selected' : '' ?>>Chambre</option>
                    <option value="espace" <?= $g['type'] === 'espace' ? 'selected' : '' ?>>Espace</option>
                </select>
            </label>
            <label class="piece-shared-field">
                <span class="piece-shared-label">Mise en page (B&amp;B)</span>
                <select name="meta_layout">
                    <option value="" <?= $layout === '' ? 'selected' : '' ?>>Auto</option>
                    <option value="normal" <?= $layout === 'normal' ? 'selected' : '' ?>>Image à droite</option>
                    <option value="alt" <?= $layout === 'alt' ? 'selected' : '' ?>>Image à gauche</option>
                </select>
            </label>
            <a href="/admin/pieces/<?= $idFr ?>/edit" class="btn btn-sm piece-images-link">🖼 Gérer les images (<?= count($images) ?>) →</a>
        </div>

        <div class="piece-langs">
            <?php foreach (SUPPORTED_LANGS as $l): $row = $g['langs'][$l] ?? null;
                $lmeta = ($row && is_string($row['meta'] ?? null)) ? (json_decode($row['meta'], true) ?: []) : [];
            ?>
            <div class="piece-lang">
                <div class="piece-lang-label"><?= htmlspecialchars($langShort[$l] ?? strtoupper($l)) ?><?= $row ? '' : ' · absente' ?></div>
                <?php if ($row): ?>
                <label class="piece-field"><span>Nom</span>
                    <input type="text" name="name[<?= $l ?>]" value="<?= htmlspecialchars($row['name'] ?? '') ?>"<?= $l === 'fr' ? ' required' : '' ?>></label>
                <label class="piece-field"><span>Tagline</span>
                    <input type="text" name="sous_titre[<?= $l ?>]" value="<?= htmlspecialchars($row['sous_titre'] ?? '') ?>"></label>
                <label class="piece-field"><span>Description</span>
                    <textarea name="description[<?= $l ?>]" rows="3" data-autogrow><?= htmlspecialchars($row['description'] ?? '') ?></textarea></label>
                <label class="piece-field"><span>Équipements <span class="piece-field-hint">(séparés par des virgules)</span></span>
                    <input type="text" name="equip[<?= $l ?>]" value="<?= htmlspecialchars($row['equip'] ?? '') ?>" placeholder="Lit 160×200, Vue jardin, Wifi"></label>
                <label class="piece-field"><span>Pill spécial</span>
                    <input type="text" name="note[<?= $l ?>]" value="<?= htmlspecialchars($row['note'] ?? '') ?>" placeholder="ex. Rez-de-chaussée"></label>
                <label class="piece-field"><span>Meta label A <span class="piece-field-hint">(B&amp;B)</span></span>
                    <input type="text" name="meta_label_a[<?= $l ?>]" value="<?= htmlspecialchars($lmeta['label_a'] ?? '') ?>"></label>
                <label class="piece-field"><span>Meta label B <span class="piece-field-hint">(B&amp;B)</span></span>
                    <input type="text" name="meta_label_b[<?= $l ?>]" value="<?= htmlspecialchars($lmeta['label_b'] ?? '') ?>"></label>
                <?php else: ?>
                <p class="text-muted text-sm">Cette langue n'existe pas encore pour la pièce.</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="piece-editor-actions">
            <span class="text-sm text-muted">Les offres, le type et la mise en page s'appliquent aux 3 langues.</span>
            <button type="submit" class="btn btn-sm btn-primary">Enregistrer la pièce</button>
        </div>
    </form>
</div>

<?php endforeach; ?>
<?php endif; ?>

<p class="text-sm text-muted mt-2">
    💡 Clique <strong>Modifier</strong> pour déplier une pièce et éditer les 3 langues côte à côte, puis
    <strong>Enregistrer la pièce</strong> (ou <strong>Tout enregistrer</strong> en bas pour tout sauver d'un coup).
    Les <strong>offres</strong> (cases B&amp;B / Villa), le <strong>type</strong> et la <strong>mise en page</strong>
    s'appliquent aux 3 langues. Les <strong>images</strong> se gèrent depuis « Gérer les images ».
    Suppression et création portent sur <strong>toutes les langues</strong>.
</p>

<style>
/* ── Pièces : édition en place, 3 langues, cases d'offre ── */
.vp-pc-row.piece-card{ align-items:center; }
.piece-badge-offer{ background:var(--accent-soft); color:var(--accent); font-weight:600; }
.piece-toggle[aria-expanded="true"]{ background:var(--surface-2); border-color:var(--text-3); }

.piece-editor{
    grid-column:1 / -1;
    margin-top:4px; padding-top:16px;
    border-top:1px solid var(--border);
    display:flex; flex-direction:column; gap:16px; min-width:0;
}
.piece-editor-shared{
    display:flex; flex-wrap:wrap; align-items:end; gap:14px 20px;
    padding:12px 14px; background:var(--surface-2);
    border:1px solid var(--border); border-radius:var(--radius-md);
}
.piece-shared-label{ display:block; font-size:11px; font-weight:600; letter-spacing:.04em;
    text-transform:uppercase; color:var(--text-3); margin-bottom:6px; }
.piece-offers{ display:flex; flex-direction:column; }
.piece-offers .piece-shared-label{ margin-bottom:6px; }
.piece-offers > label{ display:inline-flex; }
.piece-check{ display:inline-flex; align-items:center; gap:6px; margin-right:14px;
    font-size:13.5px; cursor:pointer; }
.piece-check input{ width:16px; height:16px; accent-color:var(--accent); cursor:pointer; }
.piece-shared-field{ display:flex; flex-direction:column; }
.piece-shared-field select{ min-width:150px; }
.piece-images-link{ margin-left:auto; align-self:center; }

.piece-langs{ display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; }
.piece-lang{ display:flex; flex-direction:column; gap:8px;
    padding:12px; border:1px solid var(--border); border-radius:var(--radius-md);
    background:var(--surface); min-width:0; }
.piece-lang-label{ font-size:12px; font-weight:600; color:var(--text-2);
    padding-bottom:8px; border-bottom:1px solid var(--border); }
.piece-field{ display:flex; flex-direction:column; gap:4px; min-width:0; }
.piece-field > span{ font-size:11.5px; font-weight:500; color:var(--text-2); }
.piece-field-hint{ font-weight:400; color:var(--text-3); }
.piece-field input, .piece-field textarea{ width:100%; font-size:13px; }
.piece-field textarea{ line-height:1.5; resize:vertical; overflow:hidden; min-height:3em; }
.piece-editor-actions{ display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; }

@media (max-width:900px){
    .piece-langs{ grid-template-columns:1fr; }
}

/* Champ modifié non enregistré */
.piece-editor :is(input:not([type="checkbox"]), textarea, select).is-changed{
    border-color:var(--accent) !important;
    box-shadow:0 0 0 3px var(--accent-soft);
}
.piece-check input.is-changed{ outline:2px solid var(--accent); outline-offset:2px; }

/* Feedback boutons */
.piece-editor button[type="submit"]{
    transition: transform 140ms cubic-bezier(.16,1,.3,1),
                background-color 180ms ease, border-color 180ms ease, color 180ms ease;
}
.piece-editor button[type="submit"]:active{ transform:scale(.97); }
.piece-editor button[type="submit"].is-saving{ opacity:.6; cursor:progress; }
.piece-editor button[type="submit"].is-saved{ background:var(--success); border-color:var(--success); color:#fff; }
.piece-editor button[type="submit"].is-error{ background:var(--error); border-color:var(--error); color:#fff; }

/* Barre « Tout enregistrer » */
.save-all-bar{
    position:fixed; left:50%; bottom:24px;
    transform:translateX(-50%) translateY(160%);
    z-index:300; display:flex; align-items:center; gap:16px;
    padding:9px 9px 9px 20px;
    background:var(--surface); border:1px solid var(--border-strong);
    border-radius:9999px; box-shadow:var(--shadow-lg);
    opacity:0; pointer-events:none;
    transition:transform 280ms cubic-bezier(.16,1,.3,1), opacity 200ms ease;
}
.save-all-bar.is-visible{ transform:translateX(-50%) translateY(0); opacity:1; pointer-events:auto; }
.save-all-bar__text{ font-size:13.5px; color:var(--text-2); white-space:nowrap; }
.save-all-bar__text strong{ color:var(--text); font-weight:600; }
.save-all-bar .btn:active{ transform:scale(.97); }
@media (max-width:560px){
    .save-all-bar{ left:12px; right:12px; bottom:12px; transform:translateY(160%); justify-content:space-between; }
    .save-all-bar.is-visible{ transform:translateY(0); }
    .save-all-bar__text{ white-space:normal; }
}
@media (prefers-reduced-motion: reduce){
    .save-all-bar,
    .save-all-bar.is-visible{ transition:opacity 200ms ease; transform:translateX(-50%); }
    .piece-editor button[type="submit"]:active{ transform:none; }
}
</style>

<div class="save-all-bar" id="saveAllBar" role="status" aria-live="polite">
    <span class="save-all-bar__text" id="saveAllText"></span>
    <button type="button" class="btn btn-primary" id="saveAllBtn">Tout enregistrer</button>
</div>

<script>
(function () {
    'use strict';

    const autogrow = (ta) => { ta.style.height = 'auto'; ta.style.height = (ta.scrollHeight + 2) + 'px'; };
    document.querySelectorAll('textarea[data-autogrow]').forEach(ta => {
        ta.addEventListener('input', () => autogrow(ta));
    });

    /* ───── Accordéon : déplier/replier une pièce ───── */
    document.querySelectorAll('.piece-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const card = btn.closest('[data-piece]');
            const editor = card ? card.querySelector('.piece-editor') : null;
            if (!editor) return;
            const opening = editor.hasAttribute('hidden');
            editor.toggleAttribute('hidden', !opening);
            btn.setAttribute('aria-expanded', opening ? 'true' : 'false');
            btn.textContent = opening ? 'Fermer ▴' : 'Modifier ▾';
            if (opening) editor.querySelectorAll('textarea[data-autogrow]').forEach(autogrow);
        });
    });

    /* ───── Sauvegarde AJAX en place + barre « Tout enregistrer » ───── */
    const forms = Array.from(document.querySelectorAll('form[data-ajax-save]'));
    if (!forms.length) return;

    const bar     = document.getElementById('saveAllBar');
    const barText = document.getElementById('saveAllText');
    const barBtn  = document.getElementById('saveAllBtn');
    if (bar && bar.parentNode !== document.body) document.body.appendChild(bar);

    const valOf = (el) => el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;
    const controls = (form) => Array.from(form.elements).filter(el =>
        el.name && el.type !== 'hidden' && el.type !== 'submit' && el.type !== 'button');

    const snapshot = (form) => {
        form._saved = {};
        controls(form).forEach(el => { form._saved[el.name] = valOf(el); });
    };
    forms.forEach(snapshot);

    const refreshDirty = (form) => {
        let dirty = false;
        controls(form).forEach(el => {
            const changed = form._saved[el.name] !== valOf(el);
            el.classList.toggle('is-changed', changed);
            if (changed) dirty = true;
        });
        form.classList.toggle('is-dirty', dirty);
        return dirty;
    };

    const dirtyForms = () => forms.filter(f => f.classList.contains('is-dirty'));

    const updateBar = () => {
        const n = dirtyForms().length;
        if (!n) { bar.classList.remove('is-visible'); return; }
        barText.innerHTML = '<strong>' + n + '</strong> pièce' + (n > 1 ? 's' : '')
            + ' modifiée' + (n > 1 ? 's' : '') + ' non enregistrée' + (n > 1 ? 's' : '');
        bar.classList.add('is-visible');
    };

    const flashBtn = (btn, cls, label, ms) => {
        btn.classList.remove('is-saving');
        btn.classList.add(cls);
        btn.textContent = label;
        btn.disabled = false;
        window.setTimeout(() => { btn.classList.remove(cls); btn.textContent = btn.dataset.label; }, ms);
    };

    const saveForm = async (form, bulk) => {
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            if (!btn.dataset.label) btn.dataset.label = btn.textContent.trim();
            btn.disabled = true;
            btn.classList.add('is-saving');
        }
        try {
            const r = await fetch(form.action, {
                method: 'POST', body: new FormData(form),
                credentials: 'same-origin', redirect: 'follow',
            });
            if (!r.ok && r.status >= 400) throw new Error('HTTP ' + r.status);
            snapshot(form);
            refreshDirty(form);
            if (btn) flashBtn(btn, 'is-saved', '✓ Enregistré', 1500);
            return true;
        } catch (err) {
            console.error('Save piece failed:', err);
            if (btn) flashBtn(btn, 'is-error', 'Erreur', 2400);
            return false;
        } finally {
            if (!bulk) updateBar();
        }
    };

    forms.forEach(form => {
        form.addEventListener('input', () => { refreshDirty(form); updateBar(); });
        form.addEventListener('change', () => { refreshDirty(form); updateBar(); });
        form.addEventListener('submit', (e) => { e.preventDefault(); saveForm(form, false); });
    });

    barBtn.addEventListener('click', async () => {
        const targets = dirtyForms();
        if (!targets.length) return;
        // déplier les pièces concernées pour que le feedback soit visible
        targets.forEach(f => {
            f.removeAttribute('hidden');
            const t = f.closest('[data-piece]')?.querySelector('.piece-toggle');
            if (t) { t.setAttribute('aria-expanded', 'true'); t.textContent = 'Fermer ▴'; }
        });
        barBtn.disabled = true;
        barText.innerHTML = 'Enregistrement…';
        const results = await Promise.all(targets.map(f => saveForm(f, true)));
        const failed = results.filter(ok => !ok).length;
        if (!failed) {
            barText.innerHTML = '✓ Tout est enregistré';
            window.setTimeout(() => bar.classList.remove('is-visible'), 1200);
        } else {
            barText.innerHTML = failed + ' échec' + (failed > 1 ? 's' : '') + ' — réessaie';
        }
        barBtn.disabled = false;
        updateBar();
    });

    // Actions de page (filtre, Supprimer, Ajouter) : enregistrer d'abord les
    // modifs en attente, puis laisser la navigation se faire.
    document.querySelectorAll('form:not([data-ajax-save])').forEach(form => {
        form.addEventListener('submit', async (e) => {
            if (e.defaultPrevented) return;
            if (!dirtyForms().length) return;
            e.preventDefault();
            await Promise.all(dirtyForms().map(f => saveForm(f, true)));
            updateBar();
            form.submit();
        });
    });

    // Garde anti-perte sur une vraie sortie (fermeture d'onglet, F5, lien images).
    window.addEventListener('beforeunload', (e) => {
        if (dirtyForms().length) { e.preventDefault(); e.returnValue = ''; }
    });
})();
</script>
