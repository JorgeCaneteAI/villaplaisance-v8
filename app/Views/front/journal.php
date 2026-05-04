<?php
// Journal V8 (liste) — porté du V2 (`journal/index.html`).
// Variables : $seo, $jsonLd, $lang, $articles, $categories.
//
// V2 statique : 10 articles en dur. Le Controller V8 fournit déjà $articles
// et $categories depuis vp_articles. À dynamiser dans une étape ultérieure.
?>

<!-- Acte 0 : opening compact -->
<section class="opening opening--compact opening--mince" aria-labelledby="opening-title">
    <div class="opening__copy opening__copy--centered">
        <p class="opening__eyebrow">Bédarrides, Provence</p>
        <h1 class="opening__title opening__title--compact" id="opening-title">Le Journal</h1>
        <p class="opening__sub">Récits, conseils et regards sur la Provence.</p>
    </div>
</section>

<!-- Acte 1 : filtres catégories — TODO : générer depuis $categories -->
<nav class="filtres" aria-label="Filtrer par catégorie">
    <a class="filtre is-actif" href="#">Tous</a>
    <a class="filtre" href="#">Voyager autrement</a>
    <a class="filtre" href="#">Hôtes &amp; hôteliers</a>
    <a class="filtre" href="#">Territoire &amp; transition</a>
    <a class="filtre" href="#">L'art de séjourner</a>
    <a class="filtre" href="#">Provence contemporaine</a>
</nav>

<!-- Acte 2 : article featured + grille magazine — TODO : foreach ($articles) -->
<section class="magazine" aria-label="Liste des articles">

    <article class="article-card article-card--featured">
        <a class="article-card__lien" href="<?= LangService::url('journal') ?>/le-tourisme-de-masse-est-une-arnaque">
            <figure class="article-card__plate">
                <img src="/assets/img/v8/article-tourisme-masse.webp"
                     alt="Vue aérienne d'une foule de touristes serrée devant un monument."
                     loading="lazy" decoding="async">
            </figure>
            <div class="article-card__corps">
                <p class="article-card__meta"><time datetime="2025-10-15">15 oct. 2025</time> · Voyager autrement</p>
                <h2 class="article-card__titre">Le tourisme de masse est une arnaque</h2>
                <p class="article-card__teaser">
                    Pourquoi le tourisme de masse persiste malgré ses travers,
                    et comment choisir une autre voie en Provence.
                </p>
                <span class="article-card__lire">Lire l'article
                    <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
                </span>
            </div>
        </a>
    </article>

    <div class="magazine__grille">

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('journal') ?>/louer-maison-plutot-hotel-voyage">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-louer-maison-provence.webp"
                         alt="Intérieur lumineux d'une maison de location en Provence."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-10-01">1 oct. 2025</time> · Voyager autrement</p>
                    <h3 class="article-card__titre">Louer une maison plutôt qu'un hôtel&nbsp;: pourquoi ça change tout au voyage</h3>
                    <p class="article-card__teaser">Comparatif entre séjour hôtel et location de maison en Provence. Ce que ça change vraiment.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('journal') ?>/vie-proprietaire-chambre-hotes">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-vie-proprietaire-bb.webp"
                         alt="Détail intérieur d'une maison d'hôtes provençale, vase de fleurs."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-09-20">20 sept. 2025</time> · Hôtes &amp; hôteliers</p>
                    <h3 class="article-card__titre">Ce que personne ne dit sur la vie d'un propriétaire de chambre d'hôtes</h3>
                    <p class="article-card__teaser">Les coulisses du métier d'hôte en Provence. Entre passion et réalité quotidienne.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('journal') ?>/recevoir-des-inconnus-chez-soi">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-recevoir-inconnus.webp"
                         alt="Table de petit-déjeuner dressée avec deux couverts."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-09-05">5 sept. 2025</time> · Hôtes &amp; hôteliers</p>
                    <h3 class="article-card__titre">Recevoir des inconnus chez soi&nbsp;: ce que ça apprend sur les gens</h3>
                    <p class="article-card__teaser">Ce que l'accueil en chambres d'hôtes révèle sur l'hospitalité et les rencontres.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('journal') ?>/chateauneuf-du-pape-2026">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-chateauneuf-du-pape.webp"
                         alt="Vignoble de Châteauneuf-du-Pape sous le soleil."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-08-25">25 août 2025</time> · Territoire &amp; transition</p>
                    <h3 class="article-card__titre">Châteauneuf-du-Pape en 2026&nbsp;: entre sécheresse et renaissance</h3>
                    <p class="article-card__teaser">Comment le vignoble de Châteauneuf-du-Pape s'adapte au changement climatique.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('journal') ?>/provence-vignerons-autrement">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-vignerons-provence.webp"
                         alt="Mains d'un vigneron tenant des grains de raisin."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-08-10">10 août 2025</time> · Territoire &amp; transition</p>
                    <h3 class="article-card__titre">La Provence qui résiste&nbsp;: portraits de vignerons qui font autrement</h3>
                    <p class="article-card__teaser">Rencontre avec des vignerons provençaux qui choisissent le bio et le respect du terroir.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('journal') ?>/duree-ideale-sejour-provence">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-duree-sejour-provence.webp"
                         alt="Valise ouverte sur un lit, fenêtre ouverte sur la campagne provençale."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-07-28">28 juil. 2025</time> · L'art de séjourner</p>
                    <h3 class="article-card__titre">Deux nuits ou deux semaines&nbsp;: comment trouver la durée idéale pour un séjour en Provence</h3>
                    <p class="article-card__teaser">Guide pour choisir la durée de son séjour en Provence selon ses envies.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('journal') ?>/deconnecter-provence">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-deconnecter-provence.webp"
                         alt="Téléphone posé face contre la table en bois sous une treille."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-07-15">15 juil. 2025</time> · L'art de séjourner</p>
                    <h3 class="article-card__titre">Déconnecter vraiment&nbsp;: ce que la Provence impose à ceux qui s'y posent</h3>
                    <p class="article-card__teaser">Pourquoi la Provence est l'endroit idéal pour une vraie coupure numérique.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('journal') ?>/bedarrides-provence-authentique">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-bedarrides-provence.webp"
                         alt="Place du village de Bédarrides, fontaine et platanes."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-07-01">1 juil. 2025</time> · Provence contemporaine</p>
                    <h3 class="article-card__titre">Bédarrides n'est pas sur les brochures. C'est pour ça qu'on y vit.</h3>
                    <p class="article-card__teaser">Portrait de Bédarrides, village provençal authentique loin du tourisme de masse.</p>
                </div>
            </a>
        </article>

        <article class="article-card">
            <a class="article-card__lien" href="<?= LangService::url('journal') ?>/touriste-2026-nouvelles-attentes">
                <figure class="article-card__plate">
                    <img src="/assets/img/v8/article-touriste-2026.webp"
                         alt="Voyageur seul devant une fenêtre ouverte sur la campagne."
                         loading="lazy" decoding="async">
                </figure>
                <div class="article-card__corps">
                    <p class="article-card__meta"><time datetime="2025-06-15">15 juin 2025</time> · Provence contemporaine</p>
                    <h3 class="article-card__titre">Le touriste de 2026&nbsp;: ce qu'il veut vraiment, et ce que l'industrie n'a pas compris</h3>
                    <p class="article-card__teaser">Analyse des nouvelles attentes des voyageurs et comment l'hébergement indépendant y répond.</p>
                </div>
            </a>
        </article>

    </div>
</section>
