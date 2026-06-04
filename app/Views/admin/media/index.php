<?php
/**
 * @var array $media
 * @var array $folders
 * @var string $folder
 * @var string $search
 * @var array $stats
 * @var string $csrf
 */
use App\Controllers\Admin\MediaController;
?>

<div class="page-header">
    <div>
        <h1>Médiathèque</h1>
        <p>
            <strong><?= $stats['total'] ?></strong> fichier<?= $stats['total'] > 1 ? 's' : '' ?>
            · <strong><?= MediaController::formatSize($stats['size']) ?></strong> au total
            · <strong><?= $stats['webp'] ?></strong> en WebP
        </p>
    </div>
</div>

<!-- ═══ Zone d'upload ═══ -->
<section class="admin-card">
    <h2>
        Importer des images
        <button type="button" class="btn btn-sm card-action-btn" data-open-unsplash>
            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" aria-hidden="true"><path d="M7.5 6.75V0h9v6.75h-9zm9 3.75H24V24H0V10.5h7.5v6.75h9V10.5z"/></svg>
            Chercher sur Unsplash
        </button>
    </h2>
    <form method="POST" action="/admin/media/upload" enctype="multipart/form-data" class="media-upload-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <label for="file-input" class="media-dropzone" id="dropzone">
            <svg class="media-dropzone-icon" viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" x2="12" y1="3" y2="15"/>
            </svg>
            <p class="media-dropzone-title">Glisse-dépose tes images ici</p>
            <p class="media-dropzone-hint">ou clique pour parcourir · JPG, PNG, WebP, AVIF, max 5 Mo · conversion auto en WebP</p>
            <input type="file" id="file-input" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif,image/avif" hidden>
        </label>

        <div id="file-preview" class="media-preview-list"></div>

        <div class="media-upload-footer">
            <label class="filter-label">
                <span>Dossier de destination</span>
                <select name="folder">
                    <?php foreach ($folders as $f): ?>
                    <option value="<?= htmlspecialchars($f) ?>"><?= ucfirst(htmlspecialchars($f)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="btn btn-primary" id="upload-btn" disabled>Importer</button>
        </div>
    </form>
</section>

<!-- ═══ Filtres ═══ -->
<div class="filter-toolbar mb-2">
    <div class="media-folder-tabs">
        <a href="/admin/media" class="media-tab <?= $folder === '' ? 'is-active' : '' ?>">Tous</a>
        <?php foreach ($folders as $f): ?>
        <a href="/admin/media?folder=<?= urlencode($f) ?>" class="media-tab <?= $folder === $f ? 'is-active' : '' ?>"><?= ucfirst(htmlspecialchars($f)) ?></a>
        <?php endforeach; ?>
    </div>
    <form method="GET" action="/admin/media" class="media-search">
        <?php if ($folder): ?>
            <input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
        <?php endif; ?>
        <svg class="media-search-icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher (nom, alt, tags)…">
    </form>
</div>

<!-- ═══ Grille ═══ -->
<?php if (empty($media)): ?>
<div class="empty-state">
    <div class="empty-icon">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
    </div>
    <strong>Aucun média<?= $search ? " pour « {$search} »" : '' ?>.</strong>
    <p class="text-muted text-sm">Upload des images avec la zone ci-dessus.</p>
</div>
<?php else: ?>
<div class="media-grid">
    <?php foreach ($media as $m):
        $src = file_exists(ROOT . '/public/uploads/' . $m['filename'])
            ? '/uploads/' . htmlspecialchars($m['filename'])
            : ImageService::placeholder((int)$m['width'] ?: 300, (int)$m['height'] ?: 200, $m['filename']);
        $altMissing = empty($m['alt_fr']);
    ?>
    <article class="media-card">
        <a href="/admin/media/<?= $m['id'] ?>/edit" class="media-card-thumb" title="<?= htmlspecialchars($m['filename']) ?>">
            <img src="<?= $src ?>" alt="<?= htmlspecialchars($m['alt_fr'] ?? $m['filename']) ?>" loading="lazy">
            <?php if ($altMissing): ?>
                <span class="media-card-badge media-card-badge--warn" title="Cette image n'a pas d'attribut alt, important pour le SEO et l'accessibilité">alt manquant</span>
            <?php endif; ?>
        </a>
        <div class="media-card-info">
            <div class="media-card-name" title="<?= htmlspecialchars($m['filename']) ?>"><?= htmlspecialchars($m['filename']) ?></div>
            <div class="media-card-meta">
                <span class="media-card-dim"><?= $m['width'] ?>×<?= $m['height'] ?></span>
                <span class="media-card-size"><?= MediaController::formatSize((int)$m['file_size']) ?></span>
                <span class="badge badge-meta"><?= htmlspecialchars($m['folder']) ?></span>
            </div>
        </div>
        <div class="media-card-actions">
            <a href="/admin/media/<?= $m['id'] ?>/edit" class="btn btn-sm btn-primary">Modifier</a>
            <form method="POST" action="/admin/media/<?= $m['id'] ?>/delete" onsubmit="return confirm('Supprimer ce média ? (irréversible)')" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <button type="submit" class="btn btn-sm" title="Supprimer">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
            </form>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ═══ Modal Unsplash ═══ -->
<div class="unsplash-modal-backdrop" id="unsplash-modal" hidden>
    <div class="unsplash-modal" role="dialog" aria-modal="true" aria-labelledby="unsplash-title">
        <header class="unsplash-modal-head">
            <h2 id="unsplash-title">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M7.5 6.75V0h9v6.75h-9zm9 3.75H24V24H0V10.5h7.5v6.75h9V10.5z"/></svg>
                Chercher sur Unsplash
            </h2>
            <button type="button" class="unsplash-modal-close" aria-label="Fermer">×</button>
        </header>

        <div class="unsplash-modal-toolbar">
            <form class="unsplash-search" id="unsplash-search-form">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="search" id="unsplash-query" placeholder="ex. lavender provence, vineyard, bedroom interior…" autocomplete="off" required>
                <button type="submit" class="btn btn-sm btn-primary">Chercher</button>
            </form>
            <label class="filter-label">
                <span>Dossier</span>
                <select id="unsplash-folder">
                    <?php foreach ($folders as $f): ?>
                    <option value="<?= htmlspecialchars($f) ?>"><?= ucfirst(htmlspecialchars($f)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div class="unsplash-grid" id="unsplash-grid" aria-live="polite">
            <p class="unsplash-hint">Tape une recherche pour voir des résultats.</p>
        </div>

        <footer class="unsplash-modal-foot">
            <span class="unsplash-credit text-muted text-sm">Photos par <a href="https://unsplash.com" target="_blank" rel="noopener">Unsplash</a> · le crédit photographe est conservé dans le champ <code class="info-mono">credit</code> de l'image</span>
        </footer>
    </div>
</div>

<script>
(function () {
    'use strict';

    const CSRF = <?= json_encode($csrf) ?>;
    const modal = document.getElementById('unsplash-modal');
    const grid  = document.getElementById('unsplash-grid');
    const form  = document.getElementById('unsplash-search-form');
    const input = document.getElementById('unsplash-query');
    const folderSel = document.getElementById('unsplash-folder');
    const closeBtn = modal?.querySelector('.unsplash-modal-close');
    const openBtn  = document.querySelector('[data-open-unsplash]');

    function open()  { modal.hidden = false; document.body.style.overflow = 'hidden'; setTimeout(() => input.focus(), 80); }
    function close() { modal.hidden = true; document.body.style.overflow = ''; }

    openBtn?.addEventListener('click', open);
    closeBtn?.addEventListener('click', close);
    modal?.addEventListener('click', e => { if (e.target === modal) close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) close(); });

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function renderResults(results) {
        if (!results.length) {
            grid.innerHTML = '<p class="unsplash-hint">Aucun résultat. Essaie une autre recherche.</p>';
            return;
        }
        grid.innerHTML = results.map(p => `
            <article class="unsplash-card" data-id="${escapeHtml(p.id)}">
                <button type="button" class="unsplash-card-img" data-action="import" title="Importer cette image" style="background:${p.color || '#eee'}">
                    <img src="${escapeHtml(p.thumb_url)}" alt="${escapeHtml(p.alt || '')}" loading="lazy">
                    <span class="unsplash-card-overlay">+ Importer</span>
                </button>
                <div class="unsplash-card-meta">
                    <a href="${escapeHtml(p.photographer_url)}?utm_source=villa_plaisance&utm_medium=referral" target="_blank" rel="noopener" class="unsplash-card-author">${escapeHtml(p.photographer)}</a>
                </div>
            </article>
        `).join('');

        // Bind import handlers
        grid.querySelectorAll('[data-action="import"]').forEach((btn, i) => {
            btn.addEventListener('click', () => importPhoto(results[i]));
        });
    }

    async function search(q, page = 1) {
        grid.innerHTML = '<p class="unsplash-hint">Recherche…</p>';
        try {
            const r = await fetch(`/admin/media/unsplash/search?q=${encodeURIComponent(q)}&page=${page}`, { credentials: 'same-origin' });
            const data = await r.json();
            if (!data.ok) { grid.innerHTML = `<p class="unsplash-hint" style="color:var(--error)">Erreur : ${escapeHtml(data.error || 'inconnue')}</p>`; return; }
            renderResults(data.results || []);
        } catch (e) {
            grid.innerHTML = `<p class="unsplash-hint" style="color:var(--error)">Erreur réseau : ${escapeHtml(e.message)}</p>`;
        }
    }

    async function importPhoto(p) {
        const card = grid.querySelector(`[data-id="${p.id}"]`);
        card?.classList.add('is-importing');
        const overlay = card?.querySelector('.unsplash-card-overlay');
        if (overlay) overlay.textContent = 'Import…';

        const fd = new FormData();
        fd.append('csrf_token', CSRF);
        fd.append('photo_id', p.id);
        fd.append('folder', folderSel.value || 'general');
        fd.append('alt_en', p.alt || '');
        fd.append('photographer', p.photographer || '');
        fd.append('photographer_url', p.photographer_url || '');
        fd.append('source_url', p.unsplash_url || '');
        fd.append('download_url', p.regular_url || p.download_url || '');
        fd.append('download_track', p.download_track || '');
        (p.tags || []).forEach(t => fd.append('tags[]', t));

        try {
            const r = await fetch('/admin/media/unsplash/import', { method: 'POST', body: fd, credentials: 'same-origin' });
            const data = await r.json();
            if (!data.ok) throw new Error(data.error || 'inconnue');
            if (overlay) overlay.textContent = '✓ Importé';
            card?.classList.remove('is-importing');
            card?.classList.add('is-done');
        } catch (e) {
            if (overlay) overlay.textContent = '✕ ' + e.message;
            card?.classList.remove('is-importing');
            card?.classList.add('is-error');
        }
    }

    form?.addEventListener('submit', e => {
        e.preventDefault();
        const q = input.value.trim();
        if (q) search(q);
    });

    /* ── Reste : Upload classique ── */
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');
    const uploadBtn = document.getElementById('upload-btn');
    const preview = document.getElementById('file-preview');
    if (!dropzone || !fileInput) return;

    function showPreview(files) {
        preview.innerHTML = '';
        if (!files.length) { uploadBtn.disabled = true; return; }
        uploadBtn.disabled = false;
        Array.from(files).forEach(f => {
            const div = document.createElement('div');
            div.className = 'media-preview-item';
            if (f.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(f);
                div.appendChild(img);
            }
            const span = document.createElement('span');
            span.textContent = f.name + ' (' + (f.size / 1024).toFixed(0) + ' Ko)';
            div.appendChild(span);
            preview.appendChild(div);
        });
    }

    fileInput.addEventListener('change', () => showPreview(fileInput.files));

    ['dragover', 'dragenter'].forEach(ev =>
        dropzone.addEventListener(ev, e => { e.preventDefault(); dropzone.classList.add('is-dragover'); })
    );
    ['dragleave', 'dragend'].forEach(ev =>
        dropzone.addEventListener(ev, () => dropzone.classList.remove('is-dragover'))
    );
    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        dropzone.classList.remove('is-dragover');
        fileInput.files = e.dataTransfer.files;
        showPreview(e.dataTransfer.files);
    });
})();
</script>
