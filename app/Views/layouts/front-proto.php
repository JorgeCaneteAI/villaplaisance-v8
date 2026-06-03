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

    <!-- Robots : extrait illimité + grande miniature pour SERP et LLMs. -->
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    <link rel="canonical" href="<?= htmlspecialchars($seo['canonical'] ?? '') ?>">

    <!-- hreflang : variantes FR/EN/ES + x-default = FR. -->
    <?php foreach (($seo['hreflang'] ?? []) as $alt): ?>
    <link rel="alternate" hreflang="<?= htmlspecialchars($alt['lang']) ?>" href="<?= htmlspecialchars($alt['url']) ?>">
    <?php endforeach; ?>

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

    <!-- Fonts proto Claude design : Cormorant Garamond + Barlow Condensed + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Barlow+Condensed:wght@300;400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- CSS proto Claude design -->
    <link rel="stylesheet" href="/assets/css/style-proto.css?v=<?= filemtime(ROOT . '/public/assets/css/style-proto.css') ?>">

    <!-- CSS menu mobile (hamburger + overlay) — inline pour ne pas multiplier les requêtes. -->
    <style>
    .nav-menu-btn { display: none; width: 36px; height: 36px; background: transparent; border: 0; padding: 0; cursor: pointer; flex-direction: column; align-items: center; justify-content: center; gap: 5px; color: var(--ink-900); }
    .nav-menu-btn span { display: block; width: 22px; height: 1.5px; background: currentColor; transition: transform .4s ease, opacity .3s ease; }
    .nav-menu-btn[aria-expanded="true"] span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
    .nav-menu-btn[aria-expanded="true"] span:nth-child(2) { opacity: 0; }
    .nav-menu-btn[aria-expanded="true"] span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }
    @media (max-width: 960px) { .nav-menu-btn { display: inline-flex; } }

    .mobile-menu { position: fixed; inset: 0; z-index: 1500; background: var(--linen-50, #FBF7EE); color: var(--ink-900, #1F1C16); display: flex; flex-direction: column; justify-content: center; align-items: flex-start; padding: 6rem 32px 4rem; opacity: 0; visibility: hidden; transition: opacity .4s ease, visibility .4s ease; }
    .mobile-menu[hidden] { display: flex; }
    .mobile-menu.is-open { opacity: 1; visibility: visible; }
    html.is-menu-open, html.is-menu-open body { overflow: hidden; }

    .mobile-menu__close { position: absolute; top: 1.4rem; right: 32px; width: 36px; height: 36px; background: transparent; border: 0; cursor: pointer; color: var(--ink-900, #1F1C16); }
    .mobile-menu__close::before, .mobile-menu__close::after { content: ''; position: absolute; top: 50%; left: 50%; width: 22px; height: 1.5px; background: currentColor; }
    .mobile-menu__close::before { transform: translate(-50%, -50%) rotate(45deg); }
    .mobile-menu__close::after { transform: translate(-50%, -50%) rotate(-45deg); }

    .mobile-menu__nav { display: flex; flex-direction: column; gap: 1rem; max-width: 100%; }
    .mobile-menu__nav a { font-family: var(--font-display, Georgia, serif); font-size: clamp(1.7rem, 7vw, 2.6rem); color: var(--ink-900, #1F1C16); text-decoration: none; line-height: 1.1; opacity: 0.92; transition: color .2s ease, opacity .2s ease; }
    .mobile-menu__nav a:hover { color: var(--sage-700, #5F7765); opacity: 1; }
    .mobile-menu__nav a.active { color: var(--terra-500, #A55C33); opacity: 1; }

    .mobile-menu__lang { margin-top: 2.5rem; display: flex; gap: 8px; align-items: center; font-family: var(--font-mono, monospace); font-size: 0.78rem; letter-spacing: 0.18em; text-transform: uppercase; color: var(--ink-700, #5C5247); }
    .mobile-menu__lang a { color: inherit; text-decoration: none; padding: 4px 6px; }
    .mobile-menu__lang a.active { color: var(--terra-500, #A55C33); font-weight: 500; }
    .mobile-menu__lang span.sep { opacity: 0.4; }

    .mobile-menu__contact { margin-top: 1.5rem; display: inline-block; padding: 12px 24px; background: var(--ink-900, #1F1C16); color: var(--linen-50, #FBF7EE); text-decoration: none; font-family: var(--font-sans, sans-serif); font-size: 0.85rem; letter-spacing: 0.05em; }
    .mobile-menu__contact:hover { background: var(--terra-500, #A55C33); }
    </style>

    <?php foreach (($jsonLd ?? []) as $ld): ?>
    <script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?></script>
    <?php endforeach; ?>
</head>
<body>
    <!-- Nav du proto Claude design -->
    <header class="nav">
        <div class="nav-inner">
            <a href="<?= LangService::url('/') ?>" class="brand" aria-label="Villa Plaisance, accueil">
                <img src="/assets/img/logo-proto.png" alt="Villa Plaisance" />
            </a>
            <nav class="nav-links" aria-label="<?= t('proto.nav.menu_principal') ?>">
                <a href="<?= LangService::url('chambres-d-hotes') ?>" data-page="chambres-hotes"><?= t('proto.nav.chambres') ?></a>
                <a href="<?= LangService::url('location-villa-provence') ?>" data-page="maison-hotes"><?= t('proto.nav.villa') ?></a>
                <a href="<?= LangService::url('disponibilites') ?>" data-page="disponibilites"><?= t('proto.nav.disponibilites') ?></a>
                <a href="<?= LangService::url('espaces-exterieurs') ?>" data-page="exterieurs"><?= t('proto.nav.exterieurs') ?></a>
                <a href="<?= LangService::url('journal') ?>" data-page="journal-tourisme"><?= t('proto.nav.journal') ?></a>
                <a href="<?= LangService::url('itineraire') ?>" data-page="journal-que-faire"><?= t('proto.nav.itineraire') ?></a>
            </nav>
            <div class="nav-right">
                <?php $currentLang = LangService::current(); ?>
                <div class="lang-toggle" role="group" aria-label="<?= t('proto.nav.lang_label') ?>">
                    <a href="<?= LangService::switchLangUrl('fr') ?>" hreflang="fr" class="<?= $currentLang === 'fr' ? 'active' : '' ?>"<?= $currentLang === 'fr' ? ' aria-current="true"' : '' ?>>FR</a>
                    <span class="sep">/</span>
                    <a href="<?= LangService::switchLangUrl('en') ?>" hreflang="en" class="<?= $currentLang === 'en' ? 'active' : '' ?>"<?= $currentLang === 'en' ? ' aria-current="true"' : '' ?>>EN</a>
                    <span class="sep">/</span>
                    <a href="<?= LangService::switchLangUrl('es') ?>" hreflang="es" class="<?= $currentLang === 'es' ? 'active' : '' ?>"<?= $currentLang === 'es' ? ' aria-current="true"' : '' ?>>ES</a>
                </div>
                <a href="<?= LangService::url('contact') ?>" class="btn"><?= t('proto.nav.contact') ?></a>
                <button class="nav-menu-btn" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mobile-menu" data-menu-open>
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Menu overlay mobile : tous les liens de la nav + langues + CTA contact. -->
    <div class="mobile-menu" id="mobile-menu" hidden data-menu-overlay>
        <button class="mobile-menu__close" type="button" aria-label="Fermer le menu" data-menu-close></button>
        <nav class="mobile-menu__nav" aria-label="<?= t('proto.nav.menu_principal') ?>">
            <a href="<?= LangService::url('/') ?>">Accueil</a>
            <a href="<?= LangService::url('chambres-d-hotes') ?>"><?= t('proto.nav.chambres') ?></a>
            <a href="<?= LangService::url('location-villa-provence') ?>"><?= t('proto.nav.villa') ?></a>
            <a href="<?= LangService::url('disponibilites') ?>"><?= t('proto.nav.disponibilites') ?></a>
            <a href="<?= LangService::url('espaces-exterieurs') ?>"><?= t('proto.nav.exterieurs') ?></a>
            <a href="<?= LangService::url('journal') ?>"><?= t('proto.nav.journal') ?></a>
            <a href="<?= LangService::url('itineraire') ?>"><?= t('proto.nav.itineraire') ?></a>
        </nav>
    </div>

    <!-- Script toggle menu mobile (inline, vanilla). -->
    <script>
    (function() {
        var btn = document.querySelector('[data-menu-open]');
        var btnClose = document.querySelector('[data-menu-close]');
        var overlay = document.querySelector('[data-menu-overlay]');
        if (!btn || !overlay) return;

        function open() {
            overlay.hidden = false;
            void overlay.offsetWidth; // force reflow
            overlay.classList.add('is-open');
            document.documentElement.classList.add('is-menu-open');
            btn.setAttribute('aria-expanded', 'true');
        }
        function close() {
            overlay.classList.remove('is-open');
            document.documentElement.classList.remove('is-menu-open');
            btn.setAttribute('aria-expanded', 'false');
            setTimeout(function() {
                if (!overlay.classList.contains('is-open')) overlay.hidden = true;
            }, 500);
        }

        btn.addEventListener('click', open);
        if (btnClose) btnClose.addEventListener('click', close);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('is-open')) close();
        });
        // Cliquer un lien ferme le menu
        overlay.querySelectorAll('a[href]').forEach(function(a) {
            a.addEventListener('click', close);
        });
    })();
    </script>

    <main id="main-content">
        <?= $content ?>
    </main>

    <!-- Footer du proto Claude design (avec CTA) -->
    <footer class="footer" style="margin-top: 0;">
        <div class="container-wide">
            <h2><?= t('proto.footer.cta_title') ?></h2>
            <div style="display:flex; gap: 16px; flex-wrap: wrap;">
                <a class="btn" style="background: var(--linen-50); color: var(--olive-900); border-color: var(--linen-50);" href="<?= LangService::url('contact') ?>"><span><?= t('proto.footer.cta_send') ?></span> &rarr;</a>
                <a class="btn btn-ghost" style="color: var(--linen-50); border-color: rgba(251,247,238,0.4);" href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
            </div>

            <div class="footer-grid">
                <div>
                    <div style="font-family: var(--font-display); font-size: 28px; color: var(--linen-50); letter-spacing: -0.01em; margin-bottom: 12px;">Villa Plaisance</div>
                    <p style="margin: 0; max-width: 36ch; color: rgba(251,247,238,0.65); font-size: 14px; line-height: 1.6;"><?= t('proto.footer.tagline') ?></p>
                </div>
                <div>
                    <h4><?= t('proto.footer.col_house') ?></h4>
                    <ul>
                        <li><a href="<?= LangService::url('chambres-d-hotes') ?>"><?= t('proto.footer.chambres') ?></a></li>
                        <li><a href="<?= LangService::url('location-villa-provence') ?>"><?= t('proto.footer.villa') ?></a></li>
                        <li><a href="<?= LangService::url('disponibilites') ?>"><?= t('proto.footer.disponibilites') ?></a></li>
                        <li><a href="<?= LangService::url('espaces-exterieurs') ?>"><?= t('proto.footer.exterieurs') ?></a></li>
                    </ul>
                </div>
                <div>
                    <h4><?= t('proto.footer.col_journal') ?></h4>
                    <ul>
                        <li><a href="<?= LangService::url('journal') ?>"><?= t('proto.footer.journal') ?></a></li>
                        <li><a href="<?= LangService::url('itineraire') ?>"><?= t('proto.footer.itineraire') ?></a></li>
                    </ul>
                </div>
                <div>
                    <h4><?= t('proto.footer.col_stay_close') ?></h4>
                    <ul>
                        <li><a href="<?= LangService::url('contact') ?>"><?= t('proto.footer.contact') ?></a></li>
                        <li><a href="<?= LangService::url('avis') ?>"><?= t('proto.footer.avis') ?></a></li>
                        <li><a href="<?= LangService::url('votre-hote') ?>"><?= t('proto.footer.votre_hote') ?></a></li>
                        <li><a href="tel:+33490330049">04 90 33 00 49</a></li>
                        <li><a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a></li>
                        <li><a href="#"><?= t('proto.footer.instagram') ?></a></li>
                        <li style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(251,247,238,0.12);"><a href="<?= LangService::url('livret') ?>"><?= t('proto.footer.livret') ?></a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; <?= date('Y') ?> Villa Plaisance</span>
                <span><?= t('proto.footer.region') ?></span>
            </div>
        </div>
    </footer>

    <!-- Cookie consent RGPD (hérité v8) -->
    <?php if (!isset($_COOKIE['vp_consent'])): ?>
    <div id="cookie-banner" class="cookie-banner" role="dialog" aria-label="<?= t('proto.cookie.dialog') ?>">
        <div class="cookie-inner">
            <p class="cookie-text"><?= t('proto.cookie.text') ?></p>
            <div class="cookie-actions">
                <button id="cookie-refuse" class="cookie-btn cookie-btn-refuse"><?= t('proto.cookie.refuse') ?></button>
                <button id="cookie-accept" class="cookie-btn cookie-btn-accept"><?= t('proto.cookie.accept') ?></button>
            </div>
        </div>
    </div>
    <style>
    .cookie-banner{position:fixed;bottom:0;left:0;right:0;z-index:10000;background:var(--ink-900);color:#fff;padding:1rem var(--gutter);transform:translateY(0);transition:transform 0.4s ease}
    .cookie-banner.hidden{transform:translateY(100%);pointer-events:none}
    .cookie-inner{max-width:var(--container-wide);margin:0 auto;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap}
    .cookie-text{flex:1;font-size:0.85rem;line-height:1.5;min-width:250px;color:rgba(255,255,255,0.85)}
    .cookie-actions{display:flex;gap:0.75rem;flex-shrink:0}
    .cookie-btn{padding:0.5rem 1.25rem;border-radius:2px;font-size:0.85rem;font-family:inherit;cursor:pointer;border:none;transition:background 0.2s}
    .cookie-btn-refuse{background:transparent;color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.3)}
    .cookie-btn-refuse:hover{background:rgba(255,255,255,0.1);color:#fff}
    .cookie-btn-accept{background:var(--sage-500);color:#fff}
    .cookie-btn-accept:hover{background:var(--sage-700)}
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

</body>
</html>
