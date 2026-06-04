<?php
/**
 * @var array $files
 * @var bool  $htaccessExists
 */
$csrf = $_SESSION['csrf_token'] ?? '';
?>
<div class="page-header">
    <div>
        <h1>Fichiers SEO</h1>
        <p>Fichiers servis dynamiquement à la racine du site. Sitemap et hreflang générés à partir des pages et articles publiés.</p>
    </div>
</div>

<!-- ═══ Liste des fichiers ═══ -->
<div class="admin-card admin-card--flush mb-2">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Fichier</th>
                <th>Type</th>
                <th>Dernière modification</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>

            <!-- Sitemap (auto-généré) -->
            <tr>
                <td>
                    <code class="info-mono">sitemap.xml</code>
                    <div class="text-sm text-muted">Pages + articles publiés</div>
                </td>
                <td><span class="badge badge-success">Auto-généré</span></td>
                <td class="text-muted text-sm">Dynamique (temps réel)</td>
                <td>
                    <div class="btn-group">
                        <a href="/sitemap.xml" target="_blank" rel="noopener" class="btn btn-sm">Voir</a>
                    </div>
                </td>
            </tr>

            <!-- Fichiers personnalisés (robots, llms, ads, humans, etc.) -->
            <?php foreach ($files as $f):
                $url = '/' . $f['filename'];
            ?>
            <tr>
                <td>
                    <code class="info-mono"><?= htmlspecialchars($f['filename']) ?></code>
                </td>
                <td><span class="badge badge-info">Personnalisé</span></td>
                <td class="text-muted text-sm"><?= date('d/m/Y H:i', strtotime($f['updated_at'])) ?></td>
                <td>
                    <div class="btn-group">
                        <a href="/admin/seo-files/<?= $f['id'] ?>/edit" class="btn btn-sm btn-primary">Éditer</a>
                        <a href="<?= $url ?>" target="_blank" rel="noopener" class="btn btn-sm">Voir</a>
                        <form method="POST" action="/admin/seo-files/<?= $f['id'] ?>/delete" onsubmit="return confirm('Supprimer ce fichier ?')" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <button type="submit" class="btn btn-sm" title="Supprimer">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <!-- .htaccess (read-only) -->
            <tr class="row-muted">
                <td>
                    <code class="info-mono">.htaccess</code>
                    <div class="text-sm text-muted">Géré via la page Redirections</div>
                </td>
                <td><span class="badge badge-meta">Système</span></td>
                <td class="text-muted text-sm"><?= $htaccessExists ? 'Présent' : 'Absent' ?></td>
                <td><span class="text-muted text-sm">Non éditable ici</span></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ═══ Création d'un nouveau fichier ═══ -->
<section class="admin-card">
    <h2>Ajouter un fichier SEO</h2>
    <p class="form-hint mb-2">Choisis un modèle pour pré-remplir nom + contenu, ou crée un fichier vierge.</p>

    <div class="seo-templates" id="seo-templates">
        <button type="button" class="seo-template-chip" data-template="robots.txt">
            <code>robots.txt</code>
            <span>Règles pour les crawlers</span>
        </button>
        <button type="button" class="seo-template-chip" data-template="llms.txt">
            <code>llms.txt</code>
            <span>Carte du site pour les LLMs</span>
        </button>
        <button type="button" class="seo-template-chip" data-template="humans.txt">
            <code>humans.txt</code>
            <span>Crédits humains du site</span>
        </button>
        <button type="button" class="seo-template-chip" data-template="ads.txt">
            <code>ads.txt</code>
            <span>Régies publicitaires autorisées</span>
        </button>
        <button type="button" class="seo-template-chip" data-template="security.txt">
            <code>security.txt</code>
            <span>Contact sécurité (RFC 9116)</span>
        </button>
        <button type="button" class="seo-template-chip seo-template-chip--blank" data-template="blank">
            <code>vide</code>
            <span>Partir d'une page blanche</span>
        </button>
    </div>

    <form method="POST" action="/admin/seo-files/create" class="admin-form mt-2">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-group">
            <label for="seo-filename">Nom du fichier</label>
            <input type="text" id="seo-filename" name="filename" placeholder="humans.txt, ads.txt, security.txt…" required>
            <p class="form-hint">Sera servi à la racine, par ex. <code class="info-mono">villaplaisance.fr/humans.txt</code></p>
        </div>

        <div class="form-group">
            <label for="seo-content">Contenu</label>
            <textarea id="seo-content" name="content" rows="10" class="mono-input" placeholder="Contenu brut du fichier…"></textarea>
        </div>

        <div class="form-card-actions">
            <span class="text-muted text-sm">Le fichier sera créé immédiatement et accessible à <code class="info-mono">villaplaisance.fr/&lt;nom&gt;</code>.</span>
            <button type="submit" class="btn btn-primary">Créer le fichier</button>
        </div>
    </form>
</section>

<script>
(function () {
    const TEMPLATES = {
        'robots.txt': {
            filename: 'robots.txt',
            content: `User-agent: *
Allow: /
Disallow: /admin/

Sitemap: https://villaplaisance.fr/sitemap.xml
`,
        },
        'llms.txt': {
            filename: 'llms.txt',
            content: `# Villa Plaisance

> Chambres d'hôtes et villa de charme à Bédarrides, au cœur du Triangle d'Or provençal (Avignon, Châteauneuf-du-Pape, Orange).

## Pages principales
- [Accueil](https://villaplaisance.fr), Présentation de la maison
- [Chambres d'hôtes](https://villaplaisance.fr/chambres-d-hotes), Réservation B&B
- [Location villa entière](https://villaplaisance.fr/location-villa-provence), Villa pour groupes
- [Avis](https://villaplaisance.fr/avis), Témoignages clients
- [Contact](https://villaplaisance.fr/contact), Coordonnées et réservation

## À propos
Villa Plaisance est une maison provençale du XIXe siècle entièrement rénovée, située à Bédarrides (Vaucluse, 84370). Elle propose 4 chambres d'hôtes en formule B&B ou la location de la villa entière (jusqu'à 10 personnes) avec piscine privée.
`,
        },
        'humans.txt': {
            filename: 'humans.txt',
            content: `/* TEAM */
Owner: Villa Plaisance
Site: https://villaplaisance.fr
Contact: contact@villaplaisance.fr

/* SITE */
Last update: ${new Date().toISOString().slice(0, 10)}
Standards: HTML5, CSS3
Components: PHP custom CMS
Language: Français, English, Español
`,
        },
        'ads.txt': {
            filename: 'ads.txt',
            content: `# Villa Plaisance n'utilise pas de régies publicitaires tierces.
# Ce fichier est volontairement vide.
`,
        },
        'security.txt': {
            filename: '.well-known/security.txt',
            content: `Contact: mailto:contact@villaplaisance.fr
Expires: ${new Date(Date.now() + 365 * 86400 * 1000).toISOString()}
Preferred-Languages: fr, en
Canonical: https://villaplaisance.fr/.well-known/security.txt
`,
        },
        'blank': {
            filename: '',
            content: '',
        },
    };

    const filenameInput = document.getElementById('seo-filename');
    const contentInput  = document.getElementById('seo-content');
    document.querySelectorAll('.seo-template-chip').forEach(btn => {
        btn.addEventListener('click', () => {
            const t = TEMPLATES[btn.dataset.template];
            if (!t) return;
            filenameInput.value = t.filename;
            contentInput.value = t.content;
            document.querySelectorAll('.seo-template-chip').forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            contentInput.focus();
        });
    });
})();
</script>
