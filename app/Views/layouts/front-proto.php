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

    <!-- Fonts proto Claude design : Cormorant Garamond + Manrope + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Manrope:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- CSS proto Claude design -->
    <link rel="stylesheet" href="/assets/css/style-proto.css?v=<?= filemtime(ROOT . '/public/assets/css/style-proto.css') ?>">

    <?php foreach (($jsonLd ?? []) as $ld): ?>
    <script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?></script>
    <?php endforeach; ?>
</head>
<body>
    <a href="#main-content" class="skip-link">Aller au contenu</a>

    <!-- Nav du proto Claude design -->
    <header class="nav">
        <div class="nav-inner">
            <a href="<?= LangService::url('/') ?>" class="brand">
                <img src="/assets/img/logo-proto.png" alt="" />
                <span class="brand-wordmark">
                    <span>Villa Plaisance</span>
                    <span class="brand-mark">VP &middot; 84</span>
                </span>
            </a>
            <nav class="nav-links" aria-label="Principal">
                <a href="<?= LangService::url('chambres-d-hotes') ?>" data-en="B&amp;B rooms" data-page="chambres-hotes">Chambres d'hôtes</a>
                <a href="<?= LangService::url('location-villa-provence') ?>" data-en="Whole house" data-page="maison-hotes">Maison d'hôtes</a>
                <a href="<?= LangService::url('disponibilites') ?>" data-en="Availability" data-page="disponibilites">Disponibilités</a>
                <a href="<?= LangService::url('espaces-exterieurs') ?>" data-en="Outdoors" data-page="exterieurs">Extérieurs</a>
                <a href="<?= LangService::url('journal') ?>" data-en="Tourism" data-page="journal-tourisme">Tourisme</a>
                <a href="<?= LangService::url('itineraire') ?>" data-en="Things to do" data-page="journal-que-faire">Que faire</a>
            </nav>
            <div class="nav-right">
                <div class="lang-toggle" role="group" aria-label="Langue">
                    <button data-lang="fr" class="active">FR</button>
                    <span class="sep">/</span>
                    <button data-lang="en">EN</button>
                </div>
                <a href="<?= LangService::url('contact') ?>" class="btn" data-en="Contact">Contact</a>
            </div>
        </div>
    </header>

    <main id="main-content">
        <?= $content ?>
    </main>

    <!-- Footer du proto Claude design (avec CTA) -->
    <footer class="footer" style="margin-top: 0;">
        <div class="container-wide">
            <h2 data-en="Want to come?">Envie de <em>venir</em> ?</h2>
            <div style="display:flex; gap: 16px; flex-wrap: wrap;">
                <a class="btn" style="background: var(--linen-50); color: var(--olive-900); border-color: var(--linen-50);" href="<?= LangService::url('contact') ?>"><span data-en="Send an enquiry">Envoyer une demande</span> &rarr;</a>
                <a class="btn btn-ghost" style="color: var(--linen-50); border-color: rgba(251,247,238,0.4);" href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
            </div>

            <div class="footer-grid">
                <div>
                    <div style="font-family: var(--font-display); font-size: 28px; color: var(--linen-50); letter-spacing: -0.01em; margin-bottom: 12px;">Villa Plaisance</div>
                    <p style="margin: 0; max-width: 36ch; color: rgba(251,247,238,0.65); font-size: 14px; line-height: 1.6;" data-en="A maison de charme in Bédarrides, at the heart of Provence's Golden Triangle.">Maison de charme à Bédarrides, au cœur du Triangle d'Or provençal.</p>
                </div>
                <div>
                    <h4 data-en="The house">La maison</h4>
                    <ul>
                        <li><a href="<?= LangService::url('chambres-d-hotes') ?>" data-en="B&amp;B rooms (Sept → June)">Chambres d'hôtes (sept &rarr; juin)</a></li>
                        <li><a href="<?= LangService::url('location-villa-provence') ?>" data-en="Whole house">Maison d'hôtes (villa entière)</a></li>
                        <li><a href="<?= LangService::url('disponibilites') ?>" data-en="Availability">Disponibilités</a></li>
                        <li><a href="<?= LangService::url('espaces-exterieurs') ?>" data-en="The outdoors">Les extérieurs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 data-en="Journal">Journal</h4>
                    <ul>
                        <li><a href="<?= LangService::url('journal') ?>" data-en="Tourism">Tourisme</a></li>
                        <li><a href="<?= LangService::url('itineraire') ?>" data-en="What to do nearby">Que faire sur place</a></li>
                    </ul>
                </div>
                <div>
                    <h4 data-en="Stay close">Rester proche</h4>
                    <ul>
                        <li><a href="<?= LangService::url('contact') ?>" data-en="Contact">Contact</a></li>
                        <li><a href="<?= LangService::url('votre-hote') ?>" data-en="Your host">Votre hôte</a></li>
                        <li><a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a></li>
                        <li><a href="#" data-en="Instagram">Instagram</a></li>
                        <li style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(251,247,238,0.12);"><a href="<?= LangService::url('livret') ?>" data-en="Welcome booklet">Livret d'accueil</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; <?= date('Y') ?> Villa Plaisance</span>
                <span data-en="Bédarrides · Vaucluse · Provence">Bédarrides &middot; Vaucluse &middot; Provence</span>
            </div>
        </div>
    </footer>

    <!-- Cookie consent RGPD (hérité v8) -->
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

    <!-- i18n FR/EN du proto (toggle client-side via data-en) -->
    <script src="/assets/js/i18n-proto.js?v=<?= filemtime(ROOT . '/public/assets/js/i18n-proto.js') ?>" defer></script>
</body>
</html>
