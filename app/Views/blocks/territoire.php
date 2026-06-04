<?php
declare(strict_types=1);
/**
 * Bloc « territoire » V8, Triangle d'Or.
 *
 * Section two-col : label + h2 + intro à gauche, liste de destinations à droite.
 *
 * Schéma JSON :
 *   - label_numeral  : ex. "03"
 *   - label_text     : ex. "Où nous sommes" (mini-md)
 *   - heading        : ex. "Au cœur du\n*Triangle d'Or*."  (mini-md)
 *   - intro          : court paragraphe sous le titre (optionnel)
 *   - surface        : 'default' | 'stone' | 'sage' | 'sage-light'
 *   - destinations   : tableau de [ { time, place, tag } ]
 *       - time   : ex. "8 MIN" (rendu tel quel)
 *       - place  : ex. "Châteauneuf-du-Pape" (rendu en italique serif via <em>)
 *       - tag    : ex. "Vignes" (sous-titre / catégorie)
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$intro         = $intro         ?? '';
$surface       = $surface       ?? 'default';
$destinations  = $destinations  ?? [];

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">
    <div class="two-col">
      <div>
        <?php if ($label_numeral !== '' || $label_text !== ''): ?>
        <div class="section-label">
          <span class="numeral"><?= htmlspecialchars($label_numeral) ?><?= ($label_numeral !== '' && $label_text !== '') ? ' / ' : '' ?><?= TextService::renderTitle($label_text) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($heading !== ''): ?>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
        <?php if ($intro !== ''): ?>
        <p class="body-lg" style="max-width: 40ch; margin: 24px 0 0; color: var(--stone-600);"><?= htmlspecialchars($intro) ?></p>
        <?php endif; ?>
      </div>
      <?php if (!empty($destinations)): ?>
      <ul class="destinations">
        <?php foreach ($destinations as $d): ?>
          <?php
            $dTime  = (string)($d['time']  ?? '');
            $dPlace = (string)($d['place'] ?? '');
            $dTag   = (string)($d['tag']   ?? '');
          ?>
        <li>
          <?php if ($dTime !== ''): ?>
          <span class="time"><?= htmlspecialchars($dTime) ?></span>
          <?php endif; ?>
          <?php if ($dPlace !== ''): ?>
          <span class="place"><em><?= htmlspecialchars($dPlace) ?></em></span>
          <?php endif; ?>
          <?php if ($dTag !== ''): ?>
          <span class="km"><?= htmlspecialchars($dTag) ?></span>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
  </div>
</section>
