<?php
/**
 * @var array  $settings
 * @var array  $bookingBB
 * @var array  $bookingVilla
 * @var array  $socials
 * @var array  $amenities       Map catégorie => items[]
 * @var array  $amenityIndex    Map [lang][category:position] => item
 * @var array  $langs           ['fr','en','es']
 * @var array  $langLabels      ['fr' => 'FR', ...]
 * @var string $csrf
 */
?>

<div class="page-header">
    <div>
        <h1>Réglages</h1>
        <p>Informations générales du site, sécurité, plateformes de réservation, réseaux sociaux et catalogue d'équipements multilingue.</p>
    </div>
</div>

<!-- ═══ 1. INFORMATIONS GÉNÉRALES ═══ -->
<section class="admin-card">
    <h2>Informations générales</h2>
    <form method="POST" action="/admin/reglages/save" class="admin-form" data-ajax-save>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="site_name">Nom du site</label>
                <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'Villa Plaisance') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email de contact</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" placeholder="+33 4 90 33 00 49">
            </div>
            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($settings['address'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="ga4_measurement_id">Google Analytics GA4 (Measurement ID)</label>
            <input type="text" id="ga4_measurement_id" name="ga4_measurement_id" value="<?= htmlspecialchars($settings['ga4_measurement_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX" class="input-num" style="max-width:280px">
            <p class="form-hint">Laisse vide pour désactiver Google Analytics.</p>
        </div>
        <div class="form-card-actions">
            <span class="text-muted text-sm">Modifié <?= !empty($settings['updated_at']) ? 'le ' . date('d/m/Y H:i', strtotime($settings['updated_at'])) : 'jamais' ?></span>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</section>

<!-- ═══ 2. CODE PIN ═══ -->
<section class="admin-card">
    <h2>Code PIN de sécurité</h2>
    <p class="form-hint mb-2">Un PIN à 6 chiffres demandé après la saisie du mot de passe à la connexion admin. Laisse les 3 champs vides et valide pour désactiver le PIN.</p>
    <form method="POST" action="/admin/reglages/pin" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <div class="form-grid form-grid-3">
            <div class="form-group">
                <label for="current_pin">PIN actuel</label>
                <input type="password" id="current_pin" name="current_pin" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" placeholder="••••••" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="new_pin">Nouveau PIN</label>
                <input type="password" id="new_pin" name="new_pin" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" placeholder="6 chiffres" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm_pin">Confirmer PIN</label>
                <input type="password" id="confirm_pin" name="confirm_pin" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" placeholder="6 chiffres" autocomplete="new-password">
            </div>
        </div>
        <div class="form-card-actions">
            <span class="text-muted text-sm">Le PIN actuel n'est requis que si tu en as déjà un.</span>
            <button type="submit" class="btn btn-primary">Enregistrer le PIN</button>
        </div>
    </form>
</section>

<!-- ═══ 3. LIENS DE RÉSERVATION ═══ -->
<section class="admin-card">
    <h2>Liens de réservation</h2>
    <p class="form-hint mb-2">Plateformes externes (Booking, Airbnb…) affichées sur les pages Chambres d'hôtes et Villa entière.</p>

    <?php foreach (['bb' => "Chambres d'hôtes", 'villa' => "Villa entière"] as $offer => $offerLabel):
        $list = $offer === 'bb' ? $bookingBB : $bookingVilla;
    ?>
    <div class="settings-block">
        <h3 class="settings-block-title"><?= htmlspecialchars($offerLabel) ?></h3>

        <?php if (!empty($list)): ?>
            <div class="settings-list">
                <?php foreach ($list as $link): ?>
                <div class="settings-row">
                    <form method="POST" action="/admin/reglages/booking/<?= $link['id'] ?>/update" class="settings-row-form" data-ajax-save>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="text" name="platform_name" value="<?= htmlspecialchars($link['platform_name']) ?>" placeholder="Nom (Booking, Airbnb…)" class="settings-name">
                        <input type="url" name="url" value="<?= htmlspecialchars($link['url']) ?>" placeholder="https://…" class="settings-url">
                        <button type="submit" class="btn btn-sm btn-primary">OK</button>
                    </form>
                    <form method="POST" action="/admin/reglages/booking/<?= $link['id'] ?>/delete" onsubmit="return confirm('Supprimer ce lien ?')" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <button type="submit" class="btn btn-sm" title="Supprimer">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted text-sm">Aucun lien pour cette offre.</p>
        <?php endif; ?>

        <form method="POST" action="/admin/reglages/booking/add" class="settings-add-row">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="offer" value="<?= $offer ?>">
            <input type="text" name="platform_name" placeholder="+ Nouvelle plateforme" class="settings-name" required>
            <input type="url" name="url" placeholder="https://…" class="settings-url" required>
            <button type="submit" class="btn btn-sm">Ajouter</button>
        </form>
    </div>
    <?php endforeach; ?>
</section>

<!-- ═══ 4. RÉSEAUX SOCIAUX ═══ -->
<section class="admin-card">
    <h2>Réseaux sociaux</h2>
    <p class="form-hint mb-2">Comptes affichés dans le footer du site et utilisés dans le JSON-LD Organization.</p>

    <?php if (!empty($socials)): ?>
    <div class="settings-list mb-2">
        <?php foreach ($socials as $social): ?>
        <div class="settings-row">
            <form method="POST" action="/admin/reglages/social/<?= $social['id'] ?>/update" class="settings-row-form" data-ajax-save>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="text" name="name" value="<?= htmlspecialchars($social['name']) ?>" placeholder="Nom" class="settings-name">
                <input type="url" name="url" value="<?= htmlspecialchars($social['url']) ?>" placeholder="https://…" class="settings-url">
                <button type="submit" class="btn btn-sm btn-primary">OK</button>
            </form>
            <form method="POST" action="/admin/reglages/social/<?= $social['id'] ?>/delete" onsubmit="return confirm('Supprimer ce réseau ?')" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <button type="submit" class="btn btn-sm" title="Supprimer">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/admin/reglages/social/add" class="settings-add-row">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="text" name="name" placeholder="+ Nouveau réseau (Instagram, Facebook…)" class="settings-name" required>
        <input type="url" name="url" placeholder="https://…" class="settings-url" required>
        <button type="submit" class="btn btn-sm">Ajouter</button>
    </form>
</section>

<!-- ═══ 5. POINTS FORTS & ÉQUIPEMENTS (multilingue) ═══ -->
<section class="admin-card">
    <h2>Points forts &amp; équipements</h2>
    <p class="form-hint mb-2">Catalogue trilingue (FR / EN / ES). Les toggles <strong>B&amp;B</strong> et <strong>Villa</strong> indiquent où l'équipement est affiché côté site.</p>

    <?php if (!empty($amenities)): ?>
        <?php foreach ($amenities as $category => $items): ?>
        <div class="amenity-category-block">
            <h3 class="settings-block-title">
                <?= htmlspecialchars($category) ?>
                <span class="badge badge-meta"><?= count($items) ?></span>
            </h3>

            <?php foreach ($items as $item):
                $key = $item['category'] . ':' . $item['position'];
            ?>
            <div class="amenity-row">
                <!-- En-tête : nom FR + toggles BB/Villa + suppression -->
                <div class="amenity-row-head">
                    <span class="amenity-pos">#<?= (int)$item['position'] ?></span>
                    <div class="amenity-toggles">
                        <form method="POST" action="/admin/reglages/amenity/<?= $item['id'] ?>/toggle" class="inline-form" data-toggle-form>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="field" value="offer_bb">
                            <button type="submit" class="amenity-toggle <?= $item['offer_bb'] ? 'is-on' : 'is-off' ?>" title="Affiché sur la page B&B" data-label="B&amp;B">
                                B&amp;B <span class="amenity-toggle-mark"><?= $item['offer_bb'] ? '✓' : '·' ?></span>
                            </button>
                        </form>
                        <form method="POST" action="/admin/reglages/amenity/<?= $item['id'] ?>/toggle" class="inline-form" data-toggle-form>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="field" value="offer_villa">
                            <button type="submit" class="amenity-toggle <?= $item['offer_villa'] ? 'is-on' : 'is-off' ?>" title="Affiché sur la page Villa" data-label="Villa">
                                Villa <span class="amenity-toggle-mark"><?= $item['offer_villa'] ? '✓' : '·' ?></span>
                            </button>
                        </form>
                    </div>
                    <form method="POST" action="/admin/reglages/amenity/<?= $item['id'] ?>/delete" onsubmit="return confirm('Supprimer cet équipement (toutes les langues) ?')" class="inline-form amenity-delete-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <button type="submit" class="btn btn-sm" title="Supprimer toutes les langues">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Corps : 3 colonnes FR/EN/ES -->
                <div class="amenity-langs">
                    <?php foreach ($langs as $l):
                        $a = $amenityIndex[$l][$key] ?? null;
                    ?>
                    <div class="amenity-lang">
                        <div class="amenity-lang-label"><?= htmlspecialchars($langLabels[$l]) ?></div>
                        <?php if ($a): ?>
                        <form method="POST" action="/admin/reglages/amenity/<?= $a['id'] ?>/update" class="amenity-lang-form" data-ajax-save>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="category" value="<?= htmlspecialchars($a['category']) ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($a['name'] ?? '') ?>" placeholder="Nom" class="amenity-lang-name">
                            <input type="text" name="description" value="<?= htmlspecialchars($a['description'] ?? '') ?>" placeholder="Description" class="amenity-lang-desc">
                            <button type="submit" class="btn btn-sm btn-primary">Sauver</button>
                        </form>
                        <?php else: ?>
                        <div class="amenity-lang-missing">Non créé</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">Aucun équipement enregistré.</p>
    <?php endif; ?>

    <hr class="form-divider">

    <h3 class="settings-block-title">Ajouter un équipement</h3>
    <form method="POST" action="/admin/reglages/amenity/add" class="amenity-add-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="text" name="category" placeholder="Catégorie (ex. Confort, Bien-être…)" list="amenity-categories" required>
        <input type="text" name="name" placeholder="Nom de l'équipement" required>
        <input type="text" name="description" placeholder="Description (optionnel)">
        <label class="amenity-add-check"><input type="checkbox" name="offer_bb" value="1" checked> Afficher B&amp;B</label>
        <label class="amenity-add-check"><input type="checkbox" name="offer_villa" value="1" checked> Afficher Villa</label>
        <button type="submit" class="btn btn-primary">Ajouter (3 langues)</button>
    </form>

    <?php if (!empty($amenities)): ?>
    <datalist id="amenity-categories">
        <?php foreach (array_keys($amenities) as $cat): ?>
        <option value="<?= htmlspecialchars($cat) ?>">
        <?php endforeach; ?>
    </datalist>
    <?php endif; ?>
</section>

<style>
/* ── Sauvegarde Réglages : feedback en place + barre « Tout enregistrer » ── */
form[data-ajax-save] button[type="submit"]{
    transition: transform 140ms cubic-bezier(.16,1,.3,1),
                background-color 180ms ease, border-color 180ms ease, color 180ms ease;
}
form[data-ajax-save] button[type="submit"]:active{ transform: scale(.97); }
form[data-ajax-save] button[type="submit"].is-saving{ opacity:.6; cursor:progress; }
form[data-ajax-save] button[type="submit"].is-saved{
    background:var(--success); border-color:var(--success); color:#fff;
}
form[data-ajax-save] button[type="submit"].is-error{
    background:var(--error); border-color:var(--error); color:#fff;
}
form[data-ajax-save] .is-changed{
    border-color:var(--accent) !important;
    box-shadow:0 0 0 3px var(--accent-soft);
}

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
.save-all-bar.is-visible{
    transform:translateX(-50%) translateY(0); opacity:1; pointer-events:auto;
}
.save-all-bar__text{ font-size:13.5px; color:var(--text-2); white-space:nowrap; }
.save-all-bar__text strong{ color:var(--text); font-weight:600; }
.save-all-bar .btn:active{ transform:scale(.97); }
@media (max-width:560px){
    .save-all-bar{ left:12px; right:12px; bottom:12px; transform:translateY(160%);
        justify-content:space-between; }
    .save-all-bar.is-visible{ transform:translateY(0); }
    .save-all-bar__text{ white-space:normal; }
}
@media (prefers-reduced-motion: reduce){
    .save-all-bar,
    .save-all-bar.is-visible{ transition:opacity 200ms ease; transform:translateX(-50%); }
    form[data-ajax-save] button[type="submit"]:active{ transform:none; }
}
</style>

<div class="save-all-bar" id="saveAllBar" role="status" aria-live="polite">
    <span class="save-all-bar__text" id="saveAllText"></span>
    <button type="button" class="btn btn-primary" id="saveAllBtn">Tout enregistrer</button>
</div>

<script>
(function () {
    'use strict';
    /* Toggle optimistic des boutons B&B / Villa : flip immédiat de la classe
       + ✓/· puis POST en arrière-plan. Pas de reload, pas de scroll perdu. */
    document.querySelectorAll('form[data-toggle-form]').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('.amenity-toggle');
            const mark = btn?.querySelector('.amenity-toggle-mark');
            if (!btn || !mark) return;

            const wasOn = btn.classList.contains('is-on');
            // Optimistic flip
            btn.classList.toggle('is-on', !wasOn);
            btn.classList.toggle('is-off', wasOn);
            mark.textContent = wasOn ? '·' : '✓';
            btn.disabled = true;

            try {
                const fd = new FormData(form);
                const r = await fetch(form.action, {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin',
                    redirect: 'follow',
                });
                if (!r.ok && r.status >= 400) throw new Error('HTTP ' + r.status);
            } catch (err) {
                // Revert si fail
                btn.classList.toggle('is-on', wasOn);
                btn.classList.toggle('is-off', !wasOn);
                mark.textContent = wasOn ? '✓' : '·';
                console.error('Toggle failed:', err);
            } finally {
                btn.disabled = false;
            }
        });
    });

    /* ───── Sauvegarde AJAX en place + barre « Tout enregistrer » ─────
       Chaque form[data-ajax-save] enregistre sans recharger la page.
       La barre flottante n'apparaît que s'il reste des modifs et les
       sauve toutes d'un coup en réutilisant les mêmes endpoints. */
    const forms = Array.from(document.querySelectorAll('form[data-ajax-save]'));
    if (!forms.length) return;

    const bar     = document.getElementById('saveAllBar');
    const barText = document.getElementById('saveAllText');
    const barBtn  = document.getElementById('saveAllBtn');
    // En fixed : remonter la barre sur <body> pour éviter tout ancêtre transformé.
    if (bar && bar.parentNode !== document.body) document.body.appendChild(bar);

    // Champs pertinents d'un form (hors hidden / submit / bouton).
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

    // Actions de page (Supprimer / Ajouter / PIN) : on enregistre d'abord les
    // modifs en attente, puis on laisse la navigation se faire. Plus de dialogue
    // « quitter la page » parasite, et aucune saisie perdue.
    document.querySelectorAll('form:not([data-ajax-save]):not([data-toggle-form])').forEach(form => {
        form.addEventListener('submit', async (e) => {
            if (e.defaultPrevented) return;        // confirm annulé ou champ requis vide
            if (!dirtyForms().length) return;       // rien en attente : navigation normale
            e.preventDefault();
            await Promise.all(dirtyForms().map(f => saveForm(f, true)));
            updateBar();
            form.submit();                          // relance sans re-déclencher submit/confirm
        });
    });

    // Garde anti-perte sur une vraie sortie (fermeture d'onglet, F5, autre URL)
    // s'il reste — exceptionnellement — des modifs non enregistrées.
    window.addEventListener('beforeunload', (e) => {
        if (dirtyForms().length) { e.preventDefault(); e.returnValue = ''; }
    });
})();
</script>
