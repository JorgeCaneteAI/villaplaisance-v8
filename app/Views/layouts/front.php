<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php $ga4Id = \AnalyticsService::getGA4Id(); ?>
    <?php if ($ga4Id): ?>
    <script>
    window.vpGA4Id='<?= htmlspecialchars($ga4Id) ?>';
    function vpLoadGA(){
        if(document.cookie.indexOf('vp_consent=accept')!==-1&&window.vpGA4Id&&!window.vpGALoaded){
            var s=document.createElement('script');s.async=true;
            s.src='https://www.googletagmanager.com/gtag/js?id='+window.vpGA4Id;
            document.head.appendChild(s);
            window.dataLayer=window.dataLayer||[];
            function gtag(){dataLayer.push(arguments);}
            gtag('js',new Date());gtag('config',window.vpGA4Id);
            window.vpGALoaded=true;
        }
    }
    vpLoadGA();
    </script>
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <title><?= htmlspecialchars($seo['title'] ?? 'Villa Plaisance') ?></title>
    <meta name="description" content="<?= htmlspecialchars($seo['description'] ?? '') ?>">

    <!-- Canonical -->
    <link rel="canonical" href="<?= htmlspecialchars($seo['canonical'] ?? '') ?>">


    <!-- Open Graph -->
    <?php if (!empty($seo['og'])): ?>
    <meta property="og:title" content="<?= htmlspecialchars($seo['og']['title'] ?? '') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seo['og']['description'] ?? '') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($seo['og']['image'] ?? '') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($seo['og']['url'] ?? '') ?>">
    <meta property="og:type" content="<?= htmlspecialchars($seo['og']['type'] ?? 'website') ?>">
    <meta property="og:locale" content="<?= htmlspecialchars($seo['og']['locale'] ?? 'fr_FR') ?>">
    <meta property="og:site_name" content="Villa Plaisance">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seo['og']['title'] ?? $seo['title'] ?? '') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seo['og']['description'] ?? $seo['description'] ?? '') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($seo['og']['image'] ?? '') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wdth,wght@12..96,75..100,300..800&family=EB+Garamond:ital,wght@0,400..600;1,400..500&display=swap" rel="stylesheet">

    <!-- CSS V7 (legacy, à supprimer une fois toutes les pages portées) -->
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(ROOT . '/public/assets/css/style.css') ?>">
    <!-- CSS V8 (design impeccable) -->
    <link rel="stylesheet" href="/assets/css/style-v8.css?v=<?= filemtime(ROOT . '/public/assets/css/style-v8.css') ?>">

    <!-- JSON-LD -->
    <?php foreach (($jsonLd ?? []) as $ld): ?>
    <script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?></script>
    <?php endforeach; ?>
</head>
<body>
    <!-- SVG Sprite (hidden) -->
    <?php
    $spriteFile = ROOT . '/public/assets/img/icons.svg';
    if (file_exists($spriteFile)) {
        echo '<div style="display:none" aria-hidden="true">';
        readfile($spriteFile);
        echo '</div>';
    }
    ?>

    <!-- Skip to content -->
    <a href="#main-content" class="skip-link">Aller au contenu</a>

    <!-- Header V8 (frame : wordmark + nav 7 liens + langues + burger) -->
    <header class="frame" role="banner">
        <a class="wordmark" href="<?= LangService::url('/') ?>" aria-label="Villa Plaisance, accueil">
            <span>Villa</span><span>Plaisance</span>
        </a>
        <nav class="frame__nav" aria-label="Navigation principale" data-nav>
            <a href="<?= LangService::url('/') ?>" data-route="/">Accueil</a>
            <a href="<?= LangService::url('chambres-d-hotes') ?>" data-route="/chambres-d-hotes/">Chambres</a>
            <a href="<?= LangService::url('location-villa-provence') ?>" data-route="/location-villa-provence/">Villa</a>
            <a href="<?= LangService::url('sur-place') ?>" data-route="/sur-place/">Sur place</a>
            <a href="<?= LangService::url('journal') ?>" data-route="/journal/">Journal</a>
            <a href="<?= LangService::url('votre-hote') ?>" data-route="/votre-hote/">L'hôte</a>
            <a href="<?= LangService::url('contact') ?>" data-route="/contact/">Contact</a>
        </nav>
        <div class="frame__right">
            <nav class="frame__lang" aria-label="Langues">
                <a href="#" aria-current="page">FR</a>
                <a href="#" aria-disabled="true" tabindex="-1">EN</a>
                <a href="#" aria-disabled="true" tabindex="-1">ES</a>
                <a href="#" aria-disabled="true" tabindex="-1">DE</a>
            </nav>
            <button class="frame__menu-btn" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="menu-overlay" data-menu-open>
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <!-- Main content -->
    <main id="main-content">
        <?= $content ?>
    </main>

    <!-- Footer V8 (pied : 3 lignes sobres) -->
    <footer class="pied" role="contentinfo">
        <p class="pied__nom">
            Villa Plaisance &middot; Bédarrides 84370 Vaucluse &middot;
            <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
        </p>
        <nav class="pied__nav" aria-label="Mentions">
            <a href="<?= LangService::url('mentions-legales') ?>">Mentions légales</a>
            <a href="<?= LangService::url('politique-confidentialite') ?>">Confidentialité</a>
            <a href="<?= LangService::url('plan-du-site') ?>">Plan du site</a>
        </nav>
        <p class="pied__copy">&copy; <?= date('Y') ?> Villa Plaisance</p>
    </footer>

    <!-- Overlay menu V8 (mobile + accessible) -->
    <div class="menu-overlay" id="menu-overlay" hidden data-menu-overlay>
        <button class="menu-overlay__close" type="button" aria-label="Fermer le menu" data-menu-close>
            <span></span><span></span>
        </button>
        <nav class="menu-overlay__nav" aria-label="Menu principal">
            <a href="<?= LangService::url('/') ?>" data-route="/">Accueil</a>
            <a href="<?= LangService::url('chambres-d-hotes') ?>" data-route="/chambres-d-hotes/">Chambres d'hôtes</a>
            <a href="<?= LangService::url('location-villa-provence') ?>" data-route="/location-villa-provence/">La villa entière</a>
            <a href="<?= LangService::url('espaces-exterieurs') ?>" data-route="/espaces-exterieurs/">Espaces extérieurs</a>
            <a href="<?= LangService::url('sur-place') ?>" data-route="/sur-place/">Sur place</a>
            <a href="<?= LangService::url('itineraire') ?>" data-route="/itineraire/">Itinéraires</a>
            <a href="<?= LangService::url('journal') ?>" data-route="/journal/">Le Journal</a>
            <a href="<?= LangService::url('votre-hote') ?>" data-route="/votre-hote/">Votre hôte</a>
            <a href="<?= LangService::url('contact') ?>" data-route="/contact/">Écrire</a>
        </nav>
        <p class="menu-overlay__contact">
            <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
        </p>
    </div>

    <!-- Cookie consent RGPD -->
    <?php if (!isset($_COOKIE['vp_consent'])): ?>
    <div id="cookie-banner" class="cookie-banner" role="dialog" aria-label="Gestion des cookies">
        <div class="cookie-inner">
            <p class="cookie-text">Ce site utilise des cookies pour mesurer l'audience et améliorer votre expérience. Vous pouvez accepter ou refuser leur utilisation.</p>
            <div class="cookie-actions">
                <button id="cookie-refuse" class="cookie-btn cookie-btn-refuse">Refuser</button>
                <button id="cookie-accept" class="cookie-btn cookie-btn-accept">Accepter</button>
            </div>
        </div>
    </div>
    <style>
    .cookie-banner{position:fixed;bottom:0;left:0;right:0;z-index:10000;background:var(--dark);color:#fff;padding:1rem var(--gutter);transform:translateY(0);transition:transform 0.4s ease}
    .cookie-banner.hidden{transform:translateY(100%);pointer-events:none}
    .cookie-inner{max-width:var(--container);margin:0 auto;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap}
    .cookie-text{flex:1;font-size:0.8rem;line-height:1.5;min-width:250px;color:rgba(255,255,255,0.85)}
    .cookie-actions{display:flex;gap:0.75rem;flex-shrink:0}
    .cookie-btn{padding:0.5rem 1.25rem;border-radius:4px;font-size:0.8rem;font-family:inherit;cursor:pointer;border:none;transition:background 0.2s}
    .cookie-btn-refuse{background:transparent;color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.3)}
    .cookie-btn-refuse:hover{background:rgba(255,255,255,0.1);color:#fff}
    .cookie-btn-accept{background:var(--accent);color:#fff}
    .cookie-btn-accept:hover{background:#6d8a7b}
    @media(max-width:600px){.cookie-inner{flex-direction:column;text-align:center}.cookie-actions{width:100%;justify-content:center}}
    </style>
    <script>
    (function(){
        var banner=document.getElementById('cookie-banner');
        if(!banner)return;
        function setCookie(v){
            var d=new Date();d.setTime(d.getTime()+180*24*60*60*1000);
            document.cookie='vp_consent='+v+';expires='+d.toUTCString()+';path=/;SameSite=Lax';
        }
        document.getElementById('cookie-accept').addEventListener('click',function(){
            setCookie('accept');banner.classList.add('hidden');
            if(typeof vpLoadGA==='function')vpLoadGA();
        });
        document.getElementById('cookie-refuse').addEventListener('click',function(){
            setCookie('refuse');banner.classList.add('hidden');
        });
    })();
    </script>
    <?php endif; ?>

    <script src="/assets/js/main.js?v=<?= filemtime(ROOT . '/public/assets/js/main.js') ?>" defer></script>
    <script src="/assets/js/main-v8.js?v=<?= filemtime(ROOT . '/public/assets/js/main-v8.js') ?>" defer></script>
</body>
</html>
