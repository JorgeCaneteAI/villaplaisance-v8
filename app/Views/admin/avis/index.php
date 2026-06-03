<div class="page-header">
    <div>
        <h1>Avis clients</h1>
        <p>Gestion des avis Airbnb, Booking, Google et directs.</p>
    </div>
</div>

<!-- ═══ Connexion Google Business Profile ═══ -->
<?php $g = $google ?? []; ?>
<section class="admin-card google-bp-card">
    <div class="google-bp-head">
        <div class="google-bp-info">
            <svg class="google-bp-logo" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.99.66-2.25 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <div>
                <h2>Google Business Profile</h2>
                <p class="google-bp-status">
                    <?php if (!$g['configured']): ?>
                        <span class="badge badge-warning">Non configuré</span>
                        <span class="text-muted text-sm">Variables <code class="info-mono">GOOGLE_OAUTH_*</code> manquantes dans le <code class="info-mono">.env</code> serveur.</span>
                    <?php elseif (!$g['connected']): ?>
                        <span class="badge badge-warning">Non connecté</span>
                        <span class="text-muted text-sm">Clique « Connecter Google » pour autoriser l'accès à ta fiche.</span>
                    <?php else: ?>
                        <span class="badge badge-success">Connecté</span>
                        <?php if (!empty($g['last_sync'])): ?>
                            <span class="text-muted text-sm">Dernière synchro : <?= htmlspecialchars(date('d/m/Y H:i', strtotime($g['last_sync']))) ?></span>
                        <?php else: ?>
                            <span class="text-muted text-sm">Aucune synchro effectuée pour le moment.</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="google-bp-actions btn-group">
            <?php if ($g['configured'] && !$g['connected']): ?>
                <a href="/admin/google/connect" class="btn btn-primary btn-sm">Connecter Google</a>
            <?php elseif ($g['connected']): ?>
                <form method="POST" action="/admin/google/sync" class="inline-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.74L21 8"/><path d="M21 3v5h-5"/></svg>
                        Sync maintenant
                    </button>
                </form>
                <form method="POST" action="/admin/google/disconnect" class="inline-form" onsubmit="return confirm('Déconnecter Google ? Le refresh token sera supprimé du serveur.')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <button type="submit" class="btn btn-sm">Déconnecter</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats -->
<div class="stats-cards stats-cards-5">
    <div class="stat-card">
        <div class="stat-number"><?= $stats['total'] ?></div>
        <div class="stat-label">Avis total</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['airbnb_bb'] ?></div>
        <div class="stat-label">Airbnb chambres</div>
        <div style="font-size:0.7rem;color:#aaa;margin-top:0.2rem;"><?= $stats['avg_airbnb'] ?>/5</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['airbnb_villa'] ?></div>
        <div class="stat-label">Airbnb villa</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['booking'] ?></div>
        <div class="stat-label">Booking</div>
        <div style="font-size:0.7rem;color:#aaa;margin-top:0.2rem;"><?= $stats['avg_booking'] ?>/10</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['google'] ?></div>
        <div class="stat-label">Google</div>
    </div>
</div>

<!-- Add new review -->
<div class="admin-card" id="form-add">
    <h2>Ajouter un avis</h2>
    <form method="POST" action="/admin/avis/create">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:0.75rem;">
            <div class="form-group" style="margin:0;">
                <label>Auteur *</label>
                <input type="text" name="author" required placeholder="Nom du voyageur">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Ville / Pays</label>
                <input type="text" name="origin" placeholder="Paris, France">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Plateforme</label>
                <select name="platform">
                    <option value="airbnb">Airbnb</option>
                    <option value="booking">Booking</option>
                    <option value="google">Google</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Offre</label>
                <select name="offer">
                    <option value="bb">Chambres d'hôtes</option>
                    <option value="villa">Villa entière</option>
                </select>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 120px 140px;gap:0.75rem;margin-top:0.75rem;">
            <div class="form-group" style="margin:0;">
                <label>Commentaire</label>
                <textarea name="content" rows="2" placeholder="Texte de l'avis..."></textarea>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Note</label>
                <input type="number" name="rating" value="5.0" min="0" max="10" step="0.1">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Date</label>
                <input type="date" name="review_date" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <div style="display:flex;gap:1.5rem;align-items:center;margin-top:0.75rem;">
            <label style="display:flex;align-items:center;gap:0.3rem;font-size:0.8rem;cursor:pointer;">
                <input type="checkbox" name="featured" value="1"> Featured
            </label>
            <label style="display:flex;align-items:center;gap:0.3rem;font-size:0.8rem;cursor:pointer;">
                <input type="checkbox" name="home_carousel" value="1"> Carousel accueil
            </label>
            <select name="status" style="width:auto;padding:0.3rem 0.5rem;font-size:0.8rem;border:1px solid var(--admin-border);border-radius:var(--admin-radius);">
                <option value="published">Publié</option>
                <option value="hidden">Masqué</option>
            </select>
            <button type="submit" class="btn btn-primary" style="margin-left:auto;">Ajouter l'avis</button>
        </div>
    </form>
</div>

<!-- List -->
<div class="admin-card">
    <h2 style="display:flex;align-items:center;gap:0.5rem;">
        Liste des avis
        <span style="font-size:0.75rem;color:#888;font-weight:400;margin-left:auto;"><?= count($reviews) ?> avis</span>
    </h2>

    <?php if (empty($reviews)): ?>
        <p class="text-muted">Aucun avis.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Auteur</th>
                <th>Plateforme</th>
                <th>Offre</th>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Date</th>
                <th>Flags</th>
                <th>Statut</th>
                <th style="width:160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reviews as $review): ?>
            <tr style="<?= $review['status'] !== 'published' ? 'opacity:0.5;' : '' ?>">
                <td style="min-width:120px;">
                    <strong style="font-size:0.85rem;"><?= htmlspecialchars($review['author']) ?></strong>
                    <?php if ($review['origin']): ?>
                    <br><span style="font-size:0.7rem;color:#888;"><?= htmlspecialchars($review['origin']) ?></span>
                    <?php endif; ?>
                </td>
                <td style="font-size:0.78rem;"><?= htmlspecialchars($review['platform']) ?></td>
                <td><span class="badge badge-info"><?= $review['offer'] ?></span></td>
                <td style="font-weight:600;font-size:0.85rem;">
                    <?= $review['rating'] ?>/<?= $review['platform'] === 'booking' ? '10' : '5' ?>
                </td>
                <td style="max-width:300px;font-size:0.78rem;color:#555;">
                    <?php if ($review['content']): ?>
                        <?= htmlspecialchars(mb_strimwidth($review['content'], 0, 120, '...')) ?>
                    <?php else: ?>
                        <span style="color:#ccc;font-style:italic;">Sans commentaire</span>
                    <?php endif; ?>
                </td>
                <td style="font-size:0.78rem;color:#888;white-space:nowrap;"><?= $review['review_date'] ? date('m/Y', strtotime($review['review_date'])) : '—' ?></td>
                <td style="font-size:0.7rem;">
                    <?php if ($review['featured']): ?><span class="badge" style="background:#fff3cd;color:#856404;">Star</span> <?php endif; ?>
                    <?php if ($review['home_carousel']): ?><span class="badge" style="background:#d4edda;color:#155724;">Home</span><?php endif; ?>
                </td>
                <td>
                    <?php if ($review['status'] === 'published'): ?>
                    <span class="badge badge-success">Visible</span>
                    <?php else: ?>
                    <span class="badge badge-warning">Masqué</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display:flex;gap:0.25rem;flex-wrap:wrap;">
                        <a href="/admin/avis/<?= $review['id'] ?>/edit" class="btn btn-sm btn-primary">Éditer</a>
                        <form method="POST" action="/admin/avis/<?= $review['id'] ?>/toggle" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <button type="submit" class="btn btn-sm"><?= $review['status'] === 'published' ? 'Masquer' : 'Publier' ?></button>
                        </form>
                        <form method="POST" action="/admin/avis/<?= $review['id'] ?>/delete" onsubmit="return confirm('Supprimer cet avis ?')" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Suppr.</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
