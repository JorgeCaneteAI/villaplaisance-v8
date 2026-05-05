<?php
/**
 * Sur place V9 — esprit weeks-off.com (Sprint 3, 2026-05-05).
 *
 * Variables disponibles depuis SurPlaceController :
 *   $articles     vp_articles WHERE type='sur-place'
 *   $categories   liste unique des catégories
 *   $seo, $jsonLd, $lang
 */

$frenchMonths = [
    1=>'janv.',2=>'févr.',3=>'mars',4=>'avr.',5=>'mai',6=>'juin',
    7=>'juil.',8=>'août',9=>'sept.',10=>'oct.',11=>'nov.',12=>'déc.',
];
$fmtDate = function (?string $iso) use ($frenchMonths): string {
    if (!$iso) return '';
    $t = strtotime($iso);
    if (!$t) return '';
    return (int)date('j', $t) . ' ' . $frenchMonths[(int)date('n', $t)] . ' ' . date('Y', $t);
};
?>

<section class="identite identite--compact">
    <div class="eyebrow">Sur place</div>
    <h1>Autour de la maison</h1>
    <p class="baseline">Adresses, restaurants, sites, balades — sélectionnés par Jorge.</p>
</section>

<?php if (!empty($categories)): ?>
<nav class="filters" aria-label="Filtres par catégorie" data-filters>
    <button class="filter is-active" type="button" data-filter="all">Toutes les catégories</button>
    <?php foreach (array_filter($categories) as $cat): ?>
    <button class="filter" type="button" data-filter="<?= htmlspecialchars((string)$cat) ?>"><?= htmlspecialchars((string)$cat) ?></button>
    <?php endforeach; ?>
</nav>
<?php endif; ?>

<section class="journal" style="padding-top:48px">
    <?php if (!empty($articles)): ?>
    <div class="journal__grid" data-articles>
        <?php foreach ($articles as $a): ?>
        <article class="journal__card" data-category="<?= htmlspecialchars((string)($a['category'] ?? '')) ?>">
            <span class="meta">
                <?php if (!empty($a['category'])): ?><?= htmlspecialchars((string)$a['category']) ?><?php endif; ?>
                <?php if (!empty($a['published_at'])): ?>
                    <?php if (!empty($a['category'])): ?> &middot; <?php endif; ?>
                    <?= htmlspecialchars($fmtDate($a['published_at'])) ?>
                <?php endif; ?>
            </span>
            <h3><a href="<?= LangService::url('sur-place') ?>/<?= htmlspecialchars((string)$a['slug']) ?>"><?= htmlspecialchars((string)$a['title']) ?></a></h3>
            <?php if (!empty($a['excerpt'])): ?>
            <p><?= htmlspecialchars((string)$a['excerpt']) ?></p>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="text-align:center;font-family:'Fraunces',serif;font-style:italic;color:var(--ink-soft);max-width:480px;margin:60px auto">Aucune fiche publiée pour le moment.</p>
    <?php endif; ?>
</section>

<section class="ecrire" id="contact">
    <h2>Écrire</h2>
    <p>Vous cherchez une adresse précise ?</p>
    <p class="sub">Jorge prépare un itinéraire sur mesure.</p>
    <div class="actions">
        <a href="<?= LangService::url('contact') ?>" class="pill pill--solid pill--big">Nous écrire</a>
        <p class="alt">Ou directement&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a></p>
    </div>
</section>

<script>
(function(){
    var filters = document.querySelectorAll('[data-filter]');
    var cards = document.querySelectorAll('[data-articles] [data-category]');
    if (!filters.length) return;
    filters.forEach(function(f){
        f.addEventListener('click', function(){
            var v = f.dataset.filter;
            filters.forEach(function(x){ x.classList.toggle('is-active', x === f); });
            cards.forEach(function(c){
                c.style.display = (v === 'all' || c.dataset.category === v) ? '' : 'none';
            });
        });
    });
})();
</script>
