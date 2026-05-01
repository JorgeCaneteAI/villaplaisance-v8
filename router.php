<?php
// Router pour le serveur de dev PHP built-in :
//   php -S localhost:8767 router.php
// Si la requête correspond à un fichier statique existant dans public/,
// on laisse le serveur le servir directement. Sinon on délègue au routeur
// applicatif (public/index.php).

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($path) && !is_dir($path)) {
    return false; // serve the static file as-is
}

require __DIR__ . '/public/index.php';
