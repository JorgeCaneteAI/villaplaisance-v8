<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Aperçu public du livret (sans gate password).
 * Réutilise la structure de show.php mais sans formulaire message + bandeau "aperçu".
 * @var string $lang  @var array $seo  @var array $jsonLd
 * @var string $type  @var array $sections
 */ ?>
<style>
.preview-banner { background: var(--terra-500); color: var(--linen-50); text-align: center; padding: 14px var(--gutter); font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase; }
.preview-banner strong { font-weight: 500; }
.preview-banner a { color: var(--linen-50); border-bottom: 1px solid rgba(251,247,238,0.5); padding-bottom: 1px; margin-left: 12px; }

.booklet-hero { padding: clamp(48px, 6vw, 96px) var(--gutter) clamp(32px, 4vw, 56px); text-align: center; }
.booklet-hero .welcome { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em; color: var(--stone-500); text-transform: uppercase; margin: 0 0 16px; }
.booklet-hero h1 { font-family: var(--font-display); font-weight: 400; font-size: clamp(44px, 6vw, 84px); line-height: 0.95; letter-spacing: -0.025em; color: var(--ink-900); margin: 0; }
.booklet-hero h1 em { font-style: italic; color: var(--sage-700); }
.booklet-hero .type-nav { margin-top: 24px; font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em; color: var(--stone-500); text-transform: uppercase; }
.booklet-hero .type-nav a { color: var(--ink-900); text-decoration: none; }
.booklet-hero .type-nav a.active { border-bottom: 1px solid var(--ink-900); padding-bottom: 1px; }
.booklet-hero .type-nav span { margin: 0 10px; color: var(--stone-400); }

.booklet-shell { max-width: 880px; margin: 0 auto; padding: 0 var(--gutter); }
.booklet-section { padding: clamp(48px, 6vw, 88px) 0; border-top: var(--hairline); }
.booklet-section:first-of-type { border-top: 0; }
.booklet-section .sec-num { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em; color: var(--stone-500); text-transform: uppercase; margin-bottom: 14px; }
.booklet-section h2 { font-family: var(--font-display); font-weight: 400; font-size: clamp(32px, 4vw, 52px); line-height: 1.0; letter-spacing: -0.02em; color: var(--ink-900); margin: 0 0 28px; }
.booklet-section h2 em { font-style: italic; color: var(--sage-700); }
.booklet-section .content { font-size: 16px; line-height: 1.65; color: var(--ink-700); max-width: 64ch; }
.booklet-section .content p { margin: 0 0 14px; }

.booklet-empty { max-width: 64ch; margin: 0 auto; padding: 4rem var(--gutter); text-align: center; font-family: var(--font-display); font-style: italic; font-size: 22px; color: var(--stone-500); }

.farewell { background: var(--linen-100); padding: clamp(48px, 6vw, 88px) clamp(32px, 4vw, 56px); border-top: var(--hairline-strong); text-align: center; margin-top: clamp(32px, 4vw, 64px); }
.farewell h2 { font-family: var(--font-display); font-weight: 400; font-size: clamp(36px, 5vw, 60px); line-height: 1.0; letter-spacing: -0.02em; color: var(--ink-900); margin: 0; }
.farewell h2 em { font-style: italic; color: var(--sage-700); }
.farewell .access-cta { margin-top: 28px; font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em; color: var(--stone-500); text-transform: uppercase; }
.farewell .access-cta a { color: var(--ink-900); border-bottom: 1px solid var(--ink-900); padding-bottom: 1px; text-decoration: none; }
</style>

<!-- BANDEAU APERÇU -->
<div class="preview-banner">
    <strong>Aperçu &middot; Livret de démonstration</strong>
    <a href="<?= \LangService::url('livret') ?>?type=<?= htmlspecialchars($type) ?>">Accéder au livret complet &rarr;</a>
</div>

<!-- HERO -->
<section class="booklet-hero">
    <p class="welcome">Aperçu du livret d'accueil</p>
    <h1><?= t('livret.title') ?> <em>Plaisance</em></h1>
    <div class="type-nav">
        <a href="<?= \LangService::url('livret-apercu') ?>?type=bb" class="<?= $type === 'bb' ? 'active' : '' ?>"><?= t('livret.type_bb') ?></a>
        <span>&middot;</span>
        <a href="<?= \LangService::url('livret-apercu') ?>?type=villa" class="<?= $type === 'villa' ? 'active' : '' ?>"><?= t('livret.type_villa') ?></a>
    </div>
</section>

<?php if (!empty($sections)): ?>
<div class="booklet-shell">
    <?php foreach ($sections as $i => $section): ?>
    <section class="booklet-section">
        <div class="sec-num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?> &middot; Villa Plaisance</div>
        <h2><?= htmlspecialchars($section['section_title']) ?></h2>
        <div class="content"><?= livret_richtext($section['content']) ?></div>
    </section>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="booklet-empty">
    <?= $lang === 'fr' ? 'Contenu à venir.' : ($lang === 'es' ? 'Contenido próximamente.' : 'Content coming soon.') ?>
</div>
<?php endif; ?>

<section class="farewell">
    <h2>Bon <em>séjour</em>.</h2>
    <p class="access-cta">
        <a href="<?= \LangService::url('livret') ?>?type=<?= htmlspecialchars($type) ?>">Accéder au livret complet &rarr;</a>
    </p>
</section>
