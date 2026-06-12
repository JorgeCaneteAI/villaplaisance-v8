<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Que faire, directory dynamique.
 * Design du proto Claude (porté le 21 mai), grille dynamisée depuis
 * vp_articles WHERE type='sur-place'. Layout : front-proto.
 *
 * @var string $lang
 * @var array  $seo
 * @var array  $jsonLd
 * @var array  $articles    rows de vp_articles type='sur-place'
 * @var array  $categories  list($cat) unique pour générer les filtres
 */
$featured = !empty($articles) ? array_shift($articles) : null;

// Mapping category label → slug court pour data-cat et classes badge.
// 'default' = fallback si nouvelle catégorie créée en admin.
$catSlug = function(string $c): string {
    return match(trim($c)) {
        'Avec des enfants'      => 'enfants',
        'Commerces'             => 'commerces',
        'Sites à visiter'       => 'sites',
        'Restaurants & tables', 'Restaurants &amp; tables' => 'tables',
        default                  => 'default',
    };
};

$totalCount = ($featured ? 1 : 0) + count($articles);

// Date courte localisée ("1 juil. 2025" / "1 Jul. 2025"), sinon date('M')
// rendait les mois en anglais sur les pages FR/ES.
$itinDate = static function (?string $d): string {
    if (!$d) return '';
    $ts = strtotime($d);
    if (!$ts) return '';
    $months = explode(',', t('cal.months'));
    return (int)date('j', $ts) . ' ' . ($months[(int)date('n', $ts) - 1] ?? '') . ' ' . date('Y', $ts);
};
?>
<style>
  /* Masthead */
  .qf-masthead {
    background: var(--linen-50);
    border-bottom: var(--hairline);
  }
  .qf-masthead-inner {
    padding: clamp(64px, 8vw, 120px) var(--gutter) clamp(40px, 5vw, 64px);
    max-width: var(--container-wide);
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(32px, 5vw, 80px);
    align-items: end;
  }
  .qf-issue {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em;
    color: var(--stone-500); text-transform: uppercase;
    display: flex; justify-content: space-between; align-items: baseline;
    border-bottom: var(--hairline);
    padding-bottom: 14px; margin-bottom: 28px;
  }
  .qf-title {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(56px, 9vw, 140px); line-height: 0.92; letter-spacing: -0.025em;
    color: var(--ink-900);
  }
  .qf-title em { font-style: italic; color: var(--sage-700); }
  .qf-lede {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(20px, 1.8vw, 26px); line-height: 1.4; letter-spacing: -0.005em;
    color: var(--ink-700); max-width: 42ch; margin: 0;
  }
  @media (max-width: 720px) { .qf-masthead-inner { grid-template-columns: 1fr; } }

  /* Sticky filter */
  .qf-filter {
    position: sticky;
    top: 60px;
    z-index: 30;
    background: color-mix(in oklab, var(--linen-50) 94%, transparent);
    backdrop-filter: saturate(140%) blur(10px);
    -webkit-backdrop-filter: saturate(140%) blur(10px);
    border-bottom: var(--hairline);
  }
  .qf-filter-inner {
    max-width: var(--container-wide); margin: 0 auto;
    padding: 14px var(--gutter);
    display: flex; gap: 6px; align-items: center;
    overflow-x: auto; scrollbar-width: none;
  }
  .qf-filter-inner::-webkit-scrollbar { display: none; }
  .qf-filter button {
    flex-shrink: 0;
    background: transparent; border: 1px solid color-mix(in oklab, var(--ink-900) 15%, transparent);
    color: var(--stone-600);
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 10px 16px; cursor: pointer;
    transition: background .2s, color .2s, border-color .2s;
  }
  .qf-filter button:hover { color: var(--ink-900); border-color: var(--ink-900); }
  .qf-filter button[aria-pressed="true"] { background: var(--olive-900); color: var(--linen-50); border-color: var(--olive-900); }
  .qf-filter .count {
    margin-left: auto; flex-shrink: 0;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.12em;
    color: var(--stone-500); padding-left: 16px;
    border-left: var(--hairline);
  }

  /* Category badges */
  .badge {
    display: inline-flex; align-items: center; gap: 6px;
    font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 5px 9px;
    background: var(--linen-100);
    color: var(--ink-900);
    border: 1px solid transparent;
  }
  .badge.enfants   { background: color-mix(in oklab, var(--sage-200) 55%, transparent); }
  .badge.commerces { background: color-mix(in oklab, var(--terra-500) 14%, var(--linen-50)); color: var(--terra-600); }
  .badge.sites     { background: var(--olive-900); color: var(--linen-50); }
  .badge.tables    { background: var(--linen-200); }

  /* Place cards */
  .qf-grid {
    display: grid; grid-template-columns: repeat(12, 1fr);
    gap: clamp(32px, 4vw, 56px) clamp(24px, 2.4vw, 36px);
  }
  .qf-card {
    grid-column: span 4;
    display: flex; flex-direction: column; gap: 14px;
    background: var(--linen-50);
    transition: transform .3s cubic-bezier(.2,.6,.2,1);
  }
  .qf-card.hidden { display: none; }
  .qf-card:hover { transform: translateY(-2px); }
  /* <img class="img"> : aspect 3/2 iso source. Le background-size n'a plus
     d'effet depuis la migration <div> → <img> (Pattern A). */
  .qf-card img.img,
  .qf-card .img {
    aspect-ratio: 3/2;
    width: 100%; height: auto;
    object-fit: cover; object-position: center;
    display: block;
    background-color: var(--linen-200);
    background-size: cover; background-position: center; /* legacy fallback */
  }
  /* Toute la card est un lien → on neutralise underline + hérite couleur. */
  a.qf-card, a.qf-featured { color: inherit; text-decoration: none; }
  a.qf-card:focus-visible, a.qf-featured:focus-visible { outline: 2px solid var(--terra-500); outline-offset: 4px; }
  .qf-card .more-link {
    color: var(--olive-900);
    border-bottom: 1px solid currentColor;
    padding-bottom: 1px;
    text-transform: none;
  }
  .qf-card .meta { display: flex; gap: 10px; align-items: baseline; flex-wrap: wrap; }
  .qf-card h3 {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(22px, 1.8vw, 28px); line-height: 1.1; letter-spacing: -0.015em;
    color: var(--ink-900);
  }
  .qf-card h3 em { font-style: italic; }
  .qf-card .desc {
    font-size: 14.5px; line-height: 1.55; color: var(--stone-600); margin: 0;
  }
  .qf-card .footer-row {
    margin-top: auto;
    padding-top: 12px;
    border-top: var(--hairline);
    display: flex; justify-content: space-between; align-items: baseline;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.08em;
    color: var(--stone-600); text-transform: uppercase;
  }
  .qf-card .footer-row a { color: var(--olive-900); border-bottom: 1px solid currentColor; padding-bottom: 1px; }

  @media (max-width: 960px) { .qf-card { grid-column: span 6; } }
  @media (max-width: 560px) { .qf-card { grid-column: span 12; } }

  /* Big featured card */
  .qf-featured {
    grid-column: span 12;
    display: grid; grid-template-columns: 1.2fr 1fr; gap: clamp(24px, 3vw, 48px);
    background: var(--linen-100);
    padding: clamp(24px, 3vw, 40px);
    align-items: center;
  }
  .qf-featured img.img,
  .qf-featured .img {
    aspect-ratio: 3/2;
    width: 100%; height: auto;
    object-fit: cover; object-position: center;
    display: block;
    background-color: var(--linen-200);
    background-size: cover; background-position: center; /* legacy fallback */
  }
  .qf-featured h3 {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(34px, 3.6vw, 52px); line-height: 1.0; letter-spacing: -0.02em;
    color: var(--ink-900); margin: 0 0 14px;
  }
  .qf-featured h3 em { font-style: italic; }
  .qf-featured .meta { display: flex; gap: 12px; align-items: baseline; flex-wrap: wrap; margin-bottom: 16px; }
  @media (max-width: 720px) { .qf-featured { grid-template-columns: 1fr; padding: 24px; } }

  /* Empty state */
  .qf-empty {
    margin: 56px auto; padding: 56px 24px; max-width: 720px;
    text-align: center; color: var(--stone-600);
    border: var(--hairline);
    font-family: var(--font-display); font-size: 22px; line-height: 1.3;
  }
</style>


<!-- ============ MASTHEAD (BDD vp_sections or fallback HTML) ============ -->
<?php
$_v8HeroItin = null;
foreach (BlockService::getSections('itineraire', $lang) as $_s) {
    if ((int)$_s['position'] === 1 && $_s['block_type'] === 'hero') {
        $_v8HeroItin = BlockService::renderBlock($_s);
        break;
    }
}
?>
<?php if ($_v8HeroItin): ?>
<?= $_v8HeroItin ?>
<?php else: ?>
<section class="qf-masthead">
  <div class="qf-masthead-inner">
    <div>
      <div class="qf-issue">
        <span><?= htmlspecialchars(t('itin.hero_issue')) ?></span>
        <span><?= htmlspecialchars(t('itin.hero_pick')) ?></span>
      </div>
      <h1 class="qf-title"><?= t('itin.hero_title') ?></h1>
    </div>
    <p class="qf-lede"><?= htmlspecialchars(t('itin.hero_lede')) ?></p>
  </div>
</section>
<?php endif; ?>

<!-- ============ FILTER ============ -->
<?php if (!empty($categories) && $totalCount > 0): ?>
<div class="qf-filter">
  <div class="qf-filter-inner" role="tablist">
    <button data-cat="all" aria-pressed="true"><?= htmlspecialchars(t('itin.filter_all')) ?></button>
    <?php foreach ($categories as $cat): if ($cat === null || $cat === '') continue; ?>
    <button data-cat="<?= htmlspecialchars($catSlug($cat)) ?>" aria-pressed="false"><?= htmlspecialchars($cat) ?></button>
    <?php endforeach; ?>
    <span class="count" id="filter-count"><?= htmlspecialchars(t($totalCount > 1 ? 'itin.address_many' : 'itin.address_one', ['n' => $totalCount])) ?></span>
  </div>
</div>
<?php endif; ?>

<!-- ============ GRID ============ -->
<section class="section">
  <div class="container-wide">

    <?php if ($totalCount === 0): ?>
      <div class="qf-empty"><?= htmlspecialchars(t('itin.empty')) ?></div>
    <?php else: ?>
    <div class="qf-grid" id="grid">

      <?php if ($featured):
        $featuredCover = !empty($featured['cover_image']) ? basename($featured['cover_image']) : 'placeholder/sur-place.webp';
      ?>
      <!-- FEATURED -->
      <a class="qf-featured qf-card" href="<?= LangService::url('sur-place') ?>/<?= htmlspecialchars($featured['slug']) ?>" data-cat="<?= htmlspecialchars($catSlug($featured['category'] ?? '')) ?>">
        <?= ImageService::imgFromBg($featuredCover, 'img', true) ?>
        <div>
          <div class="meta">
            <?php if (!empty($featured['category'])): ?>
            <span class="badge <?= htmlspecialchars($catSlug($featured['category'])) ?>"><?= htmlspecialchars($featured['category']) ?></span>
            <?php endif; ?>
            <span style="font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.1em; color: var(--stone-500);"><?= htmlspecialchars(t('itin.editors_pick')) ?></span>
          </div>
          <h3><?= htmlspecialchars($featured['title']) ?></h3>
          <?php if (!empty($featured['excerpt'])): ?>
          <p class="desc"><?= htmlspecialchars($featured['excerpt']) ?></p>
          <?php endif; ?>
          <div class="footer-row" style="margin-top: 18px;">
            <?php if (!empty($featured['published_at'])): ?>
            <span><time datetime="<?= htmlspecialchars($featured['published_at']) ?>"><?= htmlspecialchars($itinDate($featured['published_at'])) ?></time></span>
            <?php else: ?>
            <span></span>
            <?php endif; ?>
            <span class="more-link"><?= htmlspecialchars(t('itin.more')) ?></span>
          </div>
        </div>
      </a>
      <?php endif; ?>

      <!-- CARDS -->
      <?php foreach ($articles as $a):
        $cover = !empty($a['cover_image']) ? basename($a['cover_image']) : 'placeholder/sur-place.webp';
      ?>
      <a class="qf-card" href="<?= LangService::url('sur-place') ?>/<?= htmlspecialchars($a['slug']) ?>" data-cat="<?= htmlspecialchars($catSlug($a['category'] ?? '')) ?>">
        <?= ImageService::imgFromBg($cover, 'img') ?>
        <?php if (!empty($a['category'])): ?>
        <div class="meta">
          <span class="badge <?= htmlspecialchars($catSlug($a['category'])) ?>"><?= htmlspecialchars($a['category']) ?></span>
        </div>
        <?php endif; ?>
        <h3><?= htmlspecialchars($a['title']) ?></h3>
        <?php if (!empty($a['excerpt'])): ?>
        <p class="desc"><?= htmlspecialchars($a['excerpt']) ?></p>
        <?php endif; ?>
        <div class="footer-row">
          <?php if (!empty($a['published_at'])): ?>
          <span><time datetime="<?= htmlspecialchars($a['published_at']) ?>"><?= htmlspecialchars($itinDate($a['published_at'])) ?></time></span>
          <?php else: ?>
          <span></span>
          <?php endif; ?>
          <span class="more-link"><?= htmlspecialchars(t('itin.more')) ?></span>
        </div>
      </a>
      <?php endforeach; ?>

    </div>
    <?php endif; ?>

  </div>
</section>

<!-- ============ ASK US STRIP ============ -->
<section class="section surface-ink" style="background: var(--olive-900); color: var(--linen-100); padding: clamp(64px, 8vw, 112px) 0;">
  <div class="container-wide" style="text-align: center;">
    <div class="kicker dark" style="display: inline-flex; margin-bottom: 24px;"><span class="dot" style="background: var(--sage-200);"></span><span><?= htmlspecialchars(t('itin.shortcut')) ?></span></div>
    <h2 class="h-xl" style="margin: 0 auto; max-width: 22ch; color: var(--linen-50);"><?= t('itin.shortcut_title') ?></h2>
    <p class="body-lg" style="color: rgba(var(--linen-50-rgb), 0.72); max-width: 50ch; margin: 24px auto 32px;"><?= htmlspecialchars(t('itin.shortcut_lede')) ?></p>
    <div style="display:flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
      <a class="btn" style="background: var(--linen-50); color: var(--olive-900); border-color: var(--linen-50);" href="<?= LangService::url('contact') ?>"><span><?= htmlspecialchars(t('itin.write_us')) ?></span> →</a>
    </div>
  </div>
</section>

<script>
// Filtre client-side : data-cat + count dynamique
(function() {
  var buttons = document.querySelectorAll('.qf-filter button[data-cat]');
  var items = document.querySelectorAll('#grid .qf-card');
  var countEl = document.getElementById('filter-count');
  if (!buttons.length || !items.length) return;

  function pluralize(n) {
    return n + ' ' + (n > 1 ? 'adresses' : 'adresse');
  }

  buttons.forEach(function(b) {
    b.addEventListener('click', function() {
      buttons.forEach(function(x) { x.setAttribute('aria-pressed', 'false'); });
      b.setAttribute('aria-pressed', 'true');
      var cat = b.dataset.cat;
      var visible = 0;
      items.forEach(function(it) {
        if (cat === 'all' || it.dataset.cat === cat) {
          it.classList.remove('hidden');
          visible++;
        } else {
          it.classList.add('hidden');
        }
      });
      if (countEl) countEl.textContent = pluralize(visible);
    });
  });
})();
</script>
