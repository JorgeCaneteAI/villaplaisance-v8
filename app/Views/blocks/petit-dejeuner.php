<?php
declare(strict_types=1);
/**
 * Bloc « petit-dejeuner » V8.
 *
 * Layout signature : image à gauche, contenu à droite (label + h2 + texte + liste).
 *
 * Schéma JSON :
 *   - label_numeral
 *   - label_text
 *   - heading (mini-md)
 *   - text          : court paragraphe (pas multi-paragraphes, c'est court)
 *   - image_id      : vp_media.id pour l'image latérale
 *   - items[]       : tableau de strings (lignes de la liste)
 *   - surface       : 'default'|'stone'|'sage'|'sage-light'  (par défaut 'sage-light')
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$text          = $text          ?? '';
$image_id      = $image_id      ?? null;
$items         = $items         ?? [];
$surface       = $surface       ?? 'sage-light';

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';

$imgUrl = $image_id ? ImageService::urlById((int)$image_id) : null;
$imgAlt = $image_id ? ImageService::altById((int)$image_id) : '';
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">
    <div class="breakfast-layout">
      <?php if ($imgUrl): ?>
      <div class="breakfast-img" style="background-image: url('<?= htmlspecialchars($imgUrl) ?>')" role="img" aria-label="<?= htmlspecialchars($imgAlt) ?>"></div>
      <?php endif; ?>
      <div>
        <?php if ($label_numeral !== '' || $label_text !== ''): ?>
        <div class="section-label">
          <span class="numeral">— <?= htmlspecialchars($label_numeral) ?><?= ($label_numeral !== '' && $label_text !== '') ? ' / ' : '' ?><?= TextService::renderTitle($label_text) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($heading !== ''): ?>
        <h2 class="h-xl" style="margin: 0 0 20px; max-width: 18ch;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
        <?php if ($text !== ''): ?>
        <p class="body-lg" style="margin: 0 0 12px; max-width: 50ch;"><?= htmlspecialchars($text) ?></p>
        <?php endif; ?>
        <?php if (!empty($items)): ?>
        <div class="breakfast-list">
          <?php foreach ($items as $item): ?>
          <div class="item"><?= htmlspecialchars((string)$item) ?></div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
