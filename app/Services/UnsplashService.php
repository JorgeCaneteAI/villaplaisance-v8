<?php
declare(strict_types=1);

/**
 * UnsplashService, recherche + import d'images depuis Unsplash.
 *
 * - Clé API gratuite : 50 req/heure en mode Demo, 5000/h en mode Production.
 * - L'import télécharge l'image sur le serveur dans /uploads/, l'enregistre
 *   dans vp_media avec attribution (photographe + lien) conservée pour
 *   respecter les guidelines Unsplash.
 * - On "ping" l'endpoint download_location à chaque import (obligatoire
 *   selon les TOS Unsplash pour comptabiliser les downloads).
 *
 * Config .env :
 *   UNSPLASH_ACCESS_KEY=...
 *
 * Doc API : https://unsplash.com/documentation
 */
class UnsplashService
{
    private const API_BASE = 'https://api.unsplash.com';
    private const TIMEOUT  = 10;

    public static function isConfigured(): bool
    {
        return !empty($_ENV['UNSPLASH_ACCESS_KEY']);
    }

    /**
     * Recherche d'images.
     * @param string $query Terme de recherche (ex. "lavender provence")
     * @param int $page Page (1-based)
     * @param int $perPage 10..30 (Unsplash plafonne à 30)
     * @return array ['total' => int, 'total_pages' => int, 'results' => [...]]
     */
    public static function search(string $query, int $page = 1, int $perPage = 24): array
    {
        if (!self::isConfigured()) {
            throw new \RuntimeException('UNSPLASH_ACCESS_KEY non défini dans .env');
        }
        $query = trim($query);
        if ($query === '') return ['total' => 0, 'total_pages' => 0, 'results' => []];

        $params = [
            'query'    => $query,
            'page'     => max(1, $page),
            'per_page' => max(1, min(30, $perPage)),
            'orientation' => 'landscape', // défaut adapté au site Villa Plaisance
            'content_filter' => 'high', // exclut contenu mature/violent
        ];

        $data = self::httpGet('/search/photos?' . http_build_query($params));

        // On compacte : juste les champs utiles côté admin
        $results = [];
        foreach ($data['results'] ?? [] as $p) {
            $results[] = [
                'id'              => $p['id'] ?? '',
                'description'     => $p['description'] ?? $p['alt_description'] ?? '',
                'alt'             => $p['alt_description'] ?? '',
                'width'           => $p['width'] ?? 0,
                'height'          => $p['height'] ?? 0,
                'color'           => $p['color'] ?? null,
                'thumb_url'       => $p['urls']['thumb'] ?? '',
                'small_url'       => $p['urls']['small'] ?? '',
                'regular_url'     => $p['urls']['regular'] ?? '',
                'download_url'    => $p['urls']['raw'] ?? '',
                'download_track'  => $p['links']['download_location'] ?? '',
                'unsplash_url'    => $p['links']['html'] ?? '',
                'photographer'    => $p['user']['name'] ?? '',
                'photographer_url'=> $p['user']['links']['html'] ?? '',
                'tags'            => array_map(fn($t) => $t['title'] ?? '', $p['tags'] ?? []),
            ];
        }

        return [
            'total'       => (int)($data['total'] ?? 0),
            'total_pages' => (int)($data['total_pages'] ?? 0),
            'results'     => $results,
        ];
    }

    /**
     * Récupère le détail d'une photo (utile avant import si on n'a pas tout
     * en cache côté JS).
     */
    public static function getPhoto(string $photoId): array
    {
        return self::httpGet('/photos/' . urlencode($photoId));
    }

    /**
     * Ping l'endpoint download_location (obligatoire selon TOS Unsplash
     * à chaque "download/use" pour comptabiliser).
     */
    public static function trackDownload(string $downloadLocationUrl): void
    {
        if (!$downloadLocationUrl) return;
        // Ce endpoint nécessite Authorization Bearer Client-ID
        try { self::httpGet(str_replace(self::API_BASE, '', $downloadLocationUrl)); }
        catch (\Throwable) { /* best-effort, on n'échoue pas l'import si le ping rate */ }
    }

    /* ════════════════ HTTP ════════════════ */

    private static function httpGet(string $path): array
    {
        $url = str_starts_with($path, 'http') ? $path : self::API_BASE . $path;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => self::TIMEOUT,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_USERAGENT      => 'VillaPlaisance-Admin/1.0',
            CURLOPT_HTTPHEADER     => [
                'Accept-Version: v1',
                'Authorization: Client-ID ' . ($_ENV['UNSPLASH_ACCESS_KEY'] ?? ''),
            ],
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false) throw new \RuntimeException("cURL error: $err");
        $json = json_decode((string)$body, true);
        if ($code >= 400) {
            $msg = $json['errors'][0] ?? "HTTP $code";
            throw new \RuntimeException("Unsplash API error ($code): $msg");
        }
        return is_array($json) ? $json : [];
    }

    /**
     * Télécharge le binaire d'une image depuis une URL Unsplash et le sauve
     * dans un fichier local. Renvoie le path serveur du fichier.
     *
     * @param string $url URL "raw" ou "regular" ou "full" depuis l'API
     * @param string $destPath Chemin absolu de destination
     * @throws \RuntimeException si échec
     */
    public static function downloadBinary(string $url, string $destPath): void
    {
        $fp = @fopen($destPath, 'wb');
        if (!$fp) throw new \RuntimeException("Impossible d'écrire $destPath");

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FILE           => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => self::TIMEOUT,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_USERAGENT      => 'VillaPlaisance-Admin/1.0',
        ]);
        $ok = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        fclose($fp);

        if (!$ok || $code !== 200) {
            @unlink($destPath);
            throw new \RuntimeException("Téléchargement image échoué ($code): $err");
        }
    }
}
