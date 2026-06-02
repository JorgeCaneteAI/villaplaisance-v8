<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Mentions légales.
 * Portée du proto Claude design. Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  .legal-page { padding-top: clamp(72px, 9vw, 120px); }
  .legal-page .page-hero { padding-top: 0; }
  .legal-body {
    max-width: 720px;
    font-family: var(--font-sans); font-size: 15.5px; line-height: 1.7; color: var(--ink-900);
  }
  .legal-body h2 {
    font-family: var(--font-display); font-weight: 500;
    font-size: clamp(22px, 2vw, 28px); line-height: 1.2; letter-spacing: -0.005em;
    color: var(--ink-900);
    margin: 48px 0 16px;
    padding-top: 28px; border-top: var(--hairline);
  }
  .legal-body h2:first-of-type { padding-top: 0; border-top: 0; margin-top: 0; }
  .legal-body p { margin: 0 0 16px; color: var(--stone-700, var(--ink-900)); }
  .legal-body strong { color: var(--ink-900); font-weight: 600; }
  .legal-body a { color: var(--sage-700); text-decoration: underline; text-underline-offset: 3px; }
  .legal-body a:hover { color: var(--ink-900); }
  .legal-body em { color: var(--stone-500); font-style: italic; }
  .legal-crumbs {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em;
    color: var(--stone-500); text-transform: uppercase;
    margin: 0 0 24px;
  }
  .legal-crumbs a { color: var(--stone-500); }
  .legal-crumbs a:hover { color: var(--ink-900); }
  .legal-crumbs .sep { margin: 0 8px; opacity: 0.5; }
</style>

<section class="section legal-page">
  <div class="container-wide">

    <nav class="legal-crumbs" aria-label="Fil d'Ariane">
      <a href="<?= LangService::url('/') ?>" data-en="Home">Accueil</a>
      <span class="sep" aria-hidden="true">/</span>
      <span aria-current="page" data-en="Legal notice">Mentions légales</span>
    </nav>

    <?php
    $_v8HeroLegal = null;
    foreach (BlockService::getSections('mentions-legales', $lang) as $_s) {
        if ((int)$_s['position'] === 1 && $_s['block_type'] === 'hero') {
            $_v8HeroLegal = BlockService::renderBlock($_s);
            break;
        }
    }
    ?>
    <?php if ($_v8HeroLegal): ?>
    <?= $_v8HeroLegal ?>
    <?php else: ?>
    <div class="page-hero">
      <div class="page-hero-inner">
        <div>
          <div class="page-hero-issue">
            <span data-en="Legal · Notice">Légal · Mentions</span>
            <span data-en="Last update 2026-05">Mise à jour 2026-05</span>
          </div>
          <h1 data-en="Legal <em>notice</em>.">Mentions <em>légales</em>.</h1>
        </div>
        <p class="lede" data-en="The legal owner of this site, hosting details, intellectual property, photo credits and liability — all you need to know in a single page.">L'éditeur du site, l'hébergement, la propriété intellectuelle, les crédits photo et la responsabilité — tout ce qu'il faut savoir réuni sur une page.</p>
      </div>
    </div>
    <?php endif; ?>

    <div class="legal-body">
      <h2 data-en="1. Site publisher">1. Éditeur du site</h2>
      <p>
        <strong>Villa Plaisance</strong><br>
        <span data-en="Owner: Jorge Cañete">Propriétaire&nbsp;: Jorge Cañete</span><br>
        <span data-en="Address: Bédarrides, 84370 Vaucluse, France">Adresse&nbsp;: Bédarrides, 84370 Vaucluse, France</span><br>
        <span data-en="Email">Email</span>&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a><br>
        <span data-en="Site">Site</span>&nbsp;: https://villaplaisance.fr<br>
        <span data-en="Activity: Bed &amp; breakfast and seasonal villa rental.">Activité&nbsp;: chambres d'hôtes et location saisonnière de villa.</span><br>
        SIRET&nbsp;: <em data-en="to be completed">à compléter</em>
      </p>

      <h2 data-en="2. Hosting">2. Hébergement</h2>
      <p>
        <strong>o2switch</strong><br>
        222-224 Boulevard Gustave Flaubert<br>
        63000 Clermont-Ferrand, France<br>
        <span data-en="Phone">Tél</span>&nbsp;: 04 44 44 60 40<br>
        <span data-en="Site">Site</span>&nbsp;: <a href="https://www.o2switch.fr" rel="noopener">www.o2switch.fr</a>
      </p>

      <h2 data-en="3. Intellectual property">3. Propriété intellectuelle</h2>
      <p data-en="All content on this site (texts, photographs, illustrations, logo, videos, structure) is protected by the French Intellectual Property Code. Any reproduction, representation or distribution, in whole or in part, is prohibited without the prior written consent of Villa Plaisance.">
        L'ensemble du contenu de ce site (textes, photographies, illustrations, logo, vidéos, structure)
        est protégé par le Code de la propriété intellectuelle. Toute reproduction, représentation
        ou diffusion, totale ou partielle, est interdite sans autorisation écrite préalable de Villa Plaisance.
      </p>

      <h2 data-en="4. Photo credits">4. Crédits photographiques</h2>
      <p data-en="Photographs: Jorge Cañete / Villa Plaisance, unless otherwise stated.">
        Photographies&nbsp;: Jorge Cañete / Villa Plaisance, sauf mention contraire.
      </p>

      <h2 data-en="5. Liability">5. Responsabilité</h2>
      <p data-en="Information published on this site is given for guidance only and may change. Villa Plaisance strives to keep information accurate and up to date, but does not guarantee the accuracy, completeness or timeliness of the information published. Use of the information available on this site is at the user's sole responsibility.">
        Les informations publiées sur ce site sont fournies à titre indicatif et sont susceptibles
        d'évoluer. Villa Plaisance s'efforce de maintenir des informations exactes et à jour, mais
        ne saurait garantir l'exactitude, la complétude ou l'actualité des informations diffusées.
        L'utilisation des informations disponibles sur ce site se fait sous la seule responsabilité
        de l'utilisateur.
      </p>
    </div>

  </div>
</section>
