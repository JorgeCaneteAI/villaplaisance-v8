<?php
declare(strict_types=1);

class LangService
{
    private static string $currentLang = 'fr';
    private static array $translations = [];
    private static array $slugMap = [];

    public static function current(): string
    {
        return self::$currentLang;
    }

    public static function init(string $lang = 'fr'): void
    {
        if (!in_array($lang, SUPPORTED_LANGS, true)) {
            $lang = DEFAULT_LANG;
        }
        self::$currentLang = $lang;

        $file = ROOT . '/app/Lang/' . $lang . '.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        }

        // Load slug map for this lang
        $slugFile = ROOT . '/app/Lang/slugs.php';
        if (file_exists($slugFile)) {
            self::$slugMap = require $slugFile;
        }
    }

    public static function get(): string
    {
        return self::$currentLang;
    }

    public static function t(string $key, array $params = []): string
    {
        $text = self::$translations[$key] ?? $key;
        foreach ($params as $k => $v) {
            $text = str_replace(':' . $k, (string)$v, $text);
        }
        return $text;
    }

    public static function url(string $page, ?string $lang = null): string
    {
        $lang = $lang ?? self::$currentLang;
        // $page = slug FR canonique (clé du Router). On résout le slug localisé.
        $slug = self::$slugMap[$lang][$page] ?? self::$slugMap[DEFAULT_LANG][$page] ?? $page;

        if ($slug === '/') {
            return $lang === DEFAULT_LANG ? '/' : '/' . $lang . '/';
        }

        if ($lang === DEFAULT_LANG) {
            return '/' . ltrim($slug, '/');
        }
        return '/' . $lang . '/' . ltrim($slug, '/');
    }

    /**
     * URL de la page courante dans une autre langue.
     * Décompose REQUEST_URI, identifie la page-key FR canonique
     * via le slug entrant, reconstruit l'URL dans la langue cible.
     * Préserve la query string.
     */
    public static function switchLangUrl(string $targetLang): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $parts = parse_url($uri);
        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';

        // Strip préfixe langue
        $currentLang = DEFAULT_LANG;
        if (preg_match('#^/(en|es|de)(/.*)?$#', $path, $m)) {
            $currentLang = $m[1];
            $path = $m[2] ?? '/';
        }
        $path = '/' . trim($path, '/');
        if ($path === '') $path = '/';

        // Cas accueil
        if ($path === '/') {
            return self::url('accueil', $targetLang) . $query;
        }

        // Cas /journal/{slug}, /itineraire/{slug}, /sur-place/{slug}, détail d'article
        // Slug d'article = donnée DB partagée entre langues, on garde tel quel.
        foreach (['/journal/', '/itineraire/', '/sur-place/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $pageKeyFr = trim($prefix, '/'); // 'journal' / 'itineraire' / 'sur-place'
                $rest = substr($path, strlen($prefix));
                $base = self::url($pageKeyFr, $targetLang);
                return rtrim($base, '/') . '/' . $rest . $query;
            }
        }

        // Cas page statique : on cherche la page-key FR à partir du slug courant
        $currentSlug = ltrim($path, '/');
        $localMap = self::$slugMap[$currentLang] ?? [];
        $pageKey = null;
        foreach ($localMap as $key => $slug) {
            if ($slug === $currentSlug) {
                $pageKey = $key;
                break;
            }
        }

        if ($pageKey !== null) {
            return self::url($pageKey, $targetLang) . $query;
        }

        // Fallback : on ne sait pas mapper, renvoie l'accueil dans la langue cible.
        return self::url('accueil', $targetLang);
    }

    public static function getAllLangs(): array
    {
        return SUPPORTED_LANGS;
    }
}

// Global helper
function t(string $key, array $params = []): string
{
    return LangService::t($key, $params);
}
