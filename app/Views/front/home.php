<?php
// Home V8 — portage du V2 (`villaplaisance-impeccable-V2/index.html`).
//
// Variables disponibles depuis HomeController : $seo, $jsonLd, $lang.
//
// Le contenu textuel est porté en dur du V2 (statique, version FR de
// référence). Les sections suivantes restent à dynamiser via la DB :
//   - Acte 5 (voix) : à brancher sur vp_reviews (filtre featured = 1)
//   - Acte 6 (journal) : à brancher sur vp_articles (3 derniers)
//   - Acte 7 (FAQ) : à brancher sur vp_faq WHERE page_slug='accueil'
//   - Mappemonde (main-v8.js) : à brancher sur vp_reviews.location
?>

<!-- Acte 0 : opening cinematic -->
<section class="opening" aria-labelledby="opening-title">
    <figure class="opening__plate plate__ph plate--ph-opening">
        <img src="/assets/img/v8/villa-plaisance-piscine-privee-02.webp"
             alt="Villa Plaisance vue depuis la piscine, façade blanche derrière la haie, palmier et ciel de Provence."
             fetchpriority="high" decoding="async">
    </figure>
    <div class="opening__copy">
        <p class="opening__eyebrow">Bédarrides, Provence</p>
        <h1 class="opening__title" id="opening-title">Villa Plaisance</h1>
        <p class="opening__sub">Chambres d'hôtes et villa de charme à Bédarrides</p>
    </div>
    <p class="opening__hint" aria-hidden="true">défiler</p>
</section>

<!-- Acte 1 : manifesto identité -->
<section class="manifesto" aria-labelledby="manifesto-title">
    <h2 class="acte__num" id="manifesto-title"><span>I.</span> Une maison, deux façons d'y séjourner</h2>
    <div class="manifesto__grille">
        <p class="manifesto__corps">
            Villa Plaisance est une maison provençale ouverte aux voyageurs,
            à Bédarrides, entre Avignon et Orange. De septembre à juin, nous
            accueillons en chambres d'hôtes, avec petit-déjeuner maison et
            piscine partagée. En juillet et août, la villa entière se loue
            en exclusivité&nbsp;: quatre chambres, une piscine privée de
            12 mètres sur 6, un jardin sous les oliviers.
            <span class="manifesto__chute">
                Le lieu est calme. Le village est vivant.
                La campagne est à pied, le TGV à quinze minutes.
            </span>
        </p>
        <figure class="manifesto__plate plate plate--portrait plate__ph plate--ph-identite">
            <img src="/assets/img/v8/villa-plaisance-facade-04.webp"
                 alt="Façade de Villa Plaisance avec son palmier, jardin de pierre, lumière de mi-journée."
                 loading="lazy" decoding="async">
        </figure>
    </div>
</section>

<!-- Acte 2 : diptyque saisons -->
<section class="diptyque" aria-labelledby="diptyque-title" data-diptyque>
    <h2 class="acte__num" id="diptyque-title"><span>II.</span> Deux saisons, deux maisons</h2>

    <div class="diptyque__grille">

        <article class="saison saison--chambres" data-saison="chambres">
            <figure class="saison__plate plate__ph plate--ph-chambres">
                <img src="/assets/img/v8/villa-plaisance-chambre-verte-01.webp"
                     alt="Chambre Verte de Villa Plaisance, mur sombre profond, lit double, lampes murales chaudes."
                     loading="lazy" decoding="async">
            </figure>
            <div class="saison__corps">
                <p class="saison__dates">de septembre à juin</p>
                <h3 class="saison__titre">Chambres d'hôtes</h3>
                <p class="saison__texte">
                    Deux chambres climatisées, petit-déjeuner maison avec des
                    produits locaux, piscine partagée avec les hôtes. Un accueil
                    personnel, des conseils sur mesure.
                </p>
                <dl class="saison__faits">
                    <div><dt>Chambres</dt><dd>2 (Verte &amp; Bleue)</dd></div>
                    <div><dt>Petit-déjeuner</dt><dd>Inclus</dd></div>
                    <div><dt>Piscine</dt><dd>Partagée</dd></div>
                </dl>
                <a class="saison__lien" href="<?= LangService::url('chambres-d-hotes') ?>">
                    Découvrir les chambres
                    <svg viewBox="0 0 24 24" aria-hidden="true" width="22" height="22"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
                </a>
            </div>
        </article>

        <article class="saison saison--villa" data-saison="villa">
            <figure class="saison__plate plate__ph plate--ph-villa">
                <img src="/assets/img/v8/villa-plaisance-salon-salle-a-manger-04.webp"
                     alt="Salon de Villa Plaisance, arche en pierre brute, canapé en cuir cognac, salle à manger en arrière-plan."
                     loading="lazy" decoding="async">
            </figure>
            <div class="saison__corps">
                <p class="saison__dates">juillet &amp; août</p>
                <h3 class="saison__titre">La Villa en exclusivité</h3>
                <p class="saison__texte">
                    Quatre chambres, piscine privée 12×6m clôturée, cuisine
                    entièrement équipée, jardin provençal. Jusqu'à dix
                    personnes, en totale autonomie.
                </p>
                <dl class="saison__faits">
                    <div><dt>Chambres</dt><dd>4</dd></div>
                    <div><dt>Capacité</dt><dd>jusqu'à 10</dd></div>
                    <div><dt>Piscine</dt><dd>Privée 12×6 m</dd></div>
                </dl>
                <a class="saison__lien" href="<?= LangService::url('location-villa-provence') ?>">
                    Découvrir la villa entière
                    <svg viewBox="0 0 24 24" aria-hidden="true" width="22" height="22"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
                </a>
            </div>
        </article>

    </div>
</section>

<!-- Acte 3 : interlude photographique -->
<section class="interlude" aria-label="Interlude photographique">
    <figure class="interlude__plate plate__ph plate--ph-interlude">
        <img src="/assets/img/v8/villa-plaisance-jardin-exterieur-01.webp"
             alt="Coquelicots rouges au premier plan, chaises de jardin en métal blanc en arrière-plan flou, ombre des arbres."
             loading="lazy" decoding="async">
    </figure>
    <p class="interlude__legende"><em>Quelque part dans le jardin, fin août.</em></p>
</section>

<!-- Acte 4 : Triangle d'Or → mappemonde -->
<section class="lieu" aria-labelledby="lieu-title">
    <h2 class="acte__num" id="lieu-title"><span>III.</span> Au cœur du Triangle d'Or</h2>

    <div class="lieu__distances">
        <div class="distance">
            <p class="distance__valeur">8<span class="distance__unite">min</span></p>
            <p class="distance__libelle">Châteauneuf-du-Pape</p>
        </div>
        <div class="distance">
            <p class="distance__valeur">15<span class="distance__unite">min</span></p>
            <p class="distance__libelle">Avignon</p>
        </div>
        <div class="distance">
            <p class="distance__valeur">18<span class="distance__unite">min</span></p>
            <p class="distance__libelle">Orange</p>
        </div>
    </div>

    <p class="lieu__pont">
        Et au-delà des trois villes, le monde entier vient passer la porte.
    </p>

    <div class="mappemonde__viewport" data-mappemonde>
        <div class="mappemonde__map" aria-hidden="true"></div>
        <svg class="mappemonde__pins" viewBox="0 0 1000 500" preserveAspectRatio="xMidYMid meet" aria-hidden="true"></svg>
    </div>
    <div class="mappemonde__legende">
        <p class="mappemonde__titre">
            <span lang="fr">Nos hôtes viennent de…</span>
            <span lang="en">Our guests come from…</span>
            <span lang="es">Nuestros huéspedes vienen de…</span>
            <span lang="de">Unsere Gäste kommen aus…</span>
        </p>
        <p class="mappemonde__compte">plus de <strong>21</strong> destinations</p>
        <p class="mappemonde__villes" data-mappemonde-villes></p>
    </div>
</section>

<!-- Acte 5 : voix d'hôtes — TODO : brancher sur vp_reviews WHERE featured=1 -->
<section class="voix" aria-labelledby="voix-title">
    <h2 class="acte__num" id="voix-title"><span>IV.</span> Ce qu'on en dit</h2>

    <figure class="voix__bloc" lang="fr">
        <p class="voix__attribution">Marianne · Waterloo, Belgique · Airbnb · Villa entière</p>
        <blockquote class="voix__citation">
            Un endroit magnifique, calme et reposant. La piscine est superbe et le jardin enchanteur. Nous avons passé deux semaines inoubliables en famille.
        </blockquote>
    </figure>

    <figure class="voix__bloc" lang="de">
        <p class="voix__attribution">Charlotte · Allemagne · Airbnb · Villa entière</p>
        <blockquote class="voix__citation">
            Wunderbar! Die Villa ist perfekt für Familien. Der Pool, der Garten, alles war traumhaft.
        </blockquote>
    </figure>

    <figure class="voix__bloc" lang="en">
        <p class="voix__attribution">Rosemarie · Northampton, Royaume-Uni · Airbnb · Chambres d'hôtes</p>
        <blockquote class="voix__citation">
            A lovely stay. The hosts are warm and welcoming. The breakfast was delicious and the pool a wonderful bonus.
        </blockquote>
    </figure>

    <figure class="voix__bloc" lang="nl">
        <p class="voix__attribution">Jeroen · Pays-Bas · Booking · Chambres d'hôtes · 10/10</p>
        <blockquote class="voix__citation">
            Perfect verblijf. Gastvrije ontvangst, heerlijk ontbijt, prachtige tuin en zwembad.
        </blockquote>
    </figure>
</section>

<!-- Acte 6 : journal — TODO : brancher sur vp_articles ORDER BY published_at DESC LIMIT 3 -->
<section class="journal" aria-labelledby="journal-title">
    <div class="journal__entete">
        <h2 class="acte__num" id="journal-title"><span>V.</span> Le Journal</h2>
        <a class="journal__tous" href="<?= LangService::url('journal') ?>">Tous les articles
            <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        </a>
    </div>

    <article class="article article--featured">
        <p class="article__meta"><time datetime="2025-10-15">15 oct. 2025</time> · Voyager autrement</p>
        <h3 class="article__titre"><a href="<?= LangService::url('journal') ?>/le-tourisme-de-masse-est-une-arnaque">Le tourisme de masse est une arnaque</a></h3>
        <p class="article__teaser">
            Pourquoi le tourisme de masse persiste malgré ses travers,
            et comment choisir une autre voie en Provence.
        </p>
    </article>

    <div class="journal__rail">
        <article class="article">
            <p class="article__meta"><time datetime="2025-10-01">1 oct. 2025</time> · Voyager autrement</p>
            <h3 class="article__titre"><a href="<?= LangService::url('journal') ?>/louer-maison-plutot-hotel-voyage">Louer une maison plutôt qu'un hôtel&nbsp;: pourquoi ça change tout au voyage</a></h3>
            <p class="article__teaser">
                Comparatif entre séjour hôtel et location de maison en Provence.
                Ce que ça change vraiment.
            </p>
        </article>

        <article class="article">
            <p class="article__meta"><time datetime="2025-09-20">20 sept. 2025</time> · Hôtes &amp; hôteliers</p>
            <h3 class="article__titre"><a href="<?= LangService::url('journal') ?>/vie-proprietaire-chambre-hotes">Ce que personne ne dit sur la vie d'un propriétaire de chambre d'hôtes</a></h3>
            <p class="article__teaser">
                Les coulisses du métier d'hôte en Provence.
                Entre passion et réalité quotidienne.
            </p>
        </article>
    </div>
</section>

<!-- Acte 7 : FAQ — TODO : brancher sur vp_faq WHERE page_slug='accueil' -->
<section class="questions" aria-labelledby="questions-title">
    <h2 class="acte__num" id="questions-title"><span>VI.</span> Quelques questions</h2>

    <dl class="qa">
        <div class="qa__couple">
            <dt>Où se situe Villa Plaisance&nbsp;?</dt>
            <dd>
                Villa Plaisance se trouve à Bédarrides, dans le Vaucluse (84370),
                au cœur du Triangle d'Or provençal, à 8 minutes de
                Châteauneuf-du-Pape, 15 minutes d'Avignon et 18 minutes d'Orange.
            </dd>
        </div>
        <div class="qa__couple">
            <dt>Quelle est la différence entre chambres d'hôtes et villa entière&nbsp;?</dt>
            <dd>
                De septembre à juin, nous accueillons en chambres d'hôtes
                (2 chambres, petit-déjeuner inclus, piscine partagée).
                En juillet et août, la villa entière se loue en exclusivité
                (4 chambres, piscine privée, cuisine équipée, jusqu'à 10 personnes).
            </dd>
        </div>
        <div class="qa__couple">
            <dt>Y a-t-il une piscine&nbsp;?</dt>
            <dd>
                Oui. La piscine mesure 12 mètres sur 6, elle est clôturée et
                sécurisée. En chambres d'hôtes, elle est partagée avec les
                autres hôtes. En location villa, elle est entièrement privatisée.
            </dd>
        </div>
    </dl>
</section>

<!-- Acte 8 : contact -->
<section class="contact" id="contact" aria-labelledby="contact-title">
    <h2 class="acte__num" id="contact-title"><span>VII.</span> Écrire</h2>
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
