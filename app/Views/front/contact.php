<?php
/**
 * Contact V9 — esprit weeks-off.com (Sprint 3, 2026-05-05).
 *
 * Variables disponibles depuis ContactController :
 *   $flash    array{type, message} | null
 *   $csrf     string (token)
 *   $seo, $jsonLd, $lang
 *
 * POST vers /contact qui appelle ContactController::handleSubmit (honeypot
 * "website", CSRF, rate limit 5/h, anti-spam URLs).
 */
?>

<section class="identite identite--compact" style="border-bottom:1px solid var(--rule)">
    <div class="eyebrow">Contact</div>
    <h1>Écrire à Jorge</h1>
    <p class="baseline">Une question, un projet de séjour, un mot.</p>
</section>

<section class="lead-section">
    <div class="lead-section__inner">
        <div class="lead-section__num">I.</div>
        <div>
            <h2>Le plus simple</h2>
            <p>
                Écrivez directement à <a href="mailto:contact@villaplaisance.fr" style="color:var(--ink);border-bottom:1px solid var(--ink);padding-bottom:1px">contact@villaplaisance.fr</a>.
                Jorge répond personnellement, en français, anglais ou espagnol,
                en général sous 24 heures.
            </p>
            <p class="lead-section__chute">Précisez vos dates et le nombre de personnes — c'est tout ce qu'il faut.</p>
        </div>
    </div>
</section>

<section class="lead-section" style="border-top:none">
    <div class="lead-section__inner">
        <div class="lead-section__num">II.</div>
        <div>
            <h2>Ou via ce formulaire</h2>
            <p>Si vous préférez, ce formulaire fait le même travail.</p>
        </div>
    </div>
</section>

<form class="contact-form" method="post" action="<?= LangService::url('contact') ?>" novalidate>

    <?php if (!empty($flash)):
        $flashClass = ($flash['type'] ?? '') === 'success' ? 'flash--success' : 'flash--error';
    ?>
    <div class="flash <?= $flashClass ?>" role="alert">
        <?= htmlspecialchars((string)($flash['message'] ?? '')) ?>
    </div>
    <?php endif; ?>

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)$csrf) ?>">

    <div class="honey" aria-hidden="true">
        <label for="website">Ne pas remplir</label>
        <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
    </div>

    <div class="field">
        <label for="name">Votre nom <span aria-hidden="true">*</span></label>
        <input type="text" name="name" id="name" required autocomplete="name">
    </div>

    <div class="field">
        <label for="email">Votre email <span aria-hidden="true">*</span></label>
        <input type="email" name="email" id="email" required autocomplete="email">
    </div>

    <div class="field">
        <label for="subject">Sujet</label>
        <input type="text" name="subject" id="subject" placeholder="Réservation, question…">
    </div>

    <div class="field">
        <label for="message">Votre message <span aria-hidden="true">*</span></label>
        <textarea name="message" id="message" rows="6" required placeholder="Vos dates, nombre de personnes, ce qui compte pour vous…"></textarea>
    </div>

    <div class="submit">
        <button type="submit" class="pill pill--solid pill--big">
            Envoyer le message
            <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        </button>
    </div>
</form>

<section class="strip" aria-label="Coordonnées">
    <div>
        <span class="k">— Adresse</span>
        <p class="v">Bédarrides 84370</p>
        <p class="det">Vaucluse, Provence &middot; entre Avignon et Orange</p>
    </div>
    <div>
        <span class="k">— Email</span>
        <p class="v">Direct</p>
        <p class="det"><a href="mailto:contact@villaplaisance.fr">contact@villaplaisance.fr</a></p>
    </div>
    <div>
        <span class="k">— Langues</span>
        <p class="v">FR · EN · ES · DE</p>
        <p class="det">Jorge répond personnellement à chaque message.</p>
    </div>
</section>
