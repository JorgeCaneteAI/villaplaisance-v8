<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Avis clients (/avis).
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 * @var array $reviews
 * @var array $stats  ['total', 'avg', 'by_offer', 'by_platform']
 * @var string $offerFilter
 */

$platformLabels = [
    'airbnb'  => 'Airbnb',
    'booking' => 'Booking',
    'google'  => 'Google',
    'direct'  => 'Direct',
];
$offerLabels = [
    'bb'    => t('avis.offer_bb'),
    'villa' => t('avis.offer_villa'),
    'both'  => t('avis.offer_both'),
];

$normalizeRating = static function (float $rating, string $platform): float {
    if ($platform === 'booking' && $rating > 5) return $rating / 2;
    return $rating;
};

$formatDate = static function (?string $d): string {
    if (!$d) return '';
    $ts = strtotime($d);
    if (!$ts) return $d;
    // strftime() deprecated PHP 8.1+ ; mois longs localisés (clé cal.months_long).
    $months = explode(',', t('cal.months_long'));
    return ($months[(int) date('n', $ts) - 1] ?? '') . ' ' . date('Y', $ts);
};

// Linkifie la 1re mention de Jorge ou Georges vers /votre-hote
$linkifyHost = static function (string $content): string {
    $url = \LangService::url('votre-hote');
    $escaped = htmlspecialchars($content);
    return preg_replace(
        '/\b(Jorge|Georges)\b/u',
        '<a href="' . htmlspecialchars($url) . '" class="av-host-link">$1</a>',
        $escaped,
        1
    );
};
?>
<style>
  .av-hero-section { padding: clamp(48px, 7vw, 120px) var(--gutter) clamp(40px, 5vw, 80px); }
  .av-hero-inner { max-width: var(--container-wide); margin: 0 auto; }
  .av-hero h1 { font-family: var(--font-display); font-weight: 400; font-size: clamp(48px, 7vw, 96px); line-height: 0.95; letter-spacing: -0.025em; color: var(--ink-900); margin: 0 0 20px; max-width: 14ch; }
  .av-hero h1 em { font-style: italic; color: var(--sage-700); }
  .av-hero .lede { font-family: var(--font-display); font-style: italic; font-size: clamp(20px, 1.8vw, 26px); color: var(--ink-700); margin: 0; max-width: 44ch; line-height: 1.3; }
  .av-overline { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em; color: var(--terra-500); text-transform: uppercase; margin: 0 0 18px; }

  .av-stats {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 0;
    border-top: var(--hairline); border-bottom: var(--hairline);
    background: var(--linen-50);
  }
  .av-stat { padding: clamp(28px, 4vw, 48px) clamp(16px, 3vw, 32px); border-right: var(--hairline); text-align: center; }
  .av-stat:last-child { border-right: 0; }
  .av-stat .num { font-family: var(--font-display); font-weight: 400; font-size: clamp(40px, 5vw, 64px); line-height: 1; color: var(--ink-900); }
  .av-stat .num em { font-style: italic; color: var(--sage-700); }
  .av-stat .lbl { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em; color: var(--stone-500); text-transform: uppercase; margin-top: 10px; }
  @media (max-width: 720px) { .av-stats { grid-template-columns: repeat(2, 1fr); } .av-stat { border-right: 0; border-bottom: var(--hairline); } }

  .av-controls { max-width: var(--container-wide); margin: 0 auto; padding: 36px var(--gutter) 24px; display: flex; gap: 18px 40px; flex-wrap: wrap; align-items: baseline; scroll-margin-top: 90px; }
  .av-fgroup { display: flex; gap: 16px; flex-wrap: wrap; align-items: baseline; }
  .av-fgroup .lbl, .av-sort .lbl { font-family: var(--font-mono); font-size: 11px; font-weight: 700; letter-spacing: 0.16em; color: var(--terra-500); text-transform: uppercase; margin-right: 4px; }
  .av-filter { padding: 2px 0; border: 0; background: transparent; cursor: pointer; font: inherit; font-size: 15px; color: var(--ink-700); text-decoration: none; transition: color .2s; }
  .av-filter:hover { color: var(--ink-900); }
  .av-filter.active { color: var(--ink-900); text-decoration: underline; text-decoration-color: var(--terra-500); text-decoration-thickness: 1.5px; text-underline-offset: 5px; }
  .av-filter:focus-visible { outline: 2px solid var(--terra-500); outline-offset: 3px; border-radius: 2px; }

  .av-sort { margin-left: auto; display: flex; align-items: center; gap: 8px; }
  .av-sort select {
    font: inherit; font-size: 14px; color: var(--ink-900); background-color: transparent;
    border: 1px solid var(--admin-border); border-radius: 4px; padding: 8px 38px 8px 14px;
    cursor: pointer; -webkit-appearance: none; appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='8'><path d='M1 1l5 5 5-5' fill='none' stroke='%238a8178' stroke-width='1.5' stroke-linecap='round'/></svg>");
    background-repeat: no-repeat; background-position: right 13px center;
    transition: border-color .2s;
  }
  .av-sort select:hover { border-color: var(--stone-500); }
  .av-sort select:focus-visible { outline: 2px solid var(--terra-500); outline-offset: 2px; }
  @media (max-width: 640px) { .av-controls { gap: 16px; } .av-sort { margin-left: 0; width: 100%; } .av-sort select { flex: 1; } }

  .av-grid { max-width: var(--container-wide); margin: 0 auto; padding: 0 var(--gutter) clamp(48px, 6vw, 96px); display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: clamp(24px, 3vw, 40px); }
  @media (max-width: 960px) { .av-grid { grid-template-columns: 1fr 1fr; } }
  @media (max-width: 640px) { .av-grid { grid-template-columns: 1fr; } }

  .av-card { background: var(--linen-50); border: var(--hairline); padding: clamp(20px, 2.5vw, 32px); display: flex; flex-direction: column; gap: 14px; }
  .av-card-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .av-card-stars { font-family: var(--font-mono); font-size: 13px; color: var(--terra-500); letter-spacing: 0.15em; }
  .av-card-platform { font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em; color: var(--stone-500); text-transform: uppercase; padding: 3px 9px; border: 1px solid var(--stone-400); border-radius: 12px; }
  .av-card-quote {
    font-family: var(--font-display); font-style: italic;
    font-size: clamp(16px, 1.4vw, 19px); line-height: 1.5; color: var(--ink-900);
    margin: 0;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 6;
    overflow: hidden;
  }
  .av-card.is-expanded .av-card-quote {
    display: block;
    -webkit-line-clamp: unset;
    overflow: visible;
  }
  .av-card-quote a {
    color: inherit;
    text-decoration: underline;
    text-decoration-color: var(--terra-500);
    text-decoration-thickness: 1px;
    text-underline-offset: 3px;
    transition: color .2s, text-decoration-color .2s;
  }
  .av-card-quote a:hover { color: var(--terra-500); }
  .av-card-quote a:focus-visible { outline: 2px solid var(--terra-500); outline-offset: 2px; }
  .av-card-expand {
    align-self: flex-start;
    background: none; border: 0; padding: 0;
    cursor: pointer;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em;
    text-transform: uppercase; color: var(--terra-500);
    transition: color .2s;
  }
  .av-card-expand[hidden] { display: none; }
  .av-card-expand:hover { color: var(--ink-900); }
  .av-card-expand:focus-visible { outline: 2px solid var(--terra-500); outline-offset: 4px; }
  .av-card-meta { font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.12em; color: var(--stone-500); text-transform: uppercase; border-top: var(--hairline); padding-top: 12px; display: flex; justify-content: space-between; gap: 8px; flex-wrap: wrap; }
  .av-card-author { font-family: var(--font-display); font-style: normal; font-size: 14px; color: var(--ink-700); letter-spacing: 0; text-transform: none; }
  .av-card-author strong { font-weight: 500; }

  .av-empty { text-align: center; padding: 60px var(--gutter); color: var(--stone-500); font-family: var(--font-display); font-style: italic; font-size: 20px; }

  .av-cta { background: var(--ink-900); color: var(--linen-50); padding: clamp(48px, 7vw, 96px) var(--gutter); text-align: center; margin-top: 0; }
  .av-cta h2 { font-family: var(--font-display); font-weight: 400; font-size: clamp(32px, 4vw, 52px); line-height: 1; letter-spacing: -0.02em; color: var(--linen-50); margin: 0 0 16px; }
  .av-cta h2 em { font-style: italic; color: var(--sage-200); }
  .av-cta p { font-size: 16px; color: rgba(var(--linen-50-rgb), 0.78); margin: 0 auto 28px; max-width: 50ch; line-height: 1.55; }
  .av-cta .btn { background: var(--linen-50); color: var(--ink-900); border-color: var(--linen-50); }
</style>

<!-- HERO (BDD vp_sections or fallback HTML) -->
<?php
$_v8HeroAvis = null;
foreach (\BlockService::getSections('avis', $lang) as $_s) {
    if ((int)$_s['position'] === 1 && $_s['block_type'] === 'hero') {
        $_v8HeroAvis = \BlockService::renderBlock($_s);
        break;
    }
}
?>
<?php if ($_v8HeroAvis): ?>
<?= $_v8HeroAvis ?>
<?php else: ?>
<section class="av-hero-section">
  <div class="av-hero-inner av-hero">
    <p class="av-overline"><?= htmlspecialchars(t('avis.hero_overline')) ?></p>
    <h1><?= t('avis.hero_title') ?></h1>
    <p class="lede"><?= htmlspecialchars(t('avis.hero_lede')) ?></p>
  </div>
</section>
<?php endif; ?>

<!-- STATS -->
<?php if ($stats['total'] > 0): ?>
<section class="av-stats">
  <div class="av-stat">
    <div class="num"><em><?= number_format($stats['avg'], 1, ',', ' ') ?></em></div>
    <div class="lbl"><?= htmlspecialchars(t('avis.stat_avg')) ?></div>
  </div>
  <div class="av-stat">
    <div class="num"><?= $stats['total'] ?></div>
    <div class="lbl"><?= htmlspecialchars(t('avis.stat_total')) ?></div>
  </div>
  <div class="av-stat">
    <div class="num"><?= (int)$stats['by_offer']['bb'] ?></div>
    <div class="lbl"><?= htmlspecialchars(t('avis.stat_bb')) ?></div>
  </div>
  <div class="av-stat">
    <div class="num"><?= (int)$stats['by_offer']['villa'] ?></div>
    <div class="lbl"><?= htmlspecialchars(t('avis.stat_villa')) ?></div>
  </div>
</section>
<?php endif; ?>

<!-- FILTRES + TRI -->
<?php
// URL de la page en préservant offre / plateforme / tri, avec surcharges ciblées.
$avisUrl = static function (array $over) use ($offerFilter, $platformFilter, $sort): string {
    $p = [];
    if ($offerFilter !== 'all')    $p['offer'] = $offerFilter;
    if ($platformFilter !== 'all') $p['platform'] = $platformFilter;
    if ($sort !== 'recent')        $p['sort'] = $sort;
    foreach ($over as $k => $v) {
        if ($v === null || $v === 'all' || ($k === 'sort' && $v === 'recent')) unset($p[$k]);
        else $p[$k] = $v;
    }
    return $p ? '?' . http_build_query($p) : '?';
};
// Plateformes réellement présentes (ordre fixe), pour n'afficher que des filtres utiles.
$sitesAvail = array_values(array_filter(
    ['airbnb', 'booking', 'google', 'direct'],
    static fn ($pf) => !empty($stats['by_platform'][$pf])
));
?>
<div class="av-controls" id="av-controls">
  <div class="av-fgroup">
    <span class="lbl"><?= htmlspecialchars(t('avis.offer_label')) ?></span>
    <?php foreach (['all' => t('avis.filter_all'), 'bb' => t('avis.offer_bb'), 'villa' => t('avis.offer_villa')] as $key => $label): ?>
      <a class="av-filter <?= $offerFilter === $key ? 'active' : '' ?>" href="<?= htmlspecialchars($avisUrl(['offer' => $key]) . '#av-controls') ?>"><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (count($sitesAvail) > 1): ?>
  <div class="av-fgroup">
    <span class="lbl"><?= htmlspecialchars(t('avis.site_label')) ?></span>
    <a class="av-filter <?= $platformFilter === 'all' ? 'active' : '' ?>" href="<?= htmlspecialchars($avisUrl(['platform' => 'all']) . '#av-controls') ?>"><?= htmlspecialchars(t('avis.filter_all_m')) ?></a>
    <?php foreach ($sitesAvail as $pf): ?>
      <a class="av-filter <?= $platformFilter === $pf ? 'active' : '' ?>" href="<?= htmlspecialchars($avisUrl(['platform' => $pf]) . '#av-controls') ?>"><?= htmlspecialchars($platformLabels[$pf] ?? ucfirst($pf)) ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="av-sort">
    <label class="lbl" for="av-sort-select"><?= htmlspecialchars(t('avis.sort')) ?></label>
    <select id="av-sort-select" onchange="if (this.value) window.location.href = this.value;">
      <?php foreach (['recent' => t('avis.sort_recent'), 'old' => t('avis.sort_old'), 'rating_high' => t('avis.sort_rating_high'), 'rating_low' => t('avis.sort_rating_low')] as $key => $label): ?>
        <option value="<?= htmlspecialchars($avisUrl(['sort' => $key]) . '#av-controls') ?>"<?= $sort === $key ? ' selected' : '' ?>><?= htmlspecialchars($label) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>

<!-- GRID -->
<?php if (empty($reviews)): ?>
<div class="av-empty"><?= htmlspecialchars(t('avis.empty')) ?></div>
<?php else: ?>
<div class="av-grid">
  <?php foreach ($reviews as $r):
    $rating = $normalizeRating((float)$r['rating'], (string)$r['platform']);
    $starsFull = (int)floor($rating);
    $platform = (string)$r['platform'];
    $offer = (string)$r['offer'];
  ?>
  <article class="av-card">
    <div class="av-card-top">
      <div class="av-card-stars" aria-label="<?= number_format($rating, 1, ',', '') ?> sur 5">
        <?php for ($i = 0; $i < $starsFull; $i++): ?><svg class="star" width="14" height="14" aria-hidden="true"><use xlink:href="/assets/img/icons.svg#icon-etoile-pleine"/></svg><?php endfor; ?>
        <?php for ($i = $starsFull; $i < 5; $i++): ?><svg class="star star-empty" width="14" height="14" aria-hidden="true"><use xlink:href="/assets/img/icons.svg#icon-etoile"/></svg><?php endfor; ?>
      </div>
      <span class="av-card-platform"><?= htmlspecialchars($platformLabels[$platform] ?? ucfirst($platform)) ?></span>
    </div>
    <blockquote class="av-card-quote">« <?= $linkifyHost((string)$r['content']) ?> »</blockquote>
    <button type="button" class="av-card-expand" hidden aria-expanded="false"><?= htmlspecialchars(t('avis.read_more')) ?></button>
    <div class="av-card-meta">
      <span class="av-card-author">
        <strong><?= htmlspecialchars((string)$r['author']) ?></strong>
        <?php if (!empty($r['origin'])): ?> · <?= htmlspecialchars((string)$r['origin']) ?><?php endif; ?>
      </span>
      <span>
        <?= htmlspecialchars($offerLabels[$offer] ?? $offer) ?>
        <?php if (!empty($r['review_date'])): ?> · <?= htmlspecialchars($formatDate($r['review_date'])) ?><?php endif; ?>
      </span>
    </div>
  </article>
  <?php endforeach; ?>
</div>
<script>
(function () {
  var cards = document.querySelectorAll('.av-grid .av-card');
  var labelOpen = <?= json_encode(t('avis.read_less'), JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var labelShut = <?= json_encode(t('avis.read_more'), JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  cards.forEach(function (card) {
    var bq  = card.querySelector('.av-card-quote');
    var btn = card.querySelector('.av-card-expand');
    if (!bq || !btn) return;
    if (bq.scrollHeight > bq.clientHeight + 2) {
      btn.hidden = false;
    }
    btn.addEventListener('click', function () {
      var open = card.classList.toggle('is-expanded');
      btn.textContent = open ? labelOpen : labelShut;
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });
})();
</script>
<?php endif; ?>

<!-- CTA -->
<section class="av-cta">
  <h2><?= t('avis.cta_title') ?></h2>
  <p><?= htmlspecialchars(t('avis.cta_text')) ?></p>
  <a href="<?= \LangService::url('contact') ?>" class="btn"><?= htmlspecialchars(t('avis.cta_btn')) ?></a>
</section>
