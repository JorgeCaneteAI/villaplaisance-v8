<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Journal Tourisme (magazine avec filtres).
 * Portée depuis le proto Claude design (journal-tourisme.html).
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  /* Magazine masthead */
  .j-masthead {
    background: var(--linen-50);
    border-bottom: var(--hairline);
  }
  .j-masthead-inner {
    padding: clamp(64px, 8vw, 120px) var(--gutter) clamp(40px, 5vw, 64px);
    max-width: var(--container-wide);
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(32px, 5vw, 80px);
    align-items: end;
  }
  .j-issue {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em;
    color: var(--stone-500); text-transform: uppercase;
    display: flex; justify-content: space-between; align-items: baseline;
    border-bottom: var(--hairline);
    padding-bottom: 14px; margin-bottom: 28px;
  }
  .j-title {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(56px, 9vw, 140px); line-height: 0.92; letter-spacing: -0.025em;
    color: var(--ink-900);
  }
  .j-title em { font-style: italic; color: var(--sage-700); }
  .j-lede {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(20px, 1.8vw, 26px); line-height: 1.4; letter-spacing: -0.005em;
    color: var(--ink-700); max-width: 42ch; margin: 0;
  }
  @media (max-width: 720px) { .j-masthead-inner { grid-template-columns: 1fr; } }

  /* Sticky filter bar */
  .j-filter {
    position: sticky;
    top: 60px;
    z-index: 30;
    background: color-mix(in oklab, var(--linen-50) 96%, transparent);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border-bottom: var(--hairline);
  }
  .j-filter-inner {
    max-width: var(--container-wide); margin: 0 auto;
    padding: 14px var(--gutter);
    display: flex; gap: 6px; align-items: center;
    overflow-x: auto;
    scrollbar-width: none;
  }
  .j-filter-inner::-webkit-scrollbar { display: none; }
  .j-filter button {
    flex-shrink: 0;
    background: transparent; border: 1px solid color-mix(in oklab, var(--ink-900) 15%, transparent);
    color: var(--stone-600);
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 10px 16px; cursor: pointer;
    transition: background .2s, color .2s, border-color .2s;
  }
  .j-filter button:hover { color: var(--ink-900); border-color: var(--ink-900); }
  .j-filter button[aria-pressed="true"] { background: var(--ink-900); color: var(--linen-50); border-color: var(--ink-900); }
  .j-filter .count {
    margin-left: auto; flex-shrink: 0;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.12em;
    color: var(--stone-500); padding-left: 16px;
    border-left: var(--hairline);
  }

  /* Featured */
  .j-featured {
    display: grid; grid-template-columns: 1.2fr 1fr; gap: clamp(32px, 5vw, 72px);
    margin-bottom: clamp(48px, 6vw, 96px);
    align-items: center;
  }
  .j-featured .img {
    aspect-ratio: 4/3; background-size: cover; background-position: center;
  }
  .j-featured .meta {
    display: flex; gap: 14px; align-items: baseline;
    margin-bottom: 18px;
  }
  .j-cat {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 6px 10px; background: var(--linen-100);
    color: var(--ink-900);
  }
  .j-cat.solid { background: var(--ink-900); color: var(--linen-50); }
  .j-cat.sage { background: color-mix(in oklab, var(--sage-200) 50%, transparent); }
  .j-date {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.1em;
    color: var(--stone-500);
  }
  .j-featured h2 {
    margin: 0 0 18px;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(34px, 4vw, 56px); line-height: 1.0; letter-spacing: -0.02em;
    color: var(--ink-900);
  }
  .j-featured h2 em { font-style: italic; }
  .j-featured .excerpt {
    font-size: 16px; line-height: 1.6; color: var(--ink-700); max-width: 50ch;
    margin: 0 0 18px;
  }
  .j-author {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.06em;
    color: var(--stone-600);
  }
  @media (max-width: 720px) { .j-featured { grid-template-columns: 1fr; } }

  /* Article grid */
  .j-grid {
    display: grid; grid-template-columns: repeat(12, 1fr);
    gap: clamp(32px, 4vw, 56px) clamp(24px, 2.4vw, 36px);
  }
  .j-card {
    display: flex; flex-direction: column; gap: 16px;
    transition: transform .3s cubic-bezier(.2,.6,.2,1);
  }
  .j-card.hidden { display: none; }
  .j-card:hover { transform: translateY(-2px); }
  .j-card .img {
    aspect-ratio: 4/3; background-size: cover; background-position: center;
    background-color: var(--linen-200);
    transition: filter .3s;
  }
  .j-card.tall .img { aspect-ratio: 3/4; }
  .j-card.wide .img { aspect-ratio: 16/10; }
  .j-card:hover .img { filter: brightness(1.02); }
  .j-card h3 {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(22px, 2vw, 30px); line-height: 1.1; letter-spacing: -0.015em;
    color: var(--ink-900);
  }
  .j-card.lg h3 { font-size: clamp(28px, 2.6vw, 38px); }
  .j-card h3 em { font-style: italic; }
  .j-card .meta {
    display: flex; gap: 12px; align-items: baseline; flex-wrap: wrap;
    margin-bottom: -6px;
  }
  .j-card .excerpt {
    font-size: 14.5px; line-height: 1.6; color: var(--stone-600); max-width: 48ch;
    margin: 0;
  }

  @media (max-width: 960px) {
    .j-card { grid-column: span 6 !important; }
  }
  @media (max-width: 560px) {
    .j-card { grid-column: span 12 !important; }
  }

  /* Newsletter */
  .newsletter {
    background: var(--ink-900);
    color: var(--linen-100);
    padding: clamp(56px, 7vw, 96px) 0;
    border-top: var(--hairline-strong);
  }
  .nl-inner {
    max-width: 880px; margin: 0 auto;
    padding: 0 var(--gutter);
    text-align: center;
  }
  .nl-inner h2 {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(36px, 5vw, 64px); line-height: 1.0; letter-spacing: -0.02em;
    margin: 0 0 18px; color: var(--linen-50); text-wrap: balance;
  }
  .nl-inner h2 em { font-style: italic; color: var(--sage-200); }
  .nl-form {
    margin-top: 28px;
    display: flex; gap: 10px; max-width: 480px; margin-left: auto; margin-right: auto;
  }
  .nl-form input {
    flex: 1; background: transparent;
    border: 0; border-bottom: 1px solid rgba(var(--linen-50-rgb), 0.4);
    color: var(--linen-50);
    font: inherit; font-size: 16px;
    padding: 12px 0;
  }
  .nl-form input::placeholder { color: rgba(var(--linen-50-rgb), 0.5); }
  .nl-form input:focus { outline: 0; border-bottom-color: var(--linen-50); }

  /* Placeholder banner */
  .pl-banner {
    display: flex; align-items: center; gap: 14px;
    padding: 16px 22px;
    border: 1px dashed color-mix(in oklab, var(--ink-900) 30%, transparent);
    background: color-mix(in oklab, var(--terra-500) 5%, var(--linen-50));
    max-width: 720px;
    margin: 24px auto 0;
  }
  .pl-banner .dot { width: 8px; height: 8px; background: var(--terra-500); border-radius: 50%; flex-shrink: 0; }
  .pl-banner .txt { font-size: 13.5px; color: var(--ink-700); line-height: 1.55; }
  .pl-banner .lbl { font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em; color: var(--terra-600); display: block; margin-bottom: 2px; }
</style>


<!-- ============ MASTHEAD (BDD vp_sections or fallback HTML) ============ -->
<?php
$_v8HeroJournal = null;
foreach (BlockService::getSections('journal', $lang) as $_s) {
    if ((int)$_s['position'] === 1 && $_s['block_type'] === 'hero') {
        $_v8HeroJournal = BlockService::renderBlock($_s);
        break;
    }
}
?>
<?php if ($_v8HeroJournal): ?>
<?= $_v8HeroJournal ?>
<?php else: ?>
<section class="j-masthead">
  <div class="j-masthead-inner">
    <div>
      <div class="j-issue">
        <span data-en="Journal · 04 / Tourism">Journal · 04 / Tourisme</span>
        <span data-en="N° 01 · 2026">N° 01 · 2026</span>
      </div>
      <h1 class="j-title">Voyager <em>autrement</em><br/>en Provence.</h1>
    </div>
    <p class="j-lede" data-en="Five ways of looking at the region, what's changing in contemporary Provence, who keeps it alive, and how to travel through it without ticking boxes.">Cinq façons de regarder la région, ce qui bouge dans la Provence contemporaine, ceux qui la font vivre, et comment la traverser sans cocher de cases.</p>
  </div>

  <div class="pl-banner" style="margin-bottom: 28px;">
    <span class="dot"></span>
    <div class="txt">
      <span class="lbl" data-en="DESIGN MOCKUP">ÉBAUCHE GRAPHIQUE</span>
      Articles d'exemple. L'organisation et les filtres sont définitifs, le contenu sera remplacé par vos vrais articles.
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ FILTER ============ -->
<div class="j-filter">
  <div class="j-filter-inner" role="tablist">
    <button data-cat="all" aria-pressed="true">Tous</button>
    <button data-cat="contemporaine" aria-pressed="false">Provence contemporaine</button>
    <button data-cat="autrement" aria-pressed="false">Voyager autrement</button>
    <button data-cat="hotes" aria-pressed="false">Hôtes &amp; hôteliers</button>
    <button data-cat="transition" aria-pressed="false">Territoire &amp; transition</button>
    <button data-cat="art" aria-pressed="false">L'art de séjourner</button>
    <span class="count" id="filter-count">9 articles</span>
  </div>
</div>

<!-- ============ FEATURED ============ -->
<section class="section">
  <div class="container-wide">
    <div class="j-featured" data-cat="contemporaine">
      <div class="img" style="background-image: url('/uploads/villa-plaisance-vignes-provence-01.webp')"></div>
      <div>
        <div class="meta">
          <span class="j-cat solid" data-en="Contemporary Provence">Provence contemporaine</span>
          <span class="j-date" data-en="May 2026 · 12 min read">Mai 2026 · 12 min de lecture</span>
        </div>
        <h2><em>À la une</em><br/>La nouvelle Provence,<br/>celle qu'on ne regarde plus assez.</h2>
        <p class="excerpt" data-en="Behind the lavender postcards, a young generation of hoteliers, winemakers and farmers is quietly redrawing the region. Portraits of those who do, far from the clichés.">Derrière les cartes postales aux lavandes, une jeune génération d'hôteliers, de vignerons et de paysans redessine doucement la région. Portraits, loin des clichés.</p>
        <a class="btn-link" href="#"><span data-en="Read the article">Lire l'article</span> →</a>
      </div>
    </div>

    <!-- ============ ARTICLE GRID ============ -->
    <div class="j-grid" id="grid">

      <article class="j-card lg" style="grid-column: span 6;" data-cat="autrement">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-vp-itini-elisa-03-museeharibo.webp')"></div>
        <div class="meta">
          <span class="j-cat" data-en="Travel differently">Voyager autrement</span>
          <span class="j-date" data-en="April 2026 · 8 min">Avril 2026 · 8 min</span>
        </div>
        <h3>Le train de nuit, retour <em>très lent</em><br/>vers la Provence.</h3>
        <p class="excerpt" data-en="When the Intercités to Briançon rolls into Avignon at sunrise, you've already started your holiday. Notes on the slowest, gentlest way to come south.">Quand l'Intercités de Briançon arrive à Avignon au lever du soleil, les vacances ont déjà commencé. Notes sur la façon la plus lente, la plus douce, de descendre dans le Sud.</p>
      </article>

      <article class="j-card lg" style="grid-column: span 6;" data-cat="hotes">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-vp-itini-elisa-02-pont-du-gard.webp')"></div>
        <div class="meta">
          <span class="j-cat sage" data-en="Hosts &amp; hoteliers">Hôtes &amp; hôteliers</span>
          <span class="j-date" data-en="April 2026 · 6 min">Avril 2026 · 6 min</span>
        </div>
        <h3>Rencontre avec un vigneron<br/>de <em>Châteauneuf-du-Pape</em>.</h3>
        <p class="excerpt" data-en="Twenty hectares, three generations, and one question: how to keep making wine the way we like it.">Vingt hectares, trois générations, et une seule question : comment continuer à faire le vin comme on l'aime.</p>
      </article>

      <article class="j-card" style="grid-column: span 4;" data-cat="art">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-petit-dejeuner-confitures-01.webp')"></div>
        <div class="meta">
          <span class="j-cat" data-en="The art of staying">L'art de séjourner</span>
          <span class="j-date" data-en="March 2026">Mars 2026</span>
        </div>
        <h3>Petit-déjeuner :<br/>la dernière <em>cérémonie</em>.</h3>
      </article>

      <article class="j-card" style="grid-column: span 4;" data-cat="transition">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-jardin-exterieur-05.webp')"></div>
        <div class="meta">
          <span class="j-cat" data-en="Land &amp; transition">Territoire &amp; transition</span>
          <span class="j-date" data-en="March 2026">Mars 2026</span>
        </div>
        <h3>Ce que change <em>un platane</em><br/>dans une cour d'école.</h3>
      </article>

      <article class="j-card" style="grid-column: span 4;" data-cat="contemporaine">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-chambre-verte-04.webp')"></div>
        <div class="meta">
          <span class="j-cat" data-en="Contemporary Provence">Provence contemporaine</span>
          <span class="j-date" data-en="February 2026">Février 2026</span>
        </div>
        <h3>Quatre maisons d'hôtes<br/>qu'on aime, et <em>pourquoi</em>.</h3>
      </article>

      <article class="j-card tall" style="grid-column: span 4;" data-cat="autrement">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-jardin-exterieur-01.webp')"></div>
        <div class="meta">
          <span class="j-cat" data-en="Travel differently">Voyager autrement</span>
          <span class="j-date" data-en="February 2026">Février 2026</span>
        </div>
        <h3>La carte du <em>slow travel</em><br/>en Vaucluse.</h3>
        <p class="excerpt" data-en="A 50-km loop, four days, two pairs of shoes. The route we'd send a friend on.">Une boucle de 50 km, quatre jours, deux paires de chaussures. L'itinéraire qu'on conseille à un ami.</p>
      </article>

      <article class="j-card tall" style="grid-column: span 4;" data-cat="hotes">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-salon-salle-a-manger-04.webp')"></div>
        <div class="meta">
          <span class="j-cat sage" data-en="Hosts &amp; hoteliers">Hôtes &amp; hôteliers</span>
          <span class="j-date" data-en="January 2026">Janvier 2026</span>
        </div>
        <h3>Cinq questions à <em>celle</em><br/>qui tient la table d'à côté.</h3>
        <p class="excerpt" data-en="A restaurant in the village, three menus a year, and a kitchen the size of a cupboard.">Un restaurant au village, trois menus par an, et une cuisine grande comme une armoire.</p>
      </article>

      <article class="j-card tall" style="grid-column: span 4;" data-cat="transition">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-jardin-exterieur-12.webp')"></div>
        <div class="meta">
          <span class="j-cat" data-en="Land &amp; transition">Territoire &amp; transition</span>
          <span class="j-date" data-en="December 2025">Décembre 2025</span>
        </div>
        <h3>L'eau, la <em>vigne</em>,<br/>et le siècle qui vient.</h3>
        <p class="excerpt" data-en="What does climate change look like, vine by vine, here.">Le changement climatique vu d'ici, vigne par vigne.</p>
      </article>

      <article class="j-card" style="grid-column: span 6;" data-cat="art">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-piscine-privee-06.webp')"></div>
        <div class="meta">
          <span class="j-cat" data-en="The art of staying">L'art de séjourner</span>
          <span class="j-date" data-en="December 2025">Décembre 2025</span>
        </div>
        <h3>Trois jours, deux livres,<br/>une <em>piscine</em>.</h3>
        <p class="excerpt" data-en="A reasoned defence of staying put, what a long, slow weekend really repairs.">Petite défense raisonnée du « ne rien faire », ce que répare vraiment un week-end immobile.</p>
      </article>

    </div>
  </div>
</section>

<!-- ============ NEWSLETTER ============ -->
<section class="newsletter">
  <div class="nl-inner">
    <div class="kicker dark" style="display: inline-flex; margin-bottom: 24px;"><span class="dot" style="background: var(--sage-200);"></span><span data-en="The letter">La lettre</span></div>
    <h2 data-en="The journal,/once a season, in your inbox.">Le journal, une fois<br/>par saison, dans <em>votre</em> boîte.</h2>
    <p class="body-lg" style="color: rgba(var(--linen-50-rgb), 0.7); max-width: 44ch; margin: 16px auto 0;" data-en="Four letters a year. The articles, the small news of the house, the things we'd whisper at breakfast.">Quatre lettres par an. Les articles, les petites nouvelles de la maison, ce qu'on glisserait au petit-déjeuner.</p>
    <form class="nl-form" onsubmit="event.preventDefault(); this.querySelector('button').textContent='Merci ·';">
      <input type="email" placeholder="votre@adresse.fr" required />
      <button type="submit" class="btn" style="background: var(--linen-50); color: var(--ink-900); border-color: var(--linen-50);" data-en="Subscribe →">S'inscrire →</button>
    </form>
  </div>
</section>

<!-- ============ FOOTER ============ -->
