<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Disponibilités publiques.
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 *
 * VERSION DE TEST (2026-05-24) — calendriers temporairement désactivés
 * pour isoler le bug. Si cette page charge, le problème est dans
 * _partials/calendar_annual.php → calendar_month.php → PublicAvailabilityService.
 */ ?>

<!-- ============ MASTHEAD ============ -->
<section class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="page-hero-issue">
        <span>Disponibilités</span>
        <span>Page en cours</span>
      </div>
      <h1>Douze <em>mois</em>,<br/>d'un seul tenant.</h1>
    </div>
    <div>
      <p class="lede">Toutes nos disponibilités sur l'année à venir — synchronisées avec Airbnb et Booking. Pour réserver, il suffit de nous écrire.</p>
      <div class="page-hero-ctas">
        <a class="btn" href="<?= \LangService::url('contact') ?>"><span>Demander un séjour</span> →</a>
      </div>
    </div>
  </div>
</section>

<!-- ============ TEST ============ -->
<section class="section">
  <div class="container-wide">
    <div style="border: 2px dashed var(--terra-500); padding: clamp(28px, 4vw, 48px); background: color-mix(in oklab, var(--terra-500) 6%, var(--linen-50));">
      <div class="section-label" style="margin-bottom: 12px;">
        <span class="numeral" style="color: var(--terra-500);">— Test de page</span>
      </div>
      <h2 class="h-lg" style="margin: 0 0 16px; max-width: 28ch;">Si tu vois ce bloc, la page se charge correctement.</h2>
      <p class="body-lg" style="margin: 0; max-width: 60ch;">
        Les calendriers sont temporairement retirés le temps d'identifier ce
        qui bloquait. Une fois cette page validée, on les réintroduit un par
        un (mois isolé, puis ruban, puis vue annuelle).
      </p>
      <p style="margin-top: 18px; font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.1em; color: var(--stone-500); text-transform: uppercase;">
        Build : <?= date('Y-m-d H:i:s') ?>
      </p>
    </div>
  </div>
</section>
