<?php
declare(strict_types=1);
/**
 * Bloc « articles » V8 — extraits d'articles ou cartes éditoriales.
 *
 * Deux modes :
 *   - source='auto'   : lit vp_articles WHERE type IN (...) (filtre par catégorie)
 *   - source='manual' : utilise les items[] fournis dans le JSON
 *                       (utile pour les "teasers" comme la home : 2 cartes
 *                        vers /journal et /itineraire)
 *
 * Deux displays :
 *   - display='grid' (défaut V8) : grille mosaïque .journal-grid
 *   - display='list'             : liste verticale (V7-style)
 *
 * Schéma JSON :
 *   - label_numeral
 *   - label_text
 *   - heading        (mini-md)
 *   - intro          (texte à droite du titre)
 *   - surface        ('default'|'stone'|'sage'|'sage-light')
 *   - source         ('manual'|'auto')
 *   - display        ('grid'|'list')
 *   - limit          (int, si source=auto)
 *   - type           (string, ex 'journal'|'itineraire'|'all' si source=auto)
 *   - items[]        : si source=manual
 *       { image_id, kicker, title (mini-md), text, cta { label, url } }
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$intro         = $intro         ?? '';
$surface       = $surface       ?? 'default';
$source        = $source        ?? 'manual';
$display       = $display       ?? 'grid';
$limit         = (int)($limit   ?? 3);
$type          = $type          ?? 'journal';
$items         = $items         ?? [];

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';

// Mode auto : lire vp_articles
if ($source === 'auto') {
    $lang = LangService::get() ?? 'fr';
    try {
        if ($type === 'all') {
            $rows = Database::fetchAll(
                "SELECT * FROM vp_articles WHERE lang = ? AND status='published' ORDER BY date_published DESC LIMIT " . max(1, $limit),
                [$lang]
            );
        } else {
            $rows = Database::fetchAll(
                "SELECT * FROM vp_articles WHERE lang = ? AND type = ? AND status='published' ORDER BY date_published DESC LIMIT " . max(1, $limit),
                [$lang, $type]
            );
        }
        $items = array_map(static function (array $r) use ($type): array {
            $coverId = isset($r['cover_image_id']) && $r['cover_image_id'] ? (int)$r['cover_image_id'] : null;
            $coverUrl = $coverId ? ImageService::urlById($coverId) : null;
            return [
                'image_id' => $coverId,
                'image_url'=> $coverUrl,
                'kicker'   => 'Journal · ' . ucfirst((string)$type),
                'title'    => (string)($r['title'] ?? ''),
                'text'     => (string)($r['excerpt'] ?? ''),
                'cta'      => ['label' => 'Lire l\'article', 'url' => '/journal/' . ($r['slug'] ?? '')],
            ];
        }, $rows);
    } catch (\Throwable) {
        $items = [];
    }
}

$resolveUrl = static function (?string $url): ?string {
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
          <span class="numeral">— <?= htmlspecialchars($label_numeral) ?><?= ($label_numeral !== '' && $label_text !== '') ? ' / ' : '' ?><?= TextService::renderTitle($label_text) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($heading !== ''): ?>
        <h2 class="h-xl" style="margin: 0; max-width: 14ch;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
      </div>
      <?php if ($intro !== ''): ?>
      <p class="body-lg" style="max-width: 38ch; margin: 0;"><?= htmlspecialchars($intro) ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
    <div class="<?= $display === 'grid' ? 'journal-grid' : 'journal-list' ?>">
      <?php foreach ($items as $item):
        $imgId   = isset($item['image_id']) ? (int)$item['image_id'] : null;
        $imgUrl  = $item['image_url'] ?? ($imgId ? ImageService::urlById($imgId) : null);
        $imgAlt  = $imgId ? ImageService::altById($imgId) : '';
        $kicker  = (string)($item['kicker'] ?? '');
        $title   = (string)($item['title']  ?? '');
        $text    = (string)($item['text']   ?? '');
        $cta     = $item['cta'] ?? null;
        $ctaUrl  = $cta ? $resolveUrl($cta['url'] ?? null) : null;
        $ctaLab  = $cta ? (string)($cta['label'] ?? '') : '';
      ?>
      <a class="journal-card" href="<?= htmlspecialchars($ctaUrl ?? '#') ?>">
        <?php if ($imgUrl): ?>
        <div class="img" style="background-image: url('<?= htmlspecialchars($imgUrl) ?>')" role="img" aria-label="<?= htmlspecialchars($imgAlt) ?>"></div>
        <?php endif; ?>
        <div>
          <?php if ($kicker !== ''): ?>
          <div class="kicker-mono"><?= htmlspecialchars($kicker) ?></div>
          <?php endif; ?>
          <?php if ($title !== ''): ?>
          <h3><?= TextService::renderTitle($title) ?></h3>
          <?php endif; ?>
          <?php if ($text !== ''): ?>
          <p><?= htmlspecialchars($text) ?></p>
          <?php endif; ?>
          <?php if ($ctaLab !== ''): ?>
          <span class="btn-link"><?= htmlspecialchars($ctaLab) ?> →</span>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</section>
