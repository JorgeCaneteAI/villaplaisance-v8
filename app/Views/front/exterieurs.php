<?php
// Espaces extérieurs V8 — porté du V2 (`espaces-exterieurs/index.html`).
// Variables : $seo, $jsonLd, $lang.
?>

<!-- Acte 0 : opening compact -->
<section class="opening opening--compact" aria-labelledby="opening-title">
    <figure class="opening__plate plate__ph plate--ph-interlude">
        <img src="/assets/img/v8/villa-plaisance-jardin-exterieur-04.webp"
             alt="Vue large du jardin de Villa Plaisance : palmier au premier plan, piscine et transats au centre, cyprès et arbres en arrière-plan."
             fetchpriority="high" decoding="async">
    </figure>
    <div class="opening__copy">
        <p class="opening__eyebrow">Bédarrides, Provence</p>
        <h1 class="opening__title opening__title--compact" id="opening-title">Espaces extérieurs</h1>
        <p class="opening__sub">Jardin, piscine, terrasses.</p>
    </div>
</section>

<!-- Acte 1 : lead intro -->
<section class="lead" aria-labelledby="lead-title">
    <h2 class="acte__num" id="lead-title"><span>I.</span> Dehors, ici, c'est encore chez vous</h2>
    <p class="lead__corps">
        Le jardin de Villa Plaisance est un prolongement naturel de la maison.
        Oliviers centenaires, lavande, romarin. La piscine de 12 mètres sur 6,
        les terrasses ombragées, le potager en été.
        <span class="lead__chute">Un espace où l'on passe plus de temps qu'à l'intérieur.</span>
    </p>
</section>

<!-- Acte 2 : galerie photos asymétrique -->
<section class="galerie" aria-labelledby="galerie-title">
    <h2 class="acte__num" id="galerie-title"><span>II.</span> Le jardin en images</h2>

    <div class="galerie__grille">

        <figure class="galerie__cell galerie__cell--large">
            <img src="/assets/img/v8/villa-plaisance-piscine-privee-09.webp"
                 alt="Piscine vue depuis le palmier : eau bleue au cœur du jardin, cyprès à l'horizon."
                 loading="lazy" decoding="async">
            <figcaption>La piscine, depuis le palmier.</figcaption>
        </figure>

        <figure class="galerie__cell">
            <img src="/assets/img/v8/villa-plaisance-jardin-exterieur-22.webp"
                 alt="Vieille souche de vigne entourée de coquelicots rouges en pleine floraison."
                 loading="lazy" decoding="async">
            <figcaption>Vigne et coquelicots, mai.</figcaption>
        </figure>

        <figure class="galerie__cell">
            <img src="/assets/img/v8/villa-plaisance-jardin-exterieur-17.webp"
                 alt="Sentier en gravier longeant les vignes, cyprès et collines à l'horizon."
                 loading="lazy" decoding="async">
            <figcaption>Le chemin derrière la maison.</figcaption>
        </figure>

        <figure class="galerie__cell galerie__cell--portrait">
            <img src="/assets/img/v8/villa-plaisance-jardin-exterieur-01.webp"
                 alt="Coquelicots rouges au premier plan, chaises de jardin en métal blanc en arrière-plan flou."
                 loading="lazy" decoding="async">
            <figcaption>Le coin lecture, fin août.</figcaption>
        </figure>

        <figure class="galerie__cell galerie__cell--wide">
            <img src="/assets/img/v8/villa-plaisance-piscine-privee-04.webp"
                 alt="Piscine privée Villa Plaisance : eau cristalline, transats avec parasol, cyprès en arrière-plan."
                 loading="lazy" decoding="async">
            <figcaption>Transats et parasol, à l'ombre l'après-midi.</figcaption>
        </figure>

        <figure class="galerie__cell">
            <img src="/assets/img/v8/villa-plaisance-jardin-exterieur-21.webp"
                 alt="Vignes en lignes droites sous le soleil, paysage du Vaucluse."
                 loading="lazy" decoding="async">
            <figcaption>Les vignes, à deux pas.</figcaption>
        </figure>

        <figure class="galerie__cell">
            <img src="/assets/img/v8/villa-plaisance-vignes-provence-06.webp"
                 alt="Détail de grappes de raisin sur la vigne, lumière dorée."
                 loading="lazy" decoding="async">
            <figcaption>Châteauneuf à huit minutes.</figcaption>
        </figure>

        <figure class="galerie__cell">
            <img src="/assets/img/v8/villa-plaisance-piscine-privee-05.webp"
                 alt="Bord de piscine, dalles claires, eau turquoise."
                 loading="lazy" decoding="async">
            <figcaption>Bord de piscine, dalles claires.</figcaption>
        </figure>

    </div>
</section>

<!-- Acte 3 : contact -->
<section class="contact" id="contact" aria-labelledby="contact-title">
    <h2 class="acte__num" id="contact-title"><span>III.</span> Écrire</h2>
    <p class="contact__phrase">Envie de séjourner chez nous&nbsp;?</p>
    <p class="contact__sub">Contactez-nous pour organiser votre séjour en Provence.</p>
    <div class="contact__actions">
        <a class="contact__bouton" href="<?= LangService::url('contact') ?>">
            Nous écrire
            <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        </a>
        <p class="contact__alt">
            Ou directement&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
        </p>
    </div>
</section>
