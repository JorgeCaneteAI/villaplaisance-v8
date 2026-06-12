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
        <span><?= htmlspecialchars(t('ext.hero_issue')) ?></span>
        <span><?= htmlspecialchars(t('ext.hero_badge')) ?></span>
      </div>
      <h1><?= t('ext.title_home') ?></h1>
    </div>
    <div>
      <p class="lede"><?= htmlspecialchars(t('ext.hero_lede')) ?></p>
      <div class="page-hero-ctas">
        <a class="btn" href="<?= LangService::url('contact') ?>"><span><?= htmlspecialchars(t('ext.cta_stay')) ?></span> →</a>
        <a class="btn btn-ghost" href="#piscine"><span><?= htmlspecialchars(t('ext.cta_tour')) ?></span></a>
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
          <span class="numeral">01 / <span><?= htmlspecialchars(t('ext.intro_step')) ?></span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;"><?= t('ext.title_home') ?></h2>
      </div>
      <div>
        <p class="lede" style="margin: 0 0 20px;"><?= htmlspecialchars(t('ext.intro_lede')) ?></p>
        <p class="body-lg" style="margin: 0;"><?= htmlspecialchars(t('ext.intro_body')) ?></p>
      </div>
    </div>

    <!-- Quick anchors -->
    <div class="anchor-list" style="margin-top: clamp(48px, 5vw, 80px);">
      <a href="#piscine">
        <span class="num">01 · 12 × 6 m</span>
        <span class="ttl"><em><?= htmlspecialchars(t('ext.anchor_pool')) ?></em></span>
      </a>
      <a href="#terrasses">
        <span class="num">02 · 40 m²</span>
        <span class="ttl"><em><?= htmlspecialchars(t('ext.anchor_terraces')) ?></em></span>
      </a>
      <a href="#jardin">
        <span class="num">03 · 1 500 m²</span>
        <span class="ttl"><em><?= htmlspecialchars(t('ext.anchor_garden')) ?></em></span>
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
          <span>02 / <span><?= htmlspecialchars(t('ext.pool_num')) ?></span></span>
          <span><?= htmlspecialchars(t('ext.pool_badge')) ?></span>
        </div>
        <h2 class="h-xl" style="margin: 0 0 24px;"><?= t('ext.pool_title') ?></h2>
        <p class="body-lg" style="margin: 0 0 16px; max-width: 56ch;"><?= htmlspecialchars(t('ext.pool_body_lg')) ?></p>
        <p class="body" style="margin: 0; max-width: 56ch;"><?= htmlspecialchars(t('ext.pool_body')) ?></p>

        <div class="ex-space-pills">
          <span class="pill">12 × 6 m</span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_fenced')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_sunbeds')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_solar')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_heating')) ?></span>
          <span class="pill solid"><?= htmlspecialchars(t('ext.pill_shared')) ?></span>
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
          <span>03 / <span><?= htmlspecialchars(t('ext.terr_num')) ?></span></span>
          <span><?= htmlspecialchars(t('ext.terr_badge')) ?></span>
        </div>
        <h2 class="h-xl" style="margin: 0 0 24px;"><?= t('ext.terr_title') ?></h2>
        <p class="body-lg" style="margin: 0 0 16px; max-width: 56ch;"><?= htmlspecialchars(t('ext.terr_body_lg')) ?></p>
        <p class="body" style="margin: 0; max-width: 56ch;"><?= htmlspecialchars(t('ext.terr_body')) ?></p>

        <div class="ex-space-pills">
          <span class="pill"><?= htmlspecialchars(t('ext.pill_terrace40')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_south')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_lounge12')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_veranda')) ?></span>
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
          <span>04 / <span><?= htmlspecialchars(t('ext.gard_num')) ?></span></span>
          <span><?= htmlspecialchars(t('ext.gard_badge')) ?></span>
        </div>
        <h2 class="h-xl" style="margin: 0 0 24px;"><?= t('ext.gard_title') ?></h2>
        <p class="body-lg" style="margin: 0 0 16px; max-width: 56ch;"><?= htmlspecialchars(t('ext.gard_body_lg')) ?></p>
        <p class="body" style="margin: 0; max-width: 56ch;"><?= htmlspecialchars(t('ext.gard_body')) ?></p>

        <div class="ex-space-pills">
          <span class="pill"><?= htmlspecialchars(t('ext.pill_olives')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_lavender')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_herbs')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_bbq')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_petanque')) ?></span>
          <span class="pill"><?= htmlspecialchars(t('ext.pill_playground')) ?></span>
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
          <span class="numeral">05 / <span><?= htmlspecialchars(t('ext.equip_step')) ?></span></span>
        </div>
        <h2 class="h-xl" style="margin: 0; max-width: 16ch;"><?= t('ext.equip_title') ?></h2>
      </div>
      <p class="body-lg" style="margin: 0; max-width: 44ch;"><?= htmlspecialchars(t('ext.equip_lede')) ?></p>
    </div>

    <div class="equip-grid">
      <div class="equip"><span class="n">01</span><span class="l"><?= htmlspecialchars(t('ext.equip_01')) ?></span></div>
      <div class="equip"><span class="n">02</span><span class="l"><?= htmlspecialchars(t('ext.equip_02')) ?></span></div>
      <div class="equip"><span class="n">03</span><span class="l"><?= htmlspecialchars(t('ext.equip_03')) ?></span></div>
      <div class="equip"><span class="n">04</span><span class="l"><?= htmlspecialchars(t('ext.equip_04')) ?></span></div>
      <div class="equip"><span class="n">05</span><span class="l"><?= htmlspecialchars(t('ext.equip_05')) ?></span></div>
      <div class="equip"><span class="n">06</span><span class="l"><?= htmlspecialchars(t('ext.equip_06')) ?></span></div>
      <div class="equip"><span class="n">07</span><span class="l"><?= htmlspecialchars(t('ext.equip_07')) ?></span></div>
      <div class="equip"><span class="n">08</span><span class="l"><?= htmlspecialchars(t('ext.equip_08')) ?></span></div>
      <div class="equip"><span class="n">09</span><span class="l"><?= htmlspecialchars(t('ext.equip_09')) ?></span></div>
      <div class="equip"><span class="n">10</span><span class="l"><?= htmlspecialchars(t('ext.equip_10')) ?></span></div>
      <div class="equip"><span class="n">11</span><span class="l"><?= htmlspecialchars(t('ext.equip_11')) ?></span></div>
      <div class="equip"><span class="n">12</span><span class="l"><?= htmlspecialchars(t('ext.equip_12')) ?></span></div>
    </div>
  </div>
</section>

<!-- ============ 7 · FOOTER CTA ============ -->
