<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Politique de confidentialité.
 * Portée du proto Claude design. Layout : front-proto.
 * @var string $lang  @var array $seo  @var array $jsonLd
 */ ?>
<style>
  .legal-page { padding-top: clamp(72px, 9vw, 120px); }
  .legal-page .page-hero { padding-top: 0; }
  .legal-body {
    max-width: 720px;
    font-family: var(--font-sans); font-size: 15.5px; line-height: 1.7; color: var(--ink-900);
  }
  .legal-body h2 {
    font-family: var(--font-display); font-weight: 500;
    font-size: clamp(22px, 2vw, 28px); line-height: 1.2; letter-spacing: -0.005em;
    color: var(--ink-900);
    margin: 48px 0 16px;
    padding-top: 28px; border-top: var(--hairline);
  }
  .legal-body h2:first-of-type { padding-top: 0; border-top: 0; margin-top: 0; }
  .legal-body h3 {
    font-family: var(--font-sans); font-weight: 600;
    font-size: 14px; letter-spacing: 0.04em; text-transform: uppercase;
    color: var(--stone-600); margin: 24px 0 8px;
  }
  .legal-body p { margin: 0 0 16px; color: var(--stone-700, var(--ink-900)); }
  .legal-body ul { margin: 0 0 16px; padding-left: 22px; }
  .legal-body li { margin: 0 0 6px; }
  .legal-body strong { color: var(--ink-900); font-weight: 600; }
  .legal-body em { font-style: italic; color: var(--stone-500); }
  .legal-body a { color: var(--sage-700); text-decoration: underline; text-underline-offset: 3px; }
  .legal-body a:hover { color: var(--ink-900); }
  .legal-body code {
    font-family: var(--font-mono); font-size: 13px;
    background: color-mix(in oklab, var(--ink-900) 6%, transparent);
    padding: 1px 6px;
  }
  .legal-table {
    width: 100%; border-collapse: collapse;
    margin: 8px 0 24px;
    font-size: 14px;
  }
  .legal-table th, .legal-table td {
    padding: 12px 14px;
    text-align: left;
    border-bottom: var(--hairline);
    vertical-align: top;
  }
  .legal-table th {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em;
    text-transform: uppercase; color: var(--stone-500);
    border-bottom: var(--hairline-strong);
    font-weight: 500;
  }
  .legal-table td:first-child { font-family: var(--font-mono); font-size: 13px; color: var(--ink-900); }
  .legal-crumbs {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em;
    color: var(--stone-500); text-transform: uppercase;
    margin: 0 0 24px;
  }
  .legal-crumbs a { color: var(--stone-500); }
  .legal-crumbs a:hover { color: var(--ink-900); }
  .legal-crumbs .sep { margin: 0 8px; opacity: 0.5; }
</style>

<section class="section legal-page">
  <div class="container-wide">

    <nav class="legal-crumbs" aria-label="Fil d'Ariane">
      <a href="<?= LangService::url('/') ?>" data-en="Home">Accueil</a>
      <span class="sep" aria-hidden="true">/</span>
      <span aria-current="page" data-en="Privacy policy">Politique de confidentialité</span>
    </nav>

    <?php
    $_v8HeroPriv = null;
    foreach (BlockService::getSections('politique-confidentialite', $lang) as $_s) {
        if ((int)$_s['position'] === 1 && $_s['block_type'] === 'hero') {
            $_v8HeroPriv = BlockService::renderBlock($_s);
            break;
        }
    }
    ?>
    <?php if ($_v8HeroPriv): ?>
    <?= $_v8HeroPriv ?>
    <?php else: ?>
    <div class="page-hero">
      <div class="page-hero-inner">
        <div>
          <div class="page-hero-issue">
            <span data-en="Legal · Privacy">Légal · Confidentialité</span>
            <span data-en="GDPR · Last update 2026-05">RGPD · Mise à jour 2026-05</span>
          </div>
          <h1 data-en="Privacy <em>policy</em>.">Politique de <em>confidentialité</em>.</h1>
        </div>
        <p class="lede" data-en="What data we collect, why, how long we keep it, and your rights under GDPR. No tracking unless you explicitly accept it.">Quelles données on collecte, pourquoi, combien de temps on les garde, et vos droits selon le RGPD. Aucun suivi tant que vous ne l'avez pas explicitement accepté.</p>
      </div>
    </div>
    <?php endif; ?>

    <div class="legal-body">

      <h2 data-en="1. Data controller">1. Responsable du traitement</h2>
      <p>
        <strong>Villa Plaisance</strong><br>
        <span data-en="Owner: Jorge Cañete">Propriétaire&nbsp;: Jorge Cañete</span><br>
        <span data-en="Address: Bédarrides, 84370 Vaucluse, France">Adresse&nbsp;: Bédarrides, 84370 Vaucluse, France</span><br>
        <span data-en="Email">Email</span>&nbsp;: <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
      </p>

      <h2 data-en="2. Personal data collected">2. Données personnelles collectées</h2>
      <h3 data-en="a) Contact form">a) Formulaire de contact</h3>
      <p data-en="When you use the contact form, we collect: your name, email address and message content. This data is used exclusively to reply to your enquiry.">
        Lorsque vous utilisez le formulaire de contact, nous collectons&nbsp;: votre nom, votre adresse email
        et le contenu de votre message. Ces données sont utilisées exclusivement pour répondre à votre demande.
      </p>

      <h3 data-en="b) Browsing data">b) Données de navigation</h3>
      <p data-en="We collect anonymous browsing data for statistical purposes: pages viewed, device type (mobile, tablet, desktop), browser language and referrer. This data cannot identify you personally. An anonymous identifier (cookie vp_vid) distinguishes unique visitors, with no link to your identity.">
        Nous collectons de manière anonyme des données de navigation à des fins statistiques&nbsp;:
        pages visitées, type d'appareil (mobile, tablette, ordinateur), langue du navigateur et provenance
        (referrer). Ces données ne permettent pas de vous identifier personnellement. Un identifiant
        anonyme (cookie <code>vp_vid</code>) est utilisé pour distinguer les visiteurs uniques, sans
        aucun lien avec votre identité.
      </p>

      <h2 data-en="3. Cookies used">3. Cookies utilisés</h2>
      <table class="legal-table">
        <thead>
          <tr>
            <th data-en="Cookie">Cookie</th>
            <th data-en="Purpose">Finalité</th>
            <th data-en="Duration">Durée</th>
            <th data-en="Type">Type</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>PHPSESSID</td>
            <td data-en="Technical session (form, navigation)">Session technique (formulaire, navigation)</td>
            <td data-en="Session duration">Durée de la session</td>
            <td data-en="Strictly necessary">Strictement nécessaire</td>
          </tr>
          <tr>
            <td>vp_consent</td>
            <td data-en="Remember your cookie choice">Mémoriser votre choix cookies</td>
            <td data-en="180 days">180 jours</td>
            <td data-en="Strictly necessary">Strictement nécessaire</td>
          </tr>
          <tr>
            <td>vp_vid</td>
            <td data-en="Anonymous audience statistics">Statistiques anonymes de fréquentation</td>
            <td data-en="365 days">365 jours</td>
            <td data-en="Audience measurement">Mesure d'audience</td>
          </tr>
          <tr>
            <td>_ga, _ga_*</td>
            <td data-en="Google Analytics 4 — audience measurement">Google Analytics 4 — mesure d'audience</td>
            <td data-en="Up to 13 months">Jusqu'à 13 mois</td>
            <td data-en="Audience measurement (consent)">Mesure d'audience (consentement)</td>
          </tr>
        </tbody>
      </table>

      <h3 data-en="Your choice regarding cookies">Votre choix concernant les cookies</h3>
      <p data-en="On your first visit, a banner lets you accept or refuse audience-measurement cookies (Google Analytics). If you refuse, only strictly necessary cookies are set. You can change your choice at any time by deleting the vp_consent cookie from your browser settings.">
        Lors de votre première visite, un bandeau vous permet d'accepter ou de refuser les cookies de
        mesure d'audience (Google Analytics). Si vous refusez, seuls les cookies strictement nécessaires
        au fonctionnement du site sont déposés. Vous pouvez modifier votre choix à tout moment en
        supprimant le cookie <code>vp_consent</code> depuis les paramètres de votre navigateur.
      </p>

      <h2 data-en="4. Google Analytics">4. Google Analytics</h2>
      <p data-en="This site uses Google Analytics 4, a traffic analysis service from Google LLC. It is enabled only if you accept cookies via the consent banner. Data collected (page views, visit duration, device type) is transmitted to and stored by Google on servers located in the United States. Google may use this data in accordance with its own privacy policy. IP addresses are anonymised by default in GA4. You can refuse these cookies at any time.">
        Ce site utilise Google Analytics 4, un service d'analyse de trafic fourni par Google&nbsp;LLC.
        Ce service est activé uniquement si vous acceptez les cookies via le bandeau de consentement.
        Les données collectées (pages vues, durée de visite, type d'appareil) sont transmises et
        stockées par Google sur des serveurs situés aux États-Unis. Google peut utiliser ces données
        conformément à sa propre politique de confidentialité. L'adresse IP est anonymisée par défaut
        dans GA4. Vous pouvez refuser ces cookies à tout moment.
      </p>

      <h2 data-en="5. Legal basis for processing">5. Base légale du traitement</h2>
      <ul>
        <li><strong data-en="Contact form">Formulaire de contact</strong>&nbsp;: <span data-en="consent (Article 6.1.a GDPR), voluntary submission of the form.">consentement (article 6.1.a du RGPD), envoi volontaire du formulaire.</span></li>
        <li><strong data-en="Strictly necessary cookies">Cookies strictement nécessaires</strong>&nbsp;: <span data-en="legitimate interest (Article 6.1.f GDPR), site operation.">intérêt légitime (article 6.1.f du RGPD), fonctionnement du site.</span></li>
        <li><strong data-en="Audience-measurement cookies">Cookies de mesure d'audience</strong>&nbsp;: <span data-en="consent (Article 6.1.a GDPR), acceptance via the cookie banner.">consentement (article 6.1.a du RGPD), acceptation via le bandeau cookies.</span></li>
      </ul>

      <h2 data-en="6. Retention period">6. Durée de conservation</h2>
      <ul>
        <li data-en="Contact messages: 12 months after the last exchange.">Messages de contact&nbsp;: 12 mois après le dernier échange.</li>
        <li data-en="Anonymous browsing data: 26 months.">Données de navigation anonymes&nbsp;: 26 mois.</li>
        <li data-en="Google Analytics data: 14 months (default GA4 setting).">Données Google Analytics&nbsp;: 14 mois (paramétrage par défaut GA4).</li>
      </ul>

      <h2 data-en="7. Data sharing">7. Partage des données</h2>
      <p data-en="Your personal data is neither sold, rented, nor shared with third parties, except for:">
        Vos données personnelles ne sont ni vendues, ni louées, ni partagées avec des tiers, à l'exception de&nbsp;:
      </p>
      <ul>
        <li><strong>o2switch</strong> <span data-en="(host): technical hosting of the data.">(hébergeur)&nbsp;: hébergement technique des données.</span></li>
        <li><strong>Google&nbsp;LLC</strong>&nbsp;: <span data-en="only if you accept Google Analytics cookies.">uniquement si vous acceptez les cookies Google Analytics.</span></li>
      </ul>

      <h2 data-en="8. Your rights">8. Vos droits</h2>
      <p data-en="In accordance with the General Data Protection Regulation (GDPR), you have the following rights:">
        Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez
        des droits suivants&nbsp;:
      </p>
      <ul>
        <li data-en="Right to access your personal data">Droit d'accès à vos données personnelles</li>
        <li data-en="Right to rectification">Droit de rectification</li>
        <li data-en="Right to erasure (&quot;right to be forgotten&quot;)">Droit à l'effacement (« droit à l'oubli »)</li>
        <li data-en="Right to restriction of processing">Droit à la limitation du traitement</li>
        <li data-en="Right to data portability">Droit à la portabilité de vos données</li>
        <li data-en="Right to object">Droit d'opposition</li>
        <li data-en="Right to withdraw consent at any time">Droit de retirer votre consentement à tout moment</li>
      </ul>
      <p data-en="To exercise these rights, contact us at:">
        Pour exercer ces droits, contactez-nous à&nbsp;:
        <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
      </p>
    </div>

  </div>
</section>
