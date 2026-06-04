<?php
declare(strict_types=1);
/**
 * Bloc « formula » V8, nouveau type exclusivement V8.
 *
 * Section avec un en-tête (label numeral + h2 + intro) puis une grille
 * de cartes « formules » (typiquement 2 : Chambres d'hôtes / Villa).
 *
 * Schéma JSON :
 *   - label_numeral  : ex. "02"
 *   - label_text     : ex. "Deux formules" (mini-md)
 *   - heading        : titre h2 de la section (mini-md, multiligne)
 *   - intro          : court paragraphe à droite du titre (optionnel)
 *   - surface        : 'default' | 'stone' | 'sage' | 'sage-light'
 *   - formulas       : tableau de cartes (cf. ci-dessous)
 *
 * Schéma d'une carte (item de `formulas`) :
 *   - label_numeral  : ex. "01"
 *   - label_period   : ex. "Sept → Juin"
 *   - label_tag      : ex. "Chez l'habitant" (libellé droite)
 *   - title          : ex. "Chambres d'hôtes" (rendu toujours en italique serif)
 *   - text           : description
 *   - stats          : tableau de libellés courts (stat-pills)
 *   - cta            : { label, url }   (style toujours btn-link en V8)
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$intro         = $intro         ?? '';
$surface       = $surface       ?? 'stone';   // par défaut stone (la signature V8)
$formulas      = $formulas      ?? [];

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';

// CTA URL avec LangService::url pour les liens internes
$resolveCtaUrl = static function (?string $url): ?string {
    if ($url === null || $url === '') return null;
    if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
        return LangService::url(ltrim($url, '/'));
    }
    return $url;
};
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">

    <?php if ($heading !== '' || $intro !== '' || $label_numeral !== '' || $label_text !== ''): ?>
    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: clamp(40px, 5vw, 64px); flex-wrap: wrap;">
      <div>
        <?php if ($label_numeral !== '' || $label_text !== ''): ?>
        <div class="section-label">
          <span class="numeral"><?= htmlspecialchars($label_numeral) ?><?= ($label_numeral !== '' && $label_text !== '') ? ' / ' : '' ?><?= TextService::renderTitle($label_text) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($heading !== ''): ?>
        <h2 class="h-xl" style="margin: 0; max-width: 16ch;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
      </div>
      <?php if ($intro !== ''): ?>
      <p class="body-lg" style="max-width: 38ch; margin: 0;"><?= htmlspecialchars($intro) ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="formulas">
      <?php foreach ($formulas as $f):
        $fNumeral = (string)($f['label_numeral'] ?? '');
        $fPeriod  = (string)($f['label_period']  ?? '');
        $fTag     = (string)($f['label_tag']     ?? '');
        $fTitle   = (string)($f['title']         ?? '');
        $fText    = (string)($f['text']          ?? '');
        $fStats   = $f['stats'] ?? [];
        $fCta     = $f['cta']   ?? null;
        $fCtaUrl  = $fCta ? $resolveCtaUrl($fCta['url'] ?? null) : null;
      ?>
      <article class="formula">
        <?php if ($fNumeral !== '' || $fPeriod !== '' || $fTag !== ''): ?>
        <div class="num">
          <span>
            <?= htmlspecialchars($fNumeral) ?><?= ($fNumeral !== '' && $fPeriod !== '') ? ' · ' : '' ?><?= htmlspecialchars($fPeriod) ?>
          </span>
          <?php if ($fTag !== ''): ?>
          <span><?= htmlspecialchars($fTag) ?></span>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($fTitle !== ''): ?>
        <h3><em><?= htmlspecialchars($fTitle) ?></em></h3>
        <?php endif; ?>

        <?php if ($fText !== ''): ?>
        <p class="desc"><?= nl2br(htmlspecialchars($fText), false) ?></p>
        <?php endif; ?>

        <?php if (!empty($fStats)): ?>
        <div class="stats">
          <?php foreach ($fStats as $stat): ?>
          <span class="stat-pill"><?= htmlspecialchars((string)$stat) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($fCta && !empty($fCta['label']) && $fCtaUrl): ?>
        <div class="cta">
          <a class="btn-link" href="<?= htmlspecialchars($fCtaUrl) ?>"><?= htmlspecialchars($fCta['label']) ?> →</a>
        </div>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>

  </div>
</section>
