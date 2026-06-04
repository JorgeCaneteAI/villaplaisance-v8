<?php declare(strict_types=1);

/* ──────────────────────────────────────────────────────────────────────────
   Layout admin V2 (2026-06-03), sidebar + header, refonte radicale.
   ────────────────────────────────────────────────────────────────────────── */

$uri = $_SERVER['REQUEST_URI'] ?? '/admin';

// Helper "active" pour les liens sidebar
$isActive = function (string $prefix) use ($uri): bool {
    if ($prefix === '/admin' || $prefix === '/admin/dashboard') {
        return $uri === '/admin' || $uri === '/admin/dashboard' || $uri === '/admin/';
    }
    return str_starts_with($uri, $prefix);
};

// Breadcrumb : titre de la page courante depuis l'URL
$breadcrumb = (function () use ($uri): string {
    $map = [
        '/admin/dashboard'  => 'Tableau de bord',
        '/admin/pages'      => 'Pages',
        '/admin/sections'   => 'Recherche de blocs',
        '/admin/pieces'     => 'Chambres &amp; espaces',
        '/admin/articles'   => 'Articles',
        '/admin/livret'     => 'Livret d’accueil',
        '/admin/itineraires'=> 'Itinéraires',
        '/admin/messages'   => 'Messages',
        '/admin/calendrier' => 'Calendrier',
        '/admin/avis'       => 'Avis',
        '/admin/media'      => 'Médias',
        '/admin/faq'        => 'FAQ',
        '/admin/seo-files'  => 'Fichiers SEO',
        '/admin/redirects'  => 'Redirections',
        '/admin/icons-lab'  => 'Icônes',
        '/admin/reglages'   => 'Réglages',
        '/admin/securite'   => 'Sécurité',
        '/admin/analytics'  => 'Statistiques',
    ];
    foreach ($map as $prefix => $label) {
        if (str_starts_with($uri, $prefix)) return $label;
    }
    return 'Administration';
})();

// Compteur de messages non lus pour le badge sidebar
$unreadMessages = 0;
try {
    $unreadMessages = (int)(\Database::fetchOne("SELECT COUNT(*) AS n FROM vp_messages WHERE read_at IS NULL")['n'] ?? 0);
} catch (\Throwable) {}

// DB badge
$dbLabel = preg_replace('/^efkz3012_/', '', DB_NAME);
$dbClass = stripos(DB_NAME, 'prod') !== false ? 'is-prod'
         : (stripos(DB_NAME, 'dev') !== false ? 'is-dev' : 'is-other');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin, Villa Plaisance</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Inter + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/css/admin.css?v=<?= filemtime(ROOT . '/public/assets/css/admin.css') ?>">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#18181B">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Theme : appliqué AVANT le rendu pour éviter le flash -->
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('vp-admin-theme');
                if (saved === 'light' || saved === 'dark') {
                    document.documentElement.setAttribute('data-theme', saved);
                }
            } catch (_) {}
        })();
    </script>
</head>
<body class="admin-body <?= htmlspecialchars($body_class ?? '') ?>">

<div class="admin-shell">

    <!-- ═════════ SIDEBAR ═════════ -->
    <aside class="admin-sidebar" aria-label="Navigation principale">
        <a href="/admin" class="sidebar-brand">
            <span class="sidebar-brand-mark">VP</span>
            <span>Villa Plaisance</span>
        </a>

        <nav class="sidebar-section">
            <a href="/admin/dashboard" class="sidebar-link <?= $isActive('/admin/dashboard') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M3 12 12 3l9 9"/><path d="M5 10v10h14V10"/></svg>
                <span>Tableau de bord</span>
            </a>
            <a href="/admin/analytics" class="sidebar-link <?= $isActive('/admin/analytics') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="m7 14 4-4 4 4 5-5"/></svg>
                <span>Statistiques</span>
            </a>
            <a href="/admin/messages" class="sidebar-link <?= $isActive('/admin/messages') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                <span>Messages</span>
                <?php if ($unreadMessages > 0): ?>
                    <span class="sidebar-badge"><?= $unreadMessages ?></span>
                <?php endif; ?>
            </a>
            <a href="/admin/calendrier" class="sidebar-link <?= $isActive('/admin/calendrier') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <span>Calendrier</span>
            </a>
        </nav>

        <div class="sidebar-label">Contenu</div>
        <nav class="sidebar-section">
            <div class="sidebar-group <?= ($isActive('/admin/pages') || $isActive('/admin/sections')) ? 'is-open' : '' ?>">
                <button class="sidebar-group-trigger" type="button">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    <span>Pages</span>
                    <svg class="sidebar-group-chevron" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
                <div class="sidebar-group-items">
                    <a href="/admin/pages" class="sidebar-link <?= $uri === '/admin/pages' || str_starts_with($uri, '/admin/pages/') ? 'is-active' : '' ?>">Toutes les pages</a>
                    <a href="/admin/sections" class="sidebar-link <?= $isActive('/admin/sections') ? 'is-active' : '' ?>">Recherche de blocs</a>
                </div>
            </div>
            <a href="/admin/pieces" class="sidebar-link <?= $isActive('/admin/pieces') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M3 9 12 2l9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
                <span>Chambres &amp; espaces</span>
            </a>
            <a href="/admin/articles" class="sidebar-link <?= $isActive('/admin/articles') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                <span>Articles</span>
            </a>
            <a href="/admin/livret" class="sidebar-link <?= $isActive('/admin/livret') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                <span>Livret d’accueil</span>
            </a>
            <a href="/admin/itineraires" class="sidebar-link <?= $isActive('/admin/itineraires') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5 8 12 8 12s8-7 8-12a8 8 0 0 0-8-8z"/></svg>
                <span>Itinéraires</span>
            </a>
            <a href="/admin/avis" class="sidebar-link <?= $isActive('/admin/avis') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Avis</span>
            </a>
            <a href="/admin/media" class="sidebar-link <?= $isActive('/admin/media') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                <span>Médias</span>
            </a>
            <a href="/admin/faq" class="sidebar-link <?= $isActive('/admin/faq') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                <span>FAQ</span>
            </a>
        </nav>

        <div class="sidebar-label">Configuration</div>
        <nav class="sidebar-section">
            <div class="sidebar-group <?= ($isActive('/admin/seo-files') || $isActive('/admin/redirects') || $isActive('/admin/icons-lab')) ? 'is-open' : '' ?>">
                <button class="sidebar-group-trigger" type="button">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <span>SEO</span>
                    <svg class="sidebar-group-chevron" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
                <div class="sidebar-group-items">
                    <a href="/admin/seo-files" class="sidebar-link <?= $isActive('/admin/seo-files') ? 'is-active' : '' ?>">Fichiers SEO</a>
                    <a href="/admin/redirects" class="sidebar-link <?= $isActive('/admin/redirects') ? 'is-active' : '' ?>">Redirections</a>
                    <a href="/admin/icons-lab" class="sidebar-link <?= $isActive('/admin/icons-lab') ? 'is-active' : '' ?>">Icônes</a>
                </div>
            </div>
            <a href="/admin/reglages" class="sidebar-link <?= $isActive('/admin/reglages') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span>Réglages</span>
            </a>
            <a href="/admin/securite" class="sidebar-link <?= $isActive('/admin/securite') ? 'is-active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>Sécurité</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="/" target="_blank" rel="noopener" class="sidebar-link">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6M10 14 21 3"/></svg>
                <span>Voir le site</span>
            </a>
            <a href="/admin/logout" class="sidebar-link" style="color: var(--error);">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- ═════════ MAIN ═════════ -->
    <div class="admin-main-wrap">

        <header class="admin-header">
            <div class="admin-header-left">
                <button class="header-mobile-trigger" type="button" data-sidebar-toggle aria-label="Ouvrir le menu">
                    <svg class="header-action-svg" viewBox="0 0 24 24"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
                <div class="header-breadcrumb">
                    <span class="header-breadcrumb-current"><?= $breadcrumb ?></span>
                </div>
            </div>
            <div class="admin-header-right">
                <span class="db-badge <?= $dbClass ?>" title="Base de données active : <?= htmlspecialchars(DB_NAME) ?>"><?= htmlspecialchars($dbLabel) ?></span>
                <button class="theme-toggle" type="button" data-theme-toggle aria-label="Basculer le thème">
                    <svg class="theme-toggle-svg theme-icon-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    <svg class="theme-toggle-svg theme-icon-moon" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
            </div>
        </header>

        <main class="admin-main">
            <div class="admin-content">
                <?php if (!empty($flash['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash['success']) ?></div>
                <?php endif; ?>
                <?php if (!empty($flash['error'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($flash['error']) ?></div>
                <?php endif; ?>

                <?= $content ?>
            </div>
        </main>
    </div>
</div>

<script src="/assets/js/admin-theme.js?v=<?= filemtime(ROOT . '/public/assets/js/admin-theme.js') ?>"></script>

<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(e => console.warn('SW register failed', e));
    });
}
</script>
</body>
</html>
