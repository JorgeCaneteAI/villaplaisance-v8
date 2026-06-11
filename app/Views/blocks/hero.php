<?php
declare(strict_types=1);
/**
 * Bloc « hero » V8 (design impeccable, page-hero has-image).
 *
 * @var array       $section      Ligne vp_sections complète
 * @var string|null $title        Mini-md autorisé (*italique*, retour ligne)
 * @var string|null $lede         Texte d'intro
 * @var int|null    $image_id     Référence vp_media.id pour le fond
 * @var array       $tags         Badges affichés au-dessus du h1
 * @var array       $ctas         [{ label, url, style: 'primary'|'ghost' }]
 * @var string|null $surface      'default' | 'stone' | 'sage' | 'sage-light'
 */

$title    = $title    ?? '';
$lede     = $lede     ?? '';
$image_id = $image_id ?? null;
$tags     = $tags     ?? [];
$ctas     = $ctas     ?? [];

// Résolution du fond depuis vp_media
$bgUrl    = $image_id ? ImageService::urlById((int)$image_id) : null;
$bgAlt    = $image_id ? ImageService::altById((int)$image_id) : '';
$bgMedia  = $image_id ? ImageService::getById((int)$image_id) : null;
$bgWidth  = (int)($bgMedia['width']  ?? 2400);
$bgHeight = (int)($bgMedia['height'] ?? 1600);
$hasImage = $bgUrl !== null;
?>
<section class="page-hero<?= $hasImage ? ' has-image' : '' ?>">
  <?php if ($hasImage): ?>
  <img class="bg" src="<?= htmlspecialchars($bgUrl) ?>" alt="<?= htmlspecialchars($bgAlt) ?>" width="<?= $bgWidth ?>" height="<?= $bgHeight ?>" loading="eager" fetchpriority="high" decoding="async">
  <?php endif; ?>
  <div class="page-hero-inner">
    <div>
      <?php if (!empty($tags)): ?>
      <div class="page-hero-issue">
        <?php foreach ($tags as $tag): ?>
        <span><?= htmlspecialchars((string)$tag) ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <?php if ($title !== ''): ?>
      <?php // Home : le H1 visible est le wordmark seul. On le complète pour les
            // lecteurs d'écran et les moteurs avec l'offre + la géo (reprend ce
            // que l'issue-line et le lede adjacents affichent déjà à l'écran). ?>
      <h1><?= TextService::renderTitle($title) ?><?php if (($section['page_slug'] ?? '') === 'accueil'): ?><span class="sr-only"><?= htmlspecialchars(t('home.hero.h1_suffix')) ?></span><?php endif; ?></h1>
      <?php endif; ?>
    </div>
    <?php if ($lede !== '' || !empty($ctas)): ?>
    <div>
      <?php if ($lede !== ''): ?>
      <p class="lede"><?= htmlspecialchars($lede) ?></p>
      <?php endif; ?>
      <?php if (!empty($ctas)): ?>
      <div class="page-hero-ctas">
        <?php foreach ($ctas as $cta): ?>
          <?php
            $label = (string)($cta['label'] ?? '');
            $url   = (string)($cta['url']   ?? '#');
            $style = (string)($cta['style'] ?? 'primary');
            // LangService::url pour les liens internes
            if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
                $url = LangService::url(ltrim($url, '/'));
            }
            $btnClass = $style === 'ghost' ? 'btn btn-ghost' : 'btn';
          ?>
          <a href="<?= htmlspecialchars($url) ?>" class="<?= $btnClass ?>"><?= htmlspecialchars($label) ?> →</a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
