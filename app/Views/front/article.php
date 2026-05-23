<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Article détaillé (rendue par /journal/{slug} ET /sur-place/{slug}).
 * Portée en design proto. Layout : front-proto.
 * Logique conservée 100% : breadcrumb conditionnel, contentBlocks (5 types :
 *   heading, paragraph, image, quote, list), excerpt en lead, fallback texte.
 *
 * @var string $lang
 * @var array  $seo
 * @var array  $jsonLd
 * @var array  $article        row de vp_articles
 * @var array  $contentBlocks  JSON décodé du champ content
 */
$isOnSite = ($article['type'] ?? '') === 'sur-place';
$backUrl     = LangService::url($isOnSite ? 'sur-place' : 'journal');
$backLabel   = $isOnSite ? 'Retour à Sur place' : 'Retour au journal';
$backLabelEn = $isOnSite ? 'Back to On site'    : 'Back to the journal';
$listLabel   = $isOnSite ? 'Sur place'          : 'Journal';
$listLabelEn = $isOnSite ? 'On site'            : 'Journal';
$kicker      = $isOnSite ? '07 · Sur place'     : '05 · Journal';
$kickerEn    = $isOnSite ? '07 · On site'       : '05 · Journal';
?>
<style>
  .art-page { padding-top: clamp(72px, 9vw, 120px); }
  .art-page .page-hero { padding-top: 0; }
  .art-crumbs {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em;
    color: var(--stone-500); text-transform: uppercase;
    margin: 0 0 24px;
  }
  .art-crumbs a { color: var(--stone-500); }
  .art-crumbs a:hover { color: var(--ink-900); }
  .art-crumbs .sep { margin: 0 8px; opacity: 0.5; }

  .art-cover {
    margin: 56px 0 56px;
    aspect-ratio: 16/9;
    overflow: hidden;
    background: var(--linen-200);
  }
  .art-cover img { width: 100%; height: 100%; object-fit: cover; display: block; }

  .art-body {
    max-width: 720px;
    font-family: var(--font-sans); font-size: 17px; line-height: 1.7; color: var(--ink-700);
  }
  .art-lead {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(22px, 1.9vw, 28px); line-height: 1.4; letter-spacing: -0.005em;
    color: var(--ink-900);
    margin: 0 0 48px;
    padding-bottom: 36px; border-bottom: var(--hairline);
    max-width: 36ch;
  }
  .art-body h2 {
    font-family: var(--font-display); font-weight: 500;
    font-size: clamp(24px, 2.4vw, 34px); line-height: 1.2; letter-spacing: -0.01em;
    color: var(--ink-900);
    margin: 56px 0 16px;
  }
  .art-body p { margin: 0 0 20px; }
  .art-body strong { color: var(--ink-900); font-weight: 600; }
  .art-body em { font-style: italic; color: var(--sage-700); }
  .art-body a { color: var(--sage-700); text-decoration: underline; text-underline-offset: 3px; }
  .art-body a:hover { color: var(--olive-900); }
  .art-body ul { margin: 0 0 24px; padding-left: 22px; }
  .art-body li { margin: 0 0 10px; }
  .art-body figure { margin: 36px 0; }
  .art-body figure img { width: 100%; height: auto; display: block; }
  .art-body figcaption {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.06em;
    color: var(--stone-500); text-transform: uppercase;
    margin-top: 10px;
  }
  .art-body blockquote {
    margin: 36px 0;
    padding: 28px 32px;
    background: color-mix(in oklab, var(--sage-200) 45%, var(--linen-50));
    border-left: 3px solid var(--sage-700);
    font-family: var(--font-display); font-style: italic;
    font-size: clamp(20px, 1.7vw, 24px); line-height: 1.4; color: var(--ink-900);
    text-wrap: balance;
  }

  .art-back {
    margin-top: 96px; padding-top: 36px; border-top: var(--hairline);
    max-width: 720px;
  }
  .art-back a {
    display: inline-flex; align-items: center; gap: 10px;
    font-family: var(--font-sans); font-size: 13.5px; letter-spacing: 0.02em;
    color: var(--olive-900);
    padding-bottom: 3px; border-bottom: 1px solid var(--olive-900);
    transition: gap .2s, color .2s, border-color .2s;
  }
  .art-back a:hover { gap: 14px; color: var(--sage-700); border-color: var(--sage-700); }
  .art-back svg { transform: scaleX(-1); }
</style>

<section class="section art-page">
  <div class="container-wide">

    <nav class="art-crumbs" aria-label="Fil d'Ariane">
      <a href="<?= LangService::url('/') ?>" data-en="Home">Accueil</a>
      <span class="sep" aria-hidden="true">/</span>
      <a href="<?= $backUrl ?>" data-en="<?= htmlspecialchars($listLabelEn) ?>"><?= htmlspecialchars($listLabel) ?></a>
      <span class="sep" aria-hidden="true">/</span>
      <span aria-current="page"><?= htmlspecialchars($article['title']) ?></span>
    </nav>

    <div class="page-hero">
      <div class="page-hero-inner">
        <div>
          <div class="page-hero-issue">
            <span data-en="<?= htmlspecialchars($kickerEn) ?>"><?= htmlspecialchars($kicker) ?></span>
            <?php if (!empty($article['published_at'])): ?>
            <span>
              <time datetime="<?= htmlspecialchars($article['published_at']) ?>"><?= date('j F Y', strtotime($article['published_at'])) ?></time>
              <?php if (!empty($article['category'])): ?> &middot; <?= htmlspecialchars($article['category']) ?><?php endif; ?>
            </span>
            <?php elseif (!empty($article['category'])): ?>
            <span><?= htmlspecialchars($article['category']) ?></span>
            <?php endif; ?>
          </div>
          <h1><?= htmlspecialchars($article['title']) ?></h1>
        </div>
        <?php if (!empty($article['excerpt'])): ?>
        <p class="lede"><?= htmlspecialchars($article['excerpt']) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($article['cover_image'])): ?>
    <figure class="art-cover">
      <?= \ImageService::img($article['cover_image'], htmlspecialchars($article['title']), 1600, 900) ?>
    </figure>
    <?php endif; ?>

    <div class="art-body">

      <?php if (!empty($article['excerpt'])): ?>
      <p class="art-lead"><?= htmlspecialchars($article['excerpt']) ?></p>
      <?php endif; ?>

      <?php if (!empty($contentBlocks)): ?>
        <?php foreach ($contentBlocks as $block): ?>
          <?php if (is_string($block)): ?>
            <?= $block ?>
          <?php elseif (is_array($block)): ?>
            <?php $btype = $block['type'] ?? ''; ?>
            <?php if ($btype === 'heading'): ?>
              <h2><?= htmlspecialchars($block['text'] ?? '') ?></h2>
            <?php elseif ($btype === 'paragraph'): ?>
              <p><?= $block['text'] ?? '' ?></p>
            <?php elseif ($btype === 'image'): ?>
              <figure>
                <?= \ImageService::img($block['src'] ?? '', htmlspecialchars($block['alt'] ?? ''), 1200, 800) ?>
                <?php if (!empty($block['caption'])): ?>
                <figcaption><?= htmlspecialchars($block['caption']) ?></figcaption>
                <?php endif; ?>
              </figure>
            <?php elseif ($btype === 'quote'): ?>
              <blockquote><?= htmlspecialchars($block['text'] ?? '') ?></blockquote>
            <?php elseif ($btype === 'list'): ?>
              <ul>
                <?php foreach (($block['items'] ?? []) as $item): ?>
                <li><?php
                  $safe = htmlspecialchars($item);
                  if (str_contains($safe, ' : ')) {
                    [$label, $rest] = explode(' : ', $safe, 2);
                    echo '<strong>' . $label . '</strong> : ' . $rest;
                  } else {
                    echo $safe;
                  }
                ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <p><?= nl2br(htmlspecialchars($article['content'] ?? '')) ?></p>
      <?php endif; ?>

    </div>

    <div class="art-back">
      <a href="<?= $backUrl ?>" data-en="<?= htmlspecialchars($backLabelEn) ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        <?= htmlspecialchars($backLabel) ?>
      </a>
    </div>

  </div>
</section>
