<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Livret affiché après authentification.
 * Adapté du proto Claude design (livret-chambres.html / livret-maison.html).
 * Conserve le système v8 (boucle dynamique sur $sections lues depuis vp_livret).
 * @var string $lang  @var array $seo  @var array $flash  @var string $csrf
 * @var string $type  @var array $sections
 */ ?>
<style>
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

.message-block { max-width: 640px; margin: 0 auto; padding: clamp(48px, 6vw, 88px) var(--gutter); }
.message-block h2 { font-family: var(--font-display); font-weight: 400; font-size: clamp(28px, 3vw, 40px); letter-spacing: -0.015em; color: var(--ink-900); margin: 0 0 8px; text-align: center; }
.message-block h2 em { font-style: italic; color: var(--sage-700); }
.message-block .intro { text-align: center; font-size: 15px; color: var(--ink-700); margin: 0 0 24px; line-height: 1.5; }
.message-block .form-group { margin-bottom: 18px; }
.message-block label { font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em; color: var(--stone-500); text-transform: uppercase; display: block; margin-bottom: 6px; }
.message-block input, .message-block textarea { width: 100%; font: inherit; font-size: 16px; background: transparent; border: 0; border-bottom: 1px solid var(--stone-400); padding: 10px 0; color: var(--ink-900); box-sizing: border-box; }
.message-block input:focus, .message-block textarea:focus { outline: 0; border-bottom-color: var(--ink-900); }
.message-block textarea { resize: vertical; min-height: 100px; }

.alert { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.06em; padding: 10px 14px; margin: 0 auto 24px; max-width: 640px; }
.alert-success { color: var(--sage-700); border-left: 3px solid var(--sage-500); background: color-mix(in oklab, var(--sage-500) 8%, var(--linen-50)); }
.alert-error { color: var(--terra-600); border-left: 3px solid var(--terra-500); background: color-mix(in oklab, var(--terra-500) 8%, var(--linen-50)); }
</style>

<section class="booklet-hero">
    <p class="welcome">Bienvenue chez vous</p>
    <h1><?= t('livret.title') ?> <em>Plaisance</em></h1>
    <div class="type-nav">
        <a href="<?= \LangService::url('livret') ?>?type=bb" class="<?= $type === 'bb' ? 'active' : '' ?>"><?= t('livret.type_bb') ?></a>
        <span>&middot;</span>
        <a href="<?= \LangService::url('livret') ?>?type=villa" class="<?= $type === 'villa' ? 'active' : '' ?>"><?= t('livret.type_villa') ?></a>
    </div>
</section>

<?php if (!empty($flash['success'])): ?>
    <div class="alert alert-success" role="alert"><?= htmlspecialchars($flash['success']) ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($flash['error']) ?></div>
<?php endif; ?>

<?php if (!empty($sections)): ?>
<div class="booklet-shell">
    <?php foreach ($sections as $i => $section): ?>
    <section class="booklet-section">
        <div class="sec-num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?> &middot; Villa Plaisance</div>
        <h2><?= htmlspecialchars($section['section_title']) ?></h2>
        <div class="content"><?= nl2br(htmlspecialchars($section['content'])) ?></div>
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
</section>

<!-- Message form -->
<section class="message-block">
    <h2><?= t('livret.message_title') ?></h2>
    <p class="intro">Une question, une remarque, un besoin ? On vous répond rapidement.</p>
    <form method="POST" action="<?= \LangService::url('livret') ?>?type=<?= htmlspecialchars($type) ?>" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <div style="position:absolute;left:-9999px" aria-hidden="true">
            <label for="website">Ne pas remplir</label>
            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="name"><?= t('livret.message_name') ?> *</label>
            <input type="text" id="name" name="name" required autocomplete="name">
        </div>
        <div class="form-group">
            <label for="email"><?= t('livret.message_email') ?> *</label>
            <input type="email" id="email" name="email" required autocomplete="email">
        </div>
        <div class="form-group">
            <label for="message"><?= t('livret.message_body') ?> *</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn"><?= t('livret.message_send') ?> &rarr;</button>
    </form>
</section>
