/**
 * Admin / sections / edit — interactions V8.
 *
 * MediaPicker (Phase 4 Session 2 — L1) :
 *   Ouvre un modal listant /admin/api/media (records vp_media), permet
 *   de filtrer par dossier + recherche texte, et au clic place l'ID
 *   dans l'input cible + met à jour la miniature voisine.
 *
 * Markup attendu côté vue (pour chaque champ media_id) :
 *   <input type="number" id="f-image_id" name="fields[image_id]" value="42">
 *   <button type="button" class="vp-mp-trigger" data-target="f-image_id">Choisir…</button>
 *   <img class="vp-mp-preview" data-for="f-image_id" src="...">    (optionnel)
 *   <span class="vp-mp-preview-label" data-for="f-image_id">id=42</span>  (optionnel)
 *
 * Le modal lui-même est rendu une seule fois en bas de edit.php
 * (id="vp-media-picker"). Une seule instance par page suffit — l'état
 * « quel input cibler » est stocké en module-private.
 */
(function () {
    'use strict';

    const modal = document.getElementById('vp-media-picker');
    if (!modal) return;

    const grid = modal.querySelector('.vp-mp-grid');
    const search = modal.querySelector('.vp-mp-search');
    const folderSel = modal.querySelector('.vp-mp-folder');
    const closeBtn = modal.querySelector('.vp-mp-close');
    const cancelBtn = modal.querySelector('.vp-mp-cancel');
    const count = modal.querySelector('.vp-mp-count');

    let allMedia = [];
    let fetched = false;
    let activeTargetId = null;

    function open(targetId) {
        if (!targetId) return;
        activeTargetId = targetId;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        if (!fetched) {
            load();
        } else {
            render();
        }
        setTimeout(() => search.focus(), 50);
    }

    function close() {
        modal.hidden = true;
        activeTargetId = null;
        document.body.style.overflow = '';
    }

    function load() {
        grid.innerHTML = '<p class="vp-mp-empty">Chargement…</p>';
        fetch('/admin/api/media?limit=500', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                allMedia = data.media || [];
                fetched = true;
                folderSel.innerHTML = '<option value="">Tous les dossiers</option>';
                (data.folders || []).forEach(f => {
                    const opt = document.createElement('option');
                    opt.value = f.folder;
                    opt.textContent = `${f.folder} (${f.count})`;
                    folderSel.appendChild(opt);
                });
                render();
            })
            .catch(err => {
                console.error('MediaPicker fetch error', err);
                grid.innerHTML = '<p class="vp-mp-empty" style="color:#c00">Erreur de chargement. Réessaie.</p>';
            });
    }

    function render() {
        const q = (search.value || '').trim().toLowerCase();
        const folder = folderSel.value;
        const filtered = allMedia.filter(m => {
            if (folder && m.folder !== folder) return false;
            if (q) {
                const hay = `${m.filename} ${m.alt_fr || ''} ${m.title || ''}`.toLowerCase();
                if (!hay.includes(q)) return false;
            }
            return true;
        });
        count.textContent = `${filtered.length} média${filtered.length > 1 ? 's' : ''} sur ${allMedia.length}`;
        if (filtered.length === 0) {
            grid.innerHTML = '<p class="vp-mp-empty">Aucun média trouvé.</p>';
            return;
        }
        grid.innerHTML = filtered.map(m => {
            const label = m.filename
                .replace('villa-plaisance-', '')
                .replace(/\.(webp|jpg|png|gif|avif)$/i, '');
            return `
                <div class="vp-mp-card" role="button" tabindex="0" data-id="${m.id}" title="${escapeAttr(m.alt_fr || m.filename)}">
                    <div class="vp-mp-card-thumb">
                        <img src="${escapeAttr(m.url)}" alt="" loading="lazy" decoding="async" width="200" height="200">
                    </div>
                    <div class="vp-mp-card-meta">
                        <span class="vp-mp-card-id">#${m.id}</span> · ${escapeAttr(label)}
                    </div>
                </div>
            `;
        }).join('');
        grid.querySelectorAll('.vp-mp-card').forEach(card => {
            const id = parseInt(card.dataset.id, 10);
            card.addEventListener('click', () => pick(id));
            card.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    pick(id);
                }
            });
        });
    }

    function pick(id) {
        if (!activeTargetId) return close();
        const input = document.getElementById(activeTargetId);
        if (!input) return close();
        input.value = id;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
        const media = allMedia.find(m => m.id === id);
        if (media) {
            const sel = `[data-for="${cssEscape(activeTargetId)}"]`;
            const preview = document.querySelector(`img.vp-mp-preview${sel}`);
            if (preview) {
                preview.src = media.url;
                preview.alt = media.alt_fr || media.filename;
                preview.hidden = false;
            }
            const label = document.querySelector(`.vp-mp-preview-label${sel}`);
            if (label) label.textContent = `id=${id}`;
        }
        close();
    }

    function escapeAttr(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function cssEscape(s) {
        return (window.CSS && CSS.escape) ? CSS.escape(s) : String(s).replace(/[^a-zA-Z0-9_-]/g, '\\$&');
    }

    // Délégation : un clic sur un bouton .vp-mp-trigger ouvre le modal
    // sur l'input ciblé par data-target. Ça marche pour les boutons rendus
    // après chargement (futurs items de repeater en L2).
    document.addEventListener('click', e => {
        const trigger = e.target.closest('.vp-mp-trigger');
        if (trigger) {
            e.preventDefault();
            open(trigger.dataset.target);
        }
    });

    closeBtn.addEventListener('click', close);
    cancelBtn.addEventListener('click', close);
    modal.addEventListener('click', e => { if (e.target === modal) close(); });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.hidden) close();
    });
    search.addEventListener('input', render);
    folderSel.addEventListener('change', render);
})();
