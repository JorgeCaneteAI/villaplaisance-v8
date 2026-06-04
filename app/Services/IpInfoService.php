<?php
declare(strict_types=1);

/**
 * IpInfoService, enrichit une IP en pays / ville / FAI via ipapi.co.
 *
 * - Gratuit, HTTPS, sans clé, ~1k requêtes/jour.
 * - Cache fichier 30 jours dans app/cache/ip/<sha1>.json pour ne pas spammer.
 * - Renvoie null si IP privée/locale (RFC 1918) ou si l'API échoue.
 * - Timeout 3 s pour éviter de bloquer le rendu de la page admin.
 */
class IpInfoService
{
    private const ENDPOINT = 'https://ipapi.co/%s/json/';
    private const TIMEOUT  = 3;
    private const TTL      = 30 * 86400; // 30 jours

    public static function lookup(?string $ip): ?array
    {
        if (!$ip) return null;
        $ip = trim($ip);

        // IP privée / locale → pas d'info utile
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return ['local' => true, 'ip' => $ip];
        }

        // Cache fichier
        $cacheDir  = ROOT . '/app/cache/ip';
        $cacheFile = $cacheDir . '/' . sha1($ip) . '.json';
        if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < self::TTL) {
            $raw = @file_get_contents($cacheFile);
            if ($raw !== false) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) return $decoded;
            }
        }

        // Appel API (timeout court)
        $data = self::fetchRemote($ip);
        if ($data === null) {
            // On cache même les erreurs 5 min pour ne pas re-tenter à chaque hit
            $errorPayload = ['error' => 'lookup_failed', 'ip' => $ip];
            if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
            @file_put_contents($cacheFile, json_encode($errorPayload), LOCK_EX);
            @touch($cacheFile, time() - self::TTL + 300); // expire dans 5 min
            return $errorPayload;
        }

        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
        @file_put_contents($cacheFile, json_encode($data), LOCK_EX);
        return $data;
    }

    private static function fetchRemote(string $ip): ?array
    {
        $url = sprintf(self::ENDPOINT, urlencode($ip));

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => self::TIMEOUT,
                CURLOPT_TIMEOUT        => self::TIMEOUT,
                CURLOPT_USERAGENT      => 'VillaPlaisance-Admin/1.0',
            ]);
            $body = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code !== 200 || !$body) return null;
        } else {
            $ctx = stream_context_create(['http' => ['timeout' => self::TIMEOUT, 'user_agent' => 'VillaPlaisance-Admin/1.0']]);
            $body = @file_get_contents($url, false, $ctx);
            if ($body === false) return null;
        }

        $json = json_decode($body, true);
        if (!is_array($json) || isset($json['error'])) return null;
        return $json;
    }

    /**
     * Format une fiche d'affichage à partir du payload brut ipapi.co.
     * Renvoie un tableau prêt à itérer en vue.
     */
    public static function format(?array $info): array
    {
        if (!$info) return ['available' => false];
        if (!empty($info['local'])) {
            return ['available' => false, 'note' => 'Adresse IP locale (réseau privé)', 'ip' => $info['ip']];
        }
        if (!empty($info['error'])) {
            return ['available' => false, 'note' => 'Lookup IP impossible', 'ip' => $info['ip'] ?? null];
        }

        $countryCode = strtoupper($info['country_code'] ?? '');
        $flag = '';
        if (strlen($countryCode) === 2) {
            $chars = str_split($countryCode);
            $flag = mb_chr(ord($chars[0]) + 127397) . mb_chr(ord($chars[1]) + 127397);
        }

        $mapUrl = null;
        if (!empty($info['latitude']) && !empty($info['longitude'])) {
            $mapUrl = 'https://www.google.com/maps?q=' . urlencode($info['latitude'] . ',' . $info['longitude']);
        }

        return [
            'available'    => true,
            'ip'           => $info['ip'] ?? null,
            'country_code' => $countryCode,
            'country_name' => $info['country_name'] ?? null,
            'country_flag' => $flag,
            'region'       => $info['region'] ?? null,
            'city'         => $info['city'] ?? null,
            'postal'       => $info['postal'] ?? null,
            'timezone'     => $info['timezone'] ?? null,
            'utc_offset'   => $info['utc_offset'] ?? null,
            'currency'     => $info['currency'] ?? null,
            'languages'    => $info['languages'] ?? null,
            'org'          => $info['org'] ?? null,
            'asn'          => $info['asn'] ?? null,
            'network'      => $info['network'] ?? null,
            'version'      => $info['version'] ?? null,
            'latitude'     => $info['latitude'] ?? null,
            'longitude'    => $info['longitude'] ?? null,
            'map_url'      => $mapUrl,
        ];
    }
}
