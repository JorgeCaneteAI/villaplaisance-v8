<?php
// Article V8 — porté du V2 (`journal/le-tourisme-de-masse-est-une-arnaque/`).
// Variables : $seo, $jsonLd, $lang, $article, $contentBlocks.
// Le contenu est rendu dynamiquement depuis vp_articles + JSON content blocks.
$isOnSite = ($article['type'] ?? '') === 'sur-place';
$backUrl = LangService::url($isOnSite ? 'sur-place' : 'journal');
$backLabel = $isOnSite ? 'Retour à Sur place' : 'Retour au journal';
?>

<article class="post">
    <nav class="post__breadcrumb" aria-label="Fil d'Ariane">
        <a href="<?= LangService::url('/') ?>">Accueil</a>
        <span aria-hidden="true">›</span>
        <a href="<?= $backUrl ?>"><?= $isOnSite ? 'Sur place' : 'Journal' ?></a>
        <span aria-hidden="true">›</span>
        <span aria-current="page"><?= htmlspecialchars($article['title']) ?></span>
    </nav>

    <header class="post__entete">
        <p class="post__cat">
            <?php if (!empty($article['category'])): ?>
                <?= htmlspecialchars($article['category']) ?>
                <?php if (!empty($article['published_at'])): ?> · <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($article['published_at'])): ?>
                <time datetime="<?= htmlspecialchars($article['published_at']) ?>">
                    <?= date('j F Y', strtotime($article['published_at'])) ?>
                </time>
            <?php endif; ?>
        </p>
        <h1 class="post__titre"><?= htmlspecialchars($article['title']) ?></h1>
        <?php if (!empty($article['excerpt'])): ?>
        <p class="post__excerpt"><?= htmlspecialchars($article['excerpt']) ?></p>
        <?php endif; ?>
    </header>

    <?php if (!empty($article['cover_image'])): ?>
    <figure class="post__cover">
        <?= ImageService::img($article['cover_image'], htmlspecialchars($article['title']), 1200, 630) ?>
    </figure>
    <?php endif; ?>

    <div class="post__corps">
        <?php if (!empty($article['excerpt'])): ?>
        <p class="post__lead"><?= htmlspecialchars($article['excerpt']) ?></p>
        <?php endif; ?>

        <?php if (!empty($contentBlocks)): ?>
            <?php foreach ($contentBlocks as $block): ?>
                <?php if (is_string($block)): ?>
                    <?= $block ?>
                <?php elseif (is_array($block)): ?>
                    <?php if (($block['type'] ?? '') === 'heading'): ?>
                        <h2><?= htmlspecialchars($block['text'] ?? '') ?></h2>
                    <?php elseif (($block['type'] ?? '') === 'paragraph'): ?>
                        <p><?= $block['text'] ?? '' ?></p>
                    <?php elseif (($block['type'] ?? '') === 'image'): ?>
                        <figure>
                            <?= ImageService::img($block['src'] ?? '', htmlspecialchars($block['alt'] ?? ''), 1200, 800) ?>
                            <?php if (!empty($block['caption'])): ?>
                            <figcaption><?= htmlspecialchars($block['caption']) ?></figcaption>
                            <?php endif; ?>
                        </figure>
                    <?php elseif (($block['type'] ?? '') === 'quote'): ?>
                        <blockquote class="post__cite"><?= htmlspecialchars($block['text'] ?? '') ?></blockquote>
                    <?php elseif (($block['type'] ?? '') === 'list'): ?>
                        <ul>
                            <?php foreach (($block['items'] ?? []) as $item): ?>
                            <li><?php
                                $safe = htmlspecialchars($item);
                                if (str_contains($safe, ' : ')) {
                                    [$label, $rest] = explode(' : ', $safe, 2);
                                    echo '<strong>' . $label . '</strong> : ' . $rest;
                                } else {
                                    echo $safe;
                                }
                            ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= nl2br(htmlspecialchars($article['content'] ?? '')) ?></p>
        <?php endif; ?>
    </div>

    <footer class="post__pied">
        <a class="post__retour" href="<?= $backUrl ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20" style="transform:scaleX(-1)"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
            <?= $backLabel ?>
        </a>
    </footer>
</article>
