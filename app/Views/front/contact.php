<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Contact (formulaire 2 onglets : chambres / villa).
 * Portée depuis le proto Claude design (contact.html).
 * Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  /* Layout: form on left, contact info on right */
  .ct-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
    gap: clamp(40px, 5vw, 96px);
    align-items: start;
  }
  @media (max-width: 960px) { .ct-layout { grid-template-columns: 1fr; } }

  /* Mode tabs */
  .ct-tabs {
    display: grid; grid-template-columns: 1fr 1fr;
    border: var(--hairline);
    margin-bottom: 36px;
  }
  .ct-tabs button {
    background: transparent; border: 0; cursor: pointer;
    padding: 20px 24px;
    text-align: left;
    border-right: var(--hairline);
    font: inherit; color: inherit;
    transition: background .25s ease, color .25s ease;
    display: flex; flex-direction: column; gap: 6px;
  }
  .ct-tabs button:last-child { border-right: 0; }
  .ct-tabs .t-when {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.16em;
    color: var(--stone-500); text-transform: uppercase;
  }
  .ct-tabs .t-name {
    font-family: var(--font-display); font-size: clamp(22px, 1.8vw, 26px); color: var(--ink-900); letter-spacing: -0.01em;
  }
  .ct-tabs .t-name em { font-style: italic; color: var(--sage-700); }
  .ct-tabs button[aria-pressed="true"] { background: var(--ink-900); }
  .ct-tabs button[aria-pressed="true"] .t-when { color: rgba(251,247,238,0.7); }
  .ct-tabs button[aria-pressed="true"] .t-name { color: var(--linen-50); }
  .ct-tabs button[aria-pressed="true"] .t-name em { color: var(--sage-200); }

  /* ----- Bedding heading (sub-titre fort, remplace l'ancien <label> discret) ----- */
  .field-bedding { margin-top: 8px; }
  .bedding-heading { display: block; margin-bottom: 10px; }
  .bedding-kicker {
    display: block;
    font-family: var(--font-mono);
    font-size: 11px; letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--terra-500);
    margin-bottom: 10px;
  }
  .bedding-title {
    display: block;
    font-family: var(--font-display);
    font-style: italic;
    font-weight: 400;
    font-size: clamp(22px, 1.8vw, 28px);
    line-height: 1.2;
    color: var(--ink-900);
    letter-spacing: -0.005em;
  }
  .bedding-help {
    margin: 8px 0 22px;
    font-size: 14.5px;
    line-height: 1.55;
    color: var(--stone-600);
    max-width: 56ch;
  }

  /* ----- Room picker (B&B) : liste verticale avec radio + icône SVG ----- */
  .room-picker {
    display: flex;
    flex-direction: column;
    margin-top: 6px;
    border-top: 1px solid color-mix(in oklab, var(--ink-900) 10%, transparent);
  }
  .room-picker label {
    position: relative;
    display: grid;
    /* indicator | icone | texte */
    grid-template-columns: 14px clamp(56px, 6vw, 72px) 1fr;
    grid-auto-rows: auto;
    align-items: center;
    column-gap: clamp(14px, 1.6vw, 22px);
    row-gap: 2px;
    padding: clamp(14px, 1.8vw, 20px) clamp(8px, 1.4vw, 18px);
    border-bottom: 1px solid color-mix(in oklab, var(--ink-900) 10%, transparent);
    border-left: 3px solid transparent;
    cursor: pointer;
    transition: background .2s ease-out, border-left-color .2s ease-out, padding-left .2s ease-out;
    background: transparent;
    color: var(--stone-500);   /* base couleur de l'icône via currentColor */
  }
  .room-picker label:hover {
    background: color-mix(in oklab, var(--ink-900) 3%, transparent);
    padding-left: clamp(12px, 1.6vw, 20px);
  }
  .room-picker label::before {
    content: "";
    grid-column: 1;
    grid-row: 1 / span 3;
    width: 14px; height: 14px;
    border-radius: 50%;
    border: 1.5px solid var(--stone-400);
    background: transparent;
    box-sizing: border-box;
    align-self: center;
    transition: border-color .2s ease-out, background .2s ease-out, box-shadow .2s ease-out;
  }
  .room-picker .rp-icon {
    grid-column: 2;
    grid-row: 1 / span 3;
    align-self: center;
    width: 100%;
    height: auto;
    color: var(--stone-500);
    transition: color .2s ease-out, opacity .2s ease-out;
    opacity: 0.85;
  }
  .room-picker label .rn  { grid-column: 3; grid-row: 1;
    font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.16em;
    text-transform: uppercase; color: var(--stone-500); }
  .room-picker label .rn strong {
    font-weight: 500; color: var(--terra-500); letter-spacing: 0.18em;
  }
  .room-picker label .nm  { grid-column: 3; grid-row: 2;
    font-family: var(--font-display); font-style: italic;
    font-size: clamp(17px, 1.3vw, 20px); line-height: 1.2;
    color: var(--ink-900); letter-spacing: -0.005em; margin-top: 3px; }
  .room-picker label .det { grid-column: 3; grid-row: 3;
    font-size: 13px; line-height: 1.5; color: var(--stone-600); margin-top: 3px; }
  .room-picker input { position: absolute; opacity: 0; pointer-events: none; }

  /* Sélection : indicator terra + bg subtil + border-left terra + icône terra */
  .room-picker label:has(input:checked) {
    background: color-mix(in oklab, var(--terra-500) 6%, transparent);
    border-left-color: var(--terra-500);
    padding-left: clamp(12px, 1.6vw, 20px);
  }
  .room-picker label:has(input:checked)::before {
    background: var(--terra-500);
    border-color: var(--terra-500);
    box-shadow: inset 0 0 0 3px var(--linen-50);
  }
  .room-picker label:has(input:checked) .rp-icon {
    color: var(--terra-500);
    opacity: 1;
  }
  .room-picker label:has(input:checked) .rn { color: var(--terra-500); }
  .room-picker label:has(input:checked) .nm { color: var(--ink-900); }

  /* Focus clavier accessible */
  .room-picker label:focus-within { outline: 2px solid var(--terra-500); outline-offset: 2px; }

  /* Sur petit écran : on cache l'icône pour ne pas étouffer le texte */
  @media (max-width: 520px) {
    .room-picker label { grid-template-columns: 14px 1fr; }
    .room-picker .rp-icon { display: none; }
    .room-picker label .rn,
    .room-picker label .nm,
    .room-picker label .det { grid-column: 2; }
  }

  .panel { display: none; }
  .panel.active { display: block; }

  /* Contact card on the right */
  .ct-info {
    background: var(--ink-900);
    color: var(--linen-100);
    padding: clamp(28px, 3.6vw, 48px);
    display: flex; flex-direction: column; gap: 32px;
    position: sticky; top: 96px;
  }
  .ct-info .kicker {
    align-self: flex-start;
  }
  .ct-info h2 {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(28px, 2.8vw, 40px); line-height: 1.05; letter-spacing: -0.015em;
    color: var(--linen-50); margin: 0;
  }
  .ct-info h2 em { font-style: italic; color: var(--sage-200); }
  .ct-info .body-lg { color: rgba(251,247,238,0.78); margin: 0; }
  .ct-info .row {
    border-top: 1px solid rgba(251,247,238,0.16);
    padding-top: 18px;
  }
  .ct-info .row .lbl {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em;
    color: rgba(251,247,238,0.55); text-transform: uppercase;
    margin-bottom: 8px;
  }
  .ct-info .row a, .ct-info .row .v {
    font-family: var(--font-display); font-size: clamp(22px, 1.8vw, 26px); color: var(--linen-50); letter-spacing: -0.005em;
    line-height: 1.25; display: block;
  }
  .ct-info .row a:hover { color: var(--sage-200); }
  .ct-info .row .sub { font-family: var(--font-sans); font-size: 13px; color: rgba(251,247,238,0.55); margin-top: 6px; }
  @media (max-width: 960px) { .ct-info { position: static; } }

  /* Submit */
  .submit-bar {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 36px; padding-top: 24px;
    border-top: var(--hairline);
    gap: 24px; flex-wrap: wrap;
  }
  .submit-bar .note {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.06em;
    color: var(--stone-500);
  }

  /* Success */
  .success {
    display: none;
    padding: clamp(28px, 4vw, 44px); border: var(--hairline-strong);
    background: color-mix(in oklab, var(--sage-200) 40%, var(--linen-50));
    margin-top: 36px;
  }
  .success.show { display: block; }
</style>


<!-- ============ MASTHEAD (BDD vp_sections or fallback HTML) ============ -->
<?php
$_v8HeroContact = null;
foreach (BlockService::getSections('contact', $lang) as $_s) {
    if ((int)$_s['position'] === 1 && $_s['block_type'] === 'hero') {
        $_v8HeroContact = BlockService::renderBlock($_s);
        break;
    }
}
?>
<?php if ($_v8HeroContact): ?>
<?= $_v8HeroContact ?>
<?php else: ?>
<section class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="page-hero-issue">
        <span data-en="06 · Contact">06 · Contact</span>
        <span data-en="Reply within the day">Réponse dans la journée</span>
      </div>
      <h1>Une lettre,<br/>un <em>appel</em>.</h1>
    </div>
    <p class="lede" data-en="No booking engine, no automatic confirmation. Tell us what you'd like, we check the calendar, we write back — by hand, within the day.">Pas de moteur de réservation, pas de confirmation automatique. Dites-nous ce que vous souhaitez, on vérifie le calendrier, on vous répond — à la main, dans la journée.</p>
  </div>
</section>
<?php endif; ?>

<!-- ============ FORM + INFO ============ -->
<section class="section">
  <div class="container-wide">
    <div class="ct-layout">

      <!-- LEFT — FORM -->
      <main id="form">
        <div class="section-label" style="margin-bottom: 16px;">
          <span class="numeral">— 01 / <span data-en="Tell us">Dites-nous</span></span>
        </div>
        <h2 class="h-xl" style="margin: 0 0 32px; max-width: 18ch;" data-en="What kind of stay are you thinking of?">Quel genre de séjour<br/>imaginez-vous ?</h2>

        <!-- Mode tabs -->
        <div class="ct-tabs" role="tablist">
          <button role="tab" data-tab="bnb" aria-pressed="true">
            <span class="t-when" data-en="Sept → June">Sept → Juin</span>
            <span class="t-name" data-en="A room or two"><em>Chambres d'hôtes</em></span>
          </button>
          <button role="tab" data-tab="villa" aria-pressed="false">
            <span class="t-when" data-en="July → August">Juil → Août</span>
            <span class="t-name" data-en="The whole villa"><em>Villa entière</em></span>
          </button>
        </div>

        <form id="contactForm" novalidate>
          <!-- B&B Panel -->
          <div class="panel active" data-panel="bnb">
            <!-- Aperçu disponibilités (3 prochains mois) -->
            <div class="cal-preview" style="margin-bottom: 32px;">
              <?php
                $cal_propriete = 'VP-BB';
                $cal_variant   = 'bnb';
                include __DIR__ . '/_partials/calendar_focus.php';
              ?>
              <div class="legend">
                <span class="legend-key"><span class="legend-sw open"></span> Disponible</span>
                <span class="legend-key"><span class="legend-sw booked"></span> Réservé</span>
              </div>
            </div>

            <div class="form-grid">
              <div class="field">
                <label data-en="Arrival">Arrivée</label>
                <input type="date" name="arrival_bnb" />
              </div>
              <div class="field">
                <label data-en="Departure">Départ</label>
                <input type="date" name="departure_bnb" />
              </div>
              <div class="field full field-bedding">
                <div class="bedding-heading">
                  <span class="bedding-kicker" data-en="Your bedding">Votre couchage</span>
                  <span class="bedding-title" data-en="How would you like the rooms made up?">Configuration du couchage</span>
                </div>
                <p class="bedding-help" data-en="The two communicating bedrooms form a single suite, rented as one for 1 to 5 guests.">Les deux chambres communicantes forment une seule suite, louée d'un seul tenant pour 1 à 5 personnes.</p>
                <!--
                  Échelle stricte pour les icônes :
                    - 1 lit simple = 9 unités de large × 20 unités de long
                    - 1 lit double = 20 unités de large × 20 unités de long
                    - 1 chambre   = 32 × 32 unités (vue de dessus carrée, padding 6 sur chaque côté)
                    - Gap entre 2 chambres = 6 unités
                  ViewBox commun : 70 × 36.
                  Position chambre simple : (19, 2) → centrée horizontalement.
                  Position 2 chambres : (2, 2) et (36, 2).
                -->
                <div class="room-picker">
                  <label>
                    <input type="radio" name="config" value="lit-double" checked />
                    <svg class="rp-icon" viewBox="0 0 70 36" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true">
                      <!-- 1 chambre avec 1 lit double centré -->
                      <rect x="19" y="2" width="32" height="32" rx="1.5"/>
                      <rect x="25" y="8" width="20" height="20" rx="1.5"/>
                      <line x1="25" y1="13" x2="45" y2="13"/>
                    </svg>
                    <span class="rn" data-en="As a couple">À deux</span>
                    <span class="nm" data-en="Double bed">Lit double</span>
                    <span class="det" data-en="Chambre Verte prepared, Bleue stays as a lounge">Chambre Verte préparée, Bleue en salon</span>
                  </label>
                  <label>
                    <input type="radio" name="config" value="lits-simples" />
                    <svg class="rp-icon" viewBox="0 0 70 36" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true">
                      <!-- 1 chambre avec 2 lits simples (9 chacun + gap 2 = 20 total = 1 lit double équivalent) -->
                      <rect x="19" y="2" width="32" height="32" rx="1.5"/>
                      <rect x="25" y="8" width="9" height="20" rx="1"/>
                      <rect x="36" y="8" width="9" height="20" rx="1"/>
                      <line x1="25" y1="13" x2="34" y2="13"/>
                      <line x1="36" y1="13" x2="45" y2="13"/>
                    </svg>
                    <span class="rn" data-en="As a couple">À deux</span>
                    <span class="nm" data-en="Two single beds">Deux lits simples</span>
                    <span class="det" data-en="Chambre Bleue prepared, Verte stays as a lounge">Chambre Bleue préparée, Verte en salon</span>
                  </label>
                  <label>
                    <input type="radio" name="config" value="chacun" />
                    <svg class="rp-icon" viewBox="0 0 70 36" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true">
                      <!-- 2 chambres séparées (32+6+32=70), chacune avec 1 lit double -->
                      <rect x="2" y="2" width="32" height="32" rx="1.5"/>
                      <rect x="36" y="2" width="32" height="32" rx="1.5"/>
                      <rect x="8" y="8" width="20" height="20" rx="1.5"/>
                      <rect x="42" y="8" width="20" height="20" rx="1.5"/>
                      <line x1="8" y1="13" x2="28" y2="13"/>
                      <line x1="42" y1="13" x2="62" y2="13"/>
                    </svg>
                    <span class="rn" data-en="As a couple · Paid option">À deux &middot; <strong>option payante</strong></span>
                    <span class="nm" data-en="A room each">Une chambre chacun</span>
                    <span class="det" data-en="Both rooms prepared">Les deux chambres préparées</span>
                  </label>
                  <label>
                    <input type="radio" name="config" value="famille" />
                    <svg class="rp-icon" viewBox="0 0 70 36" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true">
                      <!-- 2 chambres : 1 lit double à gauche, 2 lits simples à droite (échelle conservée) -->
                      <rect x="2" y="2" width="32" height="32" rx="1.5"/>
                      <rect x="36" y="2" width="32" height="32" rx="1.5"/>
                      <rect x="8" y="8" width="20" height="20" rx="1.5"/>
                      <line x1="8" y1="13" x2="28" y2="13"/>
                      <rect x="42" y="8" width="9" height="20" rx="1"/>
                      <rect x="53" y="8" width="9" height="20" rx="1"/>
                      <line x1="42" y1="13" x2="51" y2="13"/>
                      <line x1="53" y1="13" x2="62" y2="13"/>
                    </svg>
                    <span class="rn" data-en="3 to 5 guests">À 3, 4 ou 5</span>
                    <span class="nm" data-en="Family / small group">Famille / groupe</span>
                    <span class="det" data-en="Both rooms prepared, sofa bed if needed">Les deux chambres, clic-clac si besoin</span>
                  </label>
                </div>
              </div>
              <div class="field">
                <label data-en="Adults">Adultes</label>
                <input type="number" name="adults" min="1" max="5" value="2" />
              </div>
              <div class="field">
                <label data-en="Children">Enfants</label>
                <input type="number" name="children" min="0" max="3" value="0" />
              </div>
            </div>
          </div>

          <!-- Villa Panel -->
          <div class="panel" data-panel="villa">
            <!-- Aperçu disponibilités (3 prochains mois) -->
            <div class="cal-preview" style="margin-bottom: 32px;">
              <?php
                $cal_propriete = 'VP-ETE';
                $cal_variant   = 'villa';
                include __DIR__ . '/_partials/calendar_focus.php';
              ?>
              <div class="legend">
                <span class="legend-key"><span class="legend-sw villa"></span> Disponible</span>
                <span class="legend-key"><span class="legend-sw booked"></span> Réservé</span>
              </div>
            </div>

            <div class="form-grid">
              <div class="field">
                <label data-en="Arrival (Saturday)">Arrivée (samedi)</label>
                <input type="date" name="arrival_villa" />
              </div>
              <div class="field">
                <label data-en="Departure (Saturday)">Départ (samedi)</label>
                <input type="date" name="departure_villa" />
              </div>
              <div class="field full">
                <label data-en="Total guests (max. 10)">Personnes au total (max. 10)</label>
                <input type="number" name="villa_guests" min="2" max="10" value="8" />
              </div>
              <div class="field full">
                <label data-en="The occasion?">L'occasion ?</label>
                <input type="text" name="occasion" data-en-attr="placeholder|Family reunion, birthday, just a long week…" placeholder="Réunion de famille, anniversaire, juste une longue semaine…" />
              </div>
            </div>
          </div>

          <!-- Shared fields -->
          <div class="form-grid" style="margin-top: 28px;">
            <div class="field">
              <label data-en="Your name">Votre nom</label>
              <input type="text" name="name" required />
            </div>
            <div class="field">
              <label data-en="Email">Courriel</label>
              <input type="email" name="email" required />
            </div>
            <div class="field">
              <label data-en="Phone (optional)">Téléphone (facultatif)</label>
              <input type="tel" name="phone" />
            </div>
            <div class="field">
              <label data-en="How did you find us?">Comment nous avez-vous trouvés ?</label>
              <select name="source">
                <option data-en="A friend">Un ami</option>
                <option data-en="Instagram">Instagram</option>
                <option data-en="A magazine">Un magazine</option>
                <option data-en="Search">Recherche</option>
                <option data-en="Came back">Revenu(e)</option>
              </select>
            </div>
            <div class="field full">
              <label data-en="Anything you'd like us to know?">Quelque chose qu'on devrait savoir ?</label>
              <textarea name="note" data-en-attr="placeholder|Dietary preferences, a celebration to plan, a transfer to organise…" placeholder="Régimes particuliers, une occasion à préparer, un transfert à organiser…"></textarea>
            </div>
          </div>

          <div class="submit-bar">
            <div class="note" data-en="No payment at this stage — only an enquiry.">Aucun paiement à cette étape — uniquement une demande.</div>
            <button type="submit" class="btn" data-en="Send →">Envoyer →</button>
          </div>

          <div class="success" id="success">
            <div class="numeral" style="margin-bottom: 14px;" data-en="Got it.">Bien reçu.</div>
            <h3 class="h-lg" style="margin: 0 0 14px;" data-en="A reply is on its way.">Notre réponse <em>est en route</em>.</h3>
            <p class="body-lg" style="margin: 0;" data-en="We'll write you back within the day from contact@villaplaisance.fr — please check your spam folder, just in case.">Nous vous répondons dans la journée depuis contact@villaplaisance.fr — pensez aux indésirables, au cas où.</p>
          </div>
        </form>
      </main>

      <!-- RIGHT — INFO -->
      <aside class="ct-info">
        <div class="kicker dark" style="background: transparent; border: 1px solid rgba(251,247,238,0.25); color: var(--linen-200);">
          <span class="dot" style="background: var(--sage-200);"></span>
          <span data-en="Contact">Coordonnées</span>
        </div>

        <h2 data-en="Three ways/to reach us.">Trois façons<br/>de <em>nous joindre</em>.</h2>
        <p class="body-lg" data-en="Email is best — we keep the conversation written so nothing gets lost.">L'écrit, idéalement — la conversation reste écrite, rien ne se perd.</p>

        <div class="row">
          <div class="lbl" data-en="By email">Par courriel</div>
          <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
          <div class="sub" data-en="The fastest way to a real answer.">Le chemin le plus court vers une vraie réponse.</div>
        </div>

        <div class="row">
          <div class="lbl" data-en="In person">Sur place</div>
          <span class="v">Bédarrides<br/>Vaucluse · 84</span>
          <div class="sub" data-en="The full address is sent with your booking confirmation.">L'adresse complète est transmise avec la confirmation de séjour.</div>
        </div>

        <div class="row">
          <div class="lbl" data-en="Reply time">Délai de réponse</div>
          <span class="v" data-en="Within the day"><em>Dans la journée</em></span>
          <div class="sub" data-en="Every enquiry is read and answered by hand, not by a bot.">Chaque demande est lue et signée à la main, jamais par un robot.</div>
        </div>

        <div class="row">
          <div class="lbl" data-en="And to follow along">Et pour suivre</div>
          <a href="#" data-en="Instagram · @villaplaisance">Instagram · @villaplaisance</a>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- ============ TABS B&B / VILLA — toggle binding ============ -->
<script>
(function () {
  var tablist = document.querySelector('.ct-tabs[role="tablist"]');
  if (!tablist) return;
  var tabs = Array.prototype.slice.call(tablist.querySelectorAll('button[role="tab"]'));
  var panels = Array.prototype.slice.call(document.querySelectorAll('.panel[data-panel]'));
  if (!tabs.length || !panels.length) return;

  function activate(target, focus) {
    tabs.forEach(function (t) {
      var isActive = (t === target);
      t.setAttribute('aria-pressed', isActive ? 'true' : 'false');
      t.setAttribute('tabindex', isActive ? '0' : '-1');
    });
    var key = target.getAttribute('data-tab');
    panels.forEach(function (p) {
      p.classList.toggle('active', p.getAttribute('data-panel') === key);
    });
    if (focus) target.focus();
  }

  // Init : assure la cohérence si jamais le HTML n'a pas le bon état actif
  var preActive = tabs.filter(function (t) { return t.getAttribute('aria-pressed') === 'true'; })[0] || tabs[0];
  activate(preActive, false);

  tabs.forEach(function (tab, idx) {
    tab.addEventListener('click', function () { activate(tab, false); });
    tab.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
        e.preventDefault();
        activate(tabs[(idx + 1) % tabs.length], true);
      } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
        e.preventDefault();
        activate(tabs[(idx - 1 + tabs.length) % tabs.length], true);
      } else if (e.key === 'Home') {
        e.preventDefault();
        activate(tabs[0], true);
      } else if (e.key === 'End') {
        e.preventDefault();
        activate(tabs[tabs.length - 1], true);
      }
    });
  });

  // Deep-link via hash : #bnb ou #villa
  function applyHash() {
    var h = (location.hash || '').replace('#', '');
    if (!h) return;
    var match = tabs.filter(function (t) { return t.getAttribute('data-tab') === h; })[0];
    if (match) activate(match, false);
  }
  applyHash();
  window.addEventListener('hashchange', applyHash);
})();
</script>

<!-- ============ FOOTER ============ -->
