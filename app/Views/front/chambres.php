<?php
// Chambres d'hôtes V8 — porté du V2 (`chambres-d-hotes/index.html`).
// Variables : $seo, $jsonLd, $lang.
// À dynamiser plus tard : Acte 4 (FAQ) → vp_faq WHERE page_slug='chambres-d-hotes'.
?>

<!-- Acte 0 : opening compact -->
<section class="opening opening--compact" aria-labelledby="opening-title">
    <figure class="opening__plate plate__ph plate--ph-opening">
        <img src="/assets/img/v8/villa-plaisance-petit-dejeuner-fruits-01.webp"
             alt="Assiette de fruits frais en vue plongeante : pomme, fraises, melon, sur un set de table coloré, couverts argentés."
             fetchpriority="high" decoding="async">
    </figure>
    <div class="opening__copy">
        <p class="opening__eyebrow">Bédarrides, Provence</p>
        <h1 class="opening__title opening__title--compact" id="opening-title">Chambres d'hôtes</h1>
        <p class="opening__sub">Deux chambres, de septembre à juin.</p>
    </div>
</section>

<!-- Acte 1 : lead intro -->
<section class="lead" aria-labelledby="lead-title">
    <h2 class="acte__num" id="lead-title"><span>I.</span> Séjourner en chambres d'hôtes</h2>
    <p class="lead__corps">
        De septembre à juin, Villa Plaisance ouvre deux chambres aux voyageurs.
        Le petit-déjeuner est préparé chaque matin avec des produits locaux.
        La piscine est partagée avec les hôtes.
        <span class="lead__chute">L'accueil est personnel, les conseils aussi.</span>
    </p>
</section>

<!-- Acte 2 : les deux chambres -->
<section class="chambres" aria-labelledby="chambres-title">
    <h2 class="acte__num" id="chambres-title"><span>II.</span> Les deux chambres</h2>

    <article class="chambre chambre--verte" id="verte">
        <figure class="chambre__plate plate__ph plate--ph-chambres">
            <img src="/assets/img/v8/villa-plaisance-chambre-verte-03.webp"
                 alt="Chambre Verte : grand lit 160 contre un mur vert profond, lampes murales chaudes, couvre-lit jaune en accent."
                 loading="lazy" decoding="async">
        </figure>
        <div class="chambre__corps">
            <p class="chambre__numero">1<span>chambre</span></p>
            <h3 class="chambre__titre">La Chambre Verte</h3>
            <p class="chambre__sous">Grand lit, vue jardin</p>
            <p class="chambre__texte">
                Chambre lumineuse avec un grand lit 160×200, donnant sur le jardin
                et les oliviers. Espace cocooning, sobriété et calme.
            </p>
            <dl class="chambre__faits">
                <div><dt>Lit</dt><dd>160 × 200</dd></div>
                <div><dt>Vue</dt><dd>Jardin</dd></div>
                <div><dt>Position</dt><dd>Rez-de-chaussée</dd></div>
                <div><dt>Confort</dt><dd>Climatisation, TV, Wifi</dd></div>
            </dl>
        </div>
    </article>

    <article class="chambre chambre--bleue chambre--miroir" id="bleue">
        <figure class="chambre__plate plate__ph plate--ph-villa">
            <img src="/assets/img/v8/villa-plaisance-chambre-bleue-01.webp"
                 alt="Chambre Bleue : deux lits jumelables, mur gris-bleu, fauteuil rouge en accent, fenêtres à voilages, clic-clac au fond."
                 loading="lazy" decoding="async">
        </figure>
        <div class="chambre__corps">
            <p class="chambre__numero">2<span>chambre</span></p>
            <h3 class="chambre__titre">La Chambre Bleue</h3>
            <p class="chambre__sous">Bibliothèque, idéale famille</p>
            <p class="chambre__texte">
                Deux lits 90×200 jumelables en grand lit 180. Un clic-clac pour
                une troisième personne. Une bibliothèque de 300 livres. La chambre
                des lecteurs et des familles.
            </p>
            <dl class="chambre__faits">
                <div><dt>Lits</dt><dd>2 × 90 (jumelables 180)</dd></div>
                <div><dt>Couchage +</dt><dd>Clic-clac (1 personne)</dd></div>
                <div><dt>Bibliothèque</dt><dd>300 livres</dd></div>
                <div><dt>Confort</dt><dd>Climatisation, Wifi</dd></div>
            </dl>
        </div>
    </article>
</section>

<!-- Acte 3 : petit-déjeuner -->
<section class="rituel" aria-labelledby="rituel-title">
    <h2 class="acte__num" id="rituel-title"><span>III.</span> Le petit-déjeuner</h2>
    <div class="rituel__grille">
        <figure class="rituel__plate plate__ph plate--ph-identite">
            <img src="/assets/img/v8/villa-plaisance-petit-dejeuner-confitures-01.webp"
                 alt="Brioche dorée au sucre perlé en gros plan, table dressée du petit-déjeuner en arrière-plan flou."
                 loading="lazy" decoding="async">
        </figure>
        <p class="rituel__corps">
            <em>
                Chaque matin, le petit-déjeuner est préparé avec des produits
                locaux et de saison. Confitures maison, fruits frais, pain de
                boulanger, fromages et charcuteries du terroir.
            </em>
            <span class="rituel__chute">Servi en terrasse quand le temps le permet.</span>
        </p>
    </div>
</section>

<!-- Acte 4 : FAQ accordéon — TODO : brancher sur vp_faq WHERE page_slug='chambres-d-hotes' -->
<section class="reponses" aria-labelledby="reponses-title">
    <h2 class="acte__num" id="reponses-title"><span>IV.</span> Réponses</h2>

    <div class="qa-list">
        <details class="qa-item" open>
            <summary>Le petit-déjeuner est-il inclus&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Oui, le petit-déjeuner est inclus et préparé chaque matin avec
                    des produits locaux et de saison&nbsp;: confitures maison, fruits
                    frais, pain de boulanger, fromages et charcuteries du terroir.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>Les chambres sont-elles climatisées&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Oui, les deux chambres (Verte et Bleue) sont équipées de
                    climatisation réversible et du wifi gratuit.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>Peut-on accueillir des enfants en chambres d'hôtes&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Oui, la Chambre Bleue dispose d'un clic-clac pouvant accueillir
                    une personne supplémentaire, ce qui en fait une chambre idéale
                    pour les familles.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>À quelle période les chambres d'hôtes sont-elles disponibles&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Les chambres d'hôtes sont ouvertes de septembre à juin.
                    En juillet et août, la villa se loue en exclusivité.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>Comment se rendre à Villa Plaisance&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Bédarrides est accessible en voiture (autoroute A7), en TGV
                    (gare d'Avignon TGV à 15 minutes) ou via l'aéroport de
                    Marseille-Provence (1h). Nous pouvons vous fournir les
                    indications détaillées.
                </p>
            </div>
        </details>

        <details class="qa-item">
            <summary>Y a-t-il un parking&nbsp;?</summary>
            <div class="qa-item__corps">
                <p>
                    Oui, un parking privé gratuit est disponible sur place pour
                    tous les hôtes.
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
