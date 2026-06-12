<?php
declare(strict_types=1);

// Define ROOT if not already defined (for seeds)
if (!defined('ROOT')) {
    define('ROOT', __DIR__);
}

// Parse .env
$envFile = ROOT . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Constants
define('SUPPORTED_LANGS', ['fr', 'en', 'es']);
define('DEFAULT_LANG', 'fr');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost:8000');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');

// Load core services (no namespace, loaded explicitly)
require ROOT . '/app/Services/Database.php';
require ROOT . '/app/Services/LangService.php';
require ROOT . '/app/Services/SeoService.php';
require ROOT . '/app/Services/BlockService.php';
require ROOT . '/app/Services/BlockFieldsService.php';
require ROOT . '/app/Services/BlockFormRenderer.php';
require ROOT . '/app/Services/ImageService.php';
require ROOT . '/app/Services/SocialService.php';
require ROOT . '/app/Services/TextService.php';
require ROOT . '/app/Services/IconService.php';
require ROOT . '/app/Services/AnthropicService.php';
require ROOT . '/app/Services/AnalyticsService.php';
require ROOT . '/app/Services/IpInfoService.php';
require ROOT . '/app/Services/GoogleBusinessProfileService.php';
require ROOT . '/app/Services/UnsplashService.php';

// PSR-4 minimal autoloader for App\ namespace
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = ROOT . '/app/' . $relative . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Secure session — DÉMARRAGE CONDITIONNEL
// Avant : session_start() à chaque hit du front → PHP envoie automatiquement
// Cache-Control: no-store/no-cache, ce qui empêche Googlebot et le CDN o2switch
// de mettre en cache les pages HTML (gros pénalité crawl budget).
// Désormais : session_start() est déclenché par le Router uniquement pour
// /admin/*, /api/*, ou toute méthode non-GET (formulaires CSRF). Les pages
// front en lecture (GET) restent cacheable, sans cookie PHPSESSID posé sur
// les visiteurs anonymes (bonus RGPD).
function vp_session_start(): void
{
    if (session_status() !== PHP_SESSION_NONE) return;
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => APP_ENV === 'production',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Production : masquer les stacktraces (chemin serveur, DB, etc.) et logger.
// Local : laisser PHP afficher pour le dev.
if (APP_ENV === 'production') {
    error_reporting(E_ALL);
    @ini_set('display_errors', '0');
    @ini_set('log_errors', '1');

    set_exception_handler(static function (\Throwable $e): void {
        @error_log('[VP] uncaught: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }
        echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>500 — Erreur</title>'
           . '<meta name="viewport" content="width=device-width, initial-scale=1">'
           . '<style>body{font-family:Georgia,serif;max-width:560px;margin:15vh auto;padding:0 24px;'
           . 'color:#2b2419;text-align:center;line-height:1.6}h1{font-weight:400;font-size:2rem}'
           . 'a{color:#a0764f}</style></head><body>'
           . '<h1>Une erreur est survenue</h1>'
           . '<p>Désolé, quelque chose ne s\'est pas passé comme prévu.<br>'
           . 'L\'incident a été noté ; nous le regardons.</p>'
           . '<p><a href="/">← Retour à l\'accueil</a></p></body></html>';
        exit;
    });

    set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline): bool {
        if (!(error_reporting() & $errno)) return false;

        // Ne convertit en exception fatale QUE les vraies erreurs critiques.
        // Les warnings, notices, deprecated (ex. strftime sur PHP 8.x) sont
        // loggés mais ne cassent pas le rendu en cours.
        $fatal = E_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR
               | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
        if ($errno & $fatal) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        }

        @error_log("[VP non-fatal $errno] $errstr in $errfile:$errline");
        return true; // PHP n'exécute pas son handler interne
    });
}
