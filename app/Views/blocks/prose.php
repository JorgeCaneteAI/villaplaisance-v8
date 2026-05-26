<?php
declare(strict_types=1);
/**
 * Bloc « prose » V8.
 *
 * Layouts supportés :
 *   - `two-col`           : titre à gauche, texte à droite (signature V8 accueil)
 *   - `text-only`         : un seul flux de texte centré
 *   - `text-image-right`  : texte à gauche, image à droite
 *   - `text-image-left`   : image à gauche, texte à droite
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
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$text          = $text          ?? '';
$image_id      = $image_id      ?? null;
$cta           = $cta           ?? null;
$layout        = $layout        ?? 'two-col';
$surface       = $surface       ?? 'default';

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

// Image (optionnel) — référence vp_media
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

// Helper local pour le label "— 01 / La maison"
$_renderLabel = function () use ($label_numeral, $label_text): string {
    if ($label_numeral === '' && $label_text === '') return '';
    $sep = ($label_numeral !== '' && $label_text !== '') ? ' / ' : '';
    return '<div class="section-label"><span class="numeral">— '
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

    <?php else: /* text-image-left|right */ ?>
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
