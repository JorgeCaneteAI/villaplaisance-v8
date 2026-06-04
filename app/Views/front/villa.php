<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Maison d'hôtes (villa entière juillet-août).
 * Portée depuis le proto Claude design (maison-hotes.html).
 * Layout : front-proto (Cormorant Garamond + style-proto.css).
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  /* Hero with full image + overlay text */
  .mh-hero {
    position: relative;
    min-height: clamp(560px, 86vh, 840px);
    display: grid; grid-template-rows: 1fr;
    overflow: hidden;
    background: var(--linen-100);
  }
  .mh-hero-image { position: absolute; inset: 0; background-size: cover; background-position: center; }
  .mh-hero-image::after {
    content: ""; position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(31,28,22,0.10) 0%, rgba(31,28,22,0.10) 50%, rgba(31,28,22,0.65) 100%);
  }
  .mh-hero-content {
    position: relative; z-index: 2;
    padding: clamp(40px, 8vw, 96px) var(--gutter);
    max-width: var(--container-wide);
    margin: 0 auto;
    width: 100%;
    display: grid; grid-template-rows: 1fr auto; gap: 24px;
    min-height: clamp(560px, 86vh, 840px);
    color: var(--linen-50);
  }
  .mh-hero h1 {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(56px, 9.5vw, 148px); line-height: 0.92; letter-spacing: -0.025em;
    color: var(--linen-50);
  }
  .mh-hero h1 em { font-style: italic; color: var(--sage-200); }
  .mh-hero-overline {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em;
    color: rgba(251,247,238,0.78); text-transform: uppercase;
  }
  .mh-hero-bottom {
    display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
    gap: clamp(24px, 5vw, 80px);
    align-items: end;
  }
  .mh-hero-tag {
    font-family: var(--font-display); font-size: clamp(18px, 1.6vw, 22px);
    color: rgba(251,247,238,0.92); line-height: 1.45; max-width: 50ch; margin: 0 0 20px;
  }
  @media (max-width: 960px) { .mh-hero-bottom { grid-template-columns: 1fr; } }

  /* Room grid */
  .room-cards {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: clamp(24px, 3vw, 48px) clamp(24px, 3vw, 48px);
  }
  .room-card-x {
    display: flex; flex-direction: column; gap: 18px;
  }
  .room-card-x .img {
    aspect-ratio: 4 / 3; background-size: cover; background-position: center;
    background-color: var(--linen-200);
    transition: transform .6s cubic-bezier(.2,.6,.2,1);
  }
  .room-card-x:hover .img { transform: scale(1.015); }
  .room-card-x .head {
    display: flex; justify-content: space-between; align-items: baseline; gap: 16px;
    border-top: var(--hairline); padding-top: 14px;
  }
  .room-card-x .name {
    font-family: var(--font-display); font-size: clamp(28px, 2.6vw, 38px); color: var(--ink-900);
    letter-spacing: -0.015em;
  }
  .room-card-x .name em { font-style: italic; }
  .room-card-x .tagline {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    color: var(--terra-500); text-transform: uppercase;
    text-align: right; max-width: 22ch; line-height: 1.4;
  }
  .room-card-x .desc { margin: 0; font-size: 15.5px; color: var(--ink-700); line-height: 1.6; max-width: 60ch; }
  .room-card-x .pills {
    display: flex; flex-wrap: wrap; gap: 6px;
    margin-top: 4px;
  }
  .pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px;
    border: var(--hairline);
    font-size: 12.5px; color: var(--ink-700);
    background: transparent;
    letter-spacing: 0;
  }
  .pill.solid { background: var(--ink-900); color: var(--linen-50); border-color: var(--ink-900); }
  @media (max-width: 720px) { .room-cards { grid-template-columns: 1fr; } }

  /* Interior two-up */
  .interior {
    display: grid; grid-template-columns: 1fr 1fr; gap: clamp(24px, 3vw, 40px);
  }
  .interior > div {
    display: flex; flex-direction: column; gap: 18px;
  }
  /* <img class="img"> : aspect 3/2 iso source, object-fit cover. Le
     background-size n'a plus d'effet depuis la migration <div> → <img>. */
  .interior img.img,
  .interior .img {
    aspect-ratio: 3/2;
    width: 100%; height: auto;
    object-fit: cover; object-position: center;
    display: block;
    background-size: cover; background-position: center; /* legacy fallback */
  }
  @media (max-width: 720px) { .interior { grid-template-columns: 1fr; } }

  /* Pool section */
  .pool-block {
    display: grid; grid-template-columns: 1.4fr 1fr; gap: clamp(32px, 5vw, 80px);
    margin-top: clamp(32px, 4vw, 56px);
    align-items: start;
  }
  .pool-features {
    list-style: none; padding: 0; margin: 0;
    border-top: var(--hairline);
  }
  .pool-features li {
    padding: 16px 0; border-bottom: var(--hairline);
    display: grid; grid-template-columns: 18px 1fr; gap: 16px;
    font-size: 15px; color: var(--ink-700);
    align-items: baseline;
  }
  .pool-features li::before {
    content: "";
    width: 8px; height: 8px; background: var(--sage-500); border-radius: 50%;
    align-self: center;
  }
  @media (max-width: 720px) { .pool-block { grid-template-columns: 1fr; } }

  /* Espaces list */
  .espaces {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0 clamp(32px, 4vw, 64px);
  }
  .espaces dl { margin: 0; }
  .espaces .row {
    display: grid; grid-template-columns: 160px 1fr; gap: 16px;
    padding: 18px 0;
    border-bottom: var(--hairline);
    align-items: baseline;
  }
  .espaces .row:first-child { border-top: var(--hairline); }
  .espaces dt {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em;
    color: var(--stone-500); text-transform: uppercase;
  }
  .espaces dd {
    margin: 0; font-size: 15px; color: var(--ink-700); line-height: 1.55;
  }
  @media (max-width: 720px) {
    .espaces { grid-template-columns: 1fr; }
    .espaces .row { grid-template-columns: 1fr; gap: 4px; }
  }

  /* Practical info table */
  .practical {
    border-top: var(--hairline-strong);
  }
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
  .practical .row .v {
    font-family: var(--font-sans); font-size: 15.5px; color: var(--ink-700); line-height: 1.55;
  }
  .practical .row .v strong { font-weight: 500; color: var(--ink-900); }
  @media (max-width: 720px) {
    .practical .row { grid-template-columns: 1fr; gap: 4px; padding: 18px 0; }
  }

  /* FAQ, adapted from contact.html */
  .faq { border-top: var(--hairline); }
  .faq details { border-bottom: var(--hairline); padding: 20px 0; }
  .faq summary {
    list-style: none; cursor: pointer;
    display: grid; grid-template-columns: 1fr 24px; align-items: center; gap: 16px;
    font-family: var(--font-display); font-size: clamp(20px, 1.8vw, 26px); color: var(--ink-900); letter-spacing: -0.005em;
  }
  .faq summary::-webkit-details-marker { display: none; }
  .faq .icon {
    width: 24px; height: 24px;
    border: 1px solid var(--ink-900);
    display: grid; place-items: center;
    position: relative;
    transition: background .2s;
  }
  .faq .icon::before, .faq .icon::after {
    content: ""; position: absolute; background: var(--ink-900);
  }
  .faq .icon::before { width: 10px; height: 1px; }
  .faq .icon::after { width: 1px; height: 10px; transition: transform .2s; }
  .faq details[open] .icon::after { transform: scaleY(0); }
  .faq details[open] .icon { background: var(--ink-900); }
  .faq details[open] .icon::before { background: var(--linen-50); }
  .faq .answer { padding-top: 14px; color: var(--stone-600); font-size: 15px; line-height: 1.65; max-width: 64ch; }

  /* Room editorial layout (calqué sur chambres.php — pattern B&B big+sm).
     Image 1 grande en haut + 2 vignettes en bas, alterné gauche/droite. */
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
  /* Aspect 3/2 partout = iso source 1800×1200, zéro crop vertical. */
  .ch-room-images > img.big {
    grid-column: 1 / -1; aspect-ratio: 3/2;
    width: 100%; height: auto; object-fit: cover; object-position: center; display: block;
  }
  .ch-room-images > img.sm {
    aspect-ratio: 3/2;
    width: 100%; height: auto; object-fit: cover; object-position: center; display: block;
  }
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
  @media (max-width: 960px) {
    .ch-room, .ch-room.alt { grid-template-columns: 1fr; }
    .ch-room.alt .ch-room-text { order: 0; }
  }
</style>


<?php
/*
 * Bascule progressive vers vp_sections (page location-villa-provence).
 * Cf. home.php / chambres.php pour la mécanique.
 */
$_v8SectionsByPos = [];
foreach (BlockService::getSections('location-villa-provence', $lang) as $_s) {
    $_v8SectionsByPos[(int)$_s['position']] = $_s;
}
$renderV8BlockAt = static function (int $pos, string $expectedType) use ($_v8SectionsByPos): ?string {
    $s = $_v8SectionsByPos[$pos] ?? null;
    if (!$s) return null;
    if ($s['block_type'] !== $expectedType) {
        error_log("V8 villa: position $pos attendu '$expectedType' mais BDD a '{$s['block_type']}'");
        return null;
    }
    return BlockService::renderBlock($s);
};
?>

<!-- HERO -->
<?php if ($_heroHtml = $renderV8BlockAt(1, 'hero')): ?>
<?= $_heroHtml ?>
<?php else: ?>
<section class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="page-hero-issue">
        <span data-en="02 · Maison d'hôtes · July – August">02 · Maison d'hôtes · Juillet – août</span>
        <span data-en="Saturday to Saturday">Du samedi au samedi</span>
      </div>
      <h1>La villa <em>entière</em>,<br/>en toute autonomie.</h1>
    </div>
    <div>
      <p class="lede" data-en="In July and August, Villa Plaisance opens its doors as a whole-house rental, four bedrooms, ten guests, a private pool, a fitted kitchen and terraces facing the vines.">En juillet et en août, Villa Plaisance ouvre ses portes en location complète, quatre chambres, dix personnes, piscine privée exclusive, cuisine équipée et terrasses face aux vignes.</p>
      <div class="page-hero-ctas">
        <a class="btn" href="<?= LangService::url('contact') ?>"><span data-en="Enquire about a week">Demander une semaine</span> →</a>
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

<!-- KEY STATS STRIP -->
<?php if ($_statsHtml = $renderV8BlockAt(2, 'stats')): ?>
<?= $_statsHtml ?>
<?php else: ?>
<div style="border-bottom: var(--hairline);">
  <div class="container-wide" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; border-left: var(--hairline); border-right: var(--hairline);">
    <div style="padding: 26px 24px; border-right: var(--hairline);">
      <div class="overline" data-en="Capacity">Capacité</div>
      <div class="h-md" style="margin-top: 8px;">10 <span style="font-size: 14px; color: var(--stone-500);" data-en="guests">pers.</span></div>
    </div>
    <div style="padding: 26px 24px; border-right: var(--hairline);">
      <div class="overline" data-en="Bedrooms">Chambres</div>
      <div class="h-md" style="margin-top: 8px;">4</div>
    </div>
    <div style="padding: 26px 24px; border-right: var(--hairline);">
      <div class="overline" data-en="Private pool">Piscine privée</div>
      <div class="h-md" style="margin-top: 8px;">12 × 6 m</div>
    </div>
    <div style="padding: 26px 24px;">
      <div class="overline" data-en="Minimum stay">Séjour minimum</div>
      <div class="h-md" style="margin-top: 8px;">7 <span style="font-size: 14px; color: var(--stone-500);" data-en="nights · Sat → Sat">nuits · sam → sam</span></div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ============ 4 CHAMBRES VILLA (BDD vp_pieces or fallback HTML) ============ -->
<?php if ($_chambresVillaHtml = $renderV8BlockAt(3, 'cartes')): ?>
<?= $_chambresVillaHtml ?>
<?php else: ?>
<section class="section" id="chambres">
  <div class="container-wide">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap: 32px; flex-wrap: wrap;">
      <div>
        <div class="section-label">
          <span class="numeral">01 / <span data-en="The bedrooms">Les chambres</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 18ch;" data-en="Four bedrooms,<br/>four <em>worlds</em>.">Quatre chambres,<br/>quatre <em>univers</em>.</h2>
      </div>
      <p class="body-lg" style="max-width: 38ch; margin: 0;" data-en="Each room has a personality of its own, books, an arch, the garden, the seventies. None of them lacks for shade or light.">Chaque chambre a sa personnalité, les livres, une arche, le jardin, les seventies. Aucune ne manque ni d'ombre ni de lumière.</p>
    </div>
  </div>
</section>

<!-- VERTE -->
<section class="section">
  <div class="container-wide">
    <div class="ch-room">
      <div class="ch-room-text">
        <div class="ch-room-num">
          <span>I · <span data-en="First bedroom">Première chambre</span></span>
          <span data-en="Garden side · ground floor">Côté jardin · rez-de-chaussée</span>
        </div>
        <p class="ch-room-tagline" data-en="Large bed, garden view">GRAND LIT, VUE JARDIN</p>
        <h2 class="h-xl" style="margin: 0 0 24px;"><em>Chambre Verte</em></h2>
        <p class="body-lg" style="margin: 0 0 16px;" data-en="King-size bed 160×200, view of the garden and olive trees. Reversible air-conditioning, TV. On the ground floor.">Lit 160×200, vue sur le jardin et les oliviers. Climatisation réversible, TV. Au rez-de-chaussée.</p>
        <div class="ch-pills">
          <span class="pill">Lit 160 × 200</span>
          <span class="pill" data-en="Garden view">Vue jardin</span>
          <span class="pill" data-en="Reversible A/C">Climatisation réversible</span>
          <span class="pill">TV</span>
          <span class="pill">Wifi</span>
        </div>
      </div>
      <div class="ch-room-images">
        <?= ImageService::imgFromBg('villa-plaisance-chambre-verte-01.webp', 'big') ?>
        <?= ImageService::imgFromBg('villa-plaisance-chambre-verte-02.webp', 'sm') ?>
        <?= ImageService::imgFromBg('villa-plaisance-chambre-verte-03.webp', 'sm') ?>
      </div>
    </div>
  </div>
</section>

<!-- BLEUE -->
<section class="section" style="background: var(--linen-100);">
  <div class="container-wide">
    <div class="ch-room alt">
      <div class="ch-room-images">
        <?= ImageService::imgFromBg('villa-plaisance-chambre-bleue-01.webp', 'big') ?>
        <?= ImageService::imgFromBg('villa-plaisance-chambre-bleue-02.webp', 'sm') ?>
        <?= ImageService::imgFromBg('villa-plaisance-chambre-bleue-03.webp', 'sm') ?>
      </div>
      <div class="ch-room-text">
        <div class="ch-room-num">
          <span>II · <span data-en="Second bedroom">Deuxième chambre</span></span>
          <span data-en="Library &amp; family-friendly">Bibliothèque · famille</span>
        </div>
        <p class="ch-room-tagline" data-en="Library, 300 books">BIBLIOTHÈQUE · 300 LIVRES</p>
        <h2 class="h-xl" style="margin: 0 0 24px;"><em>Chambre Bleue</em></h2>
        <p class="body-lg" style="margin: 0 0 16px;" data-en="Two 90×200 single beds, joinable as a double. A clic-clac sofa bed and a 300-book library. Reversible air-conditioning.">Deux lits 90×200 jumelables, clic-clac, bibliothèque de 300 livres. Climatisation réversible.</p>
        <div class="ch-pills">
          <span class="pill">2 lits 90 × 200 <span style="color: var(--stone-500); margin-left: 4px;" data-en="joinable">jumelables</span></span>
          <span class="pill">Clic-clac</span>
          <span class="pill" data-en="300-book library">Bibliothèque 300 livres</span>
          <span class="pill" data-en="A/C">Climatisation</span>
          <span class="pill">Wifi</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ARCHE -->
<section class="section">
  <div class="container-wide">
    <div class="ch-room">
      <div class="ch-room-text">
        <div class="ch-room-num">
          <span>III · <span data-en="Third bedroom">Troisième chambre</span></span>
          <span data-en="Garden access · ground floor">Accès jardin · rez-de-chaussée</span>
        </div>
        <p class="ch-room-tagline" data-en="Midnight-blue arch, floor-to-ceiling libraries">ARCHE BLEU NUIT · BIBLIOTHÈQUES SOL-PLAFOND</p>
        <h2 class="h-xl" style="margin: 0 0 24px;"><em>Chambre Arche</em></h2>
        <p class="body-lg" style="margin: 0 0 16px;" data-en="A 140×180 bed beneath a great midnight-blue painted arch. Floor-to-ceiling libraries on either side. On the ground floor, with a view onto the garden.">Lit 140×180 sous une grande arche peinte en bleu nuit. Bibliothèques sol-plafond des deux côtés. Au rez-de-chaussée, avec vue sur le jardin.</p>
        <div class="ch-pills">
          <span class="pill">Lit 140 × 180</span>
          <span class="pill" data-en="Midnight-blue arch">Arche bleu nuit</span>
          <span class="pill" data-en="Floor-to-ceiling libraries">Bibliothèques sol-plafond</span>
          <span class="pill" data-en="Garden view">Vue jardin</span>
          <span class="pill" data-en="A/C">Climatisation</span>
        </div>
      </div>
      <div class="ch-room-images">
        <?= ImageService::imgFromBg('villa-plaisance-chambre-arche-01.webp', 'big') ?>
        <?= ImageService::imgFromBg('villa-plaisance-chambre-arche-02.webp', 'sm') ?>
        <?= ImageService::imgFromBg('villa-plaisance-chambre-arche-03.webp', 'sm') ?>
      </div>
    </div>
  </div>
</section>

<!-- 70 -->
<section class="section" style="background: var(--linen-100);">
  <div class="container-wide">
    <div class="ch-room alt">
      <div class="ch-room-images">
        <?= ImageService::imgFromBg('villa-plaisance-chambre-annees-70-01.webp', 'big') ?>
        <?= ImageService::imgFromBg('villa-plaisance-chambre-annees-70-02.webp', 'sm') ?>
        <?= ImageService::imgFromBg('villa-plaisance-chambre-annees-70-03.webp', 'sm') ?>
      </div>
      <div class="ch-room-text">
        <div class="ch-room-num">
          <span>IV · <span data-en="Fourth bedroom">Quatrième chambre</span></span>
          <span data-en="The most singular">La plus atypique</span>
        </div>
        <p class="ch-room-tagline" data-en="Vintage 1970s furniture">MOBILIER VINTAGE ANNÉES 70</p>
        <h2 class="h-xl" style="margin: 0 0 24px;"><em>Chambre 70</em></h2>
        <p class="body-lg" style="margin: 0 0 16px;" data-en="A large double bed, vintage 1970s furniture picked up over the years. Direct access to the garden through a French window. The villa's most singular room.">Grand lit double, mobilier chiné des années 70. Accès direct sur le jardin par une porte-fenêtre. La chambre la plus atypique de la villa.</p>
        <div class="ch-pills">
          <span class="pill" data-en="Large double bed">Grand lit double</span>
          <span class="pill" data-en="Vintage furniture">Mobilier vintage</span>
          <span class="pill" data-en="Direct garden access">Accès direct jardin</span>
          <span class="pill" data-en="A/C">Climatisation</span>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ INTÉRIEUR : CUISINE + SALON ============ -->
<?php if ($_intHtml = $renderV8BlockAt(4, 'interior')): ?>
<?= $_intHtml ?>
<?php else: ?>
<section class="section surface-stone" style="background: var(--linen-100);">
  <div class="container-wide">
    <div style="margin-bottom: clamp(40px, 5vw, 64px);">
      <div class="section-label">
        <span class="numeral">02 / <span data-en="Inside">L'intérieur</span></span>
      </div>
      <h2 class="h-xl" style="margin: 0; max-width: 18ch;">Une <em>cuisine</em>,<br/>un <em>salon</em>, une longue table.</h2>
    </div>

    <div class="interior">
      <div>
        <?= ImageService::imgFromBg('villa-plaisance-cuisine-equipee-01.webp', 'img') ?>
        <div>
          <div class="numeral" style="margin-bottom: 8px;" data-en="Kitchen, an all-in-one space">CUISINE · UN ESPACE TOUT-COMPRIS</div>
          <h3 class="h-md" style="margin: 0 0 8px;"><em data-en="Fully equipped">Entièrement équipée</em></h3>
          <p class="body" style="margin: 0; max-width: 50ch;" data-en="Gas range, dishwasher, XXL fridge, oven, microwave, and everything you need to cook for ten.">Piano gaz, lave-vaisselle, réfrigérateur XXL, four, micro-ondes, et tout ce qu'il faut pour cuisiner à dix.</p>
        </div>
      </div>

      <div>
        <?= ImageService::imgFromBg('villa-plaisance-salon-salle-a-manger-01.webp', 'img') ?>
        <div>
          <div class="numeral" style="margin-bottom: 8px;" data-en="Living &amp; dining, conviviality, simple">SALON · SALLE À MANGER · LA CONVIVIALITÉ EN TOUTE SIMPLICITÉ</div>
          <h3 class="h-md" style="margin: 0 0 8px;"><em data-en="Air-conditioned, light, long">Climatisé, clair, long</em></h3>
          <p class="body" style="margin: 0; max-width: 50ch;" data-en="A large living/dining room, air-conditioned, easy to live in. A long table that ten people can sit at without anyone elbowing anyone else.">Grand salon et salle à manger climatisés, facile à vivre. Une longue table où dix personnes tiennent sans qu'on se gêne du coude.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ PISCINE ============ -->
<?php if ($_pisHtml = $renderV8BlockAt(5, 'piscine')): ?>
<?= $_pisHtml ?>
<?php else: ?>
<section class="section" id="piscine">
  <div class="container-wide">
    <div class="section-label">
      <span class="numeral">03 / <span data-en="The pool">La piscine</span></span>
    </div>
    <h2 class="h-xl" style="margin: 0 0 28px; max-width: 18ch;">Piscine privée, <em>12 × 6</em>.<br/>Pour vous seuls.</h2>
    <p class="lede" style="margin: 0; max-width: 52ch;" data-en="Exclusively yours, 24/7. No other family or tenant has access during your stay.">Exclusivement réservée à votre groupe, 24h/24. Aucune autre famille ou locataire n'y a accès pendant votre séjour.</p>

    <div style="aspect-ratio: 21/9; background: center/cover url('/uploads/villa-plaisance-piscine-privee-05.webp'); margin-top: clamp(32px, 4vw, 56px);"></div>

    <div class="pool-block">
      <div>
        <p class="body-lg" style="margin: 0 0 16px; max-width: 56ch;" data-en="Sunbeds, parasols, garden table, an outdoor lounge and a solar shower, and the option to have the pool heated if you'd like an early-July dip.">Transats, parasols, table de jardin, salon extérieur et douche solaire, et l'option de chauffer la piscine si vous préférez vous baigner dès début juillet.</p>
        <p class="body" style="margin: 0; max-width: 56ch; color: var(--stone-600);" data-en="The pool is fenced, as required by law. Children stay close.">La piscine est clôturée, conformément à la réglementation. Les enfants restent à portée d'œil.</p>
      </div>
      <ul class="pool-features">
        <li data-en="12 × 6 m, fenced">12 × 6 m, clôturée</li>
        <li data-en="Exclusive, 24/7">Exclusive · 24h/24</li>
        <li data-en="Sunbeds &amp; parasols">Transats &amp; parasols</li>
        <li data-en="Garden table &amp; outdoor lounge">Table de jardin &amp; salon extérieur</li>
        <li data-en="Solar shower">Douche solaire</li>
        <li data-en="Heating on request">Chauffage sur demande</li>
      </ul>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ LES ESPACES ============ -->
<?php if ($_espHtml = $renderV8BlockAt(6, 'tableau')): ?>
<?= $_espHtml ?>
<?php else: ?>
<section class="section surface-stone" style="background: var(--linen-100);">
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: clamp(32px, 5vw, 96px); align-items: end; margin-bottom: clamp(40px, 5vw, 72px);">
      <div>
        <div class="section-label">
          <span class="numeral">04 / <span data-en="The spaces">Les espaces</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 12ch;">Tout ce que la <em>maison</em> contient.</h2>
      </div>
      <p class="body-lg" style="margin: 0; max-width: 44ch;" data-en="The complete inventory of the house, bedrooms, bathrooms, kitchen, terrace, garden, parking. So you can plan a long week without surprises.">L'inventaire complet de la maison, chambres, salles de bain, cuisine, terrasse, jardin, parking. De quoi préparer une longue semaine sans surprise.</p>
    </div>

    <div class="espaces">
      <dl>
        <div class="row">
          <dt data-en="Master suite">Suite parentale</dt>
          <dd data-en="King bed 180×200 + private bathroom + dressing + garden view">Lit king 180 × 200 + salle de bain privée + dressing + vue jardin</dd>
        </div>
        <div class="row">
          <dt data-en="Blue bedroom">Chambre Bleue</dt>
          <dd data-en="Queen double bed 160×200">Lit double queen 160 × 200</dd>
        </div>
        <div class="row">
          <dt data-en="Arch bedroom">Chambre Arche</dt>
          <dd data-en="Double bed 160×200">Lit double 160 × 200</dd>
        </div>
        <div class="row">
          <dt data-en="70s bedroom">Chambre Années 70</dt>
          <dd data-en="2 single beds 90×200, joinable">2 lits simples 90 × 200 (jumelables)</dd>
        </div>
        <div class="row">
          <dt data-en="Bathrooms">Salles de bain</dt>
          <dd data-en="2 full bathrooms + 3 independent WCs">2 salles de bain complètes + 3 WC indépendants</dd>
        </div>
        <div class="row">
          <dt data-en="Living / dining">Salon · S. à manger</dt>
          <dd data-en="Large air-conditioned living and dining room">Grand salon et salle à manger climatisés</dd>
        </div>
      </dl>
      <dl>
        <div class="row">
          <dt data-en="Kitchen">Cuisine</dt>
          <dd data-en="Gas range, dishwasher, XXL fridge, oven, microwave">Piano gaz, lave-vaisselle, réfrigérateur XXL, four, micro-ondes</dd>
        </div>
        <div class="row">
          <dt data-en="Covered terrace">Terrasse couverte</dt>
          <dd data-en="40 m² with garden lounge, seats 12">40 m² avec salon de jardin 12 places</dd>
        </div>
        <div class="row">
          <dt data-en="Garden">Jardin</dt>
          <dd data-en="Provençal garden, olive trees, charcoal BBQ, pétanque court">Provençal · oliviers, BBQ charbon, terrain de pétanque</dd>
        </div>
        <div class="row">
          <dt data-en="Laundry">Buanderie</dt>
          <dd data-en="Washer and dryer">Lave-linge et sèche-linge</dd>
        </div>
        <div class="row">
          <dt data-en="Connectivity">Connectivité</dt>
          <dd data-en="High-speed fibre wifi + streaming TV in every living space">Wifi haut débit (fibre) + TV streaming dans chaque pièce de vie</dd>
        </div>
        <div class="row">
          <dt data-en="Parking">Parking</dt>
          <dd data-en="Closed parking for 2 vehicles · cot &amp; high chair on request">Parking fermé 2 véhicules · lit bébé &amp; chaise haute sur demande</dd>
        </div>
      </dl>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ INFOS PRATIQUES ============ -->
<?php if ($_infHtml = $renderV8BlockAt(7, 'tableau')): ?>
<?= $_infHtml ?>
<?php else: ?>
<section class="section" id="infos">
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: clamp(32px, 5vw, 96px); align-items: end; margin-bottom: clamp(40px, 5vw, 64px);">
      <div>
        <div class="section-label">
          <span class="numeral">05 / <span data-en="Practical info">Infos pratiques</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;">Tout ce qu'il faut <em>savoir</em>.</h2>
      </div>
      <p class="body-lg" style="margin: 0; max-width: 44ch;" data-en="Dates, capacity, arrival times, what's included, the essentials at a glance, before you write to us.">Dates, capacité, horaires, ce qui est inclus, l'essentiel d'un coup d'œil, avant que vous nous écriviez.</p>
    </div>

    <div class="practical">
      <div class="row">
        <div class="k" data-en="Period">Période</div>
        <div class="v" data-en="July and August only"><strong>Juillet et août uniquement</strong></div>
      </div>
      <div class="row">
        <div class="k" data-en="Arrival">Arrivée</div>
        <div class="v" data-en="From 5 pm">À partir de 17h</div>
      </div>
      <div class="row">
        <div class="k" data-en="Departure">Départ</div>
        <div class="v" data-en="Before 10 am">Avant 10h</div>
      </div>
      <div class="row">
        <div class="k" data-en="Minimum stay">Séjour minimum</div>
        <div class="v" data-en="7 nights (Saturday → Saturday)"><strong>7 nuits</strong> (samedi → samedi)</div>
      </div>
      <div class="row">
        <div class="k" data-en="Capacity">Capacité</div>
        <div class="v" data-en="10 guests max · 4 bedrooms"><strong>10 personnes</strong> max · 4 chambres</div>
      </div>
      <div class="row">
        <div class="k" data-en="Pool">Piscine</div>
        <div class="v" data-en="Private &amp; exclusive · 12 × 6 m · heating optional">Privée exclusive · 12 × 6 m · chauffage en option</div>
      </div>
      <div class="row">
        <div class="k" data-en="Cleaning">Ménage</div>
        <div class="v" data-en="End-of-stay included · mid-stay clean optional">Fin de séjour inclus · intermédiaire en option</div>
      </div>
      <div class="row">
        <div class="k" data-en="Animals">Animaux</div>
        <div class="v" data-en="Not accepted">Non acceptés</div>
      </div>
      <div class="row">
        <div class="k" data-en="Smoking">Fumeur</div>
        <div class="v" data-en="Non-smoking">Non-fumeur</div>
      </div>
      <div class="row">
        <div class="k" data-en="Linen">Linge</div>
        <div class="v" data-en="Sheets and towels provided">Draps et serviettes fournis</div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ FAQ ============ -->
<?php if ($_faqHtml = $renderV8BlockAt(8, 'faq')): ?>
<?= $_faqHtml ?>
<?php else: ?>
<section class="section surface-stone" style="background: var(--linen-100);">
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: clamp(32px, 6vw, 96px); align-items: start;">
      <div>
        <div class="section-label">
          <span class="numeral">06 / <span data-en="Frequently asked">Questions fréquentes</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;" data-en="The whole-house questions.">Villa entière,<br/>les <em>questions</em> qui reviennent.</h2>
      </div>
      <div class="faq">
        <details>
          <summary><span data-en="How many guests can the villa accommodate?">Combien de personnes la villa peut-elle accueillir ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="The whole villa accommodates up to 10 guests across 4 bedrooms: a master suite with king bed and private bathroom, the Blue bedroom (queen 160×200), the Arch bedroom (double 160×200), and the 70s bedroom (2 joinable singles 90×200). A cot and high chair are available on request.">La villa entière accueille jusqu'à 10 personnes réparties dans 4 chambres : une suite parentale avec lit king et salle de bain privée, la Chambre Bleue (queen 160×200), la Chambre de l'Arche (double 160×200) et la Chambre Années 70 (2 lits simples jumelables). Lit bébé et chaise haute disponibles sur demande.</div>
        </details>
        <details>
          <summary><span data-en="Is the pool private during a whole-villa rental?">La piscine est-elle privée en location villa ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Yes, during a whole-villa rental the 12×6 m pool is exclusively reserved for your group, 24/7. No other family has access. Heating can be added as an option from July onwards.">Oui, en location villa entière la piscine de 12×6 m est exclusivement réservée à votre groupe, 24h/24. Aucune autre famille n'y a accès. Elle peut être chauffée en option dès le mois de juillet.</div>
        </details>
        <details>
          <summary><span data-en="Is the kitchen fully equipped?">La cuisine est-elle équipée ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Yes, oven, hob, dishwasher, microwave, fridge, plus utensils and crockery for 10.">Oui, la cuisine est entièrement équipée : four, plaques, lave-vaisselle, micro-ondes, réfrigérateur, ustensiles de cuisine et vaisselle pour 10 personnes.</div>
        </details>
        <details>
          <summary><span data-en="Are sheets and towels provided?">Le linge de maison est-il fourni ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Yes, sheets, bath towels and pool towels are provided and changed weekly.">Oui, les draps, serviettes de bain et serviettes de piscine sont fournis et changés chaque semaine.</div>
        </details>
        <details>
          <summary><span data-en="What is the minimum stay?">Quelle est la durée minimum de location ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="In high season (July–August), the minimum is one week, Saturday to Saturday.">En haute saison (juillet-août), la durée minimum est d'une semaine, du samedi au samedi.</div>
        </details>
        <details>
          <summary><span data-en="Are there shops nearby?">Y a-t-il des commerces à proximité ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Yes, Bédarrides has bakeries, a small grocery, restaurants and a pharmacy. The nearest supermarket is in Sorgues, 5 minutes by car.">Oui, Bédarrides dispose de boulangeries, supérette, restaurants et pharmacie. Le supermarché le plus proche est à Sorgues, à 5 minutes en voiture.</div>
        </details>
        <details>
          <summary><span data-en="Is there a cleaning or private-chef service?">Y a-t-il un service de ménage ou de chef à domicile ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="End-of-stay cleaning is included. A mid-stay clean can be added on request. We can also put you in touch with local chefs for dinners at the house.">Le ménage de fin de séjour est inclus dans le tarif. Un ménage intermédiaire peut être ajouté en option. Nous pouvons également vous mettre en contact avec des chefs cuisiniers locaux pour des dîners à domicile sur réservation.</div>
        </details>
        <details>
          <summary><span data-en="How do I book the whole villa?">Comment réserver la villa entière ?</span><span class="icon"></span></summary>
          <div class="answer" data-en="Send us your dates and the number of guests through the contact form, we'll get back to you within the day. There is no online booking engine; every stay is confirmed by hand.">Envoyez-nous vos dates et le nombre de personnes via le formulaire de contact, nous vous répondons dans la journée. Pas de moteur de réservation en ligne, chaque séjour se confirme à la main.</div>
        </details>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ FOOTER CTA ============ -->
