<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Que faire — directory (itinéraires).
 * Portée depuis le proto Claude design (journal-que-faire.html).
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  /* Masthead */
  .qf-masthead {
    background: var(--linen-50);
    border-bottom: var(--hairline);
  }
  .qf-masthead-inner {
    padding: clamp(64px, 8vw, 120px) var(--gutter) clamp(40px, 5vw, 64px);
    max-width: var(--container-wide);
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(32px, 5vw, 80px);
    align-items: end;
  }
  .qf-issue {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em;
    color: var(--stone-500); text-transform: uppercase;
    display: flex; justify-content: space-between; align-items: baseline;
    border-bottom: var(--hairline);
    padding-bottom: 14px; margin-bottom: 28px;
  }
  .qf-title {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(56px, 9vw, 140px); line-height: 0.92; letter-spacing: -0.025em;
    color: var(--ink-900);
  }
  .qf-title em { font-style: italic; color: var(--sage-700); }
  .qf-lede {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(20px, 1.8vw, 26px); line-height: 1.4; letter-spacing: -0.005em;
    color: var(--ink-700); max-width: 42ch; margin: 0;
  }
  @media (max-width: 720px) { .qf-masthead-inner { grid-template-columns: 1fr; } }

  /* Sticky filter */
  .qf-filter {
    position: sticky;
    top: 60px;
    z-index: 30;
    background: color-mix(in oklab, var(--linen-50) 94%, transparent);
    backdrop-filter: saturate(140%) blur(10px);
    -webkit-backdrop-filter: saturate(140%) blur(10px);
    border-bottom: var(--hairline);
  }
  .qf-filter-inner {
    max-width: var(--container-wide); margin: 0 auto;
    padding: 14px var(--gutter);
    display: flex; gap: 6px; align-items: center;
    overflow-x: auto; scrollbar-width: none;
  }
  .qf-filter-inner::-webkit-scrollbar { display: none; }
  .qf-filter button {
    flex-shrink: 0;
    background: transparent; border: 1px solid color-mix(in oklab, var(--ink-900) 15%, transparent);
    color: var(--stone-600);
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 10px 16px; cursor: pointer;
    transition: background .2s, color .2s, border-color .2s;
  }
  .qf-filter button:hover { color: var(--ink-900); border-color: var(--ink-900); }
  .qf-filter button[aria-pressed="true"] { background: var(--ink-900); color: var(--linen-50); border-color: var(--ink-900); }
  .qf-filter .count {
    margin-left: auto; flex-shrink: 0;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.12em;
    color: var(--stone-500); padding-left: 16px;
    border-left: var(--hairline);
  }

  /* Category badges */
  .badge {
    display: inline-flex; align-items: center; gap: 6px;
    font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 5px 9px;
    background: var(--linen-100);
    color: var(--ink-900);
    border: 1px solid transparent;
  }
  .badge.enfants { background: color-mix(in oklab, var(--sage-200) 55%, transparent); }
  .badge.commerces { background: color-mix(in oklab, var(--terra-500) 14%, var(--linen-50)); color: var(--terra-600); }
  .badge.sites { background: var(--ink-900); color: var(--linen-50); }
  .badge.tables { background: var(--linen-200); }

  /* Place cards */
  .qf-grid {
    display: grid; grid-template-columns: repeat(12, 1fr);
    gap: clamp(32px, 4vw, 56px) clamp(24px, 2.4vw, 36px);
  }
  .qf-card {
    grid-column: span 4;
    display: flex; flex-direction: column; gap: 14px;
    background: var(--linen-50);
    transition: transform .3s cubic-bezier(.2,.6,.2,1);
  }
  .qf-card.hidden { display: none; }
  .qf-card:hover { transform: translateY(-2px); }
  .qf-card .img {
    aspect-ratio: 4/3; background-size: cover; background-position: center;
    background-color: var(--linen-200);
  }
  .qf-card .meta { display: flex; gap: 10px; align-items: baseline; flex-wrap: wrap; }
  .qf-card h3 {
    margin: 0;
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(22px, 1.8vw, 28px); line-height: 1.1; letter-spacing: -0.015em;
    color: var(--ink-900);
  }
  .qf-card h3 em { font-style: italic; }
  .qf-card .desc {
    font-size: 14.5px; line-height: 1.55; color: var(--stone-600); margin: 0;
  }
  .qf-card .specs {
    display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px;
  }
  .qf-card .spec {
    font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.08em;
    color: var(--stone-600); text-transform: uppercase;
    padding: 4px 8px; border: var(--hairline);
  }
  .qf-card .spec.distance { color: var(--ink-900); border-color: var(--ink-900); }
  .qf-card .footer-row {
    margin-top: auto;
    padding-top: 12px;
    border-top: var(--hairline);
    display: flex; justify-content: space-between; align-items: baseline;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.08em;
    color: var(--stone-600); text-transform: uppercase;
  }
  .qf-card .footer-row a { color: var(--ink-900); border-bottom: 1px solid currentColor; padding-bottom: 1px; }

  @media (max-width: 960px) { .qf-card { grid-column: span 6; } }
  @media (max-width: 560px) { .qf-card { grid-column: span 12; } }

  /* Big featured card */
  .qf-featured {
    grid-column: span 12;
    display: grid; grid-template-columns: 1.2fr 1fr; gap: clamp(24px, 3vw, 48px);
    background: var(--linen-100);
    padding: clamp(24px, 3vw, 40px);
    align-items: center;
  }
  .qf-featured .img { aspect-ratio: 4/3; background-size: cover; background-position: center; }
  .qf-featured h3 {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(34px, 3.6vw, 52px); line-height: 1.0; letter-spacing: -0.02em;
    color: var(--ink-900); margin: 0 0 14px;
  }
  .qf-featured h3 em { font-style: italic; }
  .qf-featured .meta { display: flex; gap: 12px; align-items: baseline; flex-wrap: wrap; margin-bottom: 16px; }
  @media (max-width: 720px) { .qf-featured { grid-template-columns: 1fr; padding: 24px; } }

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


<!-- ============ MASTHEAD ============ -->
<section class="qf-masthead">
  <div class="qf-masthead-inner">
    <div>
      <div class="qf-issue">
        <span data-en="Journal · 05 / What to do nearby">Journal · 05 / Que faire sur place</span>
        <span data-en="The house's pick">La sélection de la maison</span>
      </div>
      <h1 class="qf-title">Sur place,<br/>tout est <em>là</em>.</h1>
    </div>
    <p class="qf-lede" data-en="Sites to visit, tables and restaurants, shops, things to do with children — what we'd point to ourselves over breakfast.">Sites à visiter, tables et restaurants, commerces, activités avec les enfants — ce qu'on vous indiquerait nous-mêmes au petit-déjeuner.</p>
  </div>

  <div class="pl-banner" style="margin-bottom: 28px;">
    <span class="dot"></span>
    <div class="txt">
      <span class="lbl" data-en="DESIGN MOCKUP">ÉBAUCHE GRAPHIQUE</span>
      Entrées d'exemple — l'organisation et les filtres sont définitifs, le contenu sera remplacé par vos vrais choix.
    </div>
  </div>
</section>

<!-- ============ FILTER ============ -->
<div class="qf-filter">
  <div class="qf-filter-inner" role="tablist">
    <button data-cat="all" aria-pressed="true">Tous</button>
    <button data-cat="enfants" aria-pressed="false">Avec des enfants</button>
    <button data-cat="commerces" aria-pressed="false">Commerces</button>
    <button data-cat="sites" aria-pressed="false">Sites à visiter</button>
    <button data-cat="tables" aria-pressed="false">Restaurants &amp; tables</button>
    <span class="count" id="filter-count">10 adresses</span>
  </div>
</div>

<!-- ============ GRID ============ -->
<section class="section">
  <div class="container-wide">
    <div class="qf-grid" id="grid">

      <!-- FEATURED -->
      <article class="qf-featured qf-card" data-cat="sites">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-vp-itini-elisa-03-museeharibo.webp')"></div>
        <div>
          <div class="meta">
            <span class="badge sites" data-en="Sites to visit">Site à visiter</span>
            <span style="font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.1em; color: var(--stone-500);" data-en="Editor's pick">CHOIX DE LA MAISON</span>
          </div>
          <h3>Palais des Papes,<br/><em>Avignon</em>.</h3>
          <p class="desc" data-en="The largest Gothic palace in Europe. Go in the morning, take the audio guide, allow two hours. Lunch in a quiet square afterwards.">Le plus grand palais gothique d'Europe. Y aller le matin, prendre l'audioguide, compter deux heures. Déjeuner ensuite dans une placette tranquille.</p>
          <div class="specs">
            <span class="spec distance" data-en="15 min by car">15 min · voiture</span>
            <span class="spec" data-en="Open daily">Tous les jours</span>
            <span class="spec" data-en="Admission charge">Entrée payante</span>
          </div>
          <div class="footer-row" style="margin-top: 18px;">
            <span data-en="Avignon · Vaucluse">Avignon · Vaucluse</span>
            <a href="#" data-en="More info →">Plus d'infos →</a>
          </div>
        </div>
      </article>

      <!-- CARDS -->

      <article class="qf-card" data-cat="sites">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-vp-itini-elisa-02-pont-du-gard.webp')"></div>
        <div class="meta">
          <span class="badge sites" data-en="Sites to visit">Site à visiter</span>
        </div>
        <h3><em>Théâtre antique</em><br/>d'Orange.</h3>
        <p class="desc" data-en="A Roman theatre still in use — operas in summer under the open sky.">Un théâtre romain encore en activité — opéras d'été à ciel ouvert.</p>
        <div class="specs">
          <span class="spec distance">18 min · <span data-en="car">voiture</span></span>
          <span class="spec" data-en="Open daily">Tous les jours</span>
        </div>
        <div class="footer-row">
          <span>Orange</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="sites">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-jardin-exterieur-12.webp')"></div>
        <div class="meta">
          <span class="badge sites" data-en="Sites to visit">Site à visiter</span>
        </div>
        <h3>Marché des antiquaires,<br/><em>Isle-sur-la-Sorgue</em>.</h3>
        <p class="desc" data-en="Sunday morning, antiques along the canal. Go before 10 am, before the heat.">Dimanche matin, antiquaires le long du canal. Y aller avant 10h, avant la chaleur.</p>
        <div class="specs">
          <span class="spec distance">30 min · <span data-en="car">voiture</span></span>
          <span class="spec" data-en="Sundays">Dimanche</span>
        </div>
        <div class="footer-row">
          <span data-en="L'Isle-sur-la-Sorgue">L'Isle-sur-la-Sorgue</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="sites">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-jardin-exterieur-01.webp')"></div>
        <div class="meta">
          <span class="badge sites" data-en="Sites to visit">Site à visiter</span>
        </div>
        <h3>Lavandes de <em>Sault</em>.</h3>
        <p class="desc" data-en="The lavender plateau. Best in the first three weeks of July. Take the small road in.">Le plateau des lavandes. Idéalement les trois premières semaines de juillet. Prendre la petite route.</p>
        <div class="specs">
          <span class="spec distance">45 min · <span data-en="car">voiture</span></span>
          <span class="spec" data-en="Early July">Début juillet</span>
        </div>
        <div class="footer-row">
          <span>Sault</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="tables">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-salon-salle-a-manger-04.webp')"></div>
        <div class="meta">
          <span class="badge tables" data-en="Tables &amp; restaurants">Table</span>
        </div>
        <h3>Une table à <em>Châteauneuf-du-Pape</em>.</h3>
        <p class="desc" data-en="A small Provençal table tucked into the old village. Lunch only on certain days — call ahead.">Une petite table provençale dans le vieux village. Déjeuner seulement certains jours — appeler avant.</p>
        <div class="specs">
          <span class="spec distance">8 min · <span data-en="car">voiture</span></span>
          <span class="spec" data-en="Reservation">Réservation</span>
        </div>
        <div class="footer-row">
          <span data-en="Châteauneuf-du-Pape">Châteauneuf-du-Pape</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="tables">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-petit-dejeuner-confitures-01.webp')"></div>
        <div class="meta">
          <span class="badge tables" data-en="Tables &amp; restaurants">Table</span>
        </div>
        <h3>Bistrot du village,<br/><em>Bédarrides</em>.</h3>
        <p class="desc" data-en="Three menus, a short wine list, a terrace under the plane trees. The locals' canteen.">Trois menus, une carte courte de vins, terrasse sous les platanes. La cantine des habitués.</p>
        <div class="specs">
          <span class="spec distance">5 min · <span data-en="walk">à pied</span></span>
          <span class="spec" data-en="Closed Wednesdays">Fermé mercredi</span>
        </div>
        <div class="footer-row">
          <span>Bédarrides</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="tables">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-vignes-provence-04.webp')"></div>
        <div class="meta">
          <span class="badge tables" data-en="Tables &amp; restaurants">Table</span>
        </div>
        <h3>Restaurant <em>sous le figuier</em>.</h3>
        <p class="desc" data-en="A summer-only table in a vineyard. Long lunches, short menu, very good wine.">Une table d'été dans les vignes. Longs déjeuners, petite carte, très bon vin.</p>
        <div class="specs">
          <span class="spec distance">12 min · <span data-en="car">voiture</span></span>
          <span class="spec" data-en="July → August">Juil → août</span>
        </div>
        <div class="footer-row">
          <span data-en="The Comtat plain">Plaine du Comtat</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="commerces">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-jardin-exterieur-15.webp')"></div>
        <div class="meta">
          <span class="badge commerces" data-en="Shops">Commerce</span>
        </div>
        <h3>Boulangerie<br/><em>du village</em>.</h3>
        <p class="desc" data-en="Bread from 7 am, pastries until they run out. Five minutes' walk.">Pain à partir de 7h, viennoiseries jusqu'à épuisement. Cinq minutes à pied.</p>
        <div class="specs">
          <span class="spec distance">5 min · <span data-en="walk">à pied</span></span>
          <span class="spec" data-en="Mon → Sat">Lun → sam</span>
        </div>
        <div class="footer-row">
          <span>Bédarrides</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="commerces">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-jardin-exterieur-05.webp')"></div>
        <div class="meta">
          <span class="badge commerces" data-en="Shops">Commerce</span>
        </div>
        <h3>Cave coopérative,<br/><em>route des vignes</em>.</h3>
        <p class="desc" data-en="A small co-op cellar where you can buy by the bottle, the box or the bag-in-box.">Une petite cave coopérative où on achète à la bouteille, au carton ou au bib.</p>
        <div class="specs">
          <span class="spec distance">10 min · <span data-en="car">voiture</span></span>
          <span class="spec" data-en="Tastings">Dégustations</span>
        </div>
        <div class="footer-row">
          <span data-en="On the way to Châteauneuf">Route de Châteauneuf</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="enfants">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-vignes-provence-01.webp')"></div>
        <div class="meta">
          <span class="badge enfants" data-en="With children">Avec des enfants</span>
        </div>
        <h3>Sentier des <em>oliviers</em>.</h3>
        <p class="desc" data-en="A 4-km looped path through olive groves. Doable with a 5-year-old, with two breaks for ice cream.">Une boucle de 4 km à travers les oliveraies. Faisable avec un enfant de cinq ans, deux pauses glace prévues.</p>
        <div class="specs">
          <span class="spec distance">2 min · <span data-en="walk">à pied</span></span>
          <span class="spec" data-en="All ages">Tous âges</span>
          <span class="spec" data-en="Free">Gratuit</span>
        </div>
        <div class="footer-row">
          <span>Bédarrides</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="enfants">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-piscine-privee-09.webp')"></div>
        <div class="meta">
          <span class="badge enfants" data-en="With children">Avec des enfants</span>
        </div>
        <h3>Baignade à la <em>Sorgue</em>.</h3>
        <p class="desc" data-en="A river beach, cold water, shallow on the side. Picnic spots in the shade of plane trees.">Une plage de rivière, eau fraîche, peu profonde sur les côtés. Coins pique-nique à l'ombre des platanes.</p>
        <div class="specs">
          <span class="spec distance">20 min · <span data-en="car">voiture</span></span>
          <span class="spec" data-en="June → September">Juin → sept.</span>
          <span class="spec" data-en="Free">Gratuit</span>
        </div>
        <div class="footer-row">
          <span>Fontaine-de-Vaucluse</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

      <article class="qf-card" data-cat="enfants">
        <div class="img" style="background-image: url('/uploads/villa-plaisance-chambre-verte-04.webp')"></div>
        <div class="meta">
          <span class="badge enfants" data-en="With children">Avec des enfants</span>
        </div>
        <h3>Ferme pédagogique<br/><em>aux ânes</em>.</h3>
        <p class="desc" data-en="A small working farm — donkeys, goats, a vegetable garden you can poke around in.">Une petite ferme en activité — ânes, chèvres, un potager où l'on peut chiner.</p>
        <div class="specs">
          <span class="spec distance">15 min · <span data-en="car">voiture</span></span>
          <span class="spec" data-en="Reservation">Sur réservation</span>
        </div>
        <div class="footer-row">
          <span data-en="The Comtat plain">Plaine du Comtat</span>
          <a href="#" data-en="More info →">Plus d'infos →</a>
        </div>
      </article>

    </div>
  </div>
</section>

<!-- ============ ASK US STRIP ============ -->
<section class="section surface-ink" style="background: var(--ink-900); color: var(--linen-100); padding: clamp(64px, 8vw, 112px) 0;">
  <div class="container-wide" style="text-align: center;">
    <div class="kicker dark" style="display: inline-flex; margin-bottom: 24px;"><span class="dot" style="background: var(--sage-200);"></span><span data-en="The shortcut">Le raccourci</span></div>
    <h2 class="h-xl" style="margin: 0 auto; max-width: 22ch; color: var(--linen-50);" data-en="The shortest way is/to ask us at breakfast.">Le plus court chemin<br/>reste de nous demander<br/><em>au petit-déjeuner</em>.</h2>
    <p class="body-lg" style="color: rgba(251,247,238,0.72); max-width: 50ch; margin: 24px auto 32px;" data-en="A list is a list. We know what's worth your morning, your evening, your detour. Ask us — that's the whole point of staying with people who live here.">Une liste est une liste. Nous savons ce qui vaut votre matinée, votre soirée, votre détour. Demandez — c'est tout l'intérêt de dormir chez ceux qui vivent ici.</p>
    <div style="display:flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
      <a class="btn" style="background: var(--linen-50); color: var(--ink-900); border-color: var(--linen-50);" href="<?= LangService::url('contact') ?>"><span data-en="Write to us">Nous écrire</span> →</a>
    </div>
  </div>
</section>

<!-- ============ FOOTER ============ -->
