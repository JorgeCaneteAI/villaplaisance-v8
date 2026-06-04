<?php
declare(strict_types=1);
/**
 * Bloc « faq » V8.
 *
 * Lit la table vp_faq filtrée par page_slug + lang courant.
 * Rendu : header section (label + h2 à gauche, intro à droite) puis
 * liste de <details> empilés (le 1er ouvert par défaut).
 *
 * Schéma JSON :
 *   - label_numeral, label_text
 *   - heading (mini-md)
 *   - intro
 *   - surface ('default'|'stone'|'sage'|'sage-light')
 *   - page_slug    : slug à filtrer dans vp_faq (défaut: section.page_slug)
 *   - limit        : int (optionnel), limite le nombre de FAQ affichées
 *   - first_open   : bool (défaut true), 1ère réponse dépliée
 */

$label_numeral = $label_numeral ?? '';
$label_text    = $label_text    ?? '';
$heading       = $heading       ?? '';
$intro         = $intro         ?? '';
$surface       = $surface       ?? 'default';
$page_slug     = $page_slug     ?? ($section['page_slug'] ?? 'accueil');
$limit         = isset($limit) ? (int)$limit : null;
$first_open    = $first_open    ?? true;

$surfaceClass = $surface !== 'default' ? 'surface-' . $surface : '';
$surfaceStyle = '';
if ($surface === 'stone')      $surfaceStyle = 'background: var(--linen-100);';
if ($surface === 'sage')       $surfaceStyle = 'background: var(--sage-200);';
if ($surface === 'sage-light') $surfaceStyle = 'background: color-mix(in oklab, var(--sage-200) 28%, var(--linen-50));';

// Lecture vp_faq
$lang = LangService::get() ?? 'fr';
$faqs = [];
try {
    $sql = "SELECT question, answer FROM vp_faq WHERE page_slug = ? AND lang = ? AND active = 1 ORDER BY position";
    if ($limit !== null && $limit > 0) {
        $sql .= " LIMIT " . $limit;
    }
    $faqs = Database::fetchAll($sql, [$page_slug, $lang]);
} catch (\Throwable) {
    $faqs = [];
}

$hasHeader = ($heading !== '' || $intro !== '' || $label_numeral !== '' || $label_text !== '');
?>
<section class="section <?= htmlspecialchars($surfaceClass) ?>"<?= $surfaceStyle ? ' style="' . htmlspecialchars($surfaceStyle) . '"' : '' ?>>
  <div class="container-wide">
    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: clamp(32px, 6vw, 96px); align-items: start;">

      <?php if ($hasHeader): ?>
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
        <p class="body-lg" style="margin: 24px 0 0; max-width: 38ch; color: var(--stone-600);"><?= htmlspecialchars($intro) ?></p>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($faqs)): ?>
      <div class="faq">
        <?php foreach ($faqs as $i => $faq): ?>
        <details<?= ($first_open && $i === 0) ? ' open' : '' ?>>
          <summary><span><?= htmlspecialchars((string)$faq['question']) ?></span><span class="icon"></span></summary>
          <div class="answer"><?= nl2br(htmlspecialchars((string)$faq['answer']), false) ?></div>
        </details>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div>
  </div>
</section>
