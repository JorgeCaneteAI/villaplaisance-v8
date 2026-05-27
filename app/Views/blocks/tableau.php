<?php
declare(strict_types=1);
/**
 * Bloc « tableau » V8.
 *
 * Displays supportés :
 *   - 'key-value' (signature V8 : liste verticale .practical avec .row > .k + .v)
 *   - 'columns'   (tableau classique entêtes + lignes)
 *
 * Schéma JSON :
 *   - label_numeral, label_text
 *   - heading (mini-md), intro
 *   - surface ('default'|'stone'|'sage'|'sage-light')
 *   - display ('key-value'|'columns')   défaut: 'key-value'
 *   - anchor_id : id HTML pour ancres internes (ex. 'infos')
 *
 * Si display='key-value' :
 *   - rows[] : tableau de { key, value }   (value supporte mini-md gras/italique)
 *
 * Si display='columns' :
 *   - columns[] : tableau d'entêtes
 *   - rows[]    : tableau de tableaux (lignes)
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$intro         = $intro         ?? '';
$surface       = $surface       ?? 'default';
$display       = $display       ?? 'key-value';
$anchor_id     = $anchor_id     ?? '';
$columns       = $columns       ?? [];
$rows          = $rows          ?? [];

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';

$hasHeader = ($heading !== '' || $intro !== '' || $label_numeral !== '' || $label_text !== '');
$idAttr = $anchor_id !== '' ? ' id="' . htmlspecialchars($anchor_id) . '"' : '';
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $idAttr ?><?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
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
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
      </div>
      <?php if ($intro !== ''): ?>
      <p class="body-lg" style="margin: 0; max-width: 44ch;"><?= htmlspecialchars($intro) ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($display === 'key-value' && !empty($rows)): ?>
    <div class="practical">
      <?php foreach ($rows as $row):
        $k = (string)($row['key']   ?? '');
        $v = (string)($row['value'] ?? '');
      ?>
      <div class="row">
        <div class="k"><?= htmlspecialchars($k) ?></div>
        <div class="v"><?= TextService::renderTitle($v) ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php elseif ($display === 'columns' && !empty($rows)): ?>
    <table class="table-v8" style="width:100%; border-collapse: collapse;">
      <?php if (!empty($columns)): ?>
      <thead>
        <tr>
          <?php foreach ($columns as $col): ?>
          <th style="text-align:left; padding: 12px; border-bottom: var(--hairline-strong);"><?= htmlspecialchars((string)$col) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <?php endif; ?>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <?php foreach ((array)$row as $cell): ?>
          <td style="padding: 12px; border-bottom: var(--hairline);"><?= TextService::renderTitle((string)$cell) ?></td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

  </div>
</section>
