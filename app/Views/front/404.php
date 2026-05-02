<?php
// 404 V8 — porté du V2 (`404.html`).
// Variables : $seo, $jsonLd, $lang. Le code HTTP 404 est posé par le Controller.
?>

<div class="page-404">
    <div class="page-404__corps">
        <p class="page-404__num">404</p>
        <h1 class="page-404__titre">Cette page n'existe pas, ou plus.</h1>
        <p class="page-404__sub">Vous avez peut-être suivi un lien périmé, ou tapé une adresse au hasard. Tout va bien.</p>
        <a class="contact__bouton" href="<?= LangService::url('/') ?>">
            Retour à l'accueil
            <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20"><path d="M4 12h15m0 0-5-5m5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square"/></svg>
        </a>
    </div>
</div>
