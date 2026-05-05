<?php
/**
 * Article V9 — esprit weeks-off.com (Sprint 3, 2026-05-05).
 *
 * Variables disponibles depuis JournalController::show ou SurPlaceController::show :
 *   $article          vp_articles row
 *   $contentBlocks    json_decode(article.content) — array<{type,text,...}>
 *   $seo, $jsonLd, $lang
 */

$isOnSite = ($article['type'] ?? '') === 'sur-place';
$listUrl  = $isOnSite ? LangService::url('sur-place') : LangService::url('journal');
$listLabel = $isOnSite ? 'Sur place' : 'Le Journal';

$frenchMonths = [
    1=>'janv.',2=>'févr.',3=>'mars',4=>'avr.',5=>'mai',6=>'juin',
    7=>'juil.',8=>'août',9=>'sept.',10=>'oct.',11=>'nov.',12=>'déc.',
];
$fmtDate = function (?string $iso) use ($frenchMonths): string {
    if (!$iso) return '';
    $t = strtotime($iso);
    if (!$t) return '';
    return (int)date('j', $t) . ' ' . $frenchMonths[(int)date('n', $t)] . ' ' . date('Y', $t);
};

$cover = $article['cover_image'] ?? null;
?>

<header class="article-hero">
    <span class="meta">
        <a href="<?= htmlspecialchars($listUrl) ?>" style="color:var(--ink-soft);border-bottom:1px solid var(--ink-soft);padding-bottom:1px"><?= htmlspecialchars($listLabel) ?></a>
        <?php if (!empty($article['category'])): ?> &middot; <?= htmlspecialchars((string)$article['category']) ?><?php endif; ?>
        <?php if (!empty($article['published_at']) && !$isOnSite): ?> &middot; <?= htmlspecialchars($fmtDate($article['published_at'])) ?><?php endif; ?>
    </span>
    <h1><?= htmlspecialchars((string)$article['title']) ?></h1>
    <?php if (!empty($article['excerpt'])): ?>
    <p class="excerpt"><?= htmlspecialchars((string)$article['excerpt']) ?></p>
    <?php endif; ?>
</header>

<?php if ($cover): ?>
<figure class="article-cover">
    <img src="/uploads/<?= htmlspecialchars((string)$cover) ?>"
         alt="<?= htmlspecialchars((string)$article['title']) ?>"
         loading="lazy" decoding="async">
</figure>
<?php endif; ?>

<article class="article-content">
    <?php
    if (!empty($contentBlocks) && is_array($contentBlocks)) {
        foreach ($contentBlocks as $block) {
            $type = $block['type'] ?? 'paragraph';
            $text = (string)($block['text'] ?? '');
            switch ($type) {
                case 'heading':
                case 'h2':
                    echo '<h2>' . htmlspecialchars($text) . '</h2>';
                    break;
                case 'h3':
                    echo '<h3>' . htmlspecialchars($text) . '</h3>';
                    break;
                case 'quote':
                case 'blockquote':
                    echo '<blockquote>' . htmlspecialchars($text) . '</blockquote>';
                    break;
                case 'image':
                    $src = $block['src'] ?? '';
                    $caption = $block['caption'] ?? '';
                    if ($src) {
                        $imgSrc = str_starts_with($src, 'http') || str_starts_with($src, '/') ? $src : '/uploads/' . $src;
                        echo '<figure>';
                        echo '<img src="' . htmlspecialchars($imgSrc) . '" alt="' . htmlspecialchars($caption ?: '') . '" loading="lazy">';
                        if ($caption) echo '<figcaption>' . htmlspecialchars($caption) . '</figcaption>';
                        echo '</figure>';
                    }
                    break;
                case 'list':
                    $items = $block['items'] ?? [];
                    if ($items) {
                        echo '<ul>';
                        foreach ($items as $item) echo '<li>' . htmlspecialchars((string)$item) . '</li>';
                        echo '</ul>';
                    }
                    break;
                case 'paragraph':
                default:
                    if ($text !== '') echo '<p>' . nl2br(htmlspecialchars($text)) . '</p>';
                    break;
            }
        }
    } elseif (!empty($article['excerpt'])) {
        echo '<p>' . nl2br(htmlspecialchars((string)$article['excerpt'])) . '</p>';
    }
    ?>
</article>

<div class="article-back">
    <a href="<?= htmlspecialchars($listUrl) ?>">← Retour à <?= htmlspecialchars($listLabel) ?></a>
</div>

<section class="ecrire" id="contact">
    <h2>Écrire</h2>
    <p>Cet article vous a parlé ?</p>
    <p class="sub">Dites-nous, ou venez en discuter sur place.</p>
    <div class="actions">
        <a href="<?= LangService::url('contact') ?>" class="pill pill--solid pill--big">Nous écrire</a>
        <p class="alt">Ou directement&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a></p>
    </div>
</section>
