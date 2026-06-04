<?php declare(strict_types=1); ?>
<?php /**
 * Vue : 404.
 * Portée du proto Claude design. Layout : front-proto.
 * Le code HTTP 404 est posé par le Controller / Router.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  .err-wrap {
    min-height: 60vh;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    text-align: center;
    padding: clamp(72px, 9vw, 144px) var(--gutter);
  }
  .err-num {
    font-family: var(--font-mono); font-size: 12px; letter-spacing: 0.18em;
    text-transform: uppercase; color: var(--stone-500);
    margin: 0 0 24px;
  }
  .err-num .dot { display: inline-block; width: 6px; height: 6px; background: var(--sage-700); margin-right: 10px; vertical-align: middle; }
  .err-title {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(48px, 7vw, 96px); line-height: 1.0; letter-spacing: -0.02em;
    color: var(--ink-900);
    margin: 0 0 24px; max-width: 16ch; text-wrap: balance;
  }
  .err-title em { font-style: italic; color: var(--sage-700); }
  .err-lede {
    font-family: var(--font-sans); font-size: clamp(15px, 1.2vw, 17px); line-height: 1.55;
    color: var(--stone-600); margin: 0 0 36px; max-width: 48ch;
  }
  .err-actions { display: flex; gap: 16px; flex-wrap: wrap; justify-content: center; }
</style>

<section class="err-wrap">
  <p class="err-num"><span class="dot"></span><span data-en="404 · Page not found">404 · Page introuvable</span></p>
  <h1 class="err-title" data-en="This page is <em>elsewhere</em>, or no longer here.">Cette page est <em>ailleurs</em>, ou n'existe plus.</h1>
  <p class="err-lede" data-en="A stale link, a typo, it happens. Pick up the thread from the home page, or write to us directly.">Un lien périmé, une faute de frappe, ça arrive. Reprenez le fil depuis l'accueil, ou écrivez-nous directement.</p>
  <div class="err-actions">
    <a class="btn" href="<?= LangService::url('/') ?>"><span data-en="Back to home">Retour à l'accueil</span> &rarr;</a>
    <a class="btn btn-ghost" href="<?= LangService::url('contact') ?>" data-en="Contact">Contact</a>
  </div>
</section>
