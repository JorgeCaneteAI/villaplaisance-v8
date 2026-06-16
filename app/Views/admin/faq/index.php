<?php
/**
 * Admin V8, liste des FAQ (vp_faq) — édition en place (inline).
 *
 * @var array  $faqs
 * @var array  $slugs        Liste des page_slugs distinctes
 * @var string $langFilter
 * @var string $slugFilter
 * @var array  $langLabels
 * @var string $csrf
 */
?>

<div class="page-header">
    <h1>FAQ</h1>
</div>

<div class="vp-pc-toolbar">
    <form method="GET" action="/admin/faq" class="vp-pc-filters">
        <label>
            <span class="text-sm text-muted">Langue</span>
            <select name="lang" onchange="this.form.requestSubmit()">
                <?php foreach (SUPPORTED_LANGS as $l): ?>
                <option value="<?= $l ?>" <?= $langFilter === $l ? 'selected' : '' ?>><?= $langLabels[$l] ?? strtoupper($l) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span class="text-sm text-muted">Page</span>
            <select name="page_slug" onchange="this.form.requestSubmit()">
                <option value="" <?= $slugFilter === '' ? 'selected' : '' ?>>Toutes</option>
                <?php foreach ($slugs as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>" <?= $slugFilter === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </form>

    <form method="POST" action="/admin/faq/create" class="vp-pc-create">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="text" name="page_slug" placeholder="page_slug (ex. chambres-d-hotes)" required style="padding: 0.4rem 0.6rem; border: 1px solid var(--admin-border); border-radius: var(--admin-radius); width:auto;">
        <button type="submit" class="btn btn-primary btn-sm">+ Nouvelle FAQ</button>
    </form>
</div>

<?php if (empty($faqs)): ?>
<p class="text-muted mt-2">Aucune FAQ pour ces filtres.</p>
<?php else: ?>

<?php
$currentSlug = '';
foreach ($faqs as $f):
    if ($f['page_slug'] !== $currentSlug):
        $currentSlug = $f['page_slug'];
?>
<h2 class="vp-pc-group-title"><?= htmlspecialchars($currentSlug) ?></h2>
<?php endif; ?>

<div class="vp-pc-row faq-row<?= $f['active'] ? '' : ' is-hidden' ?>">
    <div class="vp-pc-pos-cell"><span class="vp-pc-pos">#<?= (int)$f['position'] ?></span></div>

    <form method="POST" action="/admin/faq/<?= (int)$f['id'] ?>/save" class="faq-edit-form" data-ajax-save>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="position" value="<?= (int)$f['position'] ?>">
        <input type="hidden" name="active" value="1" data-active <?= $f['active'] ? '' : 'disabled' ?>>
        <div class="faq-edit-head">
            <input type="text" name="question" class="faq-q" value="<?= htmlspecialchars($f['question'] ?? '') ?>" placeholder="Question" required>
            <span class="faq-hidden-badge">masquée</span>
        </div>
        <textarea name="answer" class="faq-a" rows="2" placeholder="Réponse" data-autogrow><?= htmlspecialchars($f['answer'] ?? '') ?></textarea>
        <div class="faq-edit-actions">
            <button type="submit" class="btn btn-sm btn-primary">Enregistrer</button>
        </div>
    </form>

    <div class="vp-pc-actions">
        <form method="POST" action="/admin/faq/<?= (int)$f['id'] ?>/toggle" class="inline-form" data-faq-toggle>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-sm" title="<?= $f['active'] ? 'Masquer' : 'Afficher' ?>"><?= $f['active'] ? '👁' : '🚫' ?></button>
        </form>
        <form method="POST" action="/admin/faq/<?= (int)$f['id'] ?>/delete" class="inline-form" onsubmit="return confirm('Supprimer cette FAQ (toutes langues) ?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">✕</button>
        </form>
    </div>
</div>

<?php endforeach; ?>
<?php endif; ?>

<p class="text-sm text-muted mt-2">
    💡 Édition directe : modifie la question ou la réponse, puis <strong>Enregistrer</strong>
    (ou <strong>Tout enregistrer</strong> en bas pour tout sauver d'un coup). L'œil masque/affiche la
    FAQ côté visiteur. Suppression et création s'appliquent à <strong>toutes les langues</strong> ;
    l'édition est sur la langue sélectionnée ci-dessus.
</p>

<style>
/* ── FAQ : édition en place ── */
.vp-pc-row.faq-row{ grid-template-columns:48px 1fr auto; align-items:start; }
.faq-row .vp-pc-pos-cell{ padding-top:6px; }
.faq-edit-form{ display:flex; flex-direction:column; gap:8px; min-width:0; }
.faq-edit-head{ display:flex; align-items:center; gap:10px; }
.faq-q{ font-weight:600; font-size:14.5px; }
.faq-a{ font-size:13px; line-height:1.55; min-height:2.6em; resize:vertical; overflow:hidden; }
.faq-edit-actions{ display:flex; justify-content:flex-end; }
.faq-hidden-badge{
    display:none; flex:none; font-size:11px; font-weight:600; letter-spacing:.02em;
    padding:2px 8px; border-radius:9999px;
    background:var(--surface-3); color:var(--text-2);
}
.faq-row.is-hidden .faq-hidden-badge{ display:inline-flex; }
.faq-row.is-hidden .faq-q,
.faq-row.is-hidden .faq-a{ opacity:.55; }

/* Champ modifié non enregistré */
.faq-edit-form .is-changed{
    border-color:var(--accent) !important;
    box-shadow:0 0 0 3px var(--accent-soft);
}

/* Feedback boutons */
.faq-edit-form button[type="submit"]{
    transition: transform 140ms cubic-bezier(.16,1,.3,1),
                background-color 180ms ease, border-color 180ms ease, color 180ms ease;
}
.faq-edit-form button[type="submit"]:active{ transform:scale(.97); }
.faq-edit-form button[type="submit"].is-saving{ opacity:.6; cursor:progress; }
.faq-edit-form button[type="submit"].is-saved{ background:var(--success); border-color:var(--success); color:#fff; }
.faq-edit-form button[type="submit"].is-error{ background:var(--error); border-color:var(--error); color:#fff; }

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
    .faq-edit-form button[type="submit"]:active{ transform:none; }
}
</style>

<div class="save-all-bar" id="saveAllBar" role="status" aria-live="polite">
    <span class="save-all-bar__text" id="saveAllText"></span>
    <button type="button" class="btn btn-primary" id="saveAllBtn">Tout enregistrer</button>
</div>

<script>
(function () {
    'use strict';

    /* ───── Auto-déploiement des réponses (affichage entier) ───── */
    const autogrow = (ta) => { ta.style.height = 'auto'; ta.style.height = (ta.scrollHeight + 2) + 'px'; };
    document.querySelectorAll('textarea[data-autogrow]').forEach(ta => {
        autogrow(ta);
        ta.addEventListener('input', () => autogrow(ta));
    });

    /* ───── Visibilité (œil) en AJAX optimiste, synchronisée au champ caché ───── */
    document.querySelectorAll('form[data-faq-toggle]').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const row = form.closest('.faq-row');
            const btn = form.querySelector('button');
            const activeInput = row ? row.querySelector('input[data-active]') : null;
            if (!row || !btn) return;
            const willHide = !row.classList.contains('is-hidden'); // visible → on masque
            row.classList.toggle('is-hidden', willHide);
            btn.textContent = willHide ? '🚫' : '👁';
            btn.title = willHide ? 'Afficher' : 'Masquer';
            if (activeInput) activeInput.disabled = willHide; // masquée → non envoyé → active=0
            btn.disabled = true;
            try {
                const r = await fetch(form.action, { method:'POST', body:new FormData(form), credentials:'same-origin', redirect:'follow' });
                if (!r.ok && r.status >= 400) throw new Error('HTTP ' + r.status);
            } catch (err) {
                row.classList.toggle('is-hidden', !willHide);
                btn.textContent = willHide ? '👁' : '🚫';
                if (activeInput) activeInput.disabled = !willHide;
                console.error('Toggle FAQ failed:', err);
            } finally {
                btn.disabled = false;
            }
        });
    });

    /* ───── Sauvegarde AJAX en place + barre « Tout enregistrer » ───── */
    const forms = Array.from(document.querySelectorAll('form[data-ajax-save]'));
    if (!forms.length) return;

    const bar     = document.getElementById('saveAllBar');
    const barText = document.getElementById('saveAllText');
    const barBtn  = document.getElementById('saveAllBtn');
    if (bar && bar.parentNode !== document.body) document.body.appendChild(bar);

    const controls = (form) => Array.from(form.elements).filter(el =>
        el.name && el.type !== 'hidden' && el.type !== 'submit' && el.type !== 'button');

    const snapshot = (form) => {
        form._saved = {};
        controls(form).forEach(el => { form._saved[el.name] = el.value; });
    };
    forms.forEach(snapshot);

    const refreshDirty = (form) => {
        let dirty = false;
        controls(form).forEach(el => {
            const changed = form._saved[el.name] !== el.value;
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
        barText.innerHTML = '<strong>' + n + '</strong> modification' + (n > 1 ? 's' : '')
            + ' non enregistrée' + (n > 1 ? 's' : '');
        bar.classList.add('is-visible');
    };

    const flashBtn = (btn, cls, label, ms) => {
        btn.classList.remove('is-saving');
        btn.classList.add(cls);
        btn.textContent = label;
        btn.disabled = false;
        window.setTimeout(() => {
            btn.classList.remove(cls);
            btn.textContent = btn.dataset.label;
        }, ms);
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
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                redirect: 'follow',
            });
            if (!r.ok && r.status >= 400) throw new Error('HTTP ' + r.status);
            snapshot(form);
            refreshDirty(form);
            if (btn) flashBtn(btn, 'is-saved', '✓ Enregistré', 1500);
            return true;
        } catch (err) {
            console.error('Save failed:', err);
            if (btn) flashBtn(btn, 'is-error', 'Erreur', 2400);
            return false;
        } finally {
            if (!bulk) updateBar();
        }
    };

    forms.forEach(form => {
        form.addEventListener('input', () => { refreshDirty(form); updateBar(); });
        form.addEventListener('submit', (e) => { e.preventDefault(); saveForm(form, false); });
    });

    barBtn.addEventListener('click', async () => {
        const targets = dirtyForms();
        if (!targets.length) return;
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

    // Actions de page (filtre, Supprimer, Ajouter) : on enregistre d'abord les
    // modifs en attente, puis on laisse la navigation se faire.
    document.querySelectorAll('form:not([data-ajax-save]):not([data-faq-toggle])').forEach(form => {
        form.addEventListener('submit', async (e) => {
            if (e.defaultPrevented) return;
            if (!dirtyForms().length) return;
            e.preventDefault();
            await Promise.all(dirtyForms().map(f => saveForm(f, true)));
            updateBar();
            form.submit();
        });
    });

    // Garde anti-perte sur une vraie sortie (fermeture d'onglet, F5).
    window.addEventListener('beforeunload', (e) => {
        if (dirtyForms().length) { e.preventDefault(); e.returnValue = ''; }
    });
})();
</script>
