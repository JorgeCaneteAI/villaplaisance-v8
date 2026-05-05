<?php
/**
 * Hôte V9 — esprit weeks-off.com (Sprint 3, 2026-05-05).
 *
 * Variables disponibles depuis HoteController :
 *   $profile  vp_host_profile (name, subtitle, photo, intro, origin, passions, philosophy, fun_facts, quote)
 *   $blocks   vp_host_blocks (4 blocs CV avec title_fr, content_fr, image, position)
 *   $reviews  6 reviews mentionnant Jorge
 *   $seo, $jsonLd, $lang
 */

$photoSrc = !empty($profile['photo']) ? '/uploads/' . htmlspecialchars((string)$profile['photo']) : null;
$initial = mb_strtoupper(mb_substr((string)($profile['name'] ?? 'J'), 0, 1));
?>

<section class="identite identite--compact" style="border-bottom:1px solid var(--rule)">
    <div class="eyebrow">L'hôte</div>
    <h1><?= htmlspecialchars((string)($profile['name'] ?? 'Jorge Cañete')) ?></h1>
    <p class="baseline"><?= htmlspecialchars((string)($profile['subtitle'] ?? 'Votre hôte à Villa Plaisance')) ?></p>
</section>

<section class="profile-bio">
    <div class="profile-bio__inner">
        <?php if ($photoSrc): ?>
        <div class="profile-bio__photo">
            <img src="<?= $photoSrc ?>" alt="Portrait de <?= htmlspecialchars((string)($profile['name'] ?? 'Jorge')) ?>" loading="lazy">
        </div>
        <?php else: ?>
        <div class="profile-bio__photo profile-bio__photo--placeholder" aria-hidden="true">
            <?= htmlspecialchars($initial) ?>
        </div>
        <?php endif; ?>
        <div class="profile-bio__text">
            <span class="eyebrow">Bienvenue</span>
            <h2>Bonjour</h2>
            <p class="subtitle">Je suis <?= htmlspecialchars((string)($profile['name'] ?? 'Jorge')) ?>.</p>
            <?php if (!empty($profile['intro'])): ?>
            <?php foreach (preg_split('/\n\n+/', (string)$profile['intro']) as $para): if (trim($para) === '') continue; ?>
            <p><?= nl2br(htmlspecialchars(trim($para))) ?></p>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($blocks)): ?>
    <?php foreach ($blocks as $i => $b):
        $title = $b['title_' . $lang] ?? $b['title_fr'] ?? '';
        $content = $b['content_' . $lang] ?? $b['content_fr'] ?? '';
        $roman = ['I.', 'II.', 'III.', 'IV.', 'V.', 'VI.'][$i] ?? (($i + 1) . '.');
    ?>
    <section class="profile-block">
        <div class="profile-block__inner">
            <div>
                <div class="profile-block__num"><?= htmlspecialchars($roman) ?></div>
                <h3><?= htmlspecialchars((string)$title) ?></h3>
            </div>
            <div class="profile-block__text">
                <?php foreach (preg_split('/\n\n+/', (string)$content) as $para): if (trim($para) === '') continue; ?>
                <p><?= nl2br(htmlspecialchars(trim($para))) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($profile['quote'])): ?>
<section class="quote-band">
    <blockquote>« <?= htmlspecialchars((string)$profile['quote']) ?> »</blockquote>
    <div class="sig">— <?= htmlspecialchars((string)($profile['name'] ?? 'Jorge Cañete')) ?></div>
</section>
<?php endif; ?>

<?php if (!empty($reviews)): ?>
<section class="voix">
    <div class="voix__head">
        <div class="eyebrow">Hôtes du monde</div>
        <h2>Ce qu'on dit de Jorge</h2>
    </div>
    <div class="voix__grid">
        <?php foreach (array_slice($reviews, 0, 3) as $r): ?>
        <figure class="voix__bloc">
            <blockquote>« <?= htmlspecialchars((string)$r['content']) ?> »</blockquote>
            <figcaption>
                <?= htmlspecialchars((string)$r['author']) ?>
                <?php if (!empty($r['origin'])): ?> &middot; <?= htmlspecialchars((string)$r['origin']) ?><?php endif; ?>
            </figcaption>
        </figure>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="ecrire" id="contact">
    <h2>Écrire à <?= htmlspecialchars((string)($profile['name'] ?? 'Jorge')) ?></h2>
    <p>Une question, un projet de séjour, un mot ?</p>
    <p class="sub">Je réponds en français, anglais ou espagnol.</p>
    <div class="actions">
        <a href="<?= LangService::url('contact') ?>" class="pill pill--solid pill--big">
            Nous écrire
            <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        </a>
        <p class="alt">Ou directement&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a></p>
    </div>
</section>
