<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Votre hôte (Jorge), V8 portée sur vp_sections.
 *
 * Préférence : rendre depuis vp_sections (édition unifiée via /admin/sections).
 * Fallback HTML legacy si aucun bloc en BDD pour cette page+langue.
 *
 * Le système legacy lit vp_host_profile + vp_host_blocks + vp_reviews 
 * conservé tant que les blocs vp_sections n'ont pas été seedés.
 *
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 * @var array|null $profile  @var array $blocks  @var array $reviews
 */

$profile  = $profile ?? [];
$blocks   = $blocks ?? [];
$reviews  = $reviews ?? [];

// Charge les sections vp_sections pour cette page + langue, indexées par position
$_v8SectionsByPos = [];
foreach (BlockService::getSections('votre-hote', $lang) as $_s) {
    $_v8SectionsByPos[(int)$_s['position']] = $_s;
}
$renderV8BlockAt = static function (int $pos, string $expectedType) use ($_v8SectionsByPos): ?string {
    $s = $_v8SectionsByPos[$pos] ?? null;
    if (!$s) return null;
    if ($s['block_type'] !== $expectedType) {
        error_log("V8 votre-hote: position $pos attendu '$expectedType' mais BDD a '{$s['block_type']}'");
        return null;
    }
    return BlockService::renderBlock($s);
};

// ─────────────────────────────────────────────────────────────────────────────
// Si AU MOINS un bloc vp_sections existe pour cette page, on rend depuis là
// (8 blocs attendus : hero, prose quote, 5 prose sections, cta).
// Sinon, fallback complet sur le HTML legacy ci-dessous.
// ─────────────────────────────────────────────────────────────────────────────
if (!empty($_v8SectionsByPos)):
    for ($pos = 1; $pos <= 8; $pos++) {
        $section = $_v8SectionsByPos[$pos] ?? null;
        if ($section && (int)$section['active'] === 1) {
            echo BlockService::renderBlock($section);
        }
    }
    return;  // Évite le fallback ci-dessous
endif;

// ─────────────────────────────────────────────────────────────────────────────
// FALLBACK V7 (legacy vp_host_profile/blocks/reviews)
// Conservé en bloc tant qu'on n'a pas migré chaque langue.
// ─────────────────────────────────────────────────────────────────────────────
$name     = $profile['name'] ?? 'Jorge';
$subtitle = $profile['subtitle'] ?? 'Votre hôte à Villa Plaisance';
$photo    = $profile['photo'] ?? '';
$intro    = $profile['intro'] ?? '';
$origin   = $profile['origin'] ?? '';
$passions = $profile['passions'] ?? '';
$philo    = $profile['philosophy'] ?? '';
$facts    = $profile['fun_facts'] ?? '';
$quote    = $profile['quote'] ?? '';

$nameParts = preg_split('/\s+/', trim($name), 2);
$firstName = $nameParts[0] ?? $name;
$lastName  = $nameParts[1] ?? '';

$initials = '';
foreach (preg_split('/\s+/', trim($name)) as $p) {
    if ($p !== '') $initials .= mb_strtoupper(mb_substr($p, 0, 1), 'UTF-8');
}
$initials = mb_substr($initials, 0, 2, 'UTF-8') ?: 'JC';
?>
<style>
.hote-hero { padding: clamp(48px, 7vw, 120px) var(--gutter) clamp(40px, 5vw, 80px); }
.hote-hero-inner { max-width: var(--container-wide); margin: 0 auto; display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.3fr); gap: clamp(32px, 5vw, 80px); align-items: end; }
.hote-portrait { aspect-ratio: 4/5; overflow: hidden; background: var(--linen-100); position: relative; }
.hote-portrait img { width: 100%; height: 100%; object-fit: cover; display: block; }
.hote-portrait .placeholder { width: 100%; height: 100%; display: grid; place-items: center; background: var(--ink-900); color: var(--linen-50); font-family: var(--font-display); font-style: italic; font-weight: 400; font-size: clamp(80px, 14vw, 180px); letter-spacing: -0.03em; line-height: 1; }
.hote-portrait .placeholder::after { content: "À COMPLÉTER"; position: absolute; bottom: 16px; left: 16px; font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.18em; color: rgba(var(--linen-50-rgb), 0.72); text-transform: uppercase; }
.hote-hero .overline { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em; color: var(--terra-500); text-transform: uppercase; margin: 0 0 18px; }
.hote-hero h1 { font-family: var(--font-display); font-weight: 400; font-size: clamp(56px, 9vw, 132px); line-height: 0.92; letter-spacing: -0.025em; color: var(--ink-900); margin: 0 0 18px; }
.hote-hero h1 em { font-style: italic; color: var(--sage-700); }
.hote-hero .subtitle { font-family: var(--font-display); font-style: italic; font-size: clamp(20px, 1.8vw, 26px); color: var(--ink-700); margin: 0 0 28px; max-width: 36ch; line-height: 1.3; }
.hote-hero blockquote { margin: 0; padding-left: 18px; border-left: 2px solid var(--sage-500); font-family: var(--font-display); font-style: italic; font-size: clamp(18px, 1.6vw, 22px); color: var(--ink-700); line-height: 1.45; max-width: 44ch; }
@media (max-width: 860px) { .hote-hero-inner { grid-template-columns: 1fr; gap: 32px; } .hote-portrait { max-width: 360px; } }

.hote-section { max-width: 880px; margin: 0 auto; padding: clamp(48px, 6vw, 88px) var(--gutter); border-top: var(--hairline); }
.hote-section .overline { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em; color: var(--stone-500); text-transform: uppercase; margin-bottom: 16px; }
.hote-section h2 { font-family: var(--font-display); font-weight: 400; font-size: clamp(32px, 4vw, 52px); line-height: 1.0; letter-spacing: -0.02em; color: var(--ink-900); margin: 0 0 28px; max-width: 22ch; }
.hote-section h2 em { font-style: italic; color: var(--sage-700); }
.hote-section .content { font-size: 17px; line-height: 1.65; color: var(--ink-700); max-width: 64ch; }
.hote-section .content p { margin: 0 0 16px; }
.hote-section .block-image { aspect-ratio: 16/10; background-size: cover; background-position: center; margin: 28px 0; max-width: 64ch; }

.hote-reviews { max-width: var(--container-wide); margin: 0 auto; padding: clamp(48px, 6vw, 88px) var(--gutter); border-top: var(--hairline); }
.hote-reviews .overline { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em; color: var(--stone-500); text-transform: uppercase; margin-bottom: 16px; }
.hote-reviews h2 { font-family: var(--font-display); font-weight: 400; font-size: clamp(32px, 4vw, 52px); line-height: 1.0; letter-spacing: -0.02em; color: var(--ink-900); margin: 0 0 36px; max-width: 22ch; }
.hote-reviews h2 em { font-style: italic; color: var(--sage-700); }
.reviews-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: clamp(24px, 3vw, 40px); }
.review { border-top: var(--hairline); padding-top: 22px; }
.review .stars { font-family: var(--font-mono); font-size: 12px; color: var(--terra-500); letter-spacing: 0.2em; margin-bottom: 14px; }
.review blockquote { margin: 0 0 16px; font-family: var(--font-display); font-style: italic; font-size: clamp(17px, 1.4vw, 19px); color: var(--ink-900); line-height: 1.45; max-width: 36ch; }
.review cite { font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em; color: var(--stone-500); text-transform: uppercase; font-style: normal; }
@media (max-width: 960px) { .reviews-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 640px) { .reviews-grid { grid-template-columns: 1fr; } }

.hote-cta { background: var(--ink-900); color: var(--linen-50); padding: clamp(56px, 8vw, 120px) var(--gutter); text-align: center; margin-top: clamp(40px, 5vw, 80px); }
.hote-cta h2 { font-family: var(--font-display); font-weight: 400; font-size: clamp(40px, 5vw, 68px); line-height: 0.95; letter-spacing: -0.02em; color: var(--linen-50); margin: 0 0 18px; }
.hote-cta h2 em { font-style: italic; color: var(--sage-200); }
.hote-cta p { font-size: 17px; color: rgba(var(--linen-50-rgb), 0.75); margin: 0 auto 32px; max-width: 48ch; line-height: 1.55; }
.hote-cta .btn { background: var(--linen-50); color: var(--ink-900); border-color: var(--linen-50); }
</style>

<!-- HERO -->
<section class="hote-hero">
    <div class="hote-hero-inner">
        <div class="hote-portrait">
            <?php if ($photo): ?>
                <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($name) ?>">
            <?php else: ?>
                <div class="placeholder" aria-label="Portrait à venir"><?= htmlspecialchars($initials) ?></div>
            <?php endif; ?>
        </div>
        <div>
            <p class="overline">01 &middot; Votre hôte</p>
            <h1><?= htmlspecialchars($firstName) ?><?php if ($lastName): ?> <em><?= htmlspecialchars($lastName) ?></em><?php endif; ?></h1>
            <p class="subtitle"><?= htmlspecialchars($subtitle) ?></p>
            <?php if ($quote): ?>
                <blockquote><?= nl2br(htmlspecialchars($quote)) ?></blockquote>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- INTRO -->
<?php if ($intro): ?>
<section class="hote-section">
    <div class="overline">02 &middot; En quelques mots</div>
    <h2>Qui je <em>suis</em></h2>
    <div class="content"><?= nl2br(htmlspecialchars($intro)) ?></div>
</section>
<?php endif; ?>

<!-- BLOCS DYNAMIQUES (vp_host_blocks) -->
<?php $blockIdx = 3;
foreach ($blocks as $block):
    $titleKey   = 'title_' . $lang;
    $contentKey = 'content_' . $lang;
    $bTitle = !empty($block[$titleKey]) ? $block[$titleKey] : ($block['title_fr'] ?? '');
    $bCont  = !empty($block[$contentKey]) ? $block[$contentKey] : ($block['content_fr'] ?? '');
    if (!$bTitle && !$bCont) continue;
?>
<section class="hote-section">
    <div class="overline"><?= str_pad((string)$blockIdx, 2, '0', STR_PAD_LEFT) ?> &middot; <?= htmlspecialchars($bTitle) ?></div>
    <h2><?= htmlspecialchars($bTitle) ?></h2>
    <?php if (!empty($block['image'])): ?>
        <div class="block-image" style="background-image: url('<?= htmlspecialchars($block['image']) ?>')"></div>
    <?php endif; ?>
    <div class="content"><?= nl2br(htmlspecialchars($bCont)) ?></div>
</section>
<?php $blockIdx++; endforeach; ?>

<!-- FALLBACK : sections statiques du profil si aucun bloc en BDD -->
<?php if (empty($blocks)): ?>
    <?php if ($origin): ?>
    <section class="hote-section">
        <div class="overline">03 &middot; D'où je viens</div>
        <h2>Mes <em>origines</em></h2>
        <div class="content"><?= nl2br(htmlspecialchars($origin)) ?></div>
    </section>
    <?php endif; ?>
    <?php if ($passions): ?>
    <section class="hote-section">
        <div class="overline">04 &middot; Ce qui me passionne</div>
        <h2>Mes <em>passions</em></h2>
        <div class="content"><?= nl2br(htmlspecialchars($passions)) ?></div>
    </section>
    <?php endif; ?>
    <?php if ($philo): ?>
    <section class="hote-section">
        <div class="overline">05 &middot; Ma vision de l'accueil</div>
        <h2>Ma <em>philosophie</em></h2>
        <div class="content"><?= nl2br(htmlspecialchars($philo)) ?></div>
    </section>
    <?php endif; ?>
    <?php if ($facts): ?>
    <section class="hote-section">
        <div class="overline">06 &middot; Fun facts</div>
        <h2>Trois <em>détails</em></h2>
        <div class="content"><?= nl2br(htmlspecialchars($facts)) ?></div>
    </section>
    <?php endif; ?>
<?php endif; ?>

<!-- REVIEWS -->
<?php if (!empty($reviews)): ?>
<section class="hote-reviews">
    <div class="overline"><?= str_pad((string)$blockIdx, 2, '0', STR_PAD_LEFT) ?> &middot; Ils en parlent</div>
    <h2>Ce qu'ils <em>en disent</em></h2>
    <div class="reviews-grid">
        <?php foreach ($reviews as $r):
            $rContent = $r['content'] ?? ($r['comment'] ?? '');
            $rAuthor  = $r['author']  ?? ($r['author_name'] ?? '');
            $rRating  = (int)($r['rating'] ?? 5);
        ?>
        <article class="review">
            <div class="stars" aria-label="<?= $rRating ?> sur 5">
                <?php for ($i = 0; $i < $rRating; $i++) echo '★'; ?>
            </div>
            <blockquote><?= htmlspecialchars(mb_strimwidth($rContent, 0, 280, '…', 'UTF-8')) ?></blockquote>
            <?php if ($rAuthor): ?><cite><?= htmlspecialchars($rAuthor) ?></cite><?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="hote-cta">
    <h2>Échanger <em>directement</em></h2>
    <p>Pour préparer votre séjour, poser une question, ou simplement parler de la Provence.</p>
    <a href="<?= LangService::url('contact') ?>" class="btn">Écrire à <?= htmlspecialchars($firstName) ?> &rarr;</a>
</section>
