<?php
declare(strict_types=1);
/**
 * Bloc « interior » V8, nouveau type.
 *
 * Section avec un header (label + h2) puis une grille `.interior` de cards.
 * Chaque card = image + kicker (mono uppercase) + titre italique serif + texte.
 * Pas de CTA (descriptif d'espace, pas de teaser).
 *
 * Schéma JSON :
 *   - label_numeral, label_text
 *   - heading (mini-md)
 *   - surface ('default'|'stone'|'sage'|'sage-light')
 *   - items[] : { image_id, kicker, title (mini-md), text }
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$surface       = $surface       ?? 'stone';   // signature villa = stone
$items         = $items         ?? [];

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">

    <?php if ($heading !== '' || $label_numeral !== '' || $label_text !== ''): ?>
    <div style="margin-bottom: clamp(40px, 5vw, 64px);">
      <?php if ($label_numeral !== '' || $label_text !== ''): ?>
      <div class="section-label">
        <span class="numeral"><?= htmlspecialchars($label_numeral) ?><?= ($label_numeral !== '' && $label_text !== '') ? ' / ' : '' ?><?= TextService::renderTitle($label_text) ?></span>
      </div>
      <?php endif; ?>
      <?php if ($heading !== ''): ?>
      <h2 class="h-xl" style="margin: 0; max-width: 18ch;"><?= TextService::renderTitle($heading) ?></h2>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
    <div class="interior">
      <?php foreach ($items as $item):
        $imgId  = isset($item['image_id']) ? (int)$item['image_id'] : null;
        $imgUrl = $imgId ? ImageService::urlById($imgId) : null;
        $imgAlt = $imgId ? ImageService::altById($imgId) : '';
        $kicker = (string)($item['kicker'] ?? '');
        $title  = (string)($item['title']  ?? '');
        $text   = (string)($item['text']   ?? '');
      ?>
      <div>
        <?php if ($imgUrl): ?>
        <?= ImageService::imgFromBg(basename($imgUrl), 'img') ?>
        <?php endif; ?>
        <div>
          <?php if ($kicker !== ''): ?>
          <div class="numeral" style="margin-bottom: 8px;"><?= htmlspecialchars($kicker) ?></div>
          <?php endif; ?>
          <?php if ($title !== ''): ?>
          <h3 class="h-md" style="margin: 0 0 8px;"><em><?= TextService::renderTitle($title) ?></em></h3>
          <?php endif; ?>
          <?php if ($text !== ''): ?>
          <p class="body" style="margin: 0; max-width: 50ch;"><?= nl2br(htmlspecialchars($text), false) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</section>
