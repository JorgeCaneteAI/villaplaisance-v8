<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Disponibilités publiques (vue annuelle 12 mois).
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 *
 * Source design : docs/design-refs/2026-05-24-calendriers.html (Style A).
 */ ?>

<!-- ============ MASTHEAD (BDD vp_sections or fallback HTML) ============ -->
<?php
$_v8HeroDispo = null;
foreach (BlockService::getSections('disponibilites', $lang) as $_s) {
    if ((int)$_s['position'] === 1 && $_s['block_type'] === 'hero') {
        $_v8HeroDispo = BlockService::renderBlock($_s);
        break;
    }
}
?>
<?php if ($_v8HeroDispo): ?>
<?= $_v8HeroDispo ?>
<?php else: ?>
<section class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="page-hero-issue">
        <span data-en="Availability">Disponibilités</span>
        <span data-en="Synced with Airbnb &amp; Booking">Sync. Airbnb &amp; Booking</span>
      </div>
      <h1>Douze <em>mois</em>,<br/>d'un seul tenant.</h1>
    </div>
    <div>
      <p class="lede" data-en="All our availability for the year ahead — synchronised with Airbnb and Booking, updated every thirty minutes. To book, simply write to us.">Toutes nos disponibilités sur l'année à venir — synchronisées avec Airbnb et Booking, mises à jour toutes les trente minutes. Pour réserver, il suffit de nous écrire.</p>
      <div class="page-hero-ctas">
        <a class="btn" href="<?= \LangService::url('contact') ?>"><span data-en="Enquire about a stay">Demander un séjour</span> →</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ VUE ANNUELLE 12 MOIS ============ -->
<section class="section">
  <div class="container-wide">
    <?php include __DIR__ . '/_partials/calendar_annual.php'; ?>

    <div class="legend">
      <span class="legend-key"><span class="legend-sw open"></span> <span data-en="Available — B&amp;B">Disponible — Chambres</span></span>
      <span class="legend-key"><span class="legend-sw villa"></span> <span data-en="Available — Villa">Disponible — Villa</span></span>
      <span class="legend-key"><span class="legend-sw booked"></span> <span data-en="Booked">Réservé</span></span>
    </div>
  </div>
</section>

<!-- ============ RAPPEL CONTACT ============ -->
<section class="section-tight" style="padding-bottom: clamp(64px, 8vw, 96px);">
  <div class="container-wide">
    <div style="border: var(--hairline); padding: clamp(28px, 4vw, 48px); display: grid; grid-template-columns: 1fr auto; gap: 24px; align-items: center;">
      <div>
        <div class="section-label" style="margin-bottom: 12px;">
          <span class="numeral" data-en="— A free date?">— Une date libre ?</span>
        </div>
        <h2 class="h-lg" style="margin: 0; max-width: 22ch;" data-en="Write to us — we reply within the day, by hand.">Écrivez-nous — nous répondons dans la journée, à la main.</h2>
      </div>
      <a class="btn" href="<?= \LangService::url('contact') ?>"><span data-en="Contact">Contact</span> →</a>
    </div>
  </div>
</section>
