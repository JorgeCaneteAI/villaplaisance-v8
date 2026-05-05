<?php
/**
 * Villa entière V9 — esprit weeks-off.com (Sprint 2, 2026-05-05).
 *
 * Variables disponibles depuis VillaController :
 *   $seo, $jsonLd, $lang
 *   $faqs              array<{question,answer}>
 *   $featuredReviews   array<{author,origin,content,platform,offer,rating}>
 *
 * Slug conservé : /location-villa-provence (SEO).
 * Titre affiché V9 : "L'offre juillet – août · La Villa en exclusivité".
 */
?>

<!-- Hero -->
<section class="hero" aria-label="Vue piscine privée Villa Plaisance">
    <div class="hero__photo">
        <img src="/uploads/villa-plaisance-piscine-privee-09.webp"
             alt="Vue surélevée du jardin de Villa Plaisance : palmier, piscine et transats au centre, cyprès à l'horizon."
             fetchpriority="high" decoding="async">
    </div>
    <div class="hero__caption">
        <span class="lbl">Villa Plaisance, vue jardin et piscine privée</span>
        <span class="meta">juillet &amp; août</span>
    </div>
</section>

<!-- Identité -->
<section class="identite identite--compact">
    <div class="eyebrow">L'offre juillet &ndash; août</div>
    <h1>La Villa en exclusivité</h1>
    <p class="baseline">Quatre chambres, juillet et août.</p>
</section>

<!-- Acte I — Lead -->
<section class="lead-section">
    <div class="lead-section__inner">
        <div class="lead-section__num">I.</div>
        <div>
            <h2>Toute la maison pour vous</h2>
            <p>
                En juillet et août, Villa Plaisance se loue en exclusivité.
                Quatre chambres, une piscine privée clôturée de 12 mètres sur 6,
                une cuisine entièrement équipée, un jardin provençal. Jusqu'à dix
                personnes.
            </p>
            <p class="lead-section__chute">La gestion est autonome, les clés sont à vous.</p>
        </div>
    </div>
</section>

<!-- Strip 3 colonnes -->
<section class="strip" aria-label="En un coup d'œil">
    <div>
        <span class="k">— Capacité</span>
        <p class="v">Jusqu'à 10 personnes</p>
        <p class="det">4 chambres &middot; couchages détaillés ci-dessous</p>
    </div>
    <div>
        <span class="k">— Piscine</span>
        <p class="v">Privée, 12 × 6 m</p>
        <p class="det">Clôturée et sécurisée &middot; transats, parasols, douche extérieure</p>
    </div>
    <div>
        <span class="k">— Réserver</span>
        <p class="v">Écrire à Jorge</p>
        <p class="det"><a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a><br>Semaine du samedi au samedi</p>
    </div>
</section>

<!-- Acte II — Les 4 chambres en grille 2×2 -->
<section class="cellules">
    <div class="cellules__head">
        <span class="cellules__num">II.</span>
        <h2>Les quatre chambres</h2>
    </div>
    <div class="cellules__grid">

        <article class="cellule" id="verte">
            <div class="cellule__photo">
                <img src="/uploads/villa-plaisance-chambre-verte-04.webp"
                     alt="Chambre Verte : grand lit, mur vert profond, lumière naturelle latérale."
                     loading="lazy" decoding="async">
            </div>
            <p class="cellule__numero"><strong>1</strong> / 4</p>
            <h3 class="cellule__titre">La Verte</h3>
            <p class="cellule__sous">Grand lit &middot; vue jardin &middot; rez-de-chaussée</p>
            <p class="cellule__texte">
                Lit 160×200, vue sur le jardin et les oliviers. Climatisation réversible, TV.
            </p>
        </article>

        <article class="cellule" id="bleue">
            <div class="cellule__photo">
                <img src="/uploads/villa-plaisance-chambre-bleue-04.webp"
                     alt="Chambre Bleue : deux lits jumelables, mur gris-bleu, voilages aux fenêtres."
                     loading="lazy" decoding="async">
            </div>
            <p class="cellule__numero"><strong>2</strong> / 4</p>
            <h3 class="cellule__titre">La Bleue</h3>
            <p class="cellule__sous">Bibliothèque 300 livres &middot; 2 à 3 personnes</p>
            <p class="cellule__texte">
                Deux lits 90×200 jumelables, clic-clac, bibliothèque de 300 livres. Climatisation réversible.
            </p>
        </article>

        <article class="cellule" id="arche">
            <div class="cellule__photo">
                <img src="/uploads/villa-plaisance-chambre-arche-01.webp"
                     alt="Chambre Arche : grande arche peinte en bleu nuit derrière le lit, bibliothèques sol-plafond."
                     loading="lazy" decoding="async">
            </div>
            <p class="cellule__numero"><strong>3</strong> / 4</p>
            <h3 class="cellule__titre">L'Arche</h3>
            <p class="cellule__sous">Arche bleu nuit &middot; accès direct jardin</p>
            <p class="cellule__texte">
                Lit 140×180 sous une grande arche peinte en bleu nuit. Bibliothèques sol-plafond. Au rez-de-chaussée.
            </p>
        </article>

        <article class="cellule" id="annees-70">
            <div class="cellule__photo">
                <img src="/uploads/villa-plaisance-chambre-annees-70-05.webp"
                     alt="Chambre 70 : mobilier vintage années 70, porte-fenêtre ouverte sur palmier et jardin."
                     loading="lazy" decoding="async">
            </div>
            <p class="cellule__numero"><strong>4</strong> / 4</p>
            <h3 class="cellule__titre">La 70</h3>
            <p class="cellule__sous">Mobilier vintage &middot; accès direct jardin</p>
            <p class="cellule__texte">
                Grand lit double, mobilier chiné des années 70. Porte-fenêtre ouvrant sur le jardin. La plus atypique.
            </p>
        </article>

    </div>
</section>

<!-- Acte III — Piscine privée -->
<article class="chapter chapter--reverse">
    <div class="chapter__inner">
        <div class="chapter__photo">
            <img src="/uploads/villa-plaisance-piscine-privee-04.webp"
                 alt="Piscine privée Villa Plaisance : eau cristalline, transats avec parasol, cyprès en arrière-plan."
                 loading="lazy" decoding="async">
        </div>
        <div class="chapter__text">
            <span class="eyebrow">III. La piscine privée</span>
            <h2>Réservée à votre groupe</h2>
            <p>
                Piscine privée de 12 mètres sur 6, clôturée et sécurisée. Ouverte
                de mi-mai à fin septembre. Transats, parasols, douche extérieure.
            </p>
            <p class="pull">Réservée exclusivement aux locataires de la villa.</p>
        </div>
    </div>
</article>

<!-- Interlude photo -->
<section class="interlude" aria-label="Vue extérieure Villa Plaisance">
    <div class="interlude__photo">
        <img src="/uploads/villa-plaisance-jardin-exterieur-05.webp"
             alt="Vue du jardin Villa Plaisance, espace de vie en extérieur sous lumière de soir."
             loading="lazy" decoding="async">
    </div>
    <p class="interlude__caption"><em>Le jardin, à l'heure du dîner.</em></p>
</section>

<!-- Acte IV — Voix d'hôtes (Villa) -->
<?php if (!empty($featuredReviews)): ?>
<section class="voix">
    <div class="voix__head">
        <div class="eyebrow"><span class="num">IV.</span>Hôtes de la villa</div>
        <h2>Ce qu'on en dit</h2>
    </div>
    <div class="voix__grid <?= count($featuredReviews) === 4 ? 'voix__grid--4' : '' ?>">
        <?php foreach ($featuredReviews as $r): ?>
        <figure class="voix__bloc">
            <blockquote>« <?= htmlspecialchars((string)$r['content']) ?> »</blockquote>
            <figcaption>
                <?= htmlspecialchars((string)$r['author']) ?>
                <?php if (!empty($r['origin'])): ?> &middot; <?= htmlspecialchars((string)$r['origin']) ?><?php endif; ?>
                &middot; Villa entière
            </figcaption>
        </figure>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Acte V — FAQ -->
<section class="faq">
    <div class="faq__head">
        <span class="faq__num">V.</span>
        <h2>Réponses</h2>
    </div>
    <dl class="faq__list">
        <?php if (!empty($faqs)): ?>
            <?php foreach ($faqs as $q): ?>
            <div class="faq__item">
                <dt><?= htmlspecialchars((string)$q['question']) ?></dt>
                <dd><?= nl2br(htmlspecialchars((string)$q['answer'])) ?></dd>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="faq__item">
                <dt>Combien de personnes la villa peut-elle accueillir ?</dt>
                <dd>La villa entière accueille jusqu'à 10 personnes réparties dans 4 chambres : Verte, Bleue, Arche et 70.</dd>
            </div>
            <div class="faq__item">
                <dt>La piscine est-elle privée en location villa ?</dt>
                <dd>Oui, en juillet et août, la piscine de 12 mètres sur 6 est entièrement privée et réservée aux locataires. Elle est clôturée et sécurisée.</dd>
            </div>
            <div class="faq__item">
                <dt>Quelle est la durée minimum de location ?</dt>
                <dd>En haute saison (juillet-août), la durée minimum est d'une semaine, du samedi au samedi.</dd>
            </div>
        <?php endif; ?>
    </dl>
</section>

<!-- Acte VI — Écrire -->
<section class="ecrire" id="contact">
    <div class="ecrire__num">VI.</div>
    <h2>Écrire</h2>
    <p>Envie de séjourner chez nous ?</p>
    <p class="sub">Contactez-nous pour organiser votre séjour en Provence.</p>
    <div class="actions">
        <a href="<?= LangService::url('contact') ?>" class="pill pill--solid pill--big">
            Nous écrire
            <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        </a>
        <p class="alt">Ou directement&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a></p>
    </div>
</section>
