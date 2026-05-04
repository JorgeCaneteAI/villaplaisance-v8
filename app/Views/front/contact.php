<?php
// Contact V8 — porté du V2 (`contact/index.html`).
// Variables : $seo, $jsonLd, $lang, $flash, $csrf.
// Le formulaire POST vers /contact qui appelle ContactController::handleSubmit.
// Champs adaptés au backend : name, email, subject, message, website (honeypot).
?>

<!-- Acte 0 : opening compact ultra-sobre -->
<section class="opening opening--compact opening--mince" aria-labelledby="opening-title">
    <div class="opening__copy opening__copy--centered">
        <p class="opening__eyebrow">Bédarrides, Provence</p>
        <h1 class="opening__title opening__title--compact" id="opening-title">Contact</h1>
        <p class="opening__sub">Une question&nbsp;? Écrivez-nous.</p>
    </div>
</section>

<!-- Acte 1 : formulaire + coordonnées -->
<section class="duo" aria-labelledby="duo-title">
    <h2 class="acte__num" id="duo-title"><span>I.</span> Nous écrire</h2>

    <?php if (!empty($flash['success'])): ?>
    <p class="duo__flash duo__flash--success" role="status">
        <?= htmlspecialchars($flash['success']) ?>
    </p>
    <?php endif; ?>

    <?php if (!empty($flash['error'])): ?>
    <p class="duo__flash duo__flash--error" role="alert">
        <?= htmlspecialchars($flash['error']) ?>
    </p>
    <?php endif; ?>

    <div class="duo__grille">

        <form class="duo__form" action="<?= LangService::url('contact') ?>" method="post" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

            <div class="champ">
                <label for="name">Votre nom</label>
                <input id="name" name="name" type="text" autocomplete="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>

            <div class="champ">
                <label for="email">Votre courriel</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="champ">
                <label for="subject">Objet <span>(optionnel)</span></label>
                <input id="subject" name="subject" type="text" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
            </div>

            <div class="champ champ--message">
                <label for="message">Votre message</label>
                <textarea id="message" name="message" rows="6" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>

            <!-- Honeypot anti-spam : champ caché que les bots remplissent -->
            <div class="champ champ--honeypot" aria-hidden="true">
                <label for="website">Site web</label>
                <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>

            <button class="duo__envoyer" type="submit">
                Envoyer
                <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
            </button>
            <p class="duo__form-note">
                Nous répondons sous deux jours, en français,
                en anglais ou en espagnol.
            </p>
        </form>

        <aside class="duo__cotes">
            <div class="cote">
                <p class="cote__libelle">Courriel</p>
                <p class="cote__valeur">
                    <a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a>
                </p>
            </div>
            <div class="cote">
                <p class="cote__libelle">Adresse</p>
                <p class="cote__valeur">
                    Villa Plaisance<br>
                    Bédarrides<br>
                    84370 Vaucluse, France
                </p>
            </div>
            <div class="cote">
                <p class="cote__libelle">Pour venir</p>
                <p class="cote__valeur">
                    Gare TGV d'Avignon à 15&nbsp;minutes.<br>
                    Aéroport Marseille-Provence à 1&nbsp;heure.<br>
                    Sortie A7 Bédarrides.
                </p>
            </div>
            <?php
            $socialLinks = [];
            try { $socialLinks = Database::fetchAll("SELECT * FROM vp_social_links ORDER BY position ASC"); } catch (\Throwable) {}
            ?>
            <?php if ($socialLinks): ?>
            <div class="cote">
                <p class="cote__libelle">Réseaux</p>
                <p class="cote__valeur">
                    <?php foreach ($socialLinks as $i => $sl): ?>
                        <?php if ($i > 0): ?> · <?php endif; ?>
                        <a href="<?= htmlspecialchars($sl['url']) ?>" rel="me" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($sl['name']) ?></a>
                    <?php endforeach; ?>
                </p>
            </div>
            <?php endif; ?>
        </aside>

    </div>
</section>
