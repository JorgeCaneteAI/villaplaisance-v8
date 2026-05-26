<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Accueil.
 * Portée depuis le proto Claude design (index.html).
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  /* Home-only: hero with image */
  .page-hero.has-image {
    position: relative;
    overflow: hidden;
    background: var(--ink-900);
    border-bottom: 0;
  }
  .page-hero.has-image .bg {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
  }
  .page-hero.has-image .bg::after {
    content: ""; position: absolute; inset: 0;
    /* Subtle bottom-left scrim — keeps the photo bright while making the text readable */
    background:
      radial-gradient(ellipse 90% 70% at 30% 95%, rgba(31,28,22,0.55) 0%, rgba(31,28,22,0) 60%),
      linear-gradient(180deg, rgba(31,28,22,0) 55%, rgba(31,28,22,0.35) 100%);
  }
  .page-hero.has-image h1,
  .page-hero.has-image .lede,
  .page-hero.has-image .page-hero-issue {
    text-shadow: 0 1px 24px rgba(31,28,22,0.45);
  }
  .page-hero.has-image .page-hero-inner {
    position: relative; z-index: 1;
    min-height: clamp(540px, 78vh, 780px);
    align-content: end;
  }
  .page-hero.has-image h1 { color: var(--linen-50); }
  .page-hero.has-image h1 em { color: var(--sage-200); }
  .page-hero.has-image .page-hero-issue {
    color: rgba(251,247,238,0.78);
    border-bottom-color: rgba(251,247,238,0.25);
  }
  .page-hero.has-image .lede { color: rgba(251,247,238,0.92); }
  .page-hero.has-image .btn {
    background: var(--linen-50); color: var(--ink-900); border-color: var(--linen-50);
  }
  .page-hero.has-image .btn:hover {
    background: var(--sage-200); border-color: var(--sage-200);
  }
  .page-hero.has-image .btn-ghost {
    background: transparent; color: var(--linen-50); border-color: rgba(251,247,238,0.55);
  }
  .page-hero.has-image .btn-ghost:hover {
    background: var(--linen-50); color: var(--ink-900); border-color: var(--linen-50);
  }

  /* Two formulas — side-by-side cards */
  .formulas {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: clamp(20px, 2.4vw, 36px);
  }
  .formula {
    background: var(--linen-100);
    padding: clamp(28px, 3.4vw, 48px);
    display: flex; flex-direction: column; gap: 22px;
    position: relative;
    border: var(--hairline);
    transition: background .2s, border-color .2s;
  }
  .formula:hover { background: var(--linen-200); }
  .formula .num {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em;
    color: var(--terra-500); text-transform: uppercase;
    display: flex; justify-content: space-between; align-items: baseline;
    border-bottom: var(--hairline); padding-bottom: 14px;
  }
  .formula h3 {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(30px, 3.2vw, 48px); line-height: 1.0; letter-spacing: -0.02em;
    color: var(--ink-900); margin: 0;
  }
  .formula h3 em { font-style: italic; color: var(--sage-700); }
  .formula .desc { margin: 0; font-size: 15.5px; line-height: 1.6; color: var(--ink-700); }
  .formula .stats {
    display: flex; flex-wrap: wrap; gap: 6px;
  }
  .formula .stat-pill {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.1em;
    color: var(--ink-700); text-transform: uppercase;
    padding: 6px 10px; border: var(--hairline);
  }
  .formula .cta {
    margin-top: auto; padding-top: 8px;
  }
  @media (max-width: 720px) { .formulas { grid-template-columns: 1fr; } }

  /* World map — full bleed */
  .worldmap-full {
    width: 100%;
    background: var(--linen-50);
    border-top: var(--hairline);
    border-bottom: var(--hairline);
    overflow: hidden;
    position: relative;
  }
  .worldmap-full svg {
    display: block;
    width: 100%;
    height: auto;
    max-height: 60vh;
  }
  .continents { fill: color-mix(in oklab, var(--sage-200) 35%, var(--linen-100)); stroke: var(--stone-400); stroke-width: 0.5; opacity: 0.95; }
  .worldmap-full .legend {
    position: absolute;
    bottom: 16px; left: 50%; transform: translateX(-50%);
    background: var(--linen-50);
    padding: 8px 14px;
    border: var(--hairline);
    font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--stone-600);
    display: flex; gap: 20px; align-items: center;
  }
  .worldmap-full .legend .swatch { width: 8px; height: 8px; border-radius: 50%; }

  /* Origins inline list */
  .origins-inline {
    display: flex; flex-wrap: wrap; justify-content: center;
    gap: 10px 0;
    list-style: none; padding: 0; margin: 0;
    font-family: var(--font-display); font-style: italic;
    font-size: clamp(18px, 1.6vw, 22px);
    color: var(--ink-900);
    text-align: center;
    max-width: 1100px;
    margin-left: auto; margin-right: auto;
  }
  .origins-inline li {
    display: inline-flex; align-items: baseline; gap: 12px;
  }
  .origins-inline li:not(:last-child)::after {
    content: "·";
    color: var(--terra-500);
    margin-left: 12px;
    font-style: normal;
    font-weight: 700;
  }

  /* Triangle d'Or list */
  .destinations {
    list-style: none; padding: 0; margin: 0;
    border-top: var(--hairline);
  }
  .destinations li {
    display: grid;
    grid-template-columns: 80px 1fr auto;
    gap: clamp(16px, 3vw, 40px);
    padding: 22px 0;
    border-bottom: var(--hairline);
    align-items: center;
    transition: padding-left .2s;
  }
  .destinations li:hover { padding-left: 12px; }
  .destinations .time {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.08em;
    color: var(--terra-500); text-transform: uppercase;
  }
  .destinations .place {
    font-family: var(--font-display); font-size: clamp(22px, 2vw, 30px); color: var(--ink-900); letter-spacing: -0.005em;
  }
  .destinations .place em { font-style: italic; }
  .destinations .km {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.08em;
    color: var(--stone-500); text-transform: uppercase;
  }

  /* World map */
  .worldmap-section {
    display: grid; grid-template-columns: 1.4fr 1fr;
    gap: clamp(32px, 5vw, 80px);
    align-items: start;
  }
  .worldmap {
    aspect-ratio: 2 / 1;
    background: var(--linen-50);
    border: var(--hairline);
    position: relative;
    overflow: hidden;
  }
  .worldmap svg { width: 100%; height: 100%; display: block; }
  .pin-origin {
    fill: var(--stone-500);
    transition: fill .2s, r .2s;
  }
  .pin-here {
    fill: var(--terra-500);
  }
  .worldmap .legend {
    position: absolute;
    bottom: 14px; left: 14px;
    background: var(--linen-50);
    padding: 8px 12px;
    border: var(--hairline);
    font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--stone-600);
    display: flex; gap: 14px; align-items: center;
  }
  .worldmap .legend .swatch { width: 8px; height: 8px; border-radius: 50%; }
  .origins-list {
    columns: 1; column-gap: 24px;
    list-style: none; padding: 0; margin: 0;
    font-size: 14.5px; line-height: 1.9;
    color: var(--ink-700);
    border-top: var(--hairline);
  }
  .origins-list li {
    padding: 4px 0;
    break-inside: avoid;
  }
  .origins-list li::before {
    content: "·"; color: var(--terra-500); margin-right: 10px; font-weight: 700;
  }
  @media (max-width: 960px) { .worldmap-section { grid-template-columns: 1fr; } }

  /* Testimonials */
  .testimonials {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: clamp(20px, 2.4vw, 36px);
  }
  .testimonial {
    background: var(--linen-50);
    border: var(--hairline);
    padding: clamp(28px, 3vw, 36px);
    display: flex; flex-direction: column; gap: 18px;
  }
  .testimonial .stars {
    color: var(--terra-500);
    font-size: 16px; letter-spacing: 2px;
  }
  .testimonial blockquote {
    margin: 0;
    font-family: var(--font-display);
    font-size: clamp(20px, 1.8vw, 24px); line-height: 1.35; color: var(--ink-900); letter-spacing: -0.005em;
    text-wrap: pretty;
  }
  .testimonial blockquote em { font-style: italic; }
  .testimonial cite {
    font-style: normal;
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.1em;
    color: var(--stone-500); text-transform: uppercase;
    margin-top: auto;
  }
  @media (max-width: 960px) { .testimonials { grid-template-columns: 1fr; } }

  /* Journal teasers */
  .journal-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: clamp(24px, 3vw, 48px);
  }
  .journal-card {
    display: grid; grid-template-columns: 1fr 1.2fr;
    gap: clamp(20px, 2.4vw, 32px);
    align-items: center;
  }
  .journal-card .img {
    aspect-ratio: 4/3;
    background-size: cover; background-position: center;
    background-color: var(--linen-200);
  }
  .journal-card h3 {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(22px, 2vw, 30px); line-height: 1.15; letter-spacing: -0.015em;
    color: var(--ink-900); margin: 0 0 8px;
  }
  .journal-card h3 em { font-style: italic; }
  .journal-card .kicker-mono {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    color: var(--terra-500); text-transform: uppercase;
    margin-bottom: 10px;
  }
  .journal-card p { font-size: 14.5px; line-height: 1.55; color: var(--stone-600); margin: 0 0 12px; }
  @media (max-width: 960px) {
    .journal-grid { grid-template-columns: 1fr; }
    .journal-card { grid-template-columns: 1fr; }
  }

  /* Placeholder banner (used in testimonials) */
  .pl-inline {
    display: inline-flex; align-items: center; gap: 10px;
    font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.14em;
    color: var(--terra-600); text-transform: uppercase;
    padding: 6px 12px;
    border: 1px dashed color-mix(in oklab, var(--terra-500) 50%, transparent);
    background: color-mix(in oklab, var(--terra-500) 6%, var(--linen-50));
  }
  .pl-inline .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--terra-500); }
</style>


<?php
/*
 * Bascule progressive vers vp_sections.
 * On indexe les sections BDD par position. À chaque emplacement HTML,
 * on appelle renderV8BlockAt($pos, $expectedType) : si la BDD a une
 * section à cette position ET du bon type, on la rend. Sinon fallback
 * HTML en dur (comportement V8 actuel).
 *
 * Phase 2 (2026-05-26) :
 *   - position 1 : hero
 *   - position 2 : intro (prose two-col)
 * Les autres positions suivent au fur et à mesure du port.
 */
$_v8SectionsByPos = [];
foreach (BlockService::getSections('accueil', $lang) as $_s) {
    $_v8SectionsByPos[(int)$_s['position']] = $_s;
}
$renderV8BlockAt = static function (int $pos, string $expectedType) use ($_v8SectionsByPos): ?string {
    $s = $_v8SectionsByPos[$pos] ?? null;
    if (!$s) return null;
    if ($s['block_type'] !== $expectedType) {
        error_log("V8 home: position $pos attendu '$expectedType' mais BDD a '{$s['block_type']}'");
        return null;
    }
    return BlockService::renderBlock($s);
};
?>

<!-- ============ HERO MASTHEAD (home: with image) ============ -->
<?php if ($_heroHtml = $renderV8BlockAt(1, 'hero')): ?>
<?= $_heroHtml ?>
<?php else: ?>
<section class="page-hero has-image">
  <div class="bg" style="background-image: url('/uploads/hero-piscine.webp')"></div>
  <div class="page-hero-inner">
    <div>
      <div class="page-hero-issue">
        <span data-en="Bédarrides · Vaucluse · Provence">Bédarrides · Vaucluse · Provence</span>
        <span data-en="Golden Triangle">Triangle d'Or</span>
      </div>
      <h1>Villa<br/><em>Plaisance</em></h1>
    </div>
    <div>
      <p class="lede" data-en="One house, two ways to stay — B&amp;B from September to June, the whole villa in July and August.">Une maison, deux façons d'y séjourner — chambres d'hôtes de septembre à juin, maison d'hôtes en juillet et août.</p>
      <div class="page-hero-ctas">
        <a href="<?= LangService::url('chambres-d-hotes') ?>" class="btn"><span data-en="Sept → June · B&amp;B">Sept → Juin · Chambres d'hôtes</span> →</a>
        <a href="<?= LangService::url('location-villa-provence') ?>" class="btn btn-ghost"><span data-en="July → August · Whole villa">Juil → Août · Maison d'hôtes</span> →</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 02 · L'INTRO ============ -->
<?php if ($_introHtml = $renderV8BlockAt(2, 'prose')): ?>
<?= $_introHtml ?>
<?php else: ?>
<section class="section">
  <div class="container-wide">
    <div class="two-col">
      <div>
        <div class="section-label">
          <span class="numeral">— 01 / <span data-en="The house">La maison</span></span>
        </div>
        <h2 class="h-xl" style="margin:0; max-width: 12ch;">Une maison<br/>de <em>charme</em><br/>en Provence.</h2>
      </div>
      <div>
        <p class="lede" style="margin: 0 0 20px;" data-en="Nestled in the heart of Provence's Golden Triangle, Villa Plaisance is a maison de charme in Bédarrides — 15 min from Avignon, 8 min from Châteauneuf-du-Pape, 18 min from Orange.">Nichée au cœur du Triangle d'Or provençal, Villa Plaisance est une maison de charme à Bédarrides — à 15 min d'Avignon, 8 min de Châteauneuf-du-Pape, 18 min d'Orange.</p>
        <p class="body-lg" style="margin: 0 0 16px;" data-en="September to June: a B&amp;B with homemade breakfast and shared pool. July to August: the whole villa (4 bedrooms, 10 guests, private 12 × 6 m pool) in full autonomy.">De septembre à juin : chambres d'hôtes B&amp;B avec petit-déjeuner maison et piscine partagée. En juillet–août : la villa entière (4 chambres, 10 personnes, piscine privée 12 × 6 m) en toute autonomie.</p>
        <p class="body-lg" style="margin: 0;" data-en="The place is calm, the village alive, the countryside starts at the doorstep, the TGV is fifteen minutes away.">Le lieu est calme, le village vivant, la campagne à pied, le TGV à quinze minutes.</p>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 03 · DEUX FORMULES ============ -->
<?php if ($_formulasHtml = $renderV8BlockAt(3, 'formula')): ?>
<?= $_formulasHtml ?>
<?php else: ?>
<section class="section surface-stone" style="background: var(--linen-100);">
  <div class="container-wide">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: clamp(40px, 5vw, 64px); flex-wrap: wrap;">
      <div>
        <div class="section-label">
          <span class="numeral">— 02 / <span data-en="Two formulas">Deux formules</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 16ch;">Deux façons<br/>de <em>séjourner</em>,<br/>une seule maison.</h2>
      </div>
      <p class="body-lg" style="max-width: 38ch; margin: 0;" data-en="The house lives in two seasons — pick the one that suits you.">La maison vit deux saisons — choisissez celle qui vous va.</p>
    </div>

    <div class="formulas">

      <article class="formula">
        <div class="num">
          <span>01 · <span data-en="Sept → June">Sept → Juin</span></span>
          <span data-en="At the host's place">Chez l'habitant</span>
        </div>
        <h3><em data-en="B&amp;B">Chambres d'hôtes</em></h3>
        <p class="desc" data-en="You stay at our place. Two communicating, air-conditioned bedrooms with a private bathroom are strictly dedicated to you. Breakfast is included — local produce, homemade jams, fruit from the garden. Shared pool, personalised advice and a warm welcome. A communicating suite ideal for families (1 to 5 guests).">Vous séjournez chez l'habitant. Deux chambres communicantes et climatisées avec salle de bain privée vous sont strictement dédiées. Le petit-déjeuner est inclus : produits locaux, confitures maison, fruits du jardin. Piscine partagée, conseils personnalisés et accueil chaleureux. Suite communicante idéale pour les familles (1 à 5 personnes).</p>
        <div class="stats">
          <span class="stat-pill">1 – 5 <span data-en="guests">pers.</span></span>
          <span class="stat-pill" data-en="Breakfast included">Petit-déj inclus</span>
          <span class="stat-pill" data-en="Shared pool">Piscine partagée</span>
        </div>
        <div class="cta">
          <a class="btn-link" href="<?= LangService::url('chambres-d-hotes') ?>"><span data-en="Discover the B&amp;B">Découvrir les chambres d'hôtes</span> →</a>
        </div>
      </article>

      <article class="formula">
        <div class="num">
          <span>02 · <span data-en="July → August">Juil &amp; Août</span></span>
          <span data-en="On your own">Vous seuls</span>
        </div>
        <h3><em data-en="Whole villa">La Villa en exclusivité</em></h3>
        <p class="desc" data-en="You stay on your own and have the villa and the outdoors exclusively. 4 bedrooms, 2 bathrooms, fully equipped kitchen, fenced private 12 × 6 m pool and garden under the olive trees. Up to 10 guests in total autonomy — your home in Provence, no neighbours.">Vous séjournez seuls et disposez de la villa et des extérieurs en exclusivité. 4 chambres, 2 salles de bain, cuisine entièrement équipée, piscine privée clôturée 12 × 6 m et jardin sous les oliviers. Jusqu'à 10 personnes en totale autonomie — votre maison en Provence, sans vis-à-vis.</p>
        <div class="stats">
          <span class="stat-pill" data-en="Up to 10 guests">Jusqu'à 10 pers.</span>
          <span class="stat-pill" data-en="Private 12 × 6 m pool">Piscine privée 12 × 6</span>
          <span class="stat-pill" data-en="Sat → Sat">Samedi → samedi</span>
        </div>
        <div class="cta">
          <a class="btn-link" href="<?= LangService::url('location-villa-provence') ?>"><span data-en="Discover the whole villa">Découvrir la villa entière</span> →</a>
        </div>
      </article>

    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 04 · TRIANGLE D'OR ============ -->
<?php if ($_territoireHtml = $renderV8BlockAt(4, 'territoire')): ?>
<?= $_territoireHtml ?>
<?php else: ?>
<section class="section">
  <div class="container-wide">
    <div class="two-col">
      <div>
        <div class="section-label">
          <span class="numeral">— 03 / <span data-en="Where we are">Où nous sommes</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;">Au cœur du<br/><em>Triangle d'Or</em>.</h2>
        <p class="body-lg" style="max-width: 40ch; margin: 24px 0 0; color: var(--stone-600);" data-en="The whole region radiates from Bédarrides — Avignon to the south, Orange to the north, Châteauneuf to the west, the Mont Ventoux straight ahead.">Toute la région rayonne depuis Bédarrides — Avignon au sud, Orange au nord, Châteauneuf à l'ouest, le Mont Ventoux droit devant.</p>
      </div>
      <ul class="destinations">
        <li>
          <span class="time">8 MIN</span>
          <span class="place"><em>Châteauneuf-du-Pape</em></span>
          <span class="km" data-en="Vines">Vignes</span>
        </li>
        <li>
          <span class="time">15 MIN</span>
          <span class="place"><em>Avignon</em></span>
          <span class="km" data-en="TGV station">Gare TGV</span>
        </li>
        <li>
          <span class="time">18 MIN</span>
          <span class="place"><em>Orange</em></span>
          <span class="km" data-en="Roman theatre">Théâtre antique</span>
        </li>
        <li>
          <span class="time">25 MIN</span>
          <span class="place"><em>L'Isle-sur-la-Sorgue</em></span>
          <span class="km" data-en="Antiques market">Marché brocante</span>
        </li>
        <li>
          <span class="time">30 MIN</span>
          <span class="place"><em>Pont du Gard</em></span>
          <span class="km" data-en="Roman heritage">Patrimoine romain</span>
        </li>
        <li>
          <span class="time">35 MIN</span>
          <span class="place"><em>Vaison-la-Romaine</em></span>
          <span class="km" data-en="Ruins &amp; market">Antiques &amp; marché</span>
        </li>
        <li>
          <span class="time">42 MIN</span>
          <span class="place"><em>Gordes</em></span>
          <span class="km" data-en="Perched village">Village perché</span>
        </li>
        <li>
          <span class="time">45 MIN</span>
          <span class="place"><em>Les Baux-de-Provence</em></span>
          <span class="km" data-en="Carrières de Lumières">Carrières de Lumières</span>
        </li>
        <li>
          <span class="time">45 MIN</span>
          <span class="place"><em>Mont Ventoux</em></span>
          <span class="km" data-en="The road, the view">La route, la vue</span>
        </li>
      </ul>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 05 · PROVENANCE / WORLD MAP ============ -->
<section class="section surface-stone" style="background: var(--linen-100); padding-left: 0; padding-right: 0;">
  <div class="container-wide">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: clamp(40px, 5vw, 64px); flex-wrap: wrap;">
      <div>
        <div class="section-label">
          <span class="numeral">— 04 / <span data-en="Where our guests come from">D'où viennent nos hôtes</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;" data-en="Travellers from/around the world.">Voyageurs<br/>du <em>monde</em> entier.</h2>
      </div>
      <p class="body-lg" style="max-width: 40ch; margin: 0;" data-en="Twenty-two countries and counting — postcards on the kitchen wall.">Vingt-deux origines et toujours plus — les cartes postales s'accrochent au mur de la cuisine.</p>
    </div>
  </div>

  <!-- Full-bleed world map -->
  <div class="worldmap-full" aria-label="Carte des provenances">
    <svg viewBox="0 0 1000 500" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

      <!-- Continents (simplified outlines, equirectangular projection) -->
      <g class="continents">
        <!-- North America -->
        <path d="M 28 64 L 139 56 L 200 70 L 270 95 L 319 111 L 305 145 L 278 181 L 240 195 L 205 188 L 175 161 L 165 145 L 139 111 L 95 95 L 83 83 L 50 75 Z"/>
        <!-- Greenland -->
        <path d="M 330 50 L 380 55 L 395 80 L 380 105 L 350 115 L 335 95 L 330 70 Z"/>
        <!-- South America -->
        <path d="M 275 264 L 310 245 L 361 250 L 403 272 L 395 305 L 381 311 L 360 335 L 339 342 L 320 380 L 300 400 L 295 370 L 290 330 L 285 295 L 275 280 Z"/>
        <!-- Eurasia (Europe + Asia + Russia) -->
        <path d="M 475 131 L 486 114 L 514 103 L 528 83 L 528 53 L 583 56 L 667 42 L 750 39 L 875 42 L 970 60 L 986 64 L 944 83 L 889 103 L 861 133 L 839 164 L 800 189 L 760 200 L 717 228 L 700 186 L 660 195 L 622 214 L 605 200 L 589 164 L 567 153 L 542 147 L 522 147 L 500 152 L 483 150 Z"/>
        <!-- UK + Ireland -->
        <path d="M 477 108 L 492 105 L 495 122 L 484 128 L 472 122 Z"/>
        <!-- Africa -->
        <path d="M 472 167 L 510 160 L 528 161 L 575 168 L 589 195 L 622 214 L 645 217 L 639 240 L 620 260 L 597 290 L 597 317 L 575 340 L 556 344 L 540 365 L 510 385 L 495 380 L 480 360 L 472 330 L 470 295 L 472 240 L 470 200 Z"/>
        <!-- Arabia + Middle East -->
        <path d="M 597 175 L 625 175 L 660 195 L 645 230 L 622 214 L 605 200 L 595 188 Z"/>
        <!-- India -->
        <path d="M 720 175 L 760 175 L 770 200 L 760 220 L 745 240 L 720 230 L 715 200 Z"/>
        <!-- Southeast Asia peninsula -->
        <path d="M 795 195 L 830 200 L 835 225 L 815 235 L 800 220 Z"/>
        <!-- Indonesia (rough) -->
        <path d="M 800 245 L 855 250 L 875 260 L 855 268 L 815 265 Z"/>
        <!-- Japan -->
        <path d="M 875 130 L 895 140 L 890 165 L 870 160 Z"/>
        <!-- Australia -->
        <path d="M 819 311 L 861 283 L 903 278 L 925 295 L 935 320 L 925 345 L 905 350 L 870 345 L 850 335 L 825 325 Z"/>
        <!-- New Zealand -->
        <path d="M 945 370 L 960 385 L 950 395 L 940 380 Z"/>
        <!-- Madagascar -->
        <path d="M 615 345 L 622 360 L 618 380 L 608 372 Z"/>
      </g>

      <!-- Lat/lon thin guides -->
      <line x1="0" y1="250" x2="1000" y2="250" stroke="#6F8466" stroke-width="0.5" stroke-dasharray="2 8" opacity="0.5"/>

      <!-- Great-circle-ish curves from each origin to Bédarrides (514, 128). -->
      <g stroke="#6F8466" stroke-width="0.7" opacity="0.45" fill="none" stroke-linecap="round">
        <path d="M 920 344 Q 700 600 514 128"/>
        <path d="M 507 115 Q 510 90 514 128"/>
        <path d="M 266 157 Q 380 30 514 128"/>
        <path d="M 173 156 Q 340 -30 514 128"/>
        <path d="M 294 137 Q 400 30 514 128"/>
        <path d="M 528 148 Q 520 140 514 128"/>
        <path d="M 296 124 Q 400 30 514 128"/>
        <path d="M 159 117 Q 340 -30 514 128"/>
        <path d="M 306 127 Q 400 30 514 128"/>
        <path d="M 490 138 Q 500 145 514 128"/>
        <path d="M 530 84 Q 525 100 514 128"/>
        <path d="M 537 104 Q 525 115 514 128"/>
        <path d="M 276 152 Q 390 40 514 128"/>
        <path d="M 229 166 Q 370 30 514 128"/>
        <path d="M 566 145 Q 540 130 514 128"/>
        <path d="M 160 145 Q 340 -20 514 128"/>
        <path d="M 286 142 Q 400 40 514 128"/>
        <path d="M 302 120 Q 400 30 514 128"/>
        <path d="M 514 105 Q 514 116 514 128"/>
        <path d="M 521 120 Q 518 124 514 128"/>
      </g>

      <!-- Origin pins -->
      <g fill="#3B362D">
        <circle cx="920" cy="344" r="4"/>
        <circle cx="507" cy="115" r="4"/>
        <circle cx="266" cy="157" r="4"/>
        <circle cx="173" cy="156" r="4"/>
        <circle cx="294" cy="137" r="4"/>
        <circle cx="528" cy="148" r="4"/>
        <circle cx="296" cy="124" r="4"/>
        <circle cx="159" cy="117" r="4"/>
        <circle cx="306" cy="127" r="4"/>
        <circle cx="490" cy="138" r="4"/>
        <circle cx="530" cy="84" r="4"/>
        <circle cx="537" cy="104" r="4"/>
        <circle cx="276" cy="152" r="4"/>
        <circle cx="229" cy="166" r="4"/>
        <circle cx="566" cy="145" r="4"/>
        <circle cx="160" cy="145" r="4"/>
        <circle cx="286" cy="142" r="4"/>
        <circle cx="302" cy="120" r="4"/>
        <circle cx="514" cy="105" r="4"/>
        <circle cx="521" cy="120" r="4"/>
      </g>

      <!-- Bédarrides — the centre -->
      <circle cx="514" cy="128" r="8" fill="#C44C24"/>
      <circle cx="514" cy="128" r="16" fill="none" stroke="#C44C24" stroke-width="1" opacity="0.55"/>
      <circle cx="514" cy="128" r="26" fill="none" stroke="#C44C24" stroke-width="0.6" opacity="0.3"/>
      <text x="514" y="95" text-anchor="middle" font-family="JetBrains Mono" font-size="11" fill="#C44C24" letter-spacing="0.16em" font-weight="500">BÉDARRIDES</text>
    </svg>
    <div class="legend">
      <span style="display: inline-flex; align-items: center; gap: 6px;"><span class="swatch" style="background: var(--terra-500);"></span> <span data-en="The house">La maison</span></span>
      <span style="display: inline-flex; align-items: center; gap: 6px;"><span class="swatch" style="background: var(--ink-700);"></span> <span data-en="22 origins">Origines des hôtes</span></span>
    </div>
  </div>

  <!-- Origins inline -->
  <div class="container-wide" style="margin-top: clamp(40px, 5vw, 64px);">
    <ul class="origins-inline">
      <li>Sydney, Australie</li>
      <li>Paris, France</li>
      <li>Géorgie, États-Unis</li>
      <li>Costa Mesa, Californie</li>
      <li>New York, États-Unis</li>
      <li>Tunisie</li>
      <li>Montréal, Canada</li>
      <li>Port Townsend, Washington</li>
      <li>Maine, États-Unis</li>
      <li>Espagne</li>
      <li>Norvège</li>
      <li>Allemagne</li>
      <li>Charlotte, Caroline du Nord</li>
      <li>Austin, Texas</li>
      <li>Grèce</li>
      <li>San Francisco, Californie</li>
      <li>Burtonsville, Maryland</li>
      <li>Québec City, Canada</li>
      <li>Pays-Bas</li>
      <li>Suisse</li>
    </ul>
  </div>
</section>

<!-- ============ 06 · TÉMOIGNAGES ============ -->
<section class="section">
  <div class="container-wide">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: clamp(40px, 5vw, 64px); flex-wrap: wrap;">
      <div>
        <div class="section-label">
          <span class="numeral">— 05 / <span data-en="What guests say">Ce qu'on en dit</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;">Quelques <em>mots</em><br/>laissés au départ.</h2>
      </div>
      <span class="pl-inline"><span class="dot"></span><span>Témoignages d'exemple — à remplacer par vos vrais avis</span></span>
    </div>

    <div class="testimonials">
      <article class="testimonial">
        <div class="stars">★ ★ ★ ★ ★</div>
        <blockquote>« <em>Placeholder</em> — un mot du voyageur sur la chambre, le petit-déjeuner, l'accueil. Deux ou trois phrases au maximum. »</blockquote>
        <cite>— Visiteur · ville</cite>
      </article>
      <article class="testimonial">
        <div class="stars">★ ★ ★ ★ ★</div>
        <blockquote>« <em>Placeholder</em> — un mot sur la villa entière, la piscine, la cuisine, le calme. Témoignage à venir. »</blockquote>
        <cite>— Visiteur · ville</cite>
      </article>
      <article class="testimonial">
        <div class="stars">★ ★ ★ ★ ★</div>
        <blockquote>« <em>Placeholder</em> — un mot sur la région, les conseils, la disponibilité des hôtes. »</blockquote>
        <cite>— Visiteur · ville</cite>
      </article>
    </div>
  </div>
</section>

<!-- ============ 07 · DU JOURNAL ============ -->
<section class="section surface-sage" style="background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));">
  <div class="container-wide">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: clamp(40px, 5vw, 64px); flex-wrap: wrap;">
      <div>
        <div class="section-label">
          <span class="numeral">— 06 / <span data-en="From the journal">Du journal</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;">Ce qu'on <em>écrit</em>,<br/>ce qu'on conseille.</h2>
      </div>
      <p class="body-lg" style="max-width: 38ch; margin: 0;" data-en="Two sections — tourism essays, and what to do nearby.">Deux rubriques — articles autour du tourisme, et la sélection sur place.</p>
    </div>

    <div class="journal-grid">
      <a class="journal-card" href="<?= LangService::url('journal') ?>">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-vignes-provence-01.webp')"></div>
        <div>
          <div class="kicker-mono" data-en="Journal · Tourism">Journal · Tourisme</div>
          <h3>Voyager <em>autrement</em><br/>en Provence.</h3>
          <p data-en="Five ways of looking at the region — Provence contemporaine, voyager autrement, hosts &amp; hoteliers, land &amp; transition, the art of staying.">Cinq façons de regarder la région — provence contemporaine, voyager autrement, hôtes &amp; hôteliers, territoire &amp; transition, l'art de séjourner.</p>
          <span class="btn-link" data-en="Read the journal →">Lire le journal →</span>
        </div>
      </a>

      <a class="journal-card" href="<?= LangService::url('itineraire') ?>">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-vp-itini-elisa-02-pont-du-gard.webp')"></div>
        <div>
          <div class="kicker-mono" data-en="Journal · What to do nearby">Journal · Que faire sur place</div>
          <h3>Sur place,<br/>tout est <em>là</em>.</h3>
          <p data-en="Sites to visit, tables, shops, things to do with children — the house's pick.">Sites à visiter, tables, commerces, activités avec les enfants — la sélection de la maison.</p>
          <span class="btn-link" data-en="See the selection →">Voir la sélection →</span>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- ============ FOOTER ============ -->
