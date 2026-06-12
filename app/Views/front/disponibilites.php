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
        <span><?= htmlspecialchars(t('dispo.hero_issue')) ?></span>
        <span><?= htmlspecialchars(t('dispo.hero_sync')) ?></span>
      </div>
      <h1><?= t('dispo.hero_title') ?></h1>
    </div>
    <div>
      <p class="lede"><?= htmlspecialchars(t('dispo.hero_lede')) ?></p>
      <div class="page-hero-ctas">
        <a class="btn" href="<?= \LangService::url('contact') ?>"><span><?= htmlspecialchars(t('dispo.cta_stay')) ?></span> →</a>
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
      <span class="legend-key"><span class="legend-sw open"></span> <span><?= htmlspecialchars(t('dispo.legend_open')) ?></span></span>
      <span class="legend-key"><span class="legend-sw villa"></span> <span><?= htmlspecialchars(t('dispo.legend_villa')) ?></span></span>
      <span class="legend-key"><span class="legend-sw booked"></span> <span><?= htmlspecialchars(t('contact.cal.booked')) ?></span></span>
    </div>
  </div>
</section>

<!-- ============ RAPPEL CONTACT ============ -->
<section class="section-tight" style="padding-bottom: clamp(64px, 8vw, 96px);">
  <div class="container-wide">
    <div style="border: var(--hairline); padding: clamp(28px, 4vw, 48px); display: grid; grid-template-columns: 1fr auto; gap: 24px; align-items: center;">
      <div>
        <div class="section-label" style="margin-bottom: 12px;">
          <span class="numeral"><?= htmlspecialchars(t('dispo.free_date')) ?></span>
        </div>
        <h2 class="h-lg" style="margin: 0; max-width: 22ch;"><?= htmlspecialchars(t('dispo.write_us')) ?></h2>
      </div>
      <a class="btn" href="<?= \LangService::url('contact') ?>"><span><?= htmlspecialchars(t('proto.nav.contact')) ?></span> →</a>
    </div>
  </div>
</section>
