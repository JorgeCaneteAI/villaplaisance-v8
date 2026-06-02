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

<!-- ============ 05 · PROVENANCE / WORLD MAP (Leaflet — vraie carte du monde) ============ -->
<?php
// Origines depuis vp_reviews — données réelles, mises à jour quand on ajoute un avis
$_mapOrigins = [];
$_mapCityList = [];
try {
    $_rows = Database::fetchAll(
        "SELECT origin, COUNT(*) AS cnt FROM vp_reviews WHERE LENGTH(origin) > 0 AND status = 'published' GROUP BY origin ORDER BY cnt DESC"
    );
    foreach ($_rows as $_r) {
        $_mapOrigins[$_r['origin']] = (int)$_r['cnt'];
        $_mapCityList[] = $_r['origin'];
    }
} catch (\Throwable) {}

// Geocoding lookup (lat, lng) — mêmes coordonnées que le bloc mappemonde
$_mapGeocode = [
    'France' => [46.6034, 2.3488],
    'Paris, France' => [48.8566, 2.3522],
    'Austin, Texas' => [30.2672, -97.7431],
    'Burtonsville, Maryland' => [39.1115, -76.9325],
    'Charlotte, Caroline du Nord' => [35.2271, -80.8431],
    'Costa Mesa, Californie' => [33.6412, -117.9187],
    'Géorgie, États-Unis' => [33.7490, -84.3880],
    'Maine, États-Unis' => [45.2538, -69.4455],
    'New York, États-Unis' => [40.7128, -74.0060],
    'New York, New York' => [40.7128, -74.0060],
    'San Francisco, Californie' => [37.7749, -122.4194],
    'Port Townsend, Washington' => [48.1170, -122.7604],
    'Montréal, Canada' => [45.5017, -73.5673],
    'Québec City, Canada' => [46.8139, -71.2080],
    'Allemagne' => [51.1657, 10.4515],
    'Pays-Bas' => [52.1326, 5.2913],
    'Suisse' => [46.8182, 8.2275],
    'Espagne' => [40.4168, -3.7038],
    'Grèce' => [39.0742, 21.8243],
    'Norvège' => [60.4720, 8.4689],
    'Tunisie' => [33.8869, 9.5375],
    'Sydney, Australie' => [-33.8688, 151.2093],
];

// Construction des pins — merge des coordonnées identiques
$_coordMap = [];
foreach ($_mapOrigins as $_origin => $_count) {
    if (isset($_mapGeocode[$_origin])) {
        $_key = $_mapGeocode[$_origin][0] . ',' . $_mapGeocode[$_origin][1];
        if (!isset($_coordMap[$_key])) {
            $_coordMap[$_key] = ['lat' => $_mapGeocode[$_origin][0], 'lng' => $_mapGeocode[$_origin][1], 'label' => $_origin, 'count' => $_count];
        } else {
            $_coordMap[$_key]['count'] += $_count;
            if (mb_strlen($_origin) < mb_strlen($_coordMap[$_key]['label'])) {
                $_coordMap[$_key]['label'] = $_origin;
            }
        }
    }
}
$_mapPins = array_values($_coordMap);
$_villaLat = 44.0410;
$_villaLng = 4.8945;
shuffle($_mapCityList);
$_nPins = count($_mapPins);
?>
<section class="section surface-stone" style="background: var(--linen-100); padding-left: 0; padding-right: 0;">
  <div class="container-wide">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: clamp(40px, 5vw, 64px); flex-wrap: wrap;">
      <div>
        <div class="section-label">
          <span class="numeral">— 04 / <span><?= t('home.map.kicker') ?></span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;"><?= t('home.map.title') ?></h2>
      </div>
      <p class="body-lg" style="max-width: 40ch; margin: 0;">
        <?= $_nPins > 0 ? t('home.map.subtitle_n', ['n' => (string)$_nPins]) : t('home.map.subtitle_empty') ?>
      </p>
    </div>
  </div>

  <?php
  // Projection equirectangular pure (cohérente avec le SVG world)
  // x = (lng + 180) / 360 * 1000 ; y = (90 - lat) / 180 * 500
  $_proj = static function (float $lng, float $lat): array {
      return [round(($lng + 180) / 360 * 1000, 2), round((90 - $lat) / 180 * 500, 2)];
  };
  [$_villaX, $_villaY] = $_proj($_villaLng, $_villaLat);
  $_pinsXY = [];
  foreach ($_mapPins as $_p) {
      [$_x, $_y] = $_proj((float)$_p['lng'], (float)$_p['lat']);
      $_pinsXY[] = ['x' => $_x, 'y' => $_y, 'label' => $_p['label']];
  }
  $_worldSvg = @file_get_contents(ROOT . '/public/assets/img/world-equirectangular.svg');
  // Strip XML prolog si présent (on l'inline dans du HTML)
  if ($_worldSvg) {
      $_worldSvg = preg_replace('/^<\?xml[^?]*\?>\s*/i', '', $_worldSvg);
  }
  ?>
  <!-- Carte du monde SVG au trait (Natural Earth 110m, projection equirectangulaire) -->
  <div class="worldmap-svg" role="img" aria-label="<?= t('home.map.kicker') ?>">
    <?php if ($_worldSvg): ?>
    <div class="worldmap-svg-frame">
      <?= preg_replace(
        '/<svg([^>]*)>/i',
        '<svg$1 class="worldmap-countries">',
        $_worldSvg,
        1
      ) ?>
      <svg class="worldmap-pins" viewBox="0 0 1000 500" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
        <!-- Pins origines clients -->
        <g class="pins-guests">
          <?php foreach ($_pinsXY as $_p): ?>
          <g class="pin-guest" transform="translate(<?= $_p['x'] ?>, <?= $_p['y'] ?>)">
            <circle r="3"></circle>
            <title><?= htmlspecialchars($_p['label']) ?></title>
          </g>
          <?php endforeach; ?>
        </g>
        <!-- Pin Villa Plaisance (Bédarrides) avec halo + label -->
        <g class="pin-home" transform="translate(<?= $_villaX ?>, <?= $_villaY ?>)">
          <circle class="pin-home-halo-3" r="14"></circle>
          <circle class="pin-home-halo-2" r="8"></circle>
          <circle class="pin-home-dot" r="3.5"></circle>
          <text class="pin-home-label" y="-12" text-anchor="middle">BÉDARRIDES</text>
          <title>Villa Plaisance — Bédarrides</title>
        </g>
      </svg>
    </div>
    <?php endif; ?>
  </div>

  <div class="container-wide map-legend-wrap">
    <div class="map-legend">
      <span><span class="swatch swatch-home"></span> <?= t('home.map.legend_home') ?></span>
      <span><span class="swatch swatch-guest"></span> <?= t('home.map.legend_guests') ?></span>
    </div>
  </div>

  <?php if (!empty($_mapCityList)): ?>
  <!-- Liste des origines réelles (depuis vp_reviews, ordre mélangé) -->
  <div class="container-wide" style="margin-top: clamp(32px, 4vw, 48px);">
    <ul class="origins-inline">
      <?php foreach ($_mapCityList as $_city): ?>
      <li><?= htmlspecialchars($_city) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
</section>

<!-- ============ 06 · TÉMOIGNAGES ============ -->
<?php if ($_avisHtml = $renderV8BlockAt(6, 'avis')): ?>
<?= $_avisHtml ?>
<?php else: ?>
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
<?php endif; ?>

<!-- ============ 07 · DU JOURNAL ============ -->
<?php if ($_journalHtml = $renderV8BlockAt(7, 'articles')): ?>
<?= $_journalHtml ?>
<?php else: ?>
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
<?php endif; ?>

<!-- ============ FOOTER ============ -->
