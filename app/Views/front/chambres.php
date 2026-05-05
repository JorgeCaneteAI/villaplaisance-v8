<?php
/**
 * Chambres d'hôtes V9 — esprit weeks-off.com (Sprint 2, 2026-05-05).
 *
 * Variables disponibles depuis ChambresController :
 *   $seo, $jsonLd, $lang
 *   $faqs              array<{question,answer}>
 *   $featuredReviews   array<{author,origin,content,platform,offer,rating}>
 *
 * Slug conservé : /chambres-d-hotes (SEO).
 * Titre affiché V9 : "L'offre sept – juin · Chambres d'hôtes".
 */
?>

<!-- Hero -->
<section class="hero" aria-label="Petit-déjeuner Villa Plaisance">
    <div class="hero__photo">
        <img src="/uploads/villa-plaisance-petit-dejeuner-fruits-01.webp"
             alt="Assiette de fruits frais : pomme, fraises, melon, sur un set coloré, couverts argentés."
             fetchpriority="high" decoding="async">
    </div>
    <div class="hero__caption">
        <span class="lbl">Petit-déjeuner Villa Plaisance, table dressée en terrasse</span>
        <span class="meta">de septembre à juin</span>
    </div>
</section>

<!-- Identité -->
<section class="identite identite--compact">
    <div class="eyebrow">L'offre sept &ndash; juin</div>
    <h1>Chambres d'hôtes</h1>
    <p class="baseline">Deux chambres, de septembre à juin.</p>
</section>

<!-- Acte I — Lead -->
<section class="lead-section">
    <div class="lead-section__inner">
        <div class="lead-section__num">I.</div>
        <div>
            <h2>Séjourner en chambres d'hôtes</h2>
            <p>
                De septembre à juin, Villa Plaisance ouvre deux chambres aux voyageurs.
                Le petit-déjeuner est préparé chaque matin avec des produits locaux.
                La piscine est partagée avec les hôtes.
            </p>
            <p class="lead-section__chute">L'accueil est personnel, les conseils aussi.</p>
        </div>
    </div>
</section>

<!-- Strip 3 colonnes -->
<section class="strip" aria-label="En un coup d'œil">
    <div>
        <span class="k">— Chambres</span>
        <p class="v">2 (Verte &amp; Bleue)</p>
        <p class="det">Climatisation réversible &middot; Wifi &middot; salles d'eau privatives</p>
    </div>
    <div>
        <span class="k">— Petit-déjeuner</span>
        <p class="v">Maison, inclus</p>
        <p class="det">Confitures maison, fruits frais, pain de boulanger, fromages et charcuteries du terroir</p>
    </div>
    <div>
        <span class="k">— Réserver</span>
        <p class="v">Écrire à Jorge</p>
        <p class="det"><a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a><br>FR &middot; EN &middot; ES &middot; DE</p>
    </div>
</section>

<!-- Acte II — Les deux chambres en chapitres -->
<section class="manifesto" style="border-top:none">
    <div class="manifesto__inner">
        <div class="manifesto__num">II.</div>
        <h2>Les deux chambres</h2>
    </div>
</section>

<article class="chapter" id="verte">
    <div class="chapter__inner">
        <div class="chapter__photo">
            <img src="/uploads/villa-plaisance-chambre-verte-03.webp"
                 alt="Chambre Verte : grand lit 160 contre un mur vert profond, lampes murales chaudes, couvre-lit jaune."
                 loading="lazy" decoding="async">
        </div>
        <div class="chapter__text">
            <span class="eyebrow">Chambre 1 sur 2</span>
            <h2>La Chambre Verte</h2>
            <p>
                Chambre lumineuse avec un grand lit 160×200, donnant sur le jardin
                et les oliviers. Espace cocooning, sobriété et calme.
            </p>
            <dl class="chapter__facts">
                <div><dt>Lit</dt><dd>160 × 200</dd></div>
                <div><dt>Vue</dt><dd>Jardin</dd></div>
                <div><dt>Position</dt><dd>Rez-de-chaussée</dd></div>
            </dl>
        </div>
    </div>
</article>

<article class="chapter chapter--reverse" id="bleue">
    <div class="chapter__inner">
        <div class="chapter__photo">
            <img src="/uploads/villa-plaisance-chambre-bleue-01.webp"
                 alt="Chambre Bleue : deux lits jumelables, mur gris-bleu, fauteuil rouge, voilages, clic-clac au fond."
                 loading="lazy" decoding="async">
        </div>
        <div class="chapter__text">
            <span class="eyebrow">Chambre 2 sur 2</span>
            <h2>La Chambre Bleue</h2>
            <p>
                Deux lits 90×200 jumelables en grand lit 180. Un clic-clac pour
                une troisième personne. Une bibliothèque de 300 livres. La chambre
                des lecteurs et des familles.
            </p>
            <dl class="chapter__facts">
                <div><dt>Lits</dt><dd>2 × 90 jumelables</dd></div>
                <div><dt>Couchage +</dt><dd>Clic-clac (1 pers.)</dd></div>
                <div><dt>Bibliothèque</dt><dd>300 livres</dd></div>
            </dl>
        </div>
    </div>
</article>

<!-- Acte III — Le petit-déjeuner -->
<article class="chapter">
    <div class="chapter__inner">
        <div class="chapter__photo">
            <img src="/uploads/villa-plaisance-petit-dejeuner-confitures-01.webp"
                 alt="Brioche dorée au sucre perlé en gros plan, table dressée du petit-déjeuner en arrière-plan flou."
                 loading="lazy" decoding="async">
        </div>
        <div class="chapter__text">
            <span class="eyebrow">III. Le petit-déjeuner</span>
            <h2>Chaque matin, les saisons</h2>
            <p>
                Chaque matin, le petit-déjeuner est préparé avec des produits
                locaux et de saison. Confitures maison, fruits frais, pain de
                boulanger, fromages et charcuteries du terroir.
            </p>
            <p class="pull">Servi en terrasse quand le temps le permet.</p>
        </div>
    </div>
</article>

<!-- Interlude photo -->
<section class="interlude" aria-label="Vue jardin Villa Plaisance">
    <div class="interlude__photo">
        <img src="/uploads/villa-plaisance-jardin-exterieur-08.webp"
             alt="Vue du jardin Villa Plaisance, oliviers et chaises de jardin sous lumière douce."
             loading="lazy" decoding="async">
    </div>
    <p class="interlude__caption"><em>Le jardin sous les oliviers, en fin d'après-midi.</em></p>
</section>

<!-- Acte IV — Voix d'hôtes (B&B) -->
<?php if (!empty($featuredReviews)): ?>
<section class="voix">
    <div class="voix__head">
        <div class="eyebrow"><span class="num">IV.</span>Hôtes des chambres</div>
        <h2>Ce qu'on en dit</h2>
    </div>
    <div class="voix__grid <?= count($featuredReviews) === 4 ? 'voix__grid--4' : '' ?>">
        <?php foreach ($featuredReviews as $r): ?>
        <figure class="voix__bloc">
            <blockquote>« <?= htmlspecialchars((string)$r['content']) ?> »</blockquote>
            <figcaption>
                <?= htmlspecialchars((string)$r['author']) ?>
                <?php if (!empty($r['origin'])): ?> &middot; <?= htmlspecialchars((string)$r['origin']) ?><?php endif; ?>
                &middot; Chambres d'hôtes
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
                <dt>Le petit-déjeuner est-il inclus ?</dt>
                <dd>Oui, le petit-déjeuner maison est inclus dans le tarif chambres d'hôtes. Il est servi de 7h30 à 10h en terrasse selon la saison.</dd>
            </div>
            <div class="faq__item">
                <dt>Les chambres sont-elles climatisées ?</dt>
                <dd>Oui, les deux chambres (Verte et Bleue) sont équipées de climatisation réversible et du wifi gratuit.</dd>
            </div>
            <div class="faq__item">
                <dt>À quelle période les chambres d'hôtes sont-elles disponibles ?</dt>
                <dd>Les chambres d'hôtes sont ouvertes de septembre à juin. En juillet et août, la villa se loue en exclusivité.</dd>
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
