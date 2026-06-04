<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Espaces extérieurs (piscine, terrasses, jardin).
 * Portée depuis le proto Claude design (exterieurs.html).
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  /* Hero */
  .ex-hero {
    position: relative;
    min-height: clamp(560px, 86vh, 840px);
    overflow: hidden;
    background: var(--linen-100);
  }
  .ex-hero-image { position: absolute; inset: 0; background-size: cover; background-position: center; }
  .ex-hero-image::after {
    content: ""; position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(31,28,22,0.08) 0%, rgba(31,28,22,0.08) 50%, rgba(31,28,22,0.6) 100%);
  }
  .ex-hero-content {
    position: relative; z-index: 2;
    padding: clamp(40px, 8vw, 96px) var(--gutter);
    max-width: var(--container-wide); margin: 0 auto; width: 100%;
    display: grid; grid-template-rows: 1fr auto; gap: 24px;
    min-height: clamp(560px, 86vh, 840px);
    color: var(--linen-50);
  }
  .ex-hero h1 {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(56px, 9vw, 144px); line-height: 0.94; letter-spacing: -0.025em;
    color: var(--linen-50);
  }
  .ex-hero h1 em { font-style: italic; color: var(--sage-200); }
  .ex-hero-overline {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em;
    color: rgba(251,247,238,0.78); text-transform: uppercase;
  }
  .ex-hero-bottom { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr); gap: clamp(24px, 5vw, 80px); align-items: end; }
  .ex-hero-tag {
    font-family: var(--font-display); font-size: clamp(18px, 1.6vw, 22px);
    color: rgba(251,247,238,0.92); line-height: 1.45; max-width: 50ch; margin: 0 0 20px;
  }
  @media (max-width: 960px) { .ex-hero-bottom { grid-template-columns: 1fr; } }

  /* Space sections, image + text */
  .ex-space {
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
    gap: clamp(32px, 5vw, 80px);
    align-items: center;
  }
  .ex-space.alt { grid-template-columns: minmax(0, 1fr) minmax(0, 1.1fr); }
  .ex-space.alt .ex-space-text { order: 2; }
  /* <img class="ex-space-img"> : aspect 3/2 iso source (Pattern A).
     Le background-size n'a plus d'effet depuis la migration <div> → <img>. */
  .ex-space img.ex-space-img,
  .ex-space-img {
    aspect-ratio: 3/2;
    width: 100%; height: auto;
    object-fit: cover; object-position: center;
    display: block;
    background-size: cover; background-position: center; /* legacy fallback */
  }
  .ex-space-num {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em;
    color: var(--stone-500); text-transform: uppercase;
    display: flex; justify-content: space-between; align-items: baseline;
    border-bottom: var(--hairline); padding-bottom: 14px; margin-bottom: 24px;
  }
  .ex-space-pills { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 24px; }

  /* Anchor list */
  .anchor-list {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 0;
    border-top: var(--hairline); border-bottom: var(--hairline);
  }
  .anchor-list a {
    padding: 22px 24px;
    border-right: var(--hairline);
    display: grid; grid-template-rows: auto auto; gap: 6px;
    transition: background .2s, color .2s;
  }
  .anchor-list a:last-child { border-right: 0; }
  .anchor-list a:hover { background: var(--ink-900); color: var(--linen-50); }
  .anchor-list a:hover .num, .anchor-list a:hover .ttl em { color: var(--sage-200); }
  .anchor-list .num {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    color: var(--stone-500); text-transform: uppercase;
  }
  .anchor-list .ttl {
    font-family: var(--font-display); font-size: clamp(22px, 2vw, 28px);
    color: inherit; letter-spacing: -0.005em;
  }
  .anchor-list .ttl em { font-style: italic; color: var(--sage-700); transition: color .2s; }

  /* Equipment grid */
  .equip-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 0 clamp(32px, 4vw, 80px);
    border-top: var(--hairline-strong);
  }
  .equip-grid .equip {
    padding: 18px 0;
    border-bottom: var(--hairline);
    display: grid; grid-template-columns: 32px 1fr;
    gap: 16px; align-items: baseline;
  }
  .equip-grid .equip .n {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.1em;
    color: var(--stone-500); padding-top: 4px;
  }
  .equip-grid .equip .l { font-family: var(--font-sans); font-size: 15.5px; color: var(--ink-700); }

  /* Photo strip */
  .photo-strip {
    display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 12px;
  }
  .photo-strip > img,
  .photo-strip > div {
    aspect-ratio: 3/2;
    width: 100%; height: auto;
    object-fit: cover; object-position: center;
    display: block;
    background-size: cover; background-position: center; /* legacy fallback */
  }

  @media (max-width: 720px) {
    .ex-space, .ex-space.alt { grid-template-columns: 1fr; }
    .ex-space.alt .ex-space-text { order: 0; }
    .anchor-list { grid-template-columns: 1fr; }
    .anchor-list a { border-right: 0; border-bottom: var(--hairline); }
    .anchor-list a:last-child { border-bottom: 0; }
    .equip-grid { grid-template-columns: 1fr; }
    .photo-strip { grid-template-columns: 1fr; }
  }
</style>


<!-- ============ 1 · HERO (BDD vp_sections or fallback HTML) ============ -->
<?php
$_v8HeroExt = null;
foreach (BlockService::getSections('espaces-exterieurs', $lang) as $_s) {
    if ((int)$_s['position'] === 1 && $_s['block_type'] === 'hero') {
        $_v8HeroExt = BlockService::renderBlock($_s);
        break;
    }
}
?>
<?php if ($_v8HeroExt): ?>
<?= $_v8HeroExt ?>
<?php else: ?>
<section class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="page-hero-issue">
        <span data-en="03 · Outdoors · 1 500 m² of garden">03 · Les extérieurs · 1 500 m² de jardin</span>
        <span data-en="Provençal garden">Jardin provençal</span>
      </div>
      <h1>Dehors, ici,<br/>c'est encore <em>chez vous</em>.</h1>
    </div>
    <div>
      <p class="lede" data-en="The garden of Villa Plaisance is a natural extension of the house, 1,500 m² of green, century-old olive trees, lavender, old roses and aromatic herbs.">Le jardin de Villa Plaisance est un prolongement naturel de la maison, 1 500 m² de verdure, oliviers centenaires, lavandes, rosiers anciens, herbes aromatiques.</p>
      <div class="page-hero-ctas">
        <a class="btn" href="<?= LangService::url('contact') ?>"><span data-en="Plan a stay">Préparer un séjour</span> →</a>
        <a class="btn btn-ghost" href="#piscine"><span data-en="Tour the outdoors">Visiter les extérieurs</span></a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 2 · INTRO ============ -->
<section class="section">
  <div class="container-wide">
    <div class="two-col">
      <div>
        <div class="section-label">
          <span class="numeral">01 / <span data-en="Outside">Dehors</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;">Dehors, ici,<br/>c'est encore <em>chez vous</em>.</h2>
      </div>
      <div>
        <p class="lede" style="margin: 0 0 20px;" data-en="The garden of Villa Plaisance is a natural extension of the house. 1,500 m² of green, century-old olive trees, lavender, old roses and aromatic herbs (thyme, rosemary, basil, fresh mint).">Le jardin de Villa Plaisance est un prolongement naturel de la maison. 1 500 m² de verdure, oliviers centenaires, lavandes, rosiers anciens et herbes aromatiques (thym, romarin, basilic, menthe fraîche).</p>
        <p class="body-lg" style="margin: 0;" data-en="The 12 by 6 m pool, the shaded terraces, the vegetable patch in summer. A place where you spend more time than indoors, between the cicadas and the first rays of sun.">La piscine de 12 mètres sur 6, les terrasses ombragées, le potager en été. Un espace où l'on passe plus de temps qu'à l'intérieur, entre les cigales et les premiers rayons du soleil.</p>
      </div>
    </div>

    <!-- Quick anchors -->
    <div class="anchor-list" style="margin-top: clamp(48px, 5vw, 80px);">
      <a href="#piscine">
        <span class="num">01 · 12 × 6 m</span>
        <span class="ttl"><em data-en="The pool">La piscine</em></span>
      </a>
      <a href="#terrasses">
        <span class="num">02 · 40 m²</span>
        <span class="ttl"><em data-en="The terraces">Les terrasses</em></span>
      </a>
      <a href="#jardin">
        <span class="num">03 · 1 500 m²</span>
        <span class="ttl"><em data-en="The Provençal garden">Le jardin</em></span>
      </a>
    </div>
  </div>
</section>

<!-- ============ 3 · PISCINE ============ -->
<section class="section" id="piscine">
  <div class="container-wide">
    <div class="ex-space">
      <div class="ex-space-text">
        <div class="ex-space-num">
          <span>02 / <span data-en="Pool">Piscine</span></span>
          <span data-en="Fenced &amp; secured">Clôturée &amp; sécurisée</span>
        </div>
        <h2 class="h-xl" style="margin: 0 0 24px;">Piscine <em>12 × 6 m</em>,<br/>clôturée et sécurisée.</h2>
        <p class="body-lg" style="margin: 0 0 16px; max-width: 56ch;" data-en="The pool is 12 by 6 metres, fully fenced for children's safety. Sunbeds, parasols, a solar shower and a garden lounge are available.">Le bassin mesure 12 mètres sur 6, entièrement clôturé pour la sécurité des enfants. Transats, parasols, douche solaire et salon de jardin sont à disposition.</p>
        <p class="body" style="margin: 0; max-width: 56ch;" data-en="In B&amp;B season (Sept–June): shared pool, accessible from 9 am to 9 pm. As a whole-villa rental (July–Aug): exclusive private pool, 24/7. Heating available on request.">En chambres d'hôtes (sept–juin) : piscine partagée, accessible de 9h à 21h. En villa entière (juil–août) : piscine privée exclusive, 24h/24. Chauffage disponible sur demande.</p>

        <div class="ex-space-pills">
          <span class="pill">12 × 6 m</span>
          <span class="pill" data-en="Fenced">Clôturée</span>
          <span class="pill" data-en="Sunbeds &amp; parasols">Transats &amp; parasols</span>
          <span class="pill" data-en="Solar shower">Douche solaire</span>
          <span class="pill" data-en="Heating on request">Chauffage sur demande</span>
          <span class="pill solid" data-en="Shared (Sept–June) or private (July–Aug)">Partagée (sept–juin) · privée (juil–août)</span>
        </div>
      </div>
      <?= ImageService::imgFromBg('villa-plaisance-piscine-privee-01.webp', 'ex-space-img') ?>
    </div>
  </div>
</section>

<!-- ============ 4 · TERRASSES ============ -->
<section class="section surface-stone" style="background: var(--linen-100);" id="terrasses">
  <div class="container-wide">
    <div class="ex-space alt">
      <?= ImageService::imgFromBg('villa-plaisance-jardin-exterieur-01.webp', 'ex-space-img') ?>
      <div class="ex-space-text">
        <div class="ex-space-num">
          <span>03 / <span data-en="Terraces">Terrasses</span></span>
          <span data-en="Facing the vineyards">Face aux vignes</span>
        </div>
        <h2 class="h-xl" style="margin: 0 0 24px;">Terrasses<br/><em>ombragées</em><br/>face aux vignes.</h2>
        <p class="body-lg" style="margin: 0 0 16px; max-width: 56ch;" data-en="A 40 m² south-facing covered terrace, perfect for outdoor meals, aperitifs facing the vines and long evenings until nightfall. A 12-seat garden lounge, a reading veranda for quiet mornings.">Terrasse couverte de 40 m² orientée sud, parfaite pour les repas en plein air, les apéritifs face aux vignes et les soirées jusqu'à la tombée de la nuit. Salon de jardin 12 places, véranda de lecture pour les matinées calmes.</p>
        <p class="body" style="margin: 0; max-width: 56ch;" data-en="In B&amp;B season, breakfast is served here each morning.">Le petit-déjeuner y est servi chaque matin (en saison B&amp;B).</p>

        <div class="ex-space-pills">
          <span class="pill" data-en="Covered terrace 40 m²">Terrasse couverte 40 m²</span>
          <span class="pill" data-en="South-facing">Orientée sud</span>
          <span class="pill" data-en="12-seat garden lounge">Salon de jardin 12 places</span>
          <span class="pill" data-en="Reading veranda">Véranda de lecture</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ 5 · JARDIN ============ -->
<section class="section" id="jardin">
  <div class="container-wide">
    <div class="ex-space">
      <div class="ex-space-text">
        <div class="ex-space-num">
          <span>04 / <span data-en="Garden">Jardin</span></span>
          <span data-en="Provençal · 1 500 m²">Provençal · 1 500 m²</span>
        </div>
        <h2 class="h-xl" style="margin: 0 0 24px;">Jardin <em>provençal</em><br/>oliviers, lavandes,<br/>herbes aromatiques.</h2>
        <p class="body-lg" style="margin: 0 0 16px; max-width: 56ch;" data-en="A garden of century-old olive trees, lavender, old roses and aromatic herbs. A charcoal BBQ space with a 12-seat table, a pétanque court under the trees, a children's play area.">Un jardin arboré planté d'oliviers centenaires, de lavandes, de rosiers anciens et d'herbes aromatiques. Espace BBQ charbon avec table 12 couverts, terrain de pétanque sous les arbres, aire de jeux pour les enfants.</p>
        <p class="body" style="margin: 0; max-width: 56ch;" data-en="The Châteauneuf-du-Pape vineyards start at the end of the lane.">Les vignes de Châteauneuf-du-Pape commencent au bout du chemin.</p>

        <div class="ex-space-pills">
          <span class="pill" data-en="Century-old olives">Oliviers centenaires</span>
          <span class="pill" data-en="Lavender · old roses">Lavandes · rosiers anciens</span>
          <span class="pill" data-en="Aromatic herbs">Herbes aromatiques</span>
          <span class="pill" data-en="Charcoal BBQ · 12 seats">BBQ charbon · 12 couverts</span>
          <span class="pill" data-en="Pétanque court">Terrain de pétanque</span>
          <span class="pill" data-en="Children's play area">Aire de jeux enfants</span>
        </div>
      </div>
      <?= ImageService::imgFromBg('villa-plaisance-piscine-privee-08.webp', 'ex-space-img') ?>
    </div>

    <!-- Photo strip below garden -->
    <div class="photo-strip" style="margin-top: clamp(40px, 4vw, 56px);">
      <?= ImageService::imgFromBg('villa-plaisance-jardin-exterieur-05.webp', '') ?>
      <?= ImageService::imgFromBg('villa-plaisance-jardin-exterieur-12.webp', '') ?>
      <?= ImageService::imgFromBg('villa-plaisance-piscine-privee-04.webp', '') ?>
    </div>
  </div>
</section>

<!-- ============ 6 · ÉQUIPEMENTS EXTÉRIEURS ============ -->
<section class="section surface-sage" style="background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));">
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: clamp(32px, 5vw, 96px); align-items: end; margin-bottom: clamp(40px, 5vw, 64px);">
      <div>
        <div class="section-label">
          <span class="numeral">05 / <span data-en="Outdoor equipment">Équipements extérieurs</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 16ch;">Tout ce que <em>le dehors</em><br/>met à votre disposition.</h2>
      </div>
      <p class="body-lg" style="margin: 0; max-width: 44ch;" data-en="The full list, end to end, pool, terrace, garden, services and the little practicalities you'd want to find.">La liste complète, sans détours, piscine, terrasse, jardin, services et les petites attentions qu'on aime trouver.</p>
    </div>

    <div class="equip-grid">
      <div class="equip"><span class="n">01</span><span class="l" data-en="Fenced 12 × 6 m pool (shared or private depending on offer)">Piscine 12 × 6 m clôturée (partagée ou privée selon offre)</span></div>
      <div class="equip"><span class="n">02</span><span class="l" data-en="Pool heating available on request">Chauffage piscine disponible sur demande</span></div>
      <div class="equip"><span class="n">03</span><span class="l" data-en="Sunbeds, parasols and solar shower">Transats, parasols et douche solaire</span></div>
      <div class="equip"><span class="n">04</span><span class="l" data-en="South-facing 40 m² covered terrace">Terrasse couverte 40 m² orientée sud</span></div>
      <div class="equip"><span class="n">05</span><span class="l" data-en="12-seat garden lounge">Salon de jardin 12 places</span></div>
      <div class="equip"><span class="n">06</span><span class="l" data-en="Charcoal BBQ + 12-seat outdoor table">BBQ charbon + table extérieure 12 couverts</span></div>
      <div class="equip"><span class="n">07</span><span class="l" data-en="Pétanque court">Terrain de pétanque</span></div>
      <div class="equip"><span class="n">08</span><span class="l" data-en="Children's play area">Aire de jeux enfants</span></div>
      <div class="equip"><span class="n">09</span><span class="l" data-en="Wooded garden (olive trees, lavender, rosemary)">Jardin arboré (oliviers, lavandes, romarin)</span></div>
      <div class="equip"><span class="n">10</span><span class="l" data-en="Vegetable patch in summer (self-serve herbs)">Potager en été (herbes aromatiques en libre-service)</span></div>
      <div class="equip"><span class="n">11</span><span class="l" data-en="Free private parking">Parking privé gratuit</span></div>
      <div class="equip"><span class="n">12</span><span class="l" data-en="Outdoor lighting for evenings">Éclairage extérieur pour les soirées</span></div>
    </div>
  </div>
</section>

<!-- ============ 7 · FOOTER CTA ============ -->
