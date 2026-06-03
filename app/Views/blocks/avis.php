<?php
declare(strict_types=1);
/**
 * Bloc « avis » V8 — Témoignages clients.
 *
 * Deux modes :
 *   - source='manual' (défaut) : utilise les items[] fournis dans le JSON.
 *   - source='auto'            : lit vp_reviews (featured=1, status='published'),
 *                                limité à `limit`.
 *
 * Schéma JSON :
 *   - label_numeral  : ex. "05"
 *   - label_text     : ex. "Ce qu'on en dit" (mini-md)
 *   - heading        : titre h2 (mini-md, multiligne)
 *   - intro          : court texte à droite du titre (optionnel)
 *   - intro_style    : 'normal' (défaut) | 'placeholder' (badge "pl-inline")
 *   - display        : 'testimonial' (défaut V8) | 'cards' (compatibilité V7)
 *   - source         : 'manual' (défaut) | 'auto'
 *   - limit          : int — si source=auto (défaut 3)
 *   - items          : tableau de { rating, quote, author, location }  (si source=manual)
 *   - surface        : 'default' | 'stone' | 'sage' | 'sage-light'
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$intro         = $intro         ?? '';
$intro_style   = $intro_style   ?? 'normal';
$display       = $display       ?? 'testimonial';
$source        = $source        ?? 'manual';
$limit         = (int)($limit   ?? 3);
$items         = $items         ?? [];
$surface       = $surface       ?? 'default';

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';

// Mode auto : lire vp_reviews (colonnes réelles : content, author, origin, review_date)
// Fallback : si pas assez d'avis featured, élargit à tous les published.
if ($source === 'auto') {
    try {
        $rows = Database::fetchAll(
            "SELECT * FROM vp_reviews
             WHERE status = 'published' AND content IS NOT NULL AND LENGTH(content) > 0
             ORDER BY featured DESC, rating DESC, review_date DESC
             LIMIT " . max(1, $limit)
        );
        // Normalisation Booking (note /10 → /5)
        $normalizeRating = static function (float $rating, string $platform): float {
            if ($platform === 'booking' && $rating > 5) return $rating / 2;
            return $rating;
        };
        $items = array_map(static function (array $r) use ($normalizeRating): array {
            return [
                'rating'   => (int)floor($normalizeRating((float)($r['rating'] ?? 5), (string)($r['platform'] ?? ''))),
                'quote'    => (string)($r['content'] ?? ''),
                'author'   => (string)($r['author']  ?? ''),
                'location' => (string)($r['origin']  ?? ''),
            ];
        }, $rows);
    } catch (\Throwable) {
        $items = [];
    }
}
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">

    <?php if ($heading !== '' || $intro !== '' || $label_numeral !== '' || $label_text !== ''): ?>
    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: clamp(40px, 5vw, 64px); flex-wrap: wrap;">
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
        <?php if ($intro_style === 'placeholder'): ?>
        <span class="pl-inline"><span class="dot"></span><span><?= htmlspecialchars($intro) ?></span></span>
        <?php else: ?>
        <p class="body-lg" style="max-width: 38ch; margin: 0;"><?= htmlspecialchars($intro) ?></p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
    <div class="<?= $display === 'testimonial' ? 'testimonials' : 'avis-cards' ?>">
      <?php foreach ($items as $item):
        $rating   = (int)($item['rating'] ?? 5);
        $quote    = (string)($item['quote'] ?? '');
        $author   = (string)($item['author'] ?? '');
        $location = (string)($item['location'] ?? '');
        $rating   = max(0, min(5, $rating));
      ?>
      <article class="testimonial">
        <?php if ($rating > 0): ?>
        <div class="stars"><?= str_repeat('★ ', $rating - 1) . '★' ?></div>
        <?php endif; ?>
        <?php if ($quote !== ''): ?>
        <blockquote><?= TextService::renderTitle($quote) ?></blockquote>
        <?php endif; ?>
        <?php if ($author !== '' || $location !== ''): ?>
        <cite>— <?= htmlspecialchars($author) ?><?= ($author !== '' && $location !== '') ? ' · ' : '' ?><?= htmlspecialchars($location) ?></cite>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</section>
