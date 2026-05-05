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

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <title><?= htmlspecialchars($seo['title'] ?? 'Villa Plaisance') ?></title>
    <meta name="description" content="<?= htmlspecialchars($seo['description'] ?? '') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($seo['canonical'] ?? '') ?>">

    <?php if (!empty($seo['og'])): ?>
    <meta property="og:title" content="<?= htmlspecialchars($seo['og']['title'] ?? '') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seo['og']['description'] ?? '') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($seo['og']['image'] ?? '') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($seo['og']['url'] ?? '') ?>">
    <meta property="og:type" content="<?= htmlspecialchars($seo['og']['type'] ?? 'website') ?>">
    <meta property="og:locale" content="<?= htmlspecialchars($seo['og']['locale'] ?? 'fr_FR') ?>">
    <meta property="og:site_name" content="Villa Plaisance">
    <?php endif; ?>

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seo['og']['title'] ?? $seo['title'] ?? '') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seo['og']['description'] ?? $seo['description'] ?? '') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($seo['og']['image'] ?? '') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,400;1,9..144,500;1,9..144,600;1,9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/css/style-v9.css?v=<?= filemtime(ROOT . '/public/assets/css/style-v9.css') ?>">

    <?php foreach (($jsonLd ?? []) as $ld): ?>
    <script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?></script>
    <?php endforeach; ?>
</head>
<body>

    <a href="#main-content" class="skip-link">Aller au contenu</a>

    <header class="topbar" role="banner">
        <div class="topbar__inner">
            <a href="<?= LangService::url('/') ?>" class="brand" aria-label="Villa Plaisance, accueil">
                <span class="brand__name">Villa Plaisance</span>
                <span class="brand__loc">Bédarrides &middot; Vaucluse</span>
            </a>
            <nav class="nav" aria-label="Navigation principale" data-nav>
                <a href="<?= LangService::url('chambres-d-hotes') ?>" data-route="/chambres-d-hotes/">L'offre sept&ndash;juin</a>
                <a href="<?= LangService::url('location-villa-provence') ?>" data-route="/location-villa-provence/">L'offre juillet&ndash;août</a>
                <a href="<?= LangService::url('votre-hote') ?>" data-route="/votre-hote/">L'hôte</a>
                <a href="<?= LangService::url('sur-place') ?>" data-route="/sur-place/">Sur place</a>
                <a href="<?= LangService::url('journal') ?>" data-route="/journal/">Journal</a>
                <a href="<?= LangService::url('contact') ?>" data-route="/contact/">Contact</a>
            </nav>
            <div class="topbar__right">
                <nav class="langs" aria-label="Langues">
                    <a href="#" aria-current="page">FR</a>
                    <a href="#" aria-disabled="true" tabindex="-1">EN</a>
                    <a href="#" aria-disabled="true" tabindex="-1">ES</a>
                    <a href="#" aria-disabled="true" tabindex="-1">DE</a>
                </nav>
                <a href="<?= LangService::url('contact') ?>" class="pill pill--solid">Écrire à Jorge</a>
                <button class="menu-btn" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="menu-overlay" data-menu-open>
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <main id="main-content">
        <?= $content ?>
    </main>

    <footer class="footer" role="contentinfo">
        <div class="footer__inner">
            <div>
                <div class="brand">
                    <span class="brand__name">Villa Plaisance</span>
                    <span class="brand__loc">Bédarrides &middot; Vaucluse</span>
                </div>
                <p class="tag">Une maison provençale, deux façons d'y séjourner. Une seule réservation à la fois.</p>
            </div>
            <div>
                <h4>Visiter</h4>
                <ul>
                    <li><a href="<?= LangService::url('chambres-d-hotes') ?>">L'offre sept&ndash;juin</a></li>
                    <li><a href="<?= LangService::url('location-villa-provence') ?>">L'offre juillet&ndash;août</a></li>
                    <li><a href="<?= LangService::url('votre-hote') ?>">L'hôte</a></li>
                    <li><a href="<?= LangService::url('sur-place') ?>">Sur place</a></li>
                    <li><a href="<?= LangService::url('journal') ?>">Journal</a></li>
                    <li><a href="<?= LangService::url('contact') ?>">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Pratique</h4>
                <ul>
                    <li><a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a></li>
                    <li>Bédarrides 84370</li>
                    <li><a href="<?= LangService::url('mentions-legales') ?>">Mentions légales</a></li>
                    <li><a href="<?= LangService::url('politique-confidentialite') ?>">Politique de confidentialité</a></li>
                    <li><a href="<?= LangService::url('plan-du-site') ?>">Plan du site</a></li>
                </ul>
            </div>
        </div>
        <div class="footer__bot">
            <span>&copy; <?= date('Y') ?> Villa Plaisance &middot; Jorge Cañete</span>
            <span>FR &middot; EN &middot; ES &middot; DE</span>
        </div>
    </footer>

    <div class="menu-overlay" id="menu-overlay" hidden data-menu-overlay>
        <button class="menu-overlay__close" type="button" aria-label="Fermer le menu" data-menu-close>×</button>
        <nav class="menu-overlay__nav" aria-label="Menu principal">
            <a href="<?= LangService::url('chambres-d-hotes') ?>" data-route="/chambres-d-hotes/">L'offre sept&ndash;juin</a>
            <a href="<?= LangService::url('location-villa-provence') ?>" data-route="/location-villa-provence/">L'offre juillet&ndash;août</a>
            <a href="<?= LangService::url('votre-hote') ?>" data-route="/votre-hote/">L'hôte</a>
            <a href="<?= LangService::url('sur-place') ?>" data-route="/sur-place/">Sur place</a>
            <a href="<?= LangService::url('journal') ?>" data-route="/journal/">Journal</a>
            <a href="<?= LangService::url('contact') ?>" data-route="/contact/">Contact</a>
        </nav>
        <p class="menu-overlay__contact">
            <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
        </p>
    </div>

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

    <script>
    (function(){
        var btn = document.querySelector('[data-menu-open]');
        var overlay = document.querySelector('[data-menu-overlay]');
        var closeBtn = document.querySelector('[data-menu-close]');
        if(!btn || !overlay) return;
        function open(){ overlay.hidden = false; requestAnimationFrame(function(){ overlay.classList.add('is-open'); }); btn.setAttribute('aria-expanded','true'); document.body.style.overflow='hidden'; }
        function close(){ overlay.classList.remove('is-open'); btn.setAttribute('aria-expanded','false'); document.body.style.overflow=''; setTimeout(function(){ overlay.hidden = true; }, 250); }
        btn.addEventListener('click', open);
        if(closeBtn) closeBtn.addEventListener('click', close);
        overlay.addEventListener('click', function(e){ if(e.target===overlay) close(); });
        document.addEventListener('keydown', function(e){ if(e.key==='Escape' && !overlay.hidden) close(); });
    })();
    (function(){
        var path = window.location.pathname.replace(/\/$/, '/') || '/';
        document.querySelectorAll('[data-route]').forEach(function(a){
            if(a.getAttribute('data-route') === path) a.classList.add('is-current');
        });
    })();
    </script>
</body>
</html>
