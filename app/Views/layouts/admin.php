<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — Villa Plaisance</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?= filemtime(ROOT . '/public/assets/css/admin.css') ?>">
    <!-- PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#2C2C2A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Calendrier VP">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(e => console.warn('SW register failed', e));
        });
    }
    </script>
</head>
<body class="admin-body <?= htmlspecialchars($body_class ?? '') ?>">
    <header class="admin-topbar">
        <a href="/admin" class="topbar-logo">VP Admin</a>
        <nav class="topbar-nav">
            <!-- ═══ À GAUCHE DU BADGE V8 — sections PAS ENCORE refondues (look V7 hérité) ═══ -->
            <a href="/admin/dashboard" class="topbar-link is-stale" title="Pas encore refondu V8">Dashboard</a>
            <a href="/admin/analytics" class="topbar-link is-stale" title="Pas encore refondu V8">Statistiques</a>

            <div class="topbar-group is-stale" title="Outils techniques en V7 hérité">
                <button class="topbar-group-btn">SEO ▾</button>
                <div class="topbar-dropdown">
                    <a href="/admin/seo-files" class="topbar-dd-link">SEO</a>
                    <a href="/admin/redirects" class="topbar-dd-link">Redirections</a>
                </div>
            </div>

            <a href="/admin/reglages" class="topbar-link is-stale" title="V7 hérité (paramètres techniques)">Réglages</a>

            <!-- ═══ Le badge V8 — DEV sert de SÉPARATEUR ═══ -->
            <span class="env-separator" title="À gauche : sections en attente de refonte V8. À droite : sections refondues et validées.">V8 — DEV</span>

            <!-- ═══ À DROITE DU BADGE V8 — sections REFONDUES et validées ═══ -->
            <a href="/admin/messages" class="topbar-link is-fresh <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/messages') ? 'active' : '' ?>">Messages</a>
            <a href="/admin/calendrier" class="topbar-link is-fresh <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/calendrier') ? 'active' : '' ?>">Calendrier</a>
            <a href="/admin/pages" class="topbar-link is-fresh <?= (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/pages') || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/sections')) ? 'active' : '' ?>">Pages</a>
            <div class="topbar-group is-fresh">
                <button class="topbar-group-btn">Contenu ▾</button>
                <div class="topbar-dropdown">
                    <a href="/admin/pieces" class="topbar-dd-link">Chambres &amp; espaces</a>
                    <a href="/admin/articles" class="topbar-dd-link">Articles</a>
                    <a href="/admin/host" class="topbar-dd-link">Votre hôte</a>
                    <a href="/admin/livret" class="topbar-dd-link">Livret d'accueil</a>
                    <a href="/admin/itineraires" class="topbar-dd-link">Itinéraires</a>
                </div>
            </div>
            <a href="/admin/faq" class="topbar-link is-fresh <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/faq') ? 'active' : '' ?>">FAQ</a>
            <a href="/admin/avis" class="topbar-link is-fresh <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/avis') ? 'active' : '' ?>">Avis</a>
            <a href="/admin/media" class="topbar-link is-fresh <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/media') ? 'active' : '' ?>">Médias</a>
        </nav>
        <div class="topbar-right">
            <a href="/" class="topbar-link" target="_blank">Voir le site</a>
            <span class="topbar-user"><?= htmlspecialchars($_SESSION['admin_user_name'] ?? 'Admin') ?></span>
            <a href="/admin/securite" class="topbar-link" title="Appareils de confiance">Sécurité</a>
            <a href="/admin/logout" class="topbar-link topbar-link-danger">Déconnexion</a>
        </div>
    </header>

    <main class="admin-main">
        <?php if (!empty($flash['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash['success']) ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($flash['error']) ?></div>
        <?php endif; ?>

        <div class="admin-content">
            <?= $content ?>
        </div>
    </main>

</body>
</html>
