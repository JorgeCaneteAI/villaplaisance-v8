<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Sur place (liste de recommandations).
 * Portée en design proto. Layout : front-proto.
 * Dynamisée depuis vp_articles (type = 'sur-place'). 1 featured + N cards.
 * Filtres client-side sur la colonne `category` (pas de query param).
 *
 * @var string $lang
 * @var array  $seo
 * @var array  $jsonLd
 * @var array  $articles    rows de vp_articles
 * @var array  $categories  list($cat) unique
 */
$featured = !empty($articles) ? array_shift($articles) : null;
?>
<style>
  .sp-hero { padding-top: clamp(72px, 9vw, 120px); }
  .sp-hero .page-hero { padding-top: 0; }
  .sp-crumbs {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em;
    color: var(--stone-500); text-transform: uppercase;
    margin: 0 0 24px;
  }
  .sp-crumbs a { color: var(--stone-500); }
  .sp-crumbs a:hover { color: var(--ink-900); }
  .sp-crumbs .sep { margin: 0 8px; opacity: 0.5; }

  /* Filtres */
  .sp-filters {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin-top: 56px; padding-top: 32px; border-top: var(--hairline);
  }
  .sp-filter {
    background: transparent; border: 1px solid color-mix(in oklab, var(--ink-900) 18%, transparent);
    color: var(--ink-700);
    font: inherit; font-size: 13px; letter-spacing: 0.02em;
    padding: 8px 16px;
    cursor: pointer;
    transition: background .2s, color .2s, border-color .2s;
  }
  .sp-filter:hover { border-color: var(--olive-900); color: var(--olive-900); }
  .sp-filter.active { background: var(--olive-900); border-color: var(--olive-900); color: var(--linen-50); }

  /* Featured */
  .sp-featured {
    display: grid; grid-template-columns: 1.1fr 1fr; gap: clamp(24px, 4vw, 64px);
    align-items: center;
    margin-top: 56px; padding-bottom: 56px; border-bottom: var(--hairline);
  }
  @media (max-width: 960px) { .sp-featured { grid-template-columns: 1fr; } }
  .sp-featured .sp-img { aspect-ratio: 4/3; overflow: hidden; background: var(--linen-200); }
  .sp-featured .sp-img img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .6s ease; }
  .sp-featured:hover .sp-img img { transform: scale(1.03); }
  .sp-featured .sp-body { display: flex; flex-direction: column; gap: 16px; }
  .sp-featured .sp-meta {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em;
    text-transform: uppercase; color: var(--stone-500);
  }
  .sp-featured h2 {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(28px, 3.6vw, 48px); line-height: 1.05; letter-spacing: -0.015em;
    color: var(--ink-900); margin: 0; max-width: 18ch;
  }
  .sp-featured h2 em { font-style: italic; color: var(--sage-700); }
  .sp-featured .sp-teaser { font-family: var(--font-sans); font-size: 16px; line-height: 1.55; color: var(--stone-600); margin: 0; max-width: 42ch; }
  .sp-featured .sp-cta {
    display: inline-flex; align-items: center; gap: 10px; align-self: flex-start;
    font-size: 13.5px; letter-spacing: 0.02em; color: var(--olive-900);
    padding-bottom: 3px; border-bottom: 1px solid var(--olive-900);
    transition: gap .2s, color .2s, border-color .2s;
  }
  .sp-featured .sp-cta:hover { gap: 14px; color: var(--sage-700); border-color: var(--sage-700); }

  /* Grille */
  .sp-grid {
    display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: clamp(24px, 3vw, 40px);
    margin-top: 56px;
  }
  @media (max-width: 960px) { .sp-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 540px) { .sp-grid { grid-template-columns: 1fr; } }
  .sp-card { display: flex; flex-direction: column; gap: 14px; }
  .sp-card .sp-img { aspect-ratio: 4/3; overflow: hidden; background: var(--linen-200); }
  .sp-card .sp-img img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .6s ease; }
  .sp-card:hover .sp-img img { transform: scale(1.03); }
  .sp-card .sp-meta {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em;
    text-transform: uppercase; color: var(--stone-500);
  }
  .sp-card h3 {
    font-family: var(--font-display); font-weight: 400;
    font-size: 22px; line-height: 1.2; letter-spacing: -0.005em;
    color: var(--ink-900); margin: 0;
  }
  .sp-card .sp-teaser { font-family: var(--font-sans); font-size: 14px; line-height: 1.55; color: var(--stone-600); margin: 0; }

  /* État vide */
  .sp-empty {
    margin-top: 56px; padding: 56px 24px;
    text-align: center; color: var(--stone-600);
    border: var(--hairline);
    font-family: var(--font-display); font-size: 22px; line-height: 1.3;
  }

  /* Filtre actif (masque les items non concernés) */
  .sp-hidden { display: none !important; }
</style>

<section class="section sp-hero">
  <div class="container-wide">

    <nav class="sp-crumbs" aria-label="Fil d'Ariane">
      <a href="<?= LangService::url('/') ?>" data-en="Home">Accueil</a>
      <span class="sep" aria-hidden="true">/</span>
      <span aria-current="page" data-en="On site">Sur place</span>
    </nav>

    <div class="page-hero">
      <div class="page-hero-inner">
        <div>
          <div class="page-hero-issue">
            <span data-en="07 · On site">07 · Sur place</span>
            <span data-en="Bédarrides &amp; around">Bédarrides &amp; alentours</span>
          </div>
          <h1 data-en="Our <em>addresses</em>.">Nos <em>adresses</em>.</h1>
        </div>
        <p class="lede" data-en="Shops, places to visit, tables we like, family outings — the addresses we give to our guests, written down so you can have them too.">Commerces, sites à visiter, tables qu'on aime, sorties en famille — les adresses qu'on donne à nos hôtes, mises par écrit pour que vous les ayez aussi.</p>
      </div>
    </div>

    <!-- Filtres dynamiques depuis $categories -->
    <?php if (!empty($categories)): ?>
    <div class="sp-filters" role="tablist" aria-label="Filtrer par catégorie">
      <button class="sp-filter active" type="button" data-cat="all" data-en="All">Tous</button>
      <?php foreach ($categories as $cat): if ($cat === null || $cat === '') continue; ?>
      <button class="sp-filter" type="button" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Featured -->
    <?php if ($featured): ?>
    <a class="sp-featured" data-cat="<?= htmlspecialchars($featured['category'] ?? '') ?>" href="<?= LangService::url('sur-place') ?>/<?= htmlspecialchars($featured['slug']) ?>">
      <figure class="sp-img">
        <?php if (!empty($featured['cover_image'])): ?>
          <?= \ImageService::img($featured['cover_image'], htmlspecialchars($featured['title']), 1200, 900) ?>
        <?php else: ?>
          <img src="/assets/img/placeholder/sur-place.webp" alt="" loading="lazy" decoding="async">
        <?php endif; ?>
      </figure>
      <div class="sp-body">
        <p class="sp-meta">
          <?php if (!empty($featured['published_at'])): ?>
          <time datetime="<?= htmlspecialchars($featured['published_at']) ?>"><?= date('j M Y', strtotime($featured['published_at'])) ?></time>
          <?php endif; ?>
          <?php if (!empty($featured['category'])): ?>
          <?php if (!empty($featured['published_at'])): ?> &middot; <?php endif; ?>
          <?= htmlspecialchars($featured['category']) ?>
          <?php endif; ?>
        </p>
        <h2><?= htmlspecialchars($featured['title']) ?></h2>
        <?php if (!empty($featured['excerpt'])): ?>
        <p class="sp-teaser"><?= htmlspecialchars($featured['excerpt']) ?></p>
        <?php endif; ?>
        <span class="sp-cta"><span data-en="Read the piece">Lire l'article</span> &rarr;</span>
      </div>
    </a>
    <?php endif; ?>

    <!-- Grille -->
    <?php if (!empty($articles)): ?>
    <div class="sp-grid">
      <?php foreach ($articles as $a): ?>
      <a class="sp-card" data-cat="<?= htmlspecialchars($a['category'] ?? '') ?>" href="<?= LangService::url('sur-place') ?>/<?= htmlspecialchars($a['slug']) ?>">
        <figure class="sp-img">
          <?php if (!empty($a['cover_image'])): ?>
            <?= \ImageService::img($a['cover_image'], htmlspecialchars($a['title']), 800, 600) ?>
          <?php else: ?>
            <img src="/assets/img/placeholder/sur-place.webp" alt="" loading="lazy" decoding="async">
          <?php endif; ?>
        </figure>
        <p class="sp-meta">
          <?php if (!empty($a['published_at'])): ?>
          <time datetime="<?= htmlspecialchars($a['published_at']) ?>"><?= date('j M Y', strtotime($a['published_at'])) ?></time>
          <?php endif; ?>
          <?php if (!empty($a['category'])): ?>
          <?php if (!empty($a['published_at'])): ?> &middot; <?php endif; ?>
          <?= htmlspecialchars($a['category']) ?>
          <?php endif; ?>
        </p>
        <h3><?= htmlspecialchars($a['title']) ?></h3>
        <?php if (!empty($a['excerpt'])): ?>
        <p class="sp-teaser"><?= htmlspecialchars($a['excerpt']) ?></p>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!$featured && empty($articles)): ?>
    <div class="sp-empty" data-en="No recommendations yet. Come back soon.">Aucune recommandation pour l'instant. À bientôt.</div>
    <?php endif; ?>

  </div>
</section>

<script>
// Filtre client-side simple sur data-cat
(function() {
  var filters = document.querySelectorAll('.sp-filter');
  var items = document.querySelectorAll('.sp-featured, .sp-card');
  if (!filters.length || !items.length) return;
  filters.forEach(function(f) {
    f.addEventListener('click', function() {
      filters.forEach(function(x) { x.classList.remove('active'); x.setAttribute('aria-pressed', 'false'); });
      f.classList.add('active');
      f.setAttribute('aria-pressed', 'true');
      var cat = f.dataset.cat;
      items.forEach(function(it) {
        if (cat === 'all' || it.dataset.cat === cat) {
          it.classList.remove('sp-hidden');
        } else {
          it.classList.add('sp-hidden');
        }
      });
    });
  });
})();
</script>
