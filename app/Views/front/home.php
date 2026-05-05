<?php
/**
 * Home V9 — esprit weeks-off.com (Sprint 1, 2026-05-05).
 *
 * Variables disponibles depuis HomeController :
 *   $seo, $jsonLd, $lang
 *   $faqs              array<{question,answer}>
 *   $recentArticles    array<{slug,title,excerpt,category,cover_image,published_at}>
 *   $featuredReviews   array<{author,origin,content,platform,offer,rating}>
 *   $guestOriginsCount int
 *
 * Tous les bouts de copy statiques restent en clair ici (ton "weeks-off-like"
 * éditorial) — Jorge ajustera les phrases plus tard. Les sections branchées
 * sur la DB ont un fallback statique si la table renvoie vide.
 */

$frenchMonths = [
    1=>'janv.',2=>'févr.',3=>'mars',4=>'avr.',5=>'mai',6=>'juin',
    7=>'juil.',8=>'août',9=>'sept.',10=>'oct.',11=>'nov.',12=>'déc.',
];
$fmtArticleDate = function (?string $iso) use ($frenchMonths): string {
    if (!$iso) return '';
    $t = strtotime($iso);
    if (!$t) return '';
    return (int)date('j', $t) . ' ' . $frenchMonths[(int)date('n', $t)] . ' ' . date('Y', $t);
};
$originsCount = max(21, (int)($guestOriginsCount ?? 0));
?>

<!-- Hero -->
<section class="hero" aria-label="Vue de la maison">
    <div class="hero__photo">
        <img src="/uploads/villa-plaisance-facade-04.webp"
             alt="Façade de Villa Plaisance avec son palmier, jardin de pierre, lumière de mi-journée."
             fetchpriority="high" decoding="async">
    </div>
    <div class="hero__caption">
        <span class="lbl">Villa Plaisance, vue depuis la terrasse</span>
        <span class="meta">une maison &middot; deux saisons</span>
    </div>
</section>

<!-- Identité -->
<section class="identite">
    <div class="eyebrow">Bédarrides, Provence</div>
    <h1>Villa Plaisance</h1>
    <p class="baseline">Chambres d'hôtes et villa de charme à Bédarrides</p>
</section>

<!-- Acte I — Manifesto -->
<section class="manifesto">
    <div class="manifesto__inner">
        <div class="manifesto__num">I.</div>
        <div>
            <h2>Une maison, deux façons d'y séjourner</h2>
            <p>
                Villa Plaisance est une maison provençale ouverte aux voyageurs, à
                Bédarrides, entre Avignon et Orange. De septembre à juin, nous accueillons
                en chambres d'hôtes, avec petit-déjeuner maison et piscine partagée.
                En juillet et août, la villa entière se loue en exclusivité&nbsp;: quatre
                chambres, une piscine privée de 12 mètres sur 6, un jardin sous les oliviers.
            </p>
            <p class="manifesto__chute">
                Le lieu est calme. Le village est vivant. La campagne est à pied,
                le TGV à quinze minutes.
            </p>
        </div>
    </div>
</section>

<!-- Strip 3 colonnes -->
<section class="strip" aria-label="En un coup d'œil">
    <div>
        <span class="k">— Septembre à juin</span>
        <p class="v">Chambres d'hôtes</p>
        <p class="det">2 chambres climatisées &middot; petit-déjeuner maison inclus &middot; piscine partagée &middot; 1 à 5 personnes</p>
    </div>
    <div>
        <span class="k">— Juillet &amp; août</span>
        <p class="v">Villa entière, en exclusivité</p>
        <p class="det">4 chambres &middot; jusqu'à 10 personnes &middot; piscine privée 12 × 6 m &middot; cuisine équipée</p>
    </div>
    <div>
        <span class="k">— Parler à Jorge</span>
        <p class="v">Écrire directement</p>
        <p class="det"><a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a><br>FR &middot; EN &middot; ES &middot; DE</p>
    </div>
</section>

<!-- Acte II — Diptyque title -->
<section class="manifesto" style="border-top:none">
    <div class="manifesto__inner">
        <div class="manifesto__num">II.</div>
        <h2>Deux saisons, deux maisons</h2>
    </div>
</section>

<!-- Chapitre 1 — Chambres d'hôtes -->
<article class="chapter">
    <div class="chapter__inner">
        <div class="chapter__photo">
            <img src="/uploads/villa-plaisance-chambre-verte-01.webp"
                 alt="Chambre Verte de Villa Plaisance, mur sombre profond, lit double, lampes murales chaudes."
                 loading="lazy" decoding="async">
        </div>
        <div class="chapter__text">
            <span class="eyebrow">de septembre à juin</span>
            <h2>Chambres d'hôtes</h2>
            <p>
                Deux chambres climatisées, petit-déjeuner maison avec des produits locaux,
                piscine partagée avec les hôtes. Un accueil personnel, des conseils sur mesure.
            </p>
            <dl class="chapter__facts">
                <div><dt>Chambres</dt><dd>2 (Verte &amp; Bleue)</dd></div>
                <div><dt>Petit-déjeuner</dt><dd>Inclus</dd></div>
                <div><dt>Piscine</dt><dd>Partagée</dd></div>
            </dl>
            <a class="chapter__link" href="<?= LangService::url('chambres-d-hotes') ?>">
                Découvrir les chambres
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
            </a>
        </div>
    </div>
</article>

<!-- Chapitre 2 — Villa entière -->
<article class="chapter chapter--reverse">
    <div class="chapter__inner">
        <div class="chapter__photo">
            <img src="/uploads/villa-plaisance-salon-salle-a-manger-04.webp"
                 alt="Salon de Villa Plaisance, arche en pierre brute, canapé en cuir cognac, salle à manger en arrière-plan."
                 loading="lazy" decoding="async">
        </div>
        <div class="chapter__text">
            <span class="eyebrow">juillet &amp; août</span>
            <h2>La Villa en exclusivité</h2>
            <p>
                Quatre chambres, piscine privée 12×6 m clôturée, cuisine entièrement
                équipée, jardin provençal. Jusqu'à dix personnes, en totale autonomie.
            </p>
            <dl class="chapter__facts">
                <div><dt>Chambres</dt><dd>4</dd></div>
                <div><dt>Capacité</dt><dd>jusqu'à 10</dd></div>
                <div><dt>Piscine</dt><dd>Privée 12 × 6 m</dd></div>
            </dl>
            <a class="chapter__link" href="<?= LangService::url('location-villa-provence') ?>">
                Découvrir la villa entière
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
            </a>
        </div>
    </div>
</article>

<!-- Interlude photo -->
<section class="interlude" aria-label="Interlude photographique">
    <div class="interlude__photo">
        <img src="/uploads/villa-plaisance-jardin-exterieur-01.webp"
             alt="Coquelicots rouges au premier plan, chaises de jardin en métal blanc en arrière-plan flou."
             loading="lazy" decoding="async">
    </div>
    <p class="interlude__caption"><em>Quelque part dans le jardin, fin août.</em></p>
</section>

<!-- Acte III — Triangle d'Or -->
<section class="distances">
    <div class="distances__num">III.</div>
    <h2>Au cœur du Triangle d'Or</h2>
    <div class="distances__grid">
        <div>
            <span class="num">8<small>min</small></span>
            <span class="lbl">Châteauneuf-du-Pape</span>
        </div>
        <div>
            <span class="num">15<small>min</small></span>
            <span class="lbl">Avignon &middot; Gare TGV</span>
        </div>
        <div>
            <span class="num">18<small>min</small></span>
            <span class="lbl">Orange &middot; Théâtre antique</span>
        </div>
    </div>
    <p class="distances__pont">
        Et au-delà des trois villes, le monde entier vient passer la porte.
    </p>
</section>

<!-- Mappemonde (placeholder visuel) -->
<section class="mappemonde" aria-label="Mappemonde des hôtes">
    <div class="mappemonde__inner">
        <p class="mappemonde__head">Nos hôtes viennent de…</p>
        <p class="mappemonde__count">plus de <strong><?= htmlspecialchars((string)$originsCount) ?></strong> destinations</p>
        <div class="mappemonde__viewport" aria-hidden="true">
            <span>[ carte du monde &middot; pins ]</span>
            <span class="mappemonde__pin" style="left:15%;top:38%"></span>
            <span class="mappemonde__pin" style="left:28%;top:30%"></span>
            <span class="mappemonde__pin" style="left:48%;top:28%"></span>
            <span class="mappemonde__pin" style="left:52%;top:42%"></span>
            <span class="mappemonde__pin" style="left:50%;top:48%"></span>
            <span class="mappemonde__pin" style="left:54%;top:36%"></span>
            <span class="mappemonde__pin" style="left:62%;top:46%"></span>
            <span class="mappemonde__pin" style="left:72%;top:55%"></span>
            <span class="mappemonde__pin" style="left:80%;top:75%"></span>
            <span class="mappemonde__pin" style="left:30%;top:62%"></span>
            <span class="mappemonde__pin" style="left:25%;top:78%"></span>
            <span class="mappemonde__pin" style="left:40%;top:70%"></span>
        </div>
    </div>
</section>

<!-- Acte IV — Voix d'hôtes (DB ou fallback) -->
<section class="voix">
    <div class="voix__head">
        <div class="eyebrow"><span class="num">IV.</span>Hôtes du monde</div>
        <h2>Ce qu'on en dit</h2>
    </div>
    <?php if (!empty($featuredReviews)): ?>
    <div class="voix__grid <?= count($featuredReviews) === 4 ? 'voix__grid--4' : '' ?>">
        <?php foreach ($featuredReviews as $r): ?>
        <figure class="voix__bloc">
            <blockquote>« <?= htmlspecialchars((string)$r['content']) ?> »</blockquote>
            <figcaption>
                <?= htmlspecialchars((string)$r['author']) ?>
                <?php if (!empty($r['origin'])): ?> &middot; <?= htmlspecialchars((string)$r['origin']) ?><?php endif; ?>
                <?php if (!empty($r['offer'])): ?>
                    &middot; <?= ($r['offer'] === 'villa') ? 'Villa entière' : (($r['offer'] === 'bb') ? 'Chambres d\'hôtes' : 'Les deux saisons') ?>
                <?php endif; ?>
            </figcaption>
        </figure>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="voix__grid voix__grid--4">
        <figure class="voix__bloc" lang="fr">
            <blockquote>« Un endroit magnifique, calme et reposant. La piscine est superbe et le jardin enchanteur. Nous avons passé deux semaines inoubliables en famille. »</blockquote>
            <figcaption>Marianne &middot; Waterloo, Belgique &middot; Villa entière</figcaption>
        </figure>
        <figure class="voix__bloc" lang="en">
            <blockquote>« A lovely stay. The hosts are warm and welcoming. The breakfast was delicious and the pool a wonderful bonus. »</blockquote>
            <figcaption>Rosemarie &middot; Northampton, UK &middot; Chambres d'hôtes</figcaption>
        </figure>
        <figure class="voix__bloc" lang="de">
            <blockquote>« Wunderbar! Die Villa ist perfekt für Familien. Der Pool, der Garten, alles war traumhaft. »</blockquote>
            <figcaption>Charlotte &middot; Allemagne &middot; Villa entière</figcaption>
        </figure>
        <figure class="voix__bloc" lang="nl">
            <blockquote>« Perfect verblijf. Gastvrije ontvangst, heerlijk ontbijt, prachtige tuin en zwembad. »</blockquote>
            <figcaption>Jeroen &middot; Pays-Bas &middot; Booking 10/10 &middot; Chambres d'hôtes</figcaption>
        </figure>
    </div>
    <?php endif; ?>
</section>

<!-- Acte V — Journal (DB ou fallback) -->
<section class="journal">
    <div class="journal__head">
        <h2><span class="num">V.</span>Le Journal</h2>
        <a href="<?= LangService::url('journal') ?>">Tous les articles →</a>
    </div>
    <?php if (!empty($recentArticles)): ?>
    <div class="journal__grid">
        <?php foreach ($recentArticles as $a): ?>
        <article class="journal__card">
            <span class="meta">
                <?= htmlspecialchars($fmtArticleDate($a['published_at'] ?? null)) ?>
                <?php if (!empty($a['category'])): ?> &middot; <?= htmlspecialchars((string)$a['category']) ?><?php endif; ?>
            </span>
            <h3><a href="<?= LangService::url('journal') ?>/<?= htmlspecialchars((string)$a['slug']) ?>"><?= htmlspecialchars((string)$a['title']) ?></a></h3>
            <?php if (!empty($a['excerpt'])): ?>
            <p><?= htmlspecialchars((string)$a['excerpt']) ?></p>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="journal__grid">
        <article class="journal__card">
            <span class="meta">15 oct. 2025 &middot; Voyager autrement</span>
            <h3><a href="<?= LangService::url('journal') ?>/le-tourisme-de-masse-est-une-arnaque">Le tourisme de masse est une arnaque</a></h3>
            <p>Pourquoi le tourisme de masse persiste malgré ses travers, et comment choisir une autre voie en Provence.</p>
        </article>
        <article class="journal__card">
            <span class="meta">1 oct. 2025 &middot; Voyager autrement</span>
            <h3><a href="<?= LangService::url('journal') ?>/louer-maison-plutot-hotel-voyage">Louer une maison plutôt qu'un hôtel : pourquoi ça change tout au voyage</a></h3>
            <p>Comparatif entre séjour hôtel et location de maison en Provence. Ce que ça change vraiment.</p>
        </article>
        <article class="journal__card">
            <span class="meta">20 sept. 2025 &middot; Hôtes &amp; hôteliers</span>
            <h3><a href="<?= LangService::url('journal') ?>/vie-proprietaire-chambre-hotes">Ce que personne ne dit sur la vie d'un propriétaire de chambre d'hôtes</a></h3>
            <p>Les coulisses du métier d'hôte en Provence. Entre passion et réalité quotidienne.</p>
        </article>
    </div>
    <?php endif; ?>
</section>

<!-- Acte VI — FAQ (DB ou fallback) -->
<section class="faq">
    <div class="faq__head">
        <span class="faq__num">VI.</span>
        <h2>Quelques questions</h2>
    </div>
    <dl class="faq__list">
        <?php if (!empty($faqs)): ?>
            <?php foreach ($faqs as $q): ?>
            <div class="faq__item">
                <dt><?= htmlspecialchars((string)$q['question']) ?></dt>
                <dd><?= nl2br(htmlspecialchars((string)$q['answer'])) ?></dd>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="faq__item">
                <dt>Où se situe Villa Plaisance ?</dt>
                <dd>Villa Plaisance se trouve à Bédarrides, dans le Vaucluse (84370), au cœur du Triangle d'Or provençal, à 8 minutes de Châteauneuf-du-Pape, 15 minutes d'Avignon et 18 minutes d'Orange.</dd>
            </div>
            <div class="faq__item">
                <dt>Quelle est la différence entre chambres d'hôtes et villa entière ?</dt>
                <dd>De septembre à juin, nous accueillons en chambres d'hôtes (2 chambres, petit-déjeuner inclus, piscine partagée). En juillet et août, la villa entière se loue en exclusivité (4 chambres, piscine privée, cuisine équipée, jusqu'à 10 personnes).</dd>
            </div>
            <div class="faq__item">
                <dt>Y a-t-il une piscine ?</dt>
                <dd>Oui. La piscine mesure 12 mètres sur 6, elle est clôturée et sécurisée. En chambres d'hôtes, elle est partagée avec les autres hôtes. En location villa, elle est entièrement privatisée.</dd>
            </div>
        <?php endif; ?>
    </dl>
</section>

<!-- Acte VII — Écrire -->
<section class="ecrire" id="contact">
    <div class="ecrire__num">VII.</div>
    <h2>Écrire</h2>
    <p>Envie de séjourner chez nous ?</p>
    <p class="sub">Contactez-nous pour organiser votre séjour en Provence.</p>
    <div class="actions">
        <a href="<?= LangService::url('contact') ?>" class="pill pill--solid pill--big">
            Nous écrire
            <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        </a>
        <p class="alt">Ou directement&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a></p>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter">
    <h3>Recevoir des nouvelles de la maison</h3>
    <p>Une lettre, deux ou trois fois par an. Annonce des semaines libres, articles du journal, jamais plus.</p>
    <form onsubmit="event.preventDefault()">
        <input type="email" placeholder="Votre adresse e-mail" aria-label="E-mail">
        <button type="submit">S'inscrire</button>
    </form>
</section>
