<?php
declare(strict_types=1);
/**
 * Bloc « piscine » V8.
 *
 * Section avec label + h2 + lede, puis grande image 21:9 full-width,
 * puis un `.pool-block` : paragraphe + note à gauche, liste de features à droite.
 *
 * Schéma JSON :
 *   - label_numeral, label_text
 *   - heading  (mini-md)
 *   - lede     (paragraphe court sous le titre)
 *   - image_id (vp_media, grande image 21:9 sous le titre)
 *   - text     (paragraphe principal du pool-block)
 *   - note     (paragraphe gris sous le text, optionnel)
 *   - features[] : tableau de strings (liste à droite)
 *   - surface ('default'|'stone'|'sage'|'sage-light')
 *   - anchor_id  : id HTML (ex. 'piscine')
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$lede          = $lede          ?? '';
$image_id      = $image_id      ?? null;
$text          = $text          ?? '';
$note          = $note          ?? '';
$features      = $features      ?? [];
$surface       = $surface       ?? 'default';
$anchor_id     = $anchor_id     ?? '';

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';

$imgUrl = $image_id ? ImageService::urlById((int)$image_id) : null;
$imgAlt = $image_id ? ImageService::altById((int)$image_id) : '';

$idAttr = $anchor_id !== '' ? ' id="' . htmlspecialchars($anchor_id) . '"' : '';
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $idAttr ?><?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">

    <?php if ($label_numeral !== '' || $label_text !== ''): ?>
    <div class="section-label">
      <span class="numeral"><?= htmlspecialchars($label_numeral) ?><?= ($label_numeral !== '' && $label_text !== '') ? ' / ' : '' ?><?= TextService::renderTitle($label_text) ?></span>
    </div>
    <?php endif; ?>

    <?php if ($heading !== ''): ?>
    <h2 class="h-xl" style="margin: 0 0 28px; max-width: 18ch;"><?= TextService::renderTitle($heading) ?></h2>
    <?php endif; ?>

    <?php if ($lede !== ''): ?>
    <p class="lede" style="margin: 0; max-width: 52ch;"><?= htmlspecialchars($lede) ?></p>
    <?php endif; ?>

    <?php if ($imgUrl): ?>
    <div style="aspect-ratio: 21/9; background: center/cover url('<?= htmlspecialchars($imgUrl) ?>'); margin-top: clamp(32px, 4vw, 56px);" role="img" aria-label="<?= htmlspecialchars($imgAlt) ?>"></div>
    <?php endif; ?>

    <?php if ($text !== '' || $note !== '' || !empty($features)): ?>
    <div class="pool-block">
      <div>
        <?php if ($text !== ''): ?>
        <p class="body-lg" style="margin: 0 0 16px; max-width: 56ch;"><?= nl2br(htmlspecialchars($text), false) ?></p>
        <?php endif; ?>
        <?php if ($note !== ''): ?>
        <p class="body" style="margin: 0; max-width: 56ch; color: var(--stone-600);"><?= nl2br(htmlspecialchars($note), false) ?></p>
        <?php endif; ?>
      </div>
      <?php if (!empty($features)): ?>
      <ul class="pool-features">
        <?php foreach ($features as $f): ?>
        <li><?= htmlspecialchars((string)$f) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </div>
</section>
