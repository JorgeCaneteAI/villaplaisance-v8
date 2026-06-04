<?php
declare(strict_types=1);
/**
 * Bloc « prose » V8.
 *
 * Layouts supportés :
 *   - `two-col`               : titre à gauche, texte à droite (signature V8 accueil)
 *   - `text-only`             : un seul flux de texte centré
 *   - `text-image-right`      : texte à gauche, image à droite (markup .prose-grid, éditorial asymétrique)
 *   - `text-image-left`       : image à gauche, texte à droite (markup .prose-grid, éditorial asymétrique)
 *   - `two-col-image-right`   : texte à gauche, image à droite (markup .two-col, simple 2 colonnes alignées)
 *   - `two-col-image-left`    : image à gauche, texte à droite (markup .two-col.reverse, simple 2 colonnes alignées)
 *
 * Différence prose-grid vs two-col : le `prose-grid` crée un chevauchement
 * éditorial avec z-index (signature V8 accueil). Le `two-col` est un layout
 * 2 colonnes simple aligné, idéal pour les blocs explicatifs (ex. salle de bain).
 *
 * Le champ `text` est splitté sur double retour ligne (`\n\n`) en paragraphes.
 * Le 1er paragraphe reçoit la classe `lede`, les suivants `body-lg`.
 *
 * @var array       $section
 * @var string|null $label_numeral   ex. "01"
 * @var string|null $label_text      ex. "La maison" (mini-md)
 * @var string|null $heading         mini-md
 * @var string|null $text            multi-paragraphes séparés par "\n\n"
 * @var int|null    $image_id        vp_media.id (optionnel)
 * @var array|null  $cta             { label, url, style }
 * @var string|null $layout          'two-col'|'text-only'|'text-image-right'|'text-image-left'
 * @var string|null $surface         'default'|'stone'|'sage'|'sage-light'
 * @var array|null  $stats_band      [ { label, value } ]  bande de stats sous le texte (signature V8)
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$text          = $text          ?? '';
$image_id      = $image_id      ?? null;
$cta           = $cta           ?? null;
$layout        = $layout        ?? 'two-col';
$surface       = $surface       ?? 'default';
$stats_band    = $stats_band    ?? [];

// Surface → class + style fond
$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';

// Paragraphes : split sur double retour ligne
$paragraphs = array_values(array_filter(
    array_map('trim', preg_split('/\n\s*\n/', $text) ?: []),
    static fn(string $p): bool => $p !== ''
));

// Image (optionnel), référence vp_media
$imgUrl = $image_id ? ImageService::urlById((int)$image_id) : null;
$imgAlt = $image_id ? ImageService::altById((int)$image_id) : '';

// CTA (optionnel) : LangService::url pour les liens internes
$ctaUrl = null;
if ($cta && !empty($cta['url'])) {
    $ctaUrl = (string)$cta['url'];
    if (str_starts_with($ctaUrl, '/') && !str_starts_with($ctaUrl, '//')) {
        $ctaUrl = LangService::url(ltrim($ctaUrl, '/'));
    }
}

// Helper local pour le label " 01 / La maison"
$_renderLabel = function () use ($label_numeral, $label_text): string {
    if ($label_numeral === '' && $label_text === '') return '';
    $sep = ($label_numeral !== '' && $label_text !== '') ? ' / ' : '';
    return '<div class="section-label"><span class="numeral">'
        . htmlspecialchars($label_numeral)
        . $sep
        . TextService::renderTitle($label_text)
        . '</span></div>';
};

// Helper local pour les paragraphes
$_renderParagraphs = function () use ($paragraphs): string {
    $out = '';
    $last = count($paragraphs) - 1;
    foreach ($paragraphs as $i => $p) {
        $class  = $i === 0 ? 'lede' : 'body-lg';
        $margin = $i === $last ? '0' : ($i === 0 ? '20px' : '16px');
        $out .= '<p class="' . $class . '" style="margin: 0 0 ' . $margin . ';">'
              . nl2br(htmlspecialchars($p), false) . '</p>';
    }
    return $out;
};

// Helper local pour le CTA
$_renderCta = function () use ($cta, $ctaUrl): string {
    if (!$cta || empty($cta['label']) || !$ctaUrl) return '';
    $ghost = ($cta['style'] ?? 'primary') === 'ghost' ? ' btn-ghost' : '';
    return '<p style="margin: 20px 0 0;"><a class="btn' . $ghost . '" href="'
         . htmlspecialchars($ctaUrl) . '">'
         . htmlspecialchars($cta['label']) . ' →</a></p>';
};

// Helper local pour la bande de stats (signature V8 : grille de N cellules
// avec overline + h-md, séparées par 1px de hairline)
$_renderStatsBand = function () use ($stats_band): string {
    if (empty($stats_band)) return '';
    $n = count($stats_band);
    $out = '<div style="display: grid; grid-template-columns: repeat(' . $n
         . ', 1fr); gap: 1px; background: color-mix(in oklab, var(--ink-900) 14%, transparent); margin-top: clamp(40px, 4vw, 56px); border-top: var(--hairline); border-bottom: var(--hairline);">';
    foreach ($stats_band as $stat) {
        $label = (string)($stat['label'] ?? '');
        $value = (string)($stat['value'] ?? '');
        $out .= '<div style="background: var(--linen-50); padding: 22px 18px;">'
              . '<div class="overline">' . htmlspecialchars($label) . '</div>'
              . '<div class="h-md" style="margin-top: 8px;">' . htmlspecialchars($value) . '</div>'
              . '</div>';
    }
    $out .= '</div>';
    return $out;
};
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">

    <?php if ($layout === 'two-col'): ?>
    <div class="two-col">
      <div>
        <?= $_renderLabel() ?>
        <?php if ($heading !== ''): ?>
        <h2 class="h-xl" style="margin:0; max-width: 12ch;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
      </div>
      <div>
        <?= $_renderParagraphs() ?>
        <?= $_renderStatsBand() ?>
        <?= $_renderCta() ?>
      </div>
    </div>

    <?php elseif ($layout === 'text-only'): ?>
    <div style="max-width: 65ch; margin: 0 auto;">
      <?= $_renderLabel() ?>
      <?php if ($heading !== ''): ?>
      <h2 class="h-xl" style="margin: 0 0 24px;"><?= TextService::renderTitle($heading) ?></h2>
      <?php endif; ?>
      <?= $_renderParagraphs() ?>
      <?= $_renderCta() ?>
    </div>

    <?php elseif ($layout === 'two-col-image-left' || $layout === 'two-col-image-right'): ?>
    <div class="two-col<?= $layout === 'two-col-image-left' ? ' reverse' : '' ?>" style="align-items: center;">
      <?php if ($imgUrl): ?>
      <?= ImageService::imgFromBg(basename($imgUrl), 'prose-aside-img') ?>
      <?php endif; ?>
      <div>
        <?= $_renderLabel() ?>
        <?php if ($heading !== ''): ?>
        <h2 class="h-xl" style="margin: 0 0 24px;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
        <?= $_renderParagraphs() ?>
        <?= $_renderCta() ?>
      </div>
    </div>

    <?php else: /* text-image-left|right, markup éditorial asymétrique */ ?>
    <div class="prose-grid <?= $layout === 'text-image-left' ? 'reverse' : '' ?>">
      <div class="prose-text">
        <?= $_renderLabel() ?>
        <?php if ($heading !== ''): ?>
        <h2 class="h-xl" style="margin: 0 0 24px;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
        <?= $_renderParagraphs() ?>
        <?= $_renderCta() ?>
      </div>
      <?php if ($imgUrl): ?>
      <div class="prose-image">
        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($imgAlt) ?>" loading="lazy" decoding="async">
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </div>
</section>
