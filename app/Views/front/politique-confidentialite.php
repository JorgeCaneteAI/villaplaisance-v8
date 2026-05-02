<?php
// Politique de confidentialité V8 — porté du V2.
// Variables : $seo, $jsonLd, $lang.
?>

<article class="legal">
    <nav class="post__breadcrumb" aria-label="Fil d'Ariane">
        <a href="<?= LangService::url('/') ?>">Accueil</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Politique de confidentialité</span>
    </nav>

    <header class="legal__entete">
        <p class="post__cat">Informations légales</p>
        <h1 class="post__titre">Politique de confidentialité</h1>
    </header>

    <div class="post__corps">
        <h2>1. Responsable du traitement</h2>
        <p>
            <strong>Villa Plaisance</strong><br>
            Propriétaire : Jorge Cañete<br>
            Adresse : Bédarrides, 84370 Vaucluse, France<br>
            Email : <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
        </p>

        <h2>2. Données personnelles collectées</h2>
        <h3>a) Formulaire de contact</h3>
        <p>
            Lorsque vous utilisez le formulaire de contact, nous collectons&nbsp;: votre nom, votre adresse email
            et le contenu de votre message. Ces données sont utilisées exclusivement pour répondre à votre demande.
        </p>

        <h3>b) Données de navigation</h3>
        <p>
            Nous collectons de manière anonyme des données de navigation à des fins statistiques&nbsp;:
            pages visitées, type d'appareil (mobile, tablette, ordinateur), langue du navigateur et provenance
            (referrer). Ces données ne permettent pas de vous identifier personnellement. Un identifiant
            anonyme (cookie <code>vp_vid</code>) est utilisé pour distinguer les visiteurs uniques, sans
            aucun lien avec votre identité.
        </p>

        <h2>3. Cookies utilisés</h2>
        <table class="legal__table">
            <thead>
                <tr><th>Cookie</th><th>Finalité</th><th>Durée</th><th>Type</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>PHPSESSID</td>
                    <td>Session technique (formulaire, navigation)</td>
                    <td>Durée de la session</td>
                    <td>Strictement nécessaire</td>
                </tr>
                <tr>
                    <td>vp_consent</td>
                    <td>Mémoriser votre choix cookies</td>
                    <td>180 jours</td>
                    <td>Strictement nécessaire</td>
                </tr>
                <tr>
                    <td>vp_vid</td>
                    <td>Statistiques anonymes de fréquentation</td>
                    <td>365 jours</td>
                    <td>Mesure d'audience</td>
                </tr>
                <tr>
                    <td>_ga, _ga_*</td>
                    <td>Google Analytics 4 — mesure d'audience</td>
                    <td>Jusqu'à 13 mois</td>
                    <td>Mesure d'audience (consentement)</td>
                </tr>
            </tbody>
        </table>

        <h3>Votre choix concernant les cookies</h3>
        <p>
            Lors de votre première visite, un bandeau vous permet d'accepter ou de refuser les cookies de
            mesure d'audience (Google Analytics). Si vous refusez, seuls les cookies strictement nécessaires
            au fonctionnement du site sont déposés. Vous pouvez modifier votre choix à tout moment en
            supprimant le cookie <code>vp_consent</code> depuis les paramètres de votre navigateur.
        </p>

        <h2>4. Google Analytics</h2>
        <p>
            Ce site utilise Google Analytics 4, un service d'analyse de trafic fourni par Google&nbsp;LLC.
            Ce service est activé uniquement si vous acceptez les cookies via le bandeau de consentement.
            Les données collectées (pages vues, durée de visite, type d'appareil) sont transmises et
            stockées par Google sur des serveurs situés aux États-Unis. Google peut utiliser ces données
            conformément à sa propre politique de confidentialité. L'adresse IP est anonymisée par défaut
            dans GA4. Vous pouvez refuser ces cookies à tout moment.
        </p>

        <h2>5. Base légale du traitement</h2>
        <ul>
            <li><strong>Formulaire de contact</strong>&nbsp;: consentement (article 6.1.a du RGPD), envoi volontaire du formulaire.</li>
            <li><strong>Cookies strictement nécessaires</strong>&nbsp;: intérêt légitime (article 6.1.f du RGPD), fonctionnement du site.</li>
            <li><strong>Cookies de mesure d'audience</strong>&nbsp;: consentement (article 6.1.a du RGPD), acceptation via le bandeau cookies.</li>
        </ul>

        <h2>6. Durée de conservation</h2>
        <ul>
            <li>Messages de contact&nbsp;: 12 mois après le dernier échange.</li>
            <li>Données de navigation anonymes&nbsp;: 26 mois.</li>
            <li>Données Google Analytics&nbsp;: 14 mois (paramétrage par défaut GA4).</li>
        </ul>

        <h2>7. Partage des données</h2>
        <p>
            Vos données personnelles ne sont ni vendues, ni louées, ni partagées avec des tiers, à l'exception de&nbsp;:
        </p>
        <ul>
            <li><strong>o2switch</strong> (hébergeur)&nbsp;: hébergement technique des données.</li>
            <li><strong>Google&nbsp;LLC</strong>&nbsp;: uniquement si vous acceptez les cookies Google Analytics.</li>
        </ul>

        <h2>8. Vos droits</h2>
        <p>
            Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez
            des droits suivants&nbsp;:
        </p>
        <ul>
            <li>Droit d'accès à vos données personnelles</li>
            <li>Droit de rectification</li>
            <li>Droit à l'effacement (« droit à l'oubli »)</li>
            <li>Droit à la limitation du traitement</li>
            <li>Droit à la portabilité de vos données</li>
            <li>Droit d'opposition</li>
            <li>Droit de retirer votre consentement à tout moment</li>
        </ul>
        <p>
            Pour exercer ces droits, contactez-nous à&nbsp;:
            <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
        </p>
    </div>
</article>
