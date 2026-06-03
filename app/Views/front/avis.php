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
    'bb'    => "Chambres d'hôtes",
    'villa' => 'Villa entière',
    'both'  => 'Les deux',
];

$normalizeRating = static function (float $rating, string $platform): float {
    if ($platform === 'booking' && $rating > 5) return $rating / 2;
    return $rating;
};

$formatDate = static function (?string $d): string {
    if (!$d) return '';
    $ts = strtotime($d);
    if (!$ts) return $d;
    // strftime() est deprecated PHP 8.1 et supprimé PHP 8.4 — mapping FR maison.
    static $months = [
        1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre',
    ];
    return $months[(int) date('n', $ts)] . ' ' . date('Y', $ts);
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

  .av-filters { max-width: var(--container-wide); margin: 0 auto; padding: 36px var(--gutter) 24px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
  .av-filters .lbl { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em; color: var(--stone-500); text-transform: uppercase; margin-right: 12px; }
  .av-filter { padding: 8px 18px; border: 1px solid var(--admin-border); background: transparent; cursor: pointer; font: inherit; font-size: 14px; color: var(--ink-700); border-radius: 4px; text-decoration: none; }
  .av-filter:hover { background: var(--linen-100); }
  .av-filter.active { background: var(--ink-900); color: var(--linen-50); border-color: var(--ink-900); }

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
    <p class="av-overline">07 · Avis clients</p>
    <h1>Ce qu'ils en <em>disent</em>.</h1>
    <p class="lede">Les mots laissés par nos hôtes au fil des saisons : Airbnb, Booking, Google et nos propres carnets de séjour.</p>
  </div>
</section>
<?php endif; ?>

<!-- STATS -->
<?php if ($stats['total'] > 0): ?>
<section class="av-stats">
  <div class="av-stat">
    <div class="num"><em><?= number_format($stats['avg'], 1, ',', ' ') ?></em></div>
    <div class="lbl" data-en="Average rating">Note moyenne / 5</div>
  </div>
  <div class="av-stat">
    <div class="num"><?= $stats['total'] ?></div>
    <div class="lbl" data-en="Reviews collected">Avis collectés</div>
  </div>
  <div class="av-stat">
    <div class="num"><?= (int)$stats['by_offer']['bb'] ?></div>
    <div class="lbl" data-en="B&amp;B reviews">Chambres d'hôtes</div>
  </div>
  <div class="av-stat">
    <div class="num"><?= (int)$stats['by_offer']['villa'] ?></div>
    <div class="lbl" data-en="Villa reviews">Villa entière</div>
  </div>
</section>
<?php endif; ?>

<!-- FILTRES -->
<div class="av-filters">
  <span class="lbl" data-en="Filter">Filtrer</span>
  <?php foreach (['all' => 'Toutes', 'bb' => "Chambres d'hôtes", 'villa' => 'Villa entière'] as $key => $label): ?>
    <a class="av-filter <?= $offerFilter === $key ? 'active' : '' ?>" href="?<?= $key === 'all' ? '' : 'offer=' . $key ?>"><?= htmlspecialchars($label) ?></a>
  <?php endforeach; ?>
</div>

<!-- GRID -->
<?php if (empty($reviews)): ?>
<div class="av-empty">Aucun avis pour ce filtre.</div>
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
    <button type="button" class="av-card-expand" hidden aria-expanded="false">Lire la suite &rarr;</button>
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
  var labelOpen = 'Réduire ↑';
  var labelShut = 'Lire la suite →';
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
  <h2>Préparer votre <em>séjour</em>.</h2>
  <p>Une lettre, un appel, et nous vous répondons à la main, dans la journée.</p>
  <a href="<?= \LangService::url('contact') ?>" class="btn">Demander un séjour →</a>
</section>
