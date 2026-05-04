<?php
// Villa entière V8 — porté du V2 (`location-villa-provence/index.html`).
// Variables : $seo, $jsonLd, $lang.
// À dynamiser : Acte 4 (FAQ) → vp_faq WHERE page_slug='location-villa-provence'.
?>

<!-- Acte 0 : opening compact -->
<section class="opening opening--compact" aria-labelledby="opening-title">
    <figure class="opening__plate plate__ph plate--ph-opening">
        <img src="/assets/img/v8/villa-plaisance-piscine-privee-09.webp"
             alt="Vue surélevée du jardin de Villa Plaisance : palmier au premier plan, piscine et transats au centre, cyprès à l'horizon."
             fetchpriority="high" decoding="async">
    </figure>
    <div class="opening__copy">
        <p class="opening__eyebrow">Bédarrides, Provence</p>
        <h1 class="opening__title opening__title--compact" id="opening-title">La Villa en exclusivité</h1>
        <p class="opening__sub">Quatre chambres, juillet et août.</p>
    </div>
</section>

<!-- Acte 1 : lead intro -->
<section class="lead" aria-labelledby="lead-title">
    <h2 class="acte__num" id="lead-title"><span>I.</span> Toute la maison pour vous</h2>
    <p class="lead__corps">
        En juillet et août, Villa Plaisance se loue en exclusivité.
        Quatre chambres, une piscine privée clôturée de 12 mètres sur 6,
        une cuisine entièrement équipée, un jardin provençal. Jusqu'à dix
        personnes.
        <span class="lead__chute">La gestion est autonome, les clés sont à vous.</span>
    </p>
</section>

<!-- Acte 2 : les quatre chambres en grille 2×2 -->
<section class="quatre" aria-labelledby="quatre-title">
    <h2 class="acte__num" id="quatre-title"><span>II.</span> Les quatre chambres</h2>

    <div class="quatre__grille">

        <article class="cellule" id="verte">
            <figure class="cellule__plate plate__ph plate--ph-chambres">
                <img src="/assets/img/v8/villa-plaisance-chambre-verte-04.webp"
                     alt="Chambre Verte : grand lit, mur vert profond, lumière naturelle latérale."
                     loading="lazy" decoding="async">
            </figure>
            <div class="cellule__corps">
                <p class="cellule__numero">1<span>/4</span></p>
                <h3 class="cellule__titre">La Verte</h3>
                <p class="cellule__sous">Grand lit, vue jardin · Rez-de-chaussée</p>
                <p class="cellule__texte">
                    Lit 160×200, vue sur le jardin et les oliviers.
                    Climatisation réversible, TV.
                </p>
            </div>
        </article>

        <article class="cellule" id="bleue">
            <figure class="cellule__plate plate__ph plate--ph-villa">
                <img src="/assets/img/v8/villa-plaisance-chambre-bleue-04.webp"
                     alt="Chambre Bleue : deux lits jumelables, mur gris-bleu, voilages aux fenêtres."
                     loading="lazy" decoding="async">
            </figure>
            <div class="cellule__corps">
                <p class="cellule__numero">2<span>/4</span></p>
                <h3 class="cellule__titre">La Bleue</h3>
                <p class="cellule__sous">Bibliothèque 300 livres · 2-3 personnes</p>
                <p class="cellule__texte">
                    Deux lits 90×200 jumelables, clic-clac, bibliothèque
                    de 300 livres. Climatisation réversible.
                </p>
            </div>
        </article>

        <article class="cellule" id="arche">
            <figure class="cellule__plate plate__ph plate--ph-identite">
                <img src="/assets/img/v8/villa-plaisance-chambre-arche-01.webp"
                     alt="Chambre Arche : grande arche peinte en bleu nuit derrière le lit, bibliothèques sol-plafond de chaque côté."
                     loading="lazy" decoding="async">
            </figure>
            <div class="cellule__corps">
                <p class="cellule__numero">3<span>/4</span></p>
                <h3 class="cellule__titre">L'Arche</h3>
                <p class="cellule__sous">Arche bleue nuit · Accès direct jardin</p>
                <p class="cellule__texte">
                    Lit 140×180 sous une grande arche peinte en bleu nuit.
                    Bibliothèques sol-plafond. Au rez-de-chaussée.
                </p>
            </div>
        </article>

        <article class="cellule" id="annees-70">
            <figure class="cellule__plate plate__ph plate--ph-interlude">
                <img src="/assets/img/v8/villa-plaisance-chambre-annees-70-05.webp"
                     alt="Chambre 70 : mobilier vintage années 70, porte-fenêtre ouverte sur palmier et jardin."
                     loading="lazy" decoding="async">
            </figure>
            <div class="cellule__corps">
                <p class="cellule__numero">4<span>/4</span></p>
                <h3 class="cellule__titre">La 70</h3>
                <p class="cellule__sous">Mobilier vintage · Accès direct jardin</p>
                <p class="cellule__texte">
                    Grand lit double, mobilier chiné des années 70.
                    Porte-fenêtre ouvrant sur le jardin. La plus atypique.
                </p>
            </div>
        </article>

    </div>
</section>

<!-- Acte 3 : piscine privée -->
<section class="rituel" aria-labelledby="rituel-title">
    <h2 class="acte__num" id="rituel-title"><span>III.</span> Piscine privée</h2>
    <div class="rituel__grille rituel__grille--miroir">
        <p class="rituel__corps">
            <em>
                Piscine privée de 12 mètres sur 6, clôturée et sécurisée.
                Ouverte de mi-mai à fin septembre. Transats, parasols,
                douche extérieure.
            </em>
            <span class="rituel__chute">Réservée exclusivement aux locataires de la villa.</span>
        </p>
        <figure class="rituel__plate plate__ph plate--ph-opening">
            <img src="/assets/img/v8/villa-plaisance-piscine-privee-04.webp"
                 alt="Piscine privée Villa Plaisance : eau cristalline, transats avec parasol, cyprès en arrière-plan."
                 loading="lazy" decoding="async">
        </figure>
    </div>
</section>

<!-- Acte 4 : FAQ accordéon — TODO : brancher sur vp_faq WHERE page_slug='location-villa-provence' -->
<section class="reponses" aria-labelledby="reponses-title">
    <h2 class="acte__num" id="reponses-title"><span>IV.</span> Réponses</h2>

    <div class="qa-list">
        <details class="qa-item" open>
            <summary>Combien de personnes la villa peut-elle accueillir&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    La villa accueille jusqu'à 10 personnes réparties dans
                    4 chambres&nbsp;: Chambre Verte, Chambre Bleue, Chambre Arche
                    et Chambre 70.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>La piscine est-elle privée en location villa&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Oui, en juillet et août, la piscine de 12 mètres sur 6
                    est entièrement privée et réservée aux locataires.
                    Elle est clôturée et sécurisée.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>La cuisine est-elle équipée&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Oui, la cuisine est entièrement équipée&nbsp;: four, plaques,
                    lave-vaisselle, micro-ondes, réfrigérateur, ustensiles de
                    cuisine et vaisselle pour 10 personnes.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>Le linge de maison est-il fourni&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Oui, les draps, serviettes de bain et serviettes de piscine
                    sont fournis et changés chaque semaine.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>Quelle est la durée minimum de location&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    En haute saison (juillet-août), la durée minimum est
                    d'une semaine, du samedi au samedi.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>Y a-t-il des commerces à proximité&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Oui, Bédarrides dispose de boulangeries, supérette,
                    restaurants et pharmacie. Le supermarché le plus proche
                    est à Sorgues, à 5 minutes en voiture.
                </p>
            </div>
        </details>
    </div>
</section>

<!-- Acte 5 : contact -->
<section class="contact" id="contact" aria-labelledby="contact-title">
    <h2 class="acte__num" id="contact-title"><span>V.</span> Écrire</h2>
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
