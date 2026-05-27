<?php
declare(strict_types=1);
/**
 * Bloc « liste » V8.
 *
 * Displays supportés :
 *   - 'numbered-grid' (signature V8 : grille .equip-grid avec numérotation auto 01..N)
 *   - 'simple'        (liste verticale classique)
 *
 * Schéma JSON :
 *   - label_numeral, label_text
 *   - heading (mini-md)
 *   - intro   (court paragraphe à droite du titre, en layout split)
 *   - surface ('default'|'stone'|'sage'|'sage-light')
 *   - display ('numbered-grid'|'simple')   défaut: 'simple'
 *   - items[]   : tableau de strings (libellés)
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$intro         = $intro         ?? '';
$surface       = $surface       ?? 'default';
$display       = $display       ?? 'simple';
$items         = $items         ?? [];

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';

$hasHeader = ($heading !== '' || $intro !== '' || $label_numeral !== '' || $label_text !== '');
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">

    <?php if ($hasHeader): ?>
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: clamp(32px, 5vw, 96px); align-items: end; margin-bottom: clamp(40px, 5vw, 64px);">
      <div>
        <?php if ($label_numeral !== '' || $label_text !== ''): ?>
        <div class="section-label">
          <span class="numeral">— <?= htmlspecialchars($label_numeral) ?><?= ($label_numeral !== '' && $label_text !== '') ? ' / ' : '' ?><?= TextService::renderTitle($label_text) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($heading !== ''): ?>
        <h2 class="h-xl" style="margin: 0; max-width: 16ch;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
      </div>
      <?php if ($intro !== ''): ?>
      <p class="body-lg" style="margin: 0; max-width: 44ch;"><?= htmlspecialchars($intro) ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
      <?php if ($display === 'numbered-grid'): ?>
      <div class="equip-grid">
        <?php foreach ($items as $i => $item): ?>
        <div class="equip">
          <span class="n"><?= sprintf('%02d', $i + 1) ?></span>
          <span class="l"><?= htmlspecialchars((string)$item) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: /* simple */ ?>
      <ul class="list-simple" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
        <?php foreach ($items as $item): ?>
        <li class="body-lg"><?= htmlspecialchars((string)$item) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    <?php endif; ?>

  </div>
</section>
