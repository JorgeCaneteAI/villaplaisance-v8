<?php
// Sur place V8 — porté du V2 (`sur-place/index.html`).
// Variables : $seo, $jsonLd, $lang, $articles, $categories.
//
// V2 statique : 9 articles en dur. Le Controller V8 fournit déjà $articles
// et $categories depuis vp_articles, mais on n'a pas validé le mapping des
// champs (image, slug, category, published_at). Pour aller vite, on porte
// d'abord le HTML statique. À dynamiser dans une étape ultérieure :
//   foreach ($articles as $i => $a) { ... rendu carte ... }
//   filtres construits depuis $categories.
?>

<!-- Acte 0 : opening compact -->
<section class="opening opening--compact opening--mince" aria-labelledby="opening-title">
    <div class="opening__copy opening__copy--centered">
        <p class="opening__eyebrow">Bédarrides, Provence</p>
        <h1 class="opening__title opening__title--compact" id="opening-title">Sur place</h1>
        <p class="opening__sub">Nos adresses et recommandations autour de Bédarrides.</p>
    </div>
</section>

<!-- Acte 1 : filtres — TODO : générer depuis $categories -->
<nav class="filtres" aria-label="Filtrer par catégorie">
    <a class="filtre is-actif" href="#">Tous</a>
    <a class="filtre" href="#">Commerces</a>
    <a class="filtre" href="#">Sites à visiter</a>
    <a class="filtre" href="#">Restaurants &amp; tables</a>
    <a class="filtre" href="#">Avec des enfants</a>
</nav>

<!-- Acte 2 : magazine grid — TODO : foreach ($articles as $a) -->
<section class="magazine" aria-label="Liste des recommandations">

    <article class="article-card article-card--featured">
        <a class="article-card__lien" href="<?= LangService::url('sur-place') ?>/courses-bedarrides-sorgues">
            <figure class="article-card__plate">
                <img src="/assets/img/v8/article-courses-bedarrides.webp"
                     alt="Étalage de fruits et légumes au marché de Bédarrides."
                     loading="lazy" decoding="async">
            </figure>
            <div class="article-card__corps">
                <p class="article-card__meta"><time datetime="2025-10-10">10 oct. 2025</time> · Commerces</p>
                <h2 class="article-card__titre">Faire ses courses à Bédarrides et Sorgues</h2>
                <p class="article-card__teaser">
                    Les adresses qu'on donne à nos hôtes pour les courses du quotidien.
                </p>
                <span class="article-card__lire">Lire l'article
                    <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
                </span>
            </div>
        </a>
    </article>

    <div class="magazine__grille">

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('sur-place') ?>/artisans-savonnerie-chocolaterie">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-savonnerie-chocolaterie.webp"
                         alt="Savons artisanaux empilés sur une étagère en bois."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-10-05">5 oct. 2025</time> · Commerces</p>
                    <h3 class="article-card__titre">Savonnerie et chocolaterie&nbsp;: deux adresses artisanales</h3>
                    <p class="article-card__teaser">Deux artisans à découvrir autour de Bédarrides.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('sur-place') ?>/fontaine-de-vaucluse-guide-pratique">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-fontaine-vaucluse.webp"
                         alt="Source de la Fontaine de Vaucluse, eau turquoise au pied de la falaise."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-09-28">28 sept. 2025</time> · Sites à visiter</p>
                    <h3 class="article-card__titre">Fontaine de Vaucluse&nbsp;: le guide pratique</h3>
                    <p class="article-card__teaser">Ce qu'on ne vous dit pas toujours avant de visiter Fontaine de Vaucluse.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('sur-place') ?>/sentier-des-ocres-roussillon">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-sentier-ocres-roussillon.webp"
                         alt="Falaises d'ocre rouge orangé à Roussillon, sentier en contrebas."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-09-15">15 sept. 2025</time> · Sites à visiter</p>
                    <h3 class="article-card__titre">Le Sentier des Ocres de Roussillon</h3>
                    <p class="article-card__teaser">Guide pratique avant de partir sur le sentier des ocres.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('sur-place') ?>/chateau-la-gardine-chateauneuf-du-pape">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-chateau-gardine-degustation.webp"
                         alt="Caves voûtées du Château La Gardine, fûts de chêne alignés."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-09-01">1 sept. 2025</time> · Sites à visiter</p>
                    <h3 class="article-card__titre">Château de la Gardine&nbsp;: dégustation à Châteauneuf-du-Pape</h3>
                    <p class="article-card__teaser">Dégustation à 8 minutes de Bédarrides, dans l'un des domaines historiques de l'appellation.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('sur-place') ?>/parc-spirou-provence-monteux">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-parc-spirou-provence.webp"
                         alt="Manège du Parc Spirou Provence en pleine action, ciel bleu."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-08-20">20 août 2025</time> · Avec des enfants</p>
                    <h3 class="article-card__titre">Parc Spirou Provence&nbsp;: tout savoir avant d'y aller</h3>
                    <p class="article-card__teaser">Guide pratique pour visiter le Parc Spirou avec des enfants.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('sur-place') ?>/ateliers-creatifs-enfants-provence">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-ateliers-creatifs-enfants.webp"
                         alt="Mains d'enfant peignant à l'aquarelle, palette de couleurs."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-08-15">15 août 2025</time> · Avec des enfants</p>
                    <h3 class="article-card__titre">Ateliers créatifs pour enfants en Provence</h3>
                    <p class="article-card__teaser">Notre sélection vérifiée d'ateliers pour enfants autour de Bédarrides.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('sur-place') ?>/imperial-bus-diner-bedarrides">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-imperial-bus-diner.webp"
                         alt="Devanture de l'Impérial Bus Diner, food truck rouge américain."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-08-05">5 août 2025</time> · Restaurants &amp; tables</p>
                    <h3 class="article-card__titre">Impérial Bus Diner&nbsp;: le burger de Bédarrides</h3>
                    <p class="article-card__teaser">Le burger de Bédarrides, 4,5/5 sur 340 avis. À tester.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('sur-place') ?>/le-numero-3-bedarrides">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-numero-3-bistrot.webp"
                         alt="Devanture du Numéro 3, bistrot en bord d'Ouvèze à Bédarrides."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-07-20">20 juil. 2025</time> · Restaurants &amp; tables</p>
                    <h3 class="article-card__titre">Le Numéro 3&nbsp;: le bistrot de Bédarrides</h3>
                    <p class="article-card__teaser">Le bistrot de Bédarrides, en bord d'Ouvèze. Cuisine simple et sincère.</p>
                </div>
            </a>
        </article>

    </div>
</section>
