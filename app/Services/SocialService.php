<?php
declare(strict_types=1);

/**
 * Réseaux sociaux : source unique = table vp_social_links (gérée dans
 * l'admin Réglages). Le front (footer, contact) lisait avant un lien mort
 * `href="#"` + un handle en dur ; il consomme désormais cette table.
 */
class SocialService
{
    /** @var array<int,array{name:string,url:string,icon:string,handle:string}>|null */
    private static ?array $cache = null;

    /**
     * Liens actifs (URL non vide), triés par position.
     *
     * @return array<int,array{name:string,url:string,icon:string,handle:string}>
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $rows = [];
        try {
            $rows = Database::fetchAll(
                "SELECT name, url, icon FROM vp_social_links WHERE url <> '' ORDER BY position ASC"
            );
        } catch (\Throwable) {
            $rows = [];
        }
        // @handle dérivé du dernier segment de l'URL (ex: .../villaplaisancebedarrides/).
        foreach ($rows as &$row) {
            $path = (string)(parse_url((string)$row['url'], PHP_URL_PATH) ?? '');
            $row['handle'] = basename(rtrim($path, '/'));
        }
        unset($row);
        self::$cache = $rows;
        return self::$cache;
    }
}
