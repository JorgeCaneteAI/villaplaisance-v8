<?php
// Plan du site V8 — porté du V2.
// Variables : $seo, $jsonLd, $lang.
?>

<article class="legal">
    <nav class="post__breadcrumb" aria-label="Fil d'Ariane">
        <a href="<?= LangService::url('/') ?>">Accueil</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Plan du site</span>
    </nav>

    <header class="legal__entete">
        <p class="post__cat">Navigation</p>
        <h1 class="post__titre">Plan du site</h1>
    </header>

    <div class="post__corps">
        <div class="sitemap">

            <section class="sitemap__bloc">
                <h2>L'offre</h2>
                <ul>
                    <li><a href="<?= LangService::url('/') ?>">Accueil</a></li>
                    <li><a href="<?= LangService::url('chambres-d-hotes') ?>">Chambres d'hôtes</a></li>
                    <li><a href="<?= LangService::url('location-villa-provence') ?>">Villa entière</a></li>
                    <li><a href="<?= LangService::url('espaces-exterieurs') ?>">Espaces extérieurs</a></li>
                </ul>
            </section>

            <section class="sitemap__bloc">
                <h2>Le territoire</h2>
                <ul>
                    <li><a href="<?= LangService::url('sur-place') ?>">Sur place</a></li>
                    <li><a href="<?= LangService::url('itineraire') ?>">Itinéraires personnalisés</a></li>
                    <li><a href="<?= LangService::url('journal') ?>">Le Journal</a></li>
                </ul>
            </section>

            <section class="sitemap__bloc">
                <h2>La maison</h2>
                <ul>
                    <li><a href="<?= LangService::url('votre-hote') ?>">Votre hôte</a></li>
                    <li><a href="<?= LangService::url('contact') ?>">Contact</a></li>
                </ul>
            </section>

            <section class="sitemap__bloc">
                <h2>Mentions</h2>
                <ul>
                    <li><a href="<?= LangService::url('mentions-legales') ?>">Mentions légales</a></li>
                    <li><a href="<?= LangService::url('politique-confidentialite') ?>">Politique de confidentialité</a></li>
                    <li><a href="<?= LangService::url('plan-du-site') ?>">Plan du site</a></li>
                </ul>
            </section>

        </div>
    </div>
</article>
