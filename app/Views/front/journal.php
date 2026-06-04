<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Journal Tourisme (magazine avec filtres).
 * Portée depuis le proto Claude design (journal-tourisme.html).
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  /* Magazine masthead */
  .j-masthead {
    background: var(--linen-50);
    border-bottom: var(--hairline);
  }
  .j-masthead-inner {
    padding: clamp(64px, 8vw, 120px) var(--gutter) clamp(40px, 5vw, 64px);
    max-width: var(--container-wide);
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(32px, 5vw, 80px);
    align-items: end;
  }
  .j-issue {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em;
    color: var(--stone-500); text-transform: uppercase;
    display: flex; justify-content: space-between; align-items: baseline;
    border-bottom: var(--hairline);
    padding-bottom: 14px; margin-bottom: 28px;
  }
  .j-title {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(56px, 9vw, 140px); line-height: 0.92; letter-spacing: -0.025em;
    color: var(--ink-900);
  }
  .j-title em { font-style: italic; color: var(--sage-700); }
  .j-lede {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(20px, 1.8vw, 26px); line-height: 1.4; letter-spacing: -0.005em;
    color: var(--ink-700); max-width: 42ch; margin: 0;
  }
  @media (max-width: 720px) { .j-masthead-inner { grid-template-columns: 1fr; } }

  /* Sticky filter bar */
  .j-filter {
    position: sticky;
    top: 60px;
    z-index: 30;
    background: color-mix(in oklab, var(--linen-50) 96%, transparent);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border-bottom: var(--hairline);
  }
  .j-filter-inner {
    max-width: var(--container-wide); margin: 0 auto;
    padding: 14px var(--gutter);
    display: flex; gap: 6px; align-items: center;
    overflow-x: auto;
    scrollbar-width: none;
  }
  .j-filter-inner::-webkit-scrollbar { display: none; }
  .j-filter button {
    flex-shrink: 0;
    background: transparent; border: 1px solid color-mix(in oklab, var(--ink-900) 15%, transparent);
    color: var(--stone-600);
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 10px 16px; cursor: pointer;
    transition: background .2s, color .2s, border-color .2s;
  }
  .j-filter button:hover { color: var(--ink-900); border-color: var(--ink-900); }
  .j-filter button[aria-pressed="true"] { background: var(--ink-900); color: var(--linen-50); border-color: var(--ink-900); }
  .j-filter .count {
    margin-left: auto; flex-shrink: 0;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.12em;
    color: var(--stone-500); padding-left: 16px;
    border-left: var(--hairline);
  }

  /* Featured */
  .j-featured {
    display: grid; grid-template-columns: 1.2fr 1fr; gap: clamp(32px, 5vw, 72px);
    margin-bottom: clamp(48px, 6vw, 96px);
    align-items: center;
  }
  /* <img class="img"> : aspect 3/2 iso source, object-fit cover. Le
     background-size n'a plus d'effet depuis la migration <div> → <img>. */
  .j-featured img.img,
  .j-featured .img {
    aspect-ratio: 3/2;
    width: 100%; height: auto;
    object-fit: cover; object-position: center;
    display: block;
    background-size: cover; background-position: center; /* legacy fallback */
  }
  .j-featured .meta {
    display: flex; gap: 14px; align-items: baseline;
    margin-bottom: 18px;
  }
  .j-cat {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 6px 10px; background: var(--linen-100);
    color: var(--ink-900);
  }
  .j-cat.solid { background: var(--ink-900); color: var(--linen-50); }
  .j-cat.sage { background: color-mix(in oklab, var(--sage-200) 50%, transparent); }
  .j-date {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.1em;
    color: var(--stone-500);
  }
  .j-featured h2 {
    margin: 0 0 18px;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(34px, 4vw, 56px); line-height: 1.0; letter-spacing: -0.02em;
    color: var(--ink-900);
  }
  .j-featured h2 em { font-style: italic; }
  .j-featured .excerpt {
    font-size: 16px; line-height: 1.6; color: var(--ink-700); max-width: 50ch;
    margin: 0 0 18px;
  }
  .j-author {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.06em;
    color: var(--stone-600);
  }
  @media (max-width: 720px) { .j-featured { grid-template-columns: 1fr; } }

  /* Article grid , grille régulière 3 colonnes, aspect 3/2 partout pour
     cohérence visuelle complète (plus de variantes tall/wide). */
  .j-grid {
    display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: clamp(32px, 4vw, 56px) clamp(24px, 2.4vw, 36px);
  }
  .j-card {
    display: flex; flex-direction: column; gap: 16px;
    transition: transform .3s cubic-bezier(.2,.6,.2,1);
  }
  .j-card.hidden { display: none; }
  .j-card:hover { transform: translateY(-2px); }
  /* <img class="img"> : aspect 3/2 iso source, partout dans la grille.
     Plus de variantes tall (portrait) ou wide (panoramique) , l'uniformité
     prime sur la variation éditoriale. */
  .j-card img.img,
  .j-card .img {
    aspect-ratio: 3/2;
    width: 100%; height: auto;
    object-fit: cover; object-position: center;
    display: block;
    background-color: var(--linen-200);
    background-size: cover; background-position: center; /* legacy fallback */
    transition: filter .3s;
  }
  .j-card:hover img.img,
  .j-card:hover .img { filter: brightness(1.02); }
  @media (max-width: 960px) { .j-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 600px) { .j-grid { grid-template-columns: 1fr; } }
  /* Toute la card est cliquable : <a class="j-card"> et <a class="j-featured">. */
  a.j-card, a.j-featured { color: inherit; text-decoration: none; }
  a.j-card:hover, a.j-featured:hover { color: inherit; }
  a.j-card:focus-visible, a.j-featured:focus-visible { outline: 2px solid var(--terra-500); outline-offset: 4px; }
  .j-card h3 {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(22px, 2vw, 30px); line-height: 1.1; letter-spacing: -0.015em;
    color: var(--ink-900);
  }
  .j-card.lg h3 { font-size: clamp(28px, 2.6vw, 38px); }
  .j-card h3 em { font-style: italic; }
  .j-card .meta {
    display: flex; gap: 12px; align-items: baseline; flex-wrap: wrap;
    margin-bottom: -6px;
  }
  .j-card .excerpt {
    font-size: 14.5px; line-height: 1.6; color: var(--stone-600); max-width: 48ch;
    margin: 0;
  }

  /* Plus de grid-column inline : la grille gère son span via grid-template-columns. */

  /* Newsletter */
  .newsletter {
    background: var(--ink-900);
    color: var(--linen-100);
    padding: clamp(56px, 7vw, 96px) 0;
    border-top: var(--hairline-strong);
  }
  .nl-inner {
    max-width: 880px; margin: 0 auto;
    padding: 0 var(--gutter);
    text-align: center;
  }
  .nl-inner h2 {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(36px, 5vw, 64px); line-height: 1.0; letter-spacing: -0.02em;
    margin: 0 0 18px; color: var(--linen-50); text-wrap: balance;
  }
  .nl-inner h2 em { font-style: italic; color: var(--sage-200); }
  .nl-form {
    margin-top: 28px;
    display: flex; gap: 10px; max-width: 480px; margin-left: auto; margin-right: auto;
  }
  .nl-form input {
    flex: 1; background: transparent;
    border: 0; border-bottom: 1px solid rgba(var(--linen-50-rgb), 0.4);
    color: var(--linen-50);
    font: inherit; font-size: 16px;
    padding: 12px 0;
  }
  .nl-form input::placeholder { color: rgba(var(--linen-50-rgb), 0.5); }
  .nl-form input:focus { outline: 0; border-bottom-color: var(--linen-50); }

  /* Placeholder banner */
  .pl-banner {
    display: flex; align-items: center; gap: 14px;
    padding: 16px 22px;
    border: 1px dashed color-mix(in oklab, var(--ink-900) 30%, transparent);
    background: color-mix(in oklab, var(--terra-500) 5%, var(--linen-50));
    max-width: 720px;
    margin: 24px auto 0;
  }
  .pl-banner .dot { width: 8px; height: 8px; background: var(--terra-500); border-radius: 50%; flex-shrink: 0; }
  .pl-banner .txt { font-size: 13.5px; color: var(--ink-700); line-height: 1.55; }
  .pl-banner .lbl { font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em; color: var(--terra-600); display: block; margin-bottom: 2px; }
</style>


<!-- ============ MASTHEAD (BDD vp_sections or fallback HTML) ============ -->
<?php
$_v8HeroJournal = null;
foreach (BlockService::getSections('journal', $lang) as $_s) {
    if ((int)$_s['position'] === 1 && $_s['block_type'] === 'hero') {
        $_v8HeroJournal = BlockService::renderBlock($_s);
        break;
    }
}
?>
<?php if ($_v8HeroJournal): ?>
<?= $_v8HeroJournal ?>
<?php else: ?>
<section class="j-masthead">
  <div class="j-masthead-inner">
    <div>
      <div class="j-issue">
        <span data-en="Journal · 04 / Tourism">Journal · 04 / Tourisme</span>
        <span data-en="N° 01 · 2026">N° 01 · 2026</span>
      </div>
      <h1 class="j-title">Voyager <em>autrement</em><br/>en Provence.</h1>
    </div>
    <p class="j-lede" data-en="Five ways of looking at the region, what's changing in contemporary Provence, who keeps it alive, and how to travel through it without ticking boxes.">Cinq façons de regarder la région, ce qui bouge dans la Provence contemporaine, ceux qui la font vivre, et comment la traverser sans cocher de cases.</p>
  </div>

  <div class="pl-banner" style="margin-bottom: 28px;">
    <span class="dot"></span>
    <div class="txt">
      <span class="lbl" data-en="DESIGN MOCKUP">ÉBAUCHE GRAPHIQUE</span>
      Articles d'exemple. L'organisation et les filtres sont définitifs, le contenu sera remplacé par vos vrais articles.
    </div>
  </div>
</section>
<?php endif; ?>

<?php
// Rendu dynamique des articles depuis $articles (vp_articles type='journal').
// Le 1er = featured, les suivants = grille de j-card (alternance lg/normal/tall).
$articlesList = $articles ?? [];
$featured = $articlesList[0] ?? null;
$rest     = array_slice($articlesList, 1);

// Mapping catégorie BDD → slug pour data-cat (filtre JS) + couleur badge.
$catSlug = static function (string $cat): string {
    $map = [
        'Provence contemporaine'   => 'contemporaine',
        'Voyager autrement'        => 'autrement',
        'Hôtes & hôteliers'        => 'hotes',
        'Territoire & transition'  => 'transition',
        "L'art de séjourner"       => 'art',
    ];
    if (isset($map[$cat])) return $map[$cat];
    return strtolower(preg_replace('/[^a-z0-9]+/i', '-', $cat) ?: 'other');
};
// Badge sage (Hôtes & hôteliers) ou solid (featured), sinon normal.
$catClass = static function (string $cat, bool $solid = false): string {
    if ($solid) return 'j-cat solid';
    if (str_contains($cat, 'Hôtes')) return 'j-cat sage';
    return 'j-cat';
};
// Date FR "Mai 2026", fallback vide si null.
$frMonths = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
$dateFr = static function (?string $iso) use ($frMonths): string {
    if (!$iso) return '';
    $ts = strtotime($iso);
    if ($ts === false) return '';
    return $frMonths[(int)date('n', $ts)] . ' ' . date('Y', $ts);
};
// Cover image fallback pour les articles sans cover.
$coverOf = static function (array $a): string {
    return !empty($a['cover_image'])
        ? basename($a['cover_image'])
        : 'villa-plaisance-vignes-provence-01.webp';
};
// Grille régulière : toutes les cards en même format (aspect 3/2 uniforme).
// Catégories uniques présentes pour le filtre (depuis $categories controller).
$cats = array_values(array_filter($categories ?? [], fn($c) => $c !== null && $c !== ''));
?>

<!-- ============ FILTER ============ -->
<?php if (!empty($cats)): ?>
<div class="j-filter">
  <div class="j-filter-inner" role="tablist">
    <button data-cat="all" aria-pressed="true">Tous</button>
    <?php foreach ($cats as $c): ?>
    <button data-cat="<?= htmlspecialchars($catSlug($c)) ?>" aria-pressed="false"><?= htmlspecialchars($c) ?></button>
    <?php endforeach; ?>
    <span class="count" id="filter-count"><?= count($articlesList) ?> articles</span>
  </div>
</div>
<?php endif; ?>

<!-- ============ FEATURED + GRID ============ -->
<section class="section">
  <div class="container-wide">

    <?php if ($featured): ?>
    <a class="j-featured" href="/journal/<?= htmlspecialchars($featured['slug']) ?>" data-cat="<?= htmlspecialchars($catSlug((string)$featured['category'])) ?>">
      <?= ImageService::imgFromBg($coverOf($featured), 'img', true) ?>
      <div>
        <div class="meta">
          <span class="<?= $catClass((string)$featured['category'], true) ?>"><?= htmlspecialchars((string)$featured['category']) ?></span>
          <?php if ($d = $dateFr($featured['published_at'] ?? null)): ?>
          <span class="j-date"><?= htmlspecialchars($d) ?></span>
          <?php endif; ?>
        </div>
        <h2><em>À la une</em><br/><?= htmlspecialchars((string)$featured['title']) ?></h2>
        <?php if (!empty($featured['excerpt'])): ?>
        <p class="excerpt"><?= htmlspecialchars((string)$featured['excerpt']) ?></p>
        <?php endif; ?>
        <span class="btn-link">Lire l'article →</span>
      </div>
    </a>
    <?php endif; ?>

    <?php if (!empty($rest)): ?>
    <div class="j-grid" id="grid">
      <?php foreach ($rest as $a): ?>
      <a class="j-card" data-cat="<?= htmlspecialchars($catSlug((string)$a['category'])) ?>" href="/journal/<?= htmlspecialchars($a['slug']) ?>">
        <?= ImageService::imgFromBg($coverOf($a), 'img') ?>
        <div class="meta">
          <span class="<?= $catClass((string)$a['category']) ?>"><?= htmlspecialchars((string)$a['category']) ?></span>
          <?php if ($d = $dateFr($a['published_at'] ?? null)): ?>
          <span class="j-date"><?= htmlspecialchars($d) ?></span>
          <?php endif; ?>
        </div>
        <h3><?= htmlspecialchars((string)$a['title']) ?></h3>
        <?php if (!empty($a['excerpt'])): ?>
        <p class="excerpt"><?= htmlspecialchars((string)$a['excerpt']) ?></p>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($articlesList)): ?>
    <p class="body-lg" style="text-align: center; padding: clamp(40px, 6vw, 80px) 0; color: var(--stone-500);">Aucun article publié pour l'instant. Revenez bientôt.</p>
    <?php endif; ?>

  </div>
</section>

<!-- ============ NEWSLETTER ============ -->
<section class="newsletter">
  <div class="nl-inner">
    <div class="kicker dark" style="display: inline-flex; margin-bottom: 24px;"><span class="dot" style="background: var(--sage-200);"></span><span data-en="The letter">La lettre</span></div>
    <h2 data-en="The journal,/once a season, in your inbox.">Le journal, une fois<br/>par saison, dans <em>votre</em> boîte.</h2>
    <p class="body-lg" style="color: rgba(var(--linen-50-rgb), 0.7); max-width: 44ch; margin: 16px auto 0;" data-en="Four letters a year. The articles, the small news of the house, the things we'd whisper at breakfast.">Quatre lettres par an. Les articles, les petites nouvelles de la maison, ce qu'on glisserait au petit-déjeuner.</p>
    <form class="nl-form" onsubmit="event.preventDefault(); this.querySelector('button').textContent='Merci ·';">
      <input type="email" placeholder="votre@adresse.fr" required />
      <button type="submit" class="btn" style="background: var(--linen-50); color: var(--ink-900); border-color: var(--linen-50);" data-en="Subscribe →">S'inscrire →</button>
    </form>
  </div>
</section>

<!-- ============ FOOTER ============ -->
