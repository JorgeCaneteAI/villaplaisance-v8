<?php
// Itinéraires V8 — page de présentation du service.
// Porté du V2 (`itineraire/index.html`).
// Variables : $seo, $jsonLd, $lang.
//
// À ne pas confondre avec front/itinerary.php qui rend le DÉTAIL d'un
// itinéraire personnalisé pour un guest (URL /itineraire/{slug}).
?>

<!-- Acte 0 : opening compact -->
<section class="opening opening--compact opening--mince" aria-labelledby="opening-title">
    <div class="opening__copy opening__copy--centered">
        <p class="opening__eyebrow">Un service pour nos hôtes</p>
        <h1 class="opening__title opening__title--compact" id="opening-title">Itinéraires personnalisés</h1>
        <p class="opening__sub">Une journée pensée pour vous, avant que vous n'arriviez.</p>
    </div>
</section>

<!-- Acte 1 : ce que c'est -->
<section class="lead" aria-labelledby="lead-title">
    <h2 class="acte__num" id="lead-title"><span>I.</span> Comment ça marche</h2>
    <p class="lead__corps">
        Avant votre venue, nous échangeons sur ce qui vous intéresse&nbsp;:
        marchés, vignobles, sites à visiter, randonnées, restaurants.
        Nous préparons ensuite un itinéraire d'une journée sur mesure&nbsp;:
        étapes, horaires, conseils, contacts.
        <span class="lead__chute">C'est inclus, c'est notre métier.</span>
    </p>
</section>

<!-- Acte 2 : 4 étapes -->
<section class="cv" aria-labelledby="etapes-title">
    <h2 class="acte__num" id="etapes-title"><span>II.</span> Quatre étapes</h2>

    <div class="cv__grille">

        <article class="cv__bloc">
            <p class="cv__numero">01</p>
            <h3 class="cv__titre">On échange</h3>
            <p class="cv__texte">
                Quelques jours avant votre arrivée, nous parlons par email
                ou téléphone. Centres d'intérêt, rythme souhaité, contraintes
                (enfants, mobilité, durée).
            </p>
        </article>

        <article class="cv__bloc">
            <p class="cv__numero">02</p>
            <h3 class="cv__titre">On compose</h3>
            <p class="cv__texte">
                Nous bâtissons une boucle d'une journée à partir de Villa Plaisance.
                Trois à six étapes, avec horaires d'ouverture vérifiés et numéros
                de téléphone à jour.
            </p>
        </article>

        <article class="cv__bloc">
            <p class="cv__numero">03</p>
            <h3 class="cv__titre">On vous remet</h3>
            <p class="cv__texte">
                Vous recevez l'itinéraire par mail&nbsp;: une carte interactive,
                une frise horaire, des notes pour chaque étape. Imprimable,
                partageable, modifiable.
            </p>
        </article>

        <article class="cv__bloc">
            <p class="cv__numero">04</p>
            <h3 class="cv__titre">Vous y allez</h3>
            <p class="cv__texte">
                Le jour J, vous suivez (ou pas&nbsp;: c'est le vôtre).
                On reste joignables si vous voulez improviser ou changer
                d'avis en chemin.
            </p>
        </article>

    </div>
</section>

<!-- Acte 3 : contact -->
<section class="contact" id="contact" aria-labelledby="contact-title">
    <h2 class="acte__num" id="contact-title"><span>III.</span> Demander un itinéraire</h2>
    <p class="contact__phrase">Vous avez réservé&nbsp;?</p>
    <p class="contact__sub">Écrivez-nous quelques jours avant votre arrivée. Précisez le jour souhaité, vos centres d'intérêt et la composition du groupe.</p>
    <div class="contact__actions">
        <a class="contact__bouton" href="<?= LangService::url('contact') ?>">
            Demander
            <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        </a>
        <p class="contact__alt">
            Ou directement&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
        </p>
    </div>
</section>
