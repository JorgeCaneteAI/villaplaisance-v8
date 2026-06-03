<?php
declare(strict_types=1);
echo '<?xml version="1.0" encoding="UTF-8"?>';
$base = APP_ENV === 'production' ? 'https://villaplaisance.fr' : APP_URL;
$today = date('Y-m-d');

// Page-keys FR (= la clé dans slugs.php). LangService::url() les traduit
// vers le slug local. On émet 3 alternates par page + x-default = FR.
// Note : changefreq et priority sont volontairement omis — Google les ignore.
$pageKeys = [
    'accueil',
    'chambres-d-hotes',
    'location-villa-provence',
    'espaces-exterieurs',
    'votre-hote',
    'journal',
    'avis',
    'disponibilites',
    'itineraire',
    'contact',
    'mentions-legales',
    'politique-confidentialite',
    'plan-du-site',
];

// Image hero par page (utilisée par l'extension image:). Permet à Google
// Images et aux LLMs de relier la photo à la bonne page. Toutes en WebP < 200 Ko.
// Si le fichier n'existe pas, on retombe sur og-default.
$pageImages = [
    'accueil'                  => '/assets/img/og-default.webp',
    'chambres-d-hotes'         => '/uploads/villa-plaisance-chambre-verte-01.webp',
    'location-villa-provence'  => '/uploads/villa-plaisance-villa-exterieur-01.webp',
    'espaces-exterieurs'       => '/uploads/villa-plaisance-jardin-01.webp',
    'votre-hote'               => '/uploads/villa-plaisance-jorge-portrait.webp',
    'journal'                  => '/uploads/villa-plaisance-vignes-provence-01.webp',
    'avis'                     => '/assets/img/og-default.webp',
    'disponibilites'           => '/assets/img/og-default.webp',
    'itineraire'               => '/uploads/villa-plaisance-itineraire-01.webp',
    'contact'                  => '/assets/img/og-default.webp',
    'mentions-legales'         => '/assets/img/og-default.webp',
    'politique-confidentialite'=> '/assets/img/og-default.webp',
    'plan-du-site'             => '/assets/img/og-default.webp',
];

// Articles dynamiques (journal + itineraires).
$articles = [];
try {
    $articles = Database::fetchAll(
        "SELECT slug, type, updated_at, cover_image FROM vp_articles WHERE status = 'published'"
    );
} catch (\Throwable $e) {}
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php foreach ($pageKeys as $pageKey):
    $imgUrl = $base . ($pageImages[$pageKey] ?? '/assets/img/og-default.webp');
    $frUrl = $base . LangService::url($pageKey, 'fr');
?>
    <url>
        <loc><?= htmlspecialchars($frUrl) ?></loc>
        <lastmod><?= $today ?></lastmod>
        <?php foreach (SUPPORTED_LANGS as $lang): ?>
        <xhtml:link rel="alternate" hreflang="<?= $lang ?>" href="<?= htmlspecialchars($base . LangService::url($pageKey, $lang)) ?>"/>
        <?php endforeach; ?>
        <xhtml:link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($frUrl) ?>"/>
        <image:image>
            <image:loc><?= htmlspecialchars($imgUrl) ?></image:loc>
        </image:image>
    </url>
<?php endforeach; ?>

<?php foreach ($articles as $art):
    $section = $art['type'] === 'journal' ? 'journal' : 'itineraire';
    $url = $base . '/' . $section . '/' . $art['slug'];
    $img = !empty($art['cover_image'])
        ? $base . '/uploads/' . $art['cover_image']
        : $base . '/assets/img/og-default.webp';
?>
    <url>
        <loc><?= htmlspecialchars($url) ?></loc>
        <lastmod><?= htmlspecialchars(substr($art['updated_at'] ?? $today, 0, 10)) ?></lastmod>
        <image:image>
            <image:loc><?= htmlspecialchars($img) ?></image:loc>
        </image:image>
    </url>
<?php endforeach; ?>
</urlset>
