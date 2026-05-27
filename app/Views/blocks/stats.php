<?php
declare(strict_types=1);
/**
 * Bloc « stats » V8.
 *
 * Displays supportés :
 *   - 'strip'  (signature V8 : bande pleine largeur sans section padding,
 *               cellules horizontales, separators hairline — utilisé sur villa)
 *   - 'grid'   (carte de chiffres clés dans une section avec header)
 *
 * Schéma JSON :
 *   - items[]    : tableau de { label, value }
 *   - display    ('strip'|'grid')   défaut: 'strip'
 *   - label_numeral, label_text, heading (uniquement display='grid')
 *   - surface    (uniquement display='grid')
 */

$items   = $items   ?? [];
$display = $display ?? 'strip';

if ($display === 'strip') {
    $n = max(1, count($items));
?>
<div style="border-bottom: var(--hairline);">
  <div class="container-wide" style="display: grid; grid-template-columns: repeat(<?= $n ?>, 1fr); gap: 0; border-left: var(--hairline); border-right: var(--hairline);">
    <?php foreach ($items as $i => $item):
      $label = (string)($item['label'] ?? '');
      $value = (string)($item['value'] ?? '');
      $isLast = ($i === $n - 1);
    ?>
    <div style="padding: 26px 24px;<?= $isLast ? '' : ' border-right: var(--hairline);' ?>">
      <?php if ($label !== ''): ?><div class="overline"><?= htmlspecialchars($label) ?></div><?php endif; ?>
      <?php if ($value !== ''): ?><div class="h-md" style="margin-top: 8px;"><?= TextService::renderTitle($value) ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php
} else {
    // display === 'grid'
    $label_numeral = $label_numeral ?? '';
    $label_text    = $label_text    ?? '';
    $heading       = $heading       ?? '';
    $surface       = $surface       ?? 'default';

    $surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
    $surfaceStyle = '';
    if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
    if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
    if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">
    <?php if ($heading !== '' || $label_numeral !== '' || $label_text !== ''): ?>
    <div style="margin-bottom: clamp(32px, 4vw, 56px);">
      <?php if ($label_numeral !== '' || $label_text !== ''): ?>
      <div class="section-label"><span class="numeral">— <?= htmlspecialchars($label_numeral) ?><?= ($label_numeral !== '' && $label_text !== '') ? ' / ' : '' ?><?= TextService::renderTitle($label_text) ?></span></div>
      <?php endif; ?>
      <?php if ($heading !== ''): ?>
      <h2 class="h-xl" style="margin: 0; max-width: 18ch;"><?= TextService::renderTitle($heading) ?></h2>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: clamp(24px, 3vw, 48px);">
      <?php foreach ($items as $item):
        $label = (string)($item['label'] ?? '');
        $value = (string)($item['value'] ?? '');
      ?>
      <div>
        <?php if ($value !== ''): ?><div style="font-family: var(--font-display); font-size: clamp(36px, 4vw, 56px); margin: 0; line-height: 1;"><?= TextService::renderTitle($value) ?></div><?php endif; ?>
        <?php if ($label !== ''): ?><div class="overline" style="margin-top: 12px;"><?= htmlspecialchars($label) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php
}
