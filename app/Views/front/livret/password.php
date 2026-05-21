<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Gate password du livret.
 * Adapté du proto Claude design (livret.html / livret-chambres.html).
 * Conserve le système v8 (POST + CSRF + flash + $type B&B/Villa).
 * @var string $lang  @var array $seo  @var array $flash  @var string $csrf  @var string $type
 */ ?>
<style>
.gate { min-height: 80vh; display: grid; place-items: center; padding: var(--gutter); }
.gate-card { max-width: 460px; width: 100%; border: var(--hairline); background: var(--linen-100); padding: clamp(32px, 4vw, 56px); }
.gate-card h1 { font-family: var(--font-display); font-weight: 400; font-size: clamp(36px, 4vw, 52px); line-height: 1.0; letter-spacing: -0.02em; color: var(--ink-900); margin: 0 0 12px; }
.gate-card h1 em { font-style: italic; color: var(--sage-700); }
.gate-card p.hint { font-size: 15px; color: var(--ink-700); margin: 0 0 24px; line-height: 1.55; }
.gate-card label { font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em; color: var(--stone-500); text-transform: uppercase; display: block; margin-bottom: 8px; }
.gate-card input[type="password"] { width: 100%; font: inherit; font-size: 16px; background: transparent; border: 0; border-bottom: 1px solid var(--stone-400); padding: 10px 0; color: var(--ink-900); margin-bottom: 24px; box-sizing: border-box; }
.gate-card input[type="password"]:focus { outline: 0; border-bottom-color: var(--ink-900); }
.gate-card .err { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.06em; color: var(--terra-600); margin: -12px 0 16px; }
.gate-card .toggle { margin-top: 28px; padding-top: 20px; border-top: var(--hairline); font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em; color: var(--stone-500); text-transform: uppercase; text-align: center; }
.gate-card .toggle a { color: var(--ink-900); text-decoration: none; }
.gate-card .toggle a.active { border-bottom: 1px solid var(--ink-900); padding-bottom: 1px; }
.gate-card .toggle span { margin: 0 8px; color: var(--stone-400); }
</style>

<section class="gate">
    <div class="gate-card">
        <h1><?= t('livret.title') ?> <em>Plaisance</em>.</h1>
        <p class="hint">Petit cadenas avant l'arrivée. Saisissez le code communiqué par mail.</p>

        <?php if (!empty($flash['error'])): ?>
            <div class="err" role="alert"><?= htmlspecialchars($flash['error']) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= \LangService::url('livret') ?>?type=<?= htmlspecialchars($type) ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <label for="livret_password">Code d'accès</label>
            <input type="password" id="livret_password" name="livret_password" autocomplete="off" autofocus required placeholder="••••••••">
            <button type="submit" class="btn">Accéder au livret &rarr;</button>
        </form>

        <div class="toggle">
            <a href="<?= \LangService::url('livret') ?>?type=bb" class="<?= $type === 'bb' ? 'active' : '' ?>"><?= t('livret.type_bb') ?></a>
            <span>&middot;</span>
            <a href="<?= \LangService::url('livret') ?>?type=villa" class="<?= $type === 'villa' ? 'active' : '' ?>"><?= t('livret.type_villa') ?></a>
        </div>
    </div>
</section>
