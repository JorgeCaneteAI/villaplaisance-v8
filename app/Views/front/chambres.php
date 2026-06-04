<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Chambres d'hôtes (sept-juin).
 * Portée depuis le proto Claude design (chambres-hotes.html).
 * Layout : front-proto (Cormorant Garamond + style-proto.css).
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  /* Hero, light/airy variant of the maison-hotes hero */
  .ch-hero {
    position: relative;
    min-height: clamp(560px, 86vh, 840px);
    overflow: hidden;
    background: var(--linen-100);
  }
  .ch-hero-image { position: absolute; inset: 0; background-size: cover; background-position: center; }
  .ch-hero-image::after {
    content: ""; position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(31,28,22,0.05) 0%, rgba(31,28,22,0.08) 50%, rgba(31,28,22,0.55) 100%);
  }
  .ch-hero-content {
    position: relative; z-index: 2;
    padding: clamp(40px, 8vw, 96px) var(--gutter);
    max-width: var(--container-wide);
    margin: 0 auto;
    width: 100%;
    display: grid; grid-template-rows: 1fr auto; gap: 24px;
    min-height: clamp(560px, 86vh, 840px);
    color: var(--linen-50);
  }
  .ch-hero h1 {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(56px, 9vw, 140px); line-height: 0.94; letter-spacing: -0.025em;
    color: var(--linen-50);
  }
  .ch-hero h1 em { font-style: italic; color: var(--sage-200); }
  .ch-hero-overline {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em;
    color: rgba(251,247,238,0.78); text-transform: uppercase;
  }
  .ch-hero-bottom {
    display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
    gap: clamp(24px, 5vw, 80px); align-items: end;
  }
  .ch-hero-tag {
    font-family: var(--font-display); font-size: clamp(18px, 1.6vw, 22px);
    color: rgba(251,247,238,0.92); line-height: 1.45; max-width: 50ch; margin: 0 0 20px;
  }
  @media (max-width: 960px) { .ch-hero-bottom { grid-template-columns: 1fr; } }

  /* Room editorial layout */
  .ch-room {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.05fr);
    gap: clamp(32px, 5vw, 80px);
    align-items: center;
  }
  .ch-room.alt { grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); }
  .ch-room.alt .ch-room-text { order: 2; }
  .ch-room-images {
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  }
  .ch-room-images .big { grid-column: 1 / -1; aspect-ratio: 16/10; background-size: cover; background-position: center; }
  .ch-room-images .sm { aspect-ratio: 4/5; background-size: cover; background-position: center; }
  .ch-room-num {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em;
    color: var(--stone-500); text-transform: uppercase;
    display: flex; justify-content: space-between; align-items: baseline;
    border-bottom: var(--hairline); padding-bottom: 14px; margin-bottom: 24px;
  }
  .ch-room-tagline {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em;
    color: var(--terra-500); text-transform: uppercase;
    margin: 0 0 14px;
  }
  .ch-pills {
    display: flex; flex-wrap: wrap; gap: 6px;
    margin-top: 24px;
  }

  /* Breakfast section, large editorial */
  .breakfast-layout {
    display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
    gap: clamp(32px, 5vw, 80px);
    align-items: center;
  }
  .breakfast-img {
    aspect-ratio: 4/5; background-size: cover; background-position: center;
  }
  .breakfast-list {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0 32px;
    border-top: var(--hairline);
    margin-top: 28px;
  }
  .breakfast-list .item {
    padding: 14px 0;
    border-bottom: var(--hairline);
    font-size: 14.5px;
    color: var(--ink-700);
    display: flex; gap: 12px; align-items: baseline;
  }
  .breakfast-list .item::before {
    content: "";
    width: 6px; height: 6px; background: var(--sage-500); border-radius: 50%;
    align-self: center; flex-shrink: 0;
  }

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
  .equip-grid .equip .l {
    font-family: var(--font-sans); font-size: 15.5px; color: var(--ink-700);
  }

  /* Practical info, reused styling */
  .practical { border-top: var(--hairline-strong); }
  .practical .row {
    display: grid; grid-template-columns: minmax(180px, 1fr) minmax(0, 2fr);
    gap: clamp(24px, 4vw, 64px);
    padding: 24px 0;
    border-bottom: var(--hairline);
    align-items: baseline;
  }
  .practical .row .k {
    font-family: var(--font-display); font-style: italic;
    font-size: clamp(22px, 2vw, 28px); color: var(--ink-900); letter-spacing: -0.005em;
  }
  .practical .row .v { font-family: var(--font-sans); font-size: 15.5px; color: var(--ink-700); line-height: 1.55; }
  .practical .row .v strong { font-weight: 500; color: var(--ink-900); }

  /* FAQ */
  .faq { border-top: var(--hairline); }
  .faq details { border-bottom: var(--hairline); padding: 20px 0; }
  .faq summary {
    list-style: none; cursor: pointer;
    display: grid; grid-template-columns: 1fr 24px; align-items: center; gap: 16px;
    font-family: var(--font-display); font-size: clamp(20px, 1.8vw, 26px); color: var(--ink-900); letter-spacing: -0.005em;
  }
  .faq summary::-webkit-details-marker { display: none; }
  .faq .icon {
    width: 24px; height: 24px; border: 1px solid var(--ink-900);
    display: grid; place-items: center; position: relative;
    transition: background .2s;
  }
  .faq .icon::before, .faq .icon::after { content: ""; position: absolute; background: var(--ink-900); }
  .faq .icon::before { width: 10px; height: 1px; }
  .faq .icon::after { width: 1px; height: 10px; transition: transform .2s; }
  .faq details[open] .icon::after { transform: scaleY(0); }
  .faq details[open] .icon { background: var(--ink-900); }
  .faq details[open] .icon::before { background: var(--linen-50); }
  .faq .answer { padding-top: 14px; color: var(--stone-600); font-size: 15px; line-height: 1.65; max-width: min(100%, 64ch); }

  @media (max-width: 720px) {
    .ch-room, .ch-room.alt { grid-template-columns: 1fr; }
    .ch-room.alt .ch-room-text { order: 0; }
    .breakfast-layout { grid-template-columns: 1fr; }
    .breakfast-list { grid-template-columns: 1fr; }
    .equip-grid { grid-template-columns: 1fr; }
    .practical .row { grid-template-columns: 1fr; gap: 4px; padding: 18px 0; }
  }
  @media (max-width: 480px) {
    .faq .answer { max-width: 48ch; font-size: 14.5px; }
  }
</style>


<?php
/*
 * Bascule progressive vers vp_sections (page chambres-d-hotes).
 * Cf. home.php pour la mécanique. Position 1 = hero, position 2 = intro.
 * Le widget Disponibilités entre les deux reste en HTML dur (calendar_ribbon
 * partial, non géré par BlockService).
 */
$_v8SectionsByPos = [];
foreach (BlockService::getSections('chambres-d-hotes', $lang) as $_s) {
    $_v8SectionsByPos[(int)$_s['position']] = $_s;
}
$renderV8BlockAt = static function (int $pos, string $expectedType) use ($_v8SectionsByPos): ?string {
    $s = $_v8SectionsByPos[$pos] ?? null;
    if (!$s) return null;
    if ($s['block_type'] !== $expectedType) {
        error_log("V8 chambres: position $pos attendu '$expectedType' mais BDD a '{$s['block_type']}'");
        return null;
    }
    return BlockService::renderBlock($s);
};
?>

<!-- ============ 1 · HERO ============ -->
<?php if ($_heroHtml = $renderV8BlockAt(1, 'hero')): ?>
<?= $_heroHtml ?>
<?php else: ?>
<section class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="page-hero-issue">
        <span data-en="01 · B&amp;B rooms · September → June">01 · Chambres d'hôtes · Septembre → juin</span>
        <span data-en="Reply within the day">Réponse dans la journée</span>
      </div>
      <h1>Une <em>suite</em>,<br/>deux chambres,<br/>un petit-déjeuner.</h1>
    </div>
    <div>
      <p class="lede" data-en="From September to June, we welcome guests at Villa Plaisance in two charming bedrooms, homemade breakfast, shared pool, shaded gardens.">De septembre à juin, nous accueillons nos hôtes à Villa Plaisance dans deux chambres de charme, petit-déjeuner maison, piscine partagée, jardins ombragés.</p>
      <div class="page-hero-ctas">
        <a class="btn" href="<?= LangService::url('contact') ?>"><span data-en="Enquire about a stay">Demander un séjour</span> →</a>
        <a class="btn btn-ghost" href="#infos"><span data-en="Practical info">Infos pratiques</span></a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ DISPONIBILITÉS (ruban saisonnier) ============ -->
<section class="section-tight" style="padding-top: clamp(40px, 5vw, 64px); padding-bottom: clamp(8px, 1vw, 16px);">
  <div class="container-wide">
    <div class="section-label" style="margin-bottom: 18px;">
      <span class="numeral" data-en=" Availability"> Disponibilités</span>
    </div>
    <?php include __DIR__ . '/_partials/calendar_ribbon.php'; ?>
  </div>
</section>

<!-- ============ 2 · INTRO ============ -->
<?php if ($_introHtml = $renderV8BlockAt(2, 'prose')): ?>
<?= $_introHtml ?>
<?php else: ?>
<section class="section">
  <div class="container-wide">
    <div class="two-col">
      <div>
        <div class="section-label">
          <span class="numeral">01 / <span data-en="The B&amp;B">Les chambres d'hôtes</span></span>
        </div>
        <h2 class="h-xl" style="margin:0; max-width: 14ch;">Chambres<br/>d'hôtes <em>B&amp;B</em><br/>à Bédarrides.</h2>
      </div>
      <div>
        <p class="lede" style="margin: 0 0 24px;" data-en="At Villa Plaisance, we welcome guests from September to June in a private suite of two communicating bedrooms, designed for comfort and Provençal authenticity.">À Villa Plaisance, nous accueillons nos hôtes de septembre à juin dans une suite privée formée de deux chambres communicantes, pensées pour le confort et l'authenticité provençale.</p>
        <p class="body-lg" style="margin: 0;" data-en="One entrance, one booking, the two rooms are rented as a single unit, for one to five guests. Homemade breakfast, shared pool, shaded gardens, 15 min from Avignon and 8 min from Châteauneuf-du-Pape.">Une seule entrée, une seule réservation, les deux chambres se louent ensemble, pour une à cinq personnes. Petit-déjeuner maison, piscine partagée, jardins ombragés, à 15 min d'Avignon et 8 min de Châteauneuf-du-Pape.</p>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: color-mix(in oklab, var(--ink-900) 14%, transparent); margin-top: clamp(40px, 4vw, 56px); border-top: var(--hairline); border-bottom: var(--hairline);">
          <div style="background: var(--linen-50); padding: 22px 18px;"><div class="overline" data-en="Capacity">Capacité</div><div class="h-md" style="margin-top: 8px;">1, 5 <span style="font-size: 13px; color: var(--stone-500);" data-en="guests">pers.</span></div></div>
          <div style="background: var(--linen-50); padding: 22px 18px;"><div class="overline" data-en="Breakfast">Petit-déjeuner</div><div class="h-md" style="margin-top: 8px;" data-en="Included">Inclus</div></div>
          <div style="background: var(--linen-50); padding: 22px 18px;"><div class="overline" data-en="Pool">Piscine</div><div class="h-md" style="margin-top: 8px;" data-en="Shared">Partagée</div></div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ HOW IT WORKS · SUITE EXPLAINER ============ -->
<section class="section surface-stone" style="background: var(--linen-100); padding-top: clamp(56px, 7vw, 96px); padding-bottom: clamp(56px, 7vw, 96px);">
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: clamp(32px, 5vw, 80px); align-items: start;">
      <div>
        <div class="section-label">
          <span class="numeral">02 / <span data-en="How it works">Comment ça fonctionne</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;">Une <em>suite</em>,<br/>louée d'un seul<br/>tenant.</h2>
        <p class="body-lg" style="margin: 24px 0 0; max-width: 40ch; color: var(--ink-700);" data-en="The two rooms share a single entrance and one connecting door. They're only rented together, your group has the suite to itself, whether you're one or five.">Les deux chambres partagent une seule entrée et une porte intérieure qui les relie. Elles ne se louent qu'ensemble, votre groupe a la suite pour lui seul, que vous soyez un ou cinq.</p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 18px;">

        <!-- 1-2 pers config -->
        <div style="border: var(--hairline); background: var(--linen-50); padding: 26px 28px;">
          <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 16px; border-bottom: var(--hairline); padding-bottom: 14px; margin-bottom: 18px;">
            <span style="font-family: var(--font-display); font-style: italic; font-size: clamp(22px, 2vw, 28px); color: var(--ink-900);" data-en="Travelling as a couple">À deux</span>
            <span class="numeral label-terra">1 – 2 <span data-en="guests">pers.</span></span>
          </div>
          <p class="body" style="margin: 0 0 14px;" data-en="Tell us how you'd like the bedding made up, we prepare one of the rooms, the other stays accessible as a reading lounge.">Dites-nous comment vous préférez le couchage, nous préparons une des chambres, l'autre reste accessible en salon de lecture.</p>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
            <li style="display: grid; grid-template-columns: 8px 1fr; gap: 12px; align-items: baseline;">
              <span style="width: 6px; height: 6px; background: var(--sage-500); border-radius: 50%; align-self: center;"></span>
              <span class="body" data-en="Double bed prepared <span style='color: var(--stone-500);'>(Chambre Verte)</span>">Lit double préparé <span style="color: var(--stone-500);">(Chambre Verte)</span></span>
            </li>
            <li style="display: grid; grid-template-columns: 8px 1fr; gap: 12px; align-items: baseline;">
              <span style="width: 6px; height: 6px; background: var(--sage-500); border-radius: 50%; align-self: center;"></span>
              <span class="body" data-en="Two singles prepared <span style='color: var(--stone-500);'>(Chambre Bleue)</span>">Deux lits simples préparés <span style="color: var(--stone-500);">(Chambre Bleue)</span></span>
            </li>
            <li style="display: grid; grid-template-columns: 8px 1fr; gap: 12px; align-items: baseline;">
              <span style="width: 6px; height: 6px; background: var(--terra-500); border-radius: 50%; align-self: center;"></span>
              <span class="body" data-en="Both rooms prepared, a bedroom each <span style='color: var(--terra-600); font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.08em; margin-left: 6px;'>OPTION PAYANTE</span>">Les deux chambres préparées, une chambre chacun <span style="color: var(--terra-600); font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.08em; margin-left: 6px;">OPTION PAYANTE</span></span>
            </li>
          </ul>
        </div>

        <!-- 3-5 pers config -->
        <div style="border: var(--hairline); background: var(--linen-50); padding: 26px 28px;">
          <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 16px; border-bottom: var(--hairline); padding-bottom: 14px; margin-bottom: 18px;">
            <span style="font-family: var(--font-display); font-style: italic; font-size: clamp(22px, 2vw, 28px); color: var(--ink-900);" data-en="As a family or small group">En famille, en petit groupe</span>
            <span class="numeral label-terra">3 – 5 <span data-en="guests">pers.</span></span>
          </div>
          <p class="body" style="margin: 0;" data-en="Both rooms are prepared. The Bleue sofa bed opens for a fifth guest. The connecting door stays where you want it, open, closed, locked from your side.">Les deux chambres sont préparées. Le clic-clac de la Bleue s'ouvre pour une cinquième personne. La porte intérieure reste comme vous voulez, ouverte, fermée, verrouillée de votre côté.</p>
        </div>

      </div>
    </div>
  </div>
</section>
<!-- ============ 3 · Préambule "Chacune sa lumière" (HTML dur, éditorial unique) ============ -->
<section class="section-tight" style="padding-top: clamp(48px, 6vw, 88px);">
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: clamp(32px, 5vw, 80px); align-items: end;">
      <div>
        <div class="section-label">
          <span class="numeral">03 / <span data-en="The two rooms of the suite">Les deux chambres de la suite</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;" data-en="Each with its/own light.">Chacune <em>sa</em><br/>lumière.</h2>
      </div>
      <p class="body-lg" style="margin: 0; max-width: 44ch;" data-en="A single door opens onto both rooms, and onto a shared corridor with a private bathroom. Their characters differ; their guests are the same.">Une seule porte ouvre sur les deux chambres, et sur leur couloir partagé avec salle de bain. Leur caractère diffère ; leurs hôtes sont les mêmes.</p>
    </div>
  </div>
</section>

<!-- ============ 4 · CHAMBRES VERTE + BLEUE (BDD vp_pieces or fallback HTML) ============ -->
<?php if ($_chambresHtml = $renderV8BlockAt(4, 'cartes')): ?>
<?= $_chambresHtml ?>
<?php else: ?>
<section class="section-tight">
  <div class="container-wide">
    <div class="ch-room">
      <div class="ch-room-text">
        <div class="ch-room-num">
          <span>I · <span data-en="First room of the suite">Première chambre de la suite</span></span>
          <span data-en="Garden side">Côté jardin</span>
        </div>
        <p class="ch-room-tagline" data-en="Large bed, garden view">GRAND LIT, VUE JARDIN</p>
        <h2 class="h-xl" style="margin: 0 0 24px;"><em>Chambre Verte</em></h2>
        <p class="body-lg" style="margin: 0 0 16px;" data-en="A bright bedroom with a large 160×200 bed, opening onto the garden and the olive trees. A cocooning, sober and quiet space. Reversible air-conditioning, TV.">Chambre lumineuse avec un grand lit 160×200, donnant sur le jardin et les oliviers. Espace cocooning, sobriété et calme. Climatisation réversible, TV.</p>

        <div class="ch-pills">
          <span class="pill">Lit 160 × 200</span>
          <span class="pill" data-en="Garden view">Vue jardin</span>
          <span class="pill" data-en="Reversible A/C">Climatisation réversible</span>
          <span class="pill">TV</span>
          <span class="pill">Wifi</span>
        </div>
      </div>
      <div class="ch-room-images">
        <div class="big" style="background-image: url('/uploads/villa-plaisance-chambre-verte-01.webp')"></div>
        <div class="sm" style="background-image: url('/uploads/villa-plaisance-chambre-verte-02.webp')"></div>
        <div class="sm" style="background-image: url('/uploads/villa-plaisance-chambre-verte-03.webp')"></div>
      </div>
    </div>
  </div>
</section>

<section class="section" style="background: var(--linen-100);">
  <div class="container-wide">
    <div class="ch-room alt">
      <div class="ch-room-images">
        <div class="big" style="background-image: url('/uploads/villa-plaisance-chambre-bleue-01.webp')"></div>
        <div class="sm" style="background-image: url('/uploads/villa-plaisance-chambre-bleue-02.webp')"></div>
        <div class="sm" style="background-image: url('/uploads/villa-plaisance-chambre-bleue-03.webp')"></div>
      </div>
      <div class="ch-room-text">
        <div class="ch-room-num">
          <span>II · <span data-en="Second room of the suite">Seconde chambre de la suite</span></span>
          <span data-en="Bedroom &amp; little reading lounge">Chambre / mini salon de lecture</span>
        </div>
        <p class="ch-room-tagline" data-en="Library, ideal for families">BIBLIOTHÈQUE, IDÉALE FAMILLE</p>
        <h2 class="h-xl" style="margin: 0 0 24px;"><em>Chambre Bleue</em></h2>
        <p class="body-lg" style="margin: 0 0 16px;" data-en="Two 90×200 single beds, joinable into a large 180. A clic-clac sofa bed for a third guest. A 300-book library. The room for readers and families.">Deux lits 90×200 jumelables en grand lit 180. Un clic-clac pour une troisième personne. Une bibliothèque de 300 livres. La chambre des lecteurs et des familles.</p>

        <div class="ch-pills">
          <span class="pill">2 lits 90 × 200 <span style="color: var(--stone-500); margin-left: 4px;" data-en="joinable">jumelables</span></span>
          <span class="pill" data-en="Sofa bed (1 guest)">Clic-clac (1 pers.)</span>
          <span class="pill" data-en="300-book library">Bibliothèque 300 livres</span>
          <span class="pill" data-en="Reversible A/C">Climatisation réversible</span>
          <span class="pill">Wifi</span>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 5 · SALLE DE BAIN (BDD prose or fallback HTML) ============ -->
<?php if ($_sdbHtml = $renderV8BlockAt(5, 'prose')): ?>
<?= $_sdbHtml ?>
<?php else: ?>
<section class="section">
  <div class="container-wide">
    <div class="two-col reverse" style="align-items: center;">
      <div style="aspect-ratio: 4/5; background: center/cover url('/uploads/villa-plaisance-salle-de-bain-chambre-hotes-01.webp')"></div>
      <div>
        <div class="section-label">
          <span class="numeral">04 / <span data-en="Bath">Salle de bain</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0 0 24px; max-width: 16ch;" data-en="A private bath/in each room.">Salle de bain<br/><em>privative</em><br/>dans chaque chambre.</h2>
        <p class="body-lg" style="margin: 0 0 16px; max-width: 50ch;" data-en="Each bedroom has its own private bathroom, no shared corridor, no waiting at the door.">Chaque chambre dispose de sa propre salle de bain privative, pas de couloir partagé, pas d'attente derrière la porte.</p>
        <p class="body" style="margin: 0; max-width: 50ch;" data-en="Organic toiletries, generous towels, a walk-in shower or a tub, depending on the room. Hairdryer, mirror lighting, and the small things you'd want to find.">Produits de toilette bio, serviettes généreuses, douche à l'italienne ou baignoire selon la chambre. Sèche-cheveux, éclairage miroir, et les petites attentions qu'on aime trouver.</p>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 6 · PETIT-DÉJEUNER ============ -->
<?php if ($_breakfastHtml = $renderV8BlockAt(6, 'petit-dejeuner')): ?>
<?= $_breakfastHtml ?>
<?php else: ?>
<section class="section surface-sage" style="background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));">
  <div class="container-wide">
    <div class="breakfast-layout">
      <div class="breakfast-img" style="background-image: url('/uploads/villa-plaisance-petit-dejeuner-brioche-01.webp')"></div>
      <div>
        <div class="section-label">
          <span class="numeral">05 / <span data-en="Breakfast">Petit-déjeuner</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0 0 20px; max-width: 18ch;">Petit-déjeuner<br/><em>maison</em>, inclus.</h2>
        <p class="body-lg" style="margin: 0 0 12px; max-width: 50ch;" data-en="Every morning, from 7:30 to 10 am, on the terrace or in the veranda depending on the season.">Chaque matin, de 7h30 à 10h, en terrasse ou en véranda selon la saison.</p>

        <div class="breakfast-list">
          <div class="item" data-en="House-made jams (fig, apricot, lavender-honey)">Confitures artisanales (figues, abricots, lavande-miel)</div>
          <div class="item" data-en="Pastries">Viennoiseries</div>
          <div class="item" data-en="Bread from the baker">Pain de boulanger</div>
          <div class="item" data-en="Provençal cheeses">Fromages provençaux</div>
          <div class="item" data-en="Regional charcuterie">Charcuterie régionale</div>
          <div class="item" data-en="Fresh seasonal fruit">Fruits frais de saison</div>
          <div class="item" data-en="Fresh-squeezed orange juice">Jus d'orange pressé</div>
          <div class="item" data-en="Coffee, tea, organic infusions">Café, thé, tisanes bio</div>
          <div class="item" data-en="Local honey">Miel</div>
          <div class="item" data-en="Homemade yoghurt">Yaourt maison</div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 7 · ÉQUIPEMENTS ============ -->
<?php if ($_equipHtml = $renderV8BlockAt(7, 'liste')): ?>
<?= $_equipHtml ?>
<?php else: ?>
<section class="section">
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: clamp(32px, 5vw, 96px); align-items: end; margin-bottom: clamp(40px, 5vw, 64px);">
      <div>
        <div class="section-label">
          <span class="numeral">06 / <span data-en="Included">Inclus</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 16ch;">Équipements<br/>&amp; <em>services</em> inclus.</h2>
      </div>
      <p class="body-lg" style="margin: 0; max-width: 44ch;" data-en="The list of what we hand over with the key, nothing fancy, just what makes a stay easy.">Ce qu'on remet avec la clé, rien d'extravagant, juste ce qui rend un séjour simple.</p>
    </div>

    <div class="equip-grid">
      <div class="equip"><span class="n">01</span><span class="l" data-en="Premium bedding (100% percale cotton sheets)">Literie premium (draps 100 % coton percale)</span></div>
      <div class="equip"><span class="n">02</span><span class="l" data-en="Private bathroom with organic toiletries">Salle de bain privative avec produits de toilette bio</span></div>
      <div class="equip"><span class="n">03</span><span class="l" data-en="Reversible A/C in every bedroom">Climatisation réversible dans chaque chambre</span></div>
      <div class="equip"><span class="n">04</span><span class="l" data-en="Free high-speed wifi">Wifi haut débit gratuit</span></div>
      <div class="equip"><span class="n">05</span><span class="l" data-en="Flat-screen television">Télévision écran plat</span></div>
      <div class="equip"><span class="n">06</span><span class="l" data-en="Shared 12 × 6 m pool (May to October)">Piscine partagée 12 × 6 m (mai à octobre)</span></div>
      <div class="equip"><span class="n">07</span><span class="l" data-en="Provençal garden and shaded terraces">Jardin provençal et terrasses ombragées</span></div>
      <div class="equip"><span class="n">08</span><span class="l" data-en="Free private parking">Parking privé gratuit</span></div>
      <div class="equip"><span class="n">09</span><span class="l" data-en="Self-check-in (key box)">Arrivée autonome (boîte à clé)</span></div>
      <div class="equip"><span class="n">10</span><span class="l" data-en="Personalised local advice and recommendations">Conseils et recommandations locales personnalisés</span></div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 8 · INFOS PRATIQUES ============ -->
<?php if ($_infosHtml = $renderV8BlockAt(8, 'tableau')): ?>
<?= $_infosHtml ?>
<?php else: ?>
<section class="section surface-stone" style="background: var(--linen-100);" id="infos">
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: clamp(32px, 5vw, 96px); align-items: end; margin-bottom: clamp(40px, 5vw, 64px);">
      <div>
        <div class="section-label">
          <span class="numeral">07 / <span data-en="Practical info">Infos pratiques</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;">Tout ce qu'il faut <em>savoir</em>.</h2>
      </div>
      <p class="body-lg" style="margin: 0; max-width: 44ch;" data-en="Dates, capacity, arrival times, what's included, the essentials at a glance.">Dates, capacité, horaires, ce qui est inclus, l'essentiel d'un coup d'œil.</p>
    </div>

    <div class="practical">
      <div class="row">
        <div class="k" data-en="Period">Période</div>
        <div class="v" data-en="September to June"><strong>De septembre à juin</strong></div>
      </div>
      <div class="row">
        <div class="k" data-en="Arrival">Arrivée</div>
        <div class="v" data-en="From 5 pm">À partir de 17h</div>
      </div>
      <div class="row">
        <div class="k" data-en="Departure">Départ</div>
        <div class="v" data-en="Before 11 am">Avant 11h</div>
      </div>
      <div class="row">
        <div class="k" data-en="Minimum stay">Séjour minimum</div>
        <div class="v" data-en="2 nights (high season: 3 nights)"><strong>2 nuits</strong> (haute saison : 3 nuits)</div>
      </div>
      <div class="row">
        <div class="k" data-en="Capacity">Capacité</div>
        <div class="v" data-en="1 to 5 guests (connecting suite)"><strong>1 à 5 personnes</strong> (suite communicante)</div>
      </div>
      <div class="row">
        <div class="k" data-en="Breakfast">Petit-déjeuner</div>
        <div class="v" data-en="Included · served 7:30 → 10 am">Inclus · servi de 7h30 à 10h</div>
      </div>
      <div class="row">
        <div class="k" data-en="Pool">Piscine</div>
        <div class="v" data-en="Shared · 12 × 6 m · May to October">Partagée · 12 × 6 m · mai à octobre</div>
      </div>
      <div class="row">
        <div class="k" data-en="Animals">Animaux</div>
        <div class="v" data-en="Not accepted">Non acceptés</div>
      </div>
      <div class="row">
        <div class="k" data-en="Smoking">Fumeur</div>
        <div class="v" data-en="Non-smoking">Non-fumeur</div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 9 · FAQ ============ -->
<?php if ($_faqHtml = $renderV8BlockAt(9, 'faq')): ?>
<?= $_faqHtml ?>
<?php else: ?>
<section class="section">
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: clamp(32px, 6vw, 96px); align-items: start;">
      <div>
        <div class="section-label">
          <span class="numeral">08 / <span data-en="Frequently asked">Questions fréquentes</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;">Les <em>questions</em><br/>qui reviennent.</h2>
      </div>
      <div class="faq">
        <details open>
          <summary><span data-en="Why are the rooms only rented together?">Pourquoi les chambres ne se louent-elles qu'ensemble ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="The two rooms communicate through an inner door and share a single entrance. Renting them separately would mean two parties crossing each other on the same threshold, so we don't. Your group has the suite to itself, whether you're one or five.">Les deux chambres communiquent par une porte intérieure et partagent une seule entrée. Les louer séparément reviendrait à faire se croiser deux groupes sur le même seuil, nous préférons éviter. Votre groupe a la suite pour lui seul, que vous soyez un ou cinq.</div>
        </details>
        <details>
          <summary><span data-en="As a couple, which room will be prepared?">À deux, quelle chambre sera préparée ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Whichever you prefer, tell us when you book. The Chambre Verte (double 160×200) or the Chambre Bleue (two singles, joinable into a 180). The other room stays accessible as a reading lounge. If you'd like both rooms prepared so you can each have your own, this is available as a paid option.">Celle que vous préférez, dites-le nous à la réservation. La Chambre Verte (lit double 160×200) ou la Chambre Bleue (deux lits simples, jumelables en 180). L'autre reste accessible en salon de lecture. Si vous souhaitez que les deux chambres soient préparées pour avoir chacun la sienne, cela est disponible en option payante.</div>
        </details>
        <details>
          <summary><span data-en="Is breakfast included?">Le petit-déjeuner est-il inclus ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Yes, homemade breakfast is included in the B&amp;B rate. Served from 7:30 to 10 am on the terrace (weather permitting) or in the veranda: artisanal jams, pastries, fresh bread, Provençal cheeses, seasonal fruit, fresh orange juice, coffee, tea and organic infusions.">Oui, le petit-déjeuner maison est inclus dans le tarif B&amp;B. Il est servi de 7h30 à 10h en terrasse (temps le permettant) ou en véranda, avec confitures artisanales, viennoiseries, pain frais, fromages provençaux, fruits de saison, jus d'orange pressé, café, thé et tisanes bio.</div>
        </details>
        <details>
          <summary><span data-en="Are the rooms air-conditioned?">Les chambres sont-elles climatisées ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Yes, both rooms (Verte and Bleue) have reversible air-conditioning and free wifi.">Oui, les deux chambres (Verte et Bleue) sont équipées de climatisation réversible et du wifi gratuit.</div>
        </details>
        <details>
          <summary><span data-en="Can we bring children?">Peut-on accueillir des enfants en chambres d'hôtes ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Yes, the Chambre Bleue has a sofa bed that can accommodate an extra guest, which makes it ideal for families.">Oui, la Chambre Bleue dispose d'un clic-clac pouvant accueillir une personne supplémentaire, ce qui en fait une chambre idéale pour les familles.</div>
        </details>
        <details>
          <summary><span data-en="When are the B&amp;B rooms open?">À quelle période les chambres d'hôtes sont-elles disponibles ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="The B&amp;B rooms are open from September to June. In July and August, the villa is rented exclusively as a whole house.">Les chambres d'hôtes sont ouvertes de septembre à juin. En juillet et août, la villa se loue en exclusivité.</div>
        </details>
        <details>
          <summary><span data-en="How do I get to Villa Plaisance?">Comment se rendre à Villa Plaisance ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Villa Plaisance is 15 min from Avignon via the RN7 or the A7 motorway (Avignon-Nord exit). By train: Avignon-Centre or Avignon TGV station, then taxi (15 min). GPS coordinates are sent at the time of booking confirmation.">Villa Plaisance est à 15 min d'Avignon par la RN7 ou l'autoroute A7 (sortie Avignon-Nord). En train : gare Avignon-Centre ou Avignon TGV avec taxi (15 min). Les coordonnées GPS sont transmises lors de la confirmation de réservation.</div>
        </details>
        <details>
          <summary><span data-en="Is there parking?">Y a-t-il un parking ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Yes, free private parking is available on site for all guests.">Oui, un parking privé gratuit est disponible sur place pour tous les hôtes.</div>
        </details>
        <details>
          <summary><span data-en="Is the pool open all B&amp;B season?">La piscine est-elle disponible toute la saison B&amp;B ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="The shared 12 × 6 m pool is open from May to October depending on weather. It's accessible to all guests in residence. Sunbeds and parasols are provided.">La piscine partagée (12 × 6 m) est disponible de mai à octobre selon conditions météo. Elle est accessible à tous les hôtes présents. Transats et parasols sont à disposition.</div>
        </details>
        <details>
          <summary><span data-en="How do I book a B&amp;B room?">Comment réserver les chambres d'hôtes ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Send us your dates and the number of guests through the contact form, we reply within the day. There's no online booking engine; every stay is confirmed by hand.">Envoyez-nous vos dates et le nombre de personnes via le formulaire de contact, nous vous répondons dans la journée. Pas de moteur de réservation en ligne, chaque séjour se confirme à la main.</div>
        </details>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ 10 · FOOTER CTA ============ -->
