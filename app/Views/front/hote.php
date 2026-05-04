<?php
// Votre hôte V8 — porté du V2 (`votre-hote/index.html`).
// Variables : $seo, $jsonLd, $lang, $profile, $blocks, $reviews.
//
// V2 statique : tout en dur (Jorge Cañete, 4 repères CV). Le Controller V8
// fournit déjà $profile et $blocks dynamiques depuis vp_host_profile et
// vp_host_blocks. À dynamiser dans une étape ultérieure : remplacer le
// contenu en dur par les variables du Controller, en gardant le V2 comme
// fallback si la DB est vide.
?>

<!-- Acte 0 : opening compact -->
<section class="opening opening--compact opening--mince" aria-labelledby="opening-title">
    <div class="opening__copy opening__copy--centered">
        <p class="opening__eyebrow">Bédarrides, Provence</p>
        <h1 class="opening__title opening__title--compact" id="opening-title">Jorge Cañete</h1>
        <p class="opening__sub">Votre hôte à Villa Plaisance.</p>
    </div>
</section>

<!-- Acte 1 : intro + photo + quote -->
<section class="bio" aria-labelledby="bio-title">
    <h2 class="acte__num" id="bio-title"><span>I.</span> L'accueil</h2>

    <div class="bio__grille">
        <figure class="bio__plate plate__ph plate--ph-identite">
            <img src="/assets/img/v8/villa-plaisance-cuisine-equipee-04.webp"
                 alt="Cuisine équipée de Villa Plaisance, four, frigo américain, plan de travail clair."
                 loading="lazy" decoding="async">
        </figure>
        <div class="bio__corps">
            <p class="bio__intro">
                Bienvenue. Je suis Jorge, votre hôte à Villa Plaisance.
                Passionné par l'accueil et la Provence, j'ai ouvert les portes
                de cette maison pour partager un art de vivre simple et authentique.
            </p>
            <blockquote class="bio__cite">
                L'hospitalité, c'est faire sentir à l'autre qu'il est chez lui,
                même quand il est chez vous.
            </blockquote>
        </div>
    </div>
</section>

<!-- Acte 2 : 4 blocs CV — TODO : utiliser $blocks de vp_host_blocks -->
<section class="cv" aria-labelledby="cv-title">
    <h2 class="acte__num" id="cv-title"><span>II.</span> Quatre repères</h2>

    <div class="cv__grille">

        <article class="cv__bloc">
            <p class="cv__numero">01</p>
            <h3 class="cv__titre">Origine</h3>
            <p class="cv__texte">
                Originaire du sud de la France, j'ai grandi entre la mer et la
                garrigue. Après des années dans le monde du digital et de la
                communication, j'ai choisi de revenir aux essentiels&nbsp;:
                le soleil, la terre, les gens.
            </p>
        </article>

        <article class="cv__bloc">
            <p class="cv__numero">02</p>
            <h3 class="cv__titre">Passions</h3>
            <p class="cv__texte">
                La cuisine provençale, les marchés du dimanche, le vin
                (surtout celui des voisins vignerons), la photographie,
                les longues discussions sur la terrasse, et faire découvrir
                les trésors cachés du Vaucluse.
            </p>
        </article>

        <article class="cv__bloc">
            <p class="cv__numero">03</p>
            <h3 class="cv__titre">Philosophie</h3>
            <p class="cv__texte">
                Ici, pas de protocole. On se tutoie, on partage les bons plans,
                on prend le temps. Villa Plaisance, c'est votre maison en Provence,
                avec un hôte qui connaît chaque recoin du territoire.
            </p>
        </article>

        <article class="cv__bloc">
            <p class="cv__numero">04</p>
            <h3 class="cv__titre">Pour la petite histoire</h3>
            <p class="cv__texte">
                Je parle trois langues (français, espagnol, anglais). Je fais
                le meilleur café du Vaucluse, selon mes hôtes. Je connais le
                prénom de chaque olivier du jardin.
            </p>
        </article>

    </div>
</section>

<!-- Acte 3 : contact -->
<section class="contact" id="contact" aria-labelledby="contact-title">
    <h2 class="acte__num" id="contact-title"><span>III.</span> Écrire</h2>
    <p class="contact__phrase">Une envie de venir&nbsp;?</p>
    <p class="contact__sub">Écrivez-moi directement, je réponds personnellement sous deux jours.</p>
    <div class="contact__actions">
        <a class="contact__bouton" href="<?= LangService::url('contact') ?>">
            M'écrire
            <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        </a>
        <p class="contact__alt">
            Ou directement&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
        </p>
    </div>
</section>
