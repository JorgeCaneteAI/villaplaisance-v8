<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Plan du site.
 * Portée du proto Claude design. Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  .sm-page { padding-top: clamp(72px, 9vw, 120px); }
  .sm-page .page-hero { padding-top: 0; }
  .sm-grid {
    display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 48px;
    margin-top: 56px; padding-top: 48px; border-top: var(--hairline);
  }
  @media (max-width: 960px) { .sm-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 36px; } }
  @media (max-width: 540px) { .sm-grid { grid-template-columns: 1fr; } }
  .sm-bloc h2 {
    font-family: var(--font-mono); font-size: 11.5px; font-weight: 500;
    letter-spacing: 0.18em; text-transform: uppercase; color: var(--stone-500);
    margin: 0 0 18px;
  }
  .sm-bloc ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
  .sm-bloc a {
    display: block;
    font-family: var(--font-display); font-size: 22px; line-height: 1.2;
    color: var(--ink-900); letter-spacing: -0.005em;
    transition: color .2s;
  }
  .sm-bloc a:hover { color: var(--sage-700); }
  .sm-bloc a em { font-style: italic; }
  .legal-crumbs {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em;
    color: var(--stone-500); text-transform: uppercase;
    margin: 0 0 24px;
  }
  .legal-crumbs a { color: var(--stone-500); }
  .legal-crumbs a:hover { color: var(--ink-900); }
  .legal-crumbs .sep { margin: 0 8px; opacity: 0.5; }
</style>

<section class="section sm-page">
  <div class="container-wide">

    <nav class="legal-crumbs" aria-label="Fil d'Ariane">
      <a href="<?= LangService::url('/') ?>" data-en="Home">Accueil</a>
      <span class="sep" aria-hidden="true">/</span>
      <span aria-current="page" data-en="Site map">Plan du site</span>
    </nav>

    <div class="page-hero">
      <div class="page-hero-inner">
        <div>
          <div class="page-hero-issue">
            <span data-en="Navigation · Map">Navigation · Plan</span>
            <span data-en="Every page in one place">Toutes les pages en un endroit</span>
          </div>
          <h1 data-en="Site <em>map</em>.">Plan du <em>site</em>.</h1>
        </div>
        <p class="lede" data-en="A bird's-eye view of every public page. If something's missing, write to us — we'll add it.">Une vue d'ensemble de toutes les pages publiques. Si quelque chose manque, écrivez-nous, on l'ajoutera.</p>
      </div>
    </div>

    <div class="sm-grid">

      <div class="sm-bloc">
        <h2 data-en="The offering">L'offre</h2>
        <ul>
          <li><a href="<?= LangService::url('/') ?>" data-en="Home">Accueil</a></li>
          <li><a href="<?= LangService::url('chambres-d-hotes') ?>" data-en="B&amp;B rooms">Chambres d'hôtes</a></li>
          <li><a href="<?= LangService::url('location-villa-provence') ?>" data-en="Whole house">Maison d'hôtes</a></li>
          <li><a href="<?= LangService::url('espaces-exterieurs') ?>" data-en="Outdoor spaces">Espaces extérieurs</a></li>
        </ul>
      </div>

      <div class="sm-bloc">
        <h2 data-en="The territory">Le territoire</h2>
        <ul>
          <li><a href="<?= LangService::url('itineraire') ?>" data-en="Things to do">Que faire sur place</a></li>
          <li><a href="<?= LangService::url('journal') ?>" data-en="The Journal">Le Journal</a></li>
        </ul>
      </div>

      <div class="sm-bloc">
        <h2 data-en="The house">La maison</h2>
        <ul>
          <li><a href="<?= LangService::url('votre-hote') ?>" data-en="Your host">Votre hôte</a></li>
          <li><a href="<?= LangService::url('contact') ?>" data-en="Contact">Contact</a></li>
          <li><a href="<?= LangService::url('livret') ?>" data-en="Welcome booklet">Livret d'accueil</a></li>
        </ul>
      </div>

      <div class="sm-bloc">
        <h2 data-en="Legal">Mentions</h2>
        <ul>
          <li><a href="<?= LangService::url('mentions-legales') ?>" data-en="Legal notice">Mentions légales</a></li>
          <li><a href="<?= LangService::url('politique-confidentialite') ?>" data-en="Privacy policy">Politique de confidentialité</a></li>
          <li><a href="<?= LangService::url('plan-du-site') ?>" data-en="Site map">Plan du site</a></li>
        </ul>
      </div>

    </div>

  </div>
</section>
