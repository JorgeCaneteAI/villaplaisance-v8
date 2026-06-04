<?php
declare(strict_types=1);

/**
 * GoogleBusinessProfileService, accès à l'API Google Business Profile
 * (anciennement Google My Business) pour synchroniser les avis et y répondre.
 *
 * Flow OAuth :
 *   1. Admin clique "Connecter Google" → redirigé vers Google consent screen
 *   2. Google redirige vers /admin/google/callback?code=... après acceptation
 *   3. On échange le code contre un access_token + refresh_token
 *   4. Le refresh_token est stocké dans app/cache/google-oauth.json (chmod 600)
 *   5. À chaque appel API, on échange le refresh_token contre un access_token frais
 *
 * Configuration .env (à ajouter manuellement) :
 *   GOOGLE_OAUTH_CLIENT_ID=...
 *   GOOGLE_OAUTH_CLIENT_SECRET=...
 *   GOOGLE_OAUTH_REDIRECT_URI=https://villaplaisance.fr/admin/google/callback
 *   GOOGLE_BUSINESS_LOCATION=accounts/123456/locations/789012  (récupéré après 1er appel listAccounts)
 */
class GoogleBusinessProfileService
{
    private const TOKEN_FILE = '/app/cache/google-oauth.json';
    private const SCOPE = 'https://www.googleapis.com/auth/business.manage';
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const REVIEWS_URL = 'https://mybusiness.googleapis.com/v4/%s/reviews';

    /* ════════════════ Config & helpers ════════════════ */

    public static function isConfigured(): bool
    {
        return !empty($_ENV['GOOGLE_OAUTH_CLIENT_ID'])
            && !empty($_ENV['GOOGLE_OAUTH_CLIENT_SECRET'])
            && !empty($_ENV['GOOGLE_OAUTH_REDIRECT_URI']);
    }

    public static function isConnected(): bool
    {
        $t = self::loadTokens();
        return !empty($t['refresh_token']);
    }

    public static function status(): array
    {
        return [
            'configured' => self::isConfigured(),
            'connected'  => self::isConnected(),
            'location'   => $_ENV['GOOGLE_BUSINESS_LOCATION'] ?? null,
            'tokens'     => self::loadTokens(),
        ];
    }

    /* ════════════════ OAuth Flow ════════════════ */

    /**
     * Construit l'URL de consentement Google.
     * Le param `state` est un anti-CSRF stocké en session.
     */
    public static function buildAuthUrl(): string
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['google_oauth_state'] = $state;
        $params = [
            'client_id'     => $_ENV['GOOGLE_OAUTH_CLIENT_ID'] ?? '',
            'redirect_uri'  => $_ENV['GOOGLE_OAUTH_REDIRECT_URI'] ?? '',
            'response_type' => 'code',
            'scope'         => self::SCOPE,
            'access_type'   => 'offline',
            'prompt'        => 'consent', // force le refresh_token à chaque connect
            'state'         => $state,
        ];
        return self::AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * Échange le code reçu après consentement contre un access+refresh token.
     * Stocke le refresh_token pour usage ultérieur.
     *
     * @throws \RuntimeException si l'échange échoue.
     */
    public static function exchangeCodeForTokens(string $code, string $state): void
    {
        if (empty($_SESSION['google_oauth_state']) || $state !== $_SESSION['google_oauth_state']) {
            throw new \RuntimeException('Anti-CSRF state mismatch');
        }
        unset($_SESSION['google_oauth_state']);

        $payload = self::httpPost(self::TOKEN_URL, [
            'code'          => $code,
            'client_id'     => $_ENV['GOOGLE_OAUTH_CLIENT_ID'] ?? '',
            'client_secret' => $_ENV['GOOGLE_OAUTH_CLIENT_SECRET'] ?? '',
            'redirect_uri'  => $_ENV['GOOGLE_OAUTH_REDIRECT_URI'] ?? '',
            'grant_type'    => 'authorization_code',
        ]);

        if (empty($payload['refresh_token'])) {
            throw new \RuntimeException('Pas de refresh_token reçu (vérifie prompt=consent + access_type=offline)');
        }

        self::saveTokens([
            'refresh_token' => $payload['refresh_token'],
            'access_token'  => $payload['access_token'] ?? null,
            'expires_at'    => time() + (int)($payload['expires_in'] ?? 3600) - 60,
            'connected_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public static function disconnect(): void
    {
        $f = ROOT . self::TOKEN_FILE;
        if (is_file($f)) @unlink($f);
    }

    /**
     * Renvoie un access_token frais. Si le précédent est expiré, en demande un nouveau
     * via le refresh_token.
     */
    private static function getAccessToken(): string
    {
        $t = self::loadTokens();
        if (empty($t['refresh_token'])) {
            throw new \RuntimeException('Pas connecté à Google. Va dans /admin/avis et clique "Connecter Google".');
        }
        if (!empty($t['access_token']) && !empty($t['expires_at']) && $t['expires_at'] > time()) {
            return $t['access_token'];
        }

        // Rafraîchir
        $payload = self::httpPost(self::TOKEN_URL, [
            'client_id'     => $_ENV['GOOGLE_OAUTH_CLIENT_ID'] ?? '',
            'client_secret' => $_ENV['GOOGLE_OAUTH_CLIENT_SECRET'] ?? '',
            'refresh_token' => $t['refresh_token'],
            'grant_type'    => 'refresh_token',
        ]);

        $t['access_token'] = $payload['access_token'] ?? null;
        $t['expires_at']   = time() + (int)($payload['expires_in'] ?? 3600) - 60;
        self::saveTokens($t);

        return $t['access_token'];
    }

    /* ════════════════ Appels Business Profile API ════════════════ */

    /**
     * Liste les comptes Google Business auxquels l'utilisateur a accès.
     * Utile pour récupérer le nom de compte (ex. accounts/123456).
     * Endpoint : https://mybusinessaccountmanagement.googleapis.com/v1/accounts
     */
    public static function listAccounts(): array
    {
        $url = 'https://mybusinessaccountmanagement.googleapis.com/v1/accounts';
        return self::httpGet($url, self::getAccessToken());
    }

    /**
     * Liste les locations (établissements) d'un compte.
     * Endpoint : https://mybusinessbusinessinformation.googleapis.com/v1/{accountName}/locations
     */
    public static function listLocations(string $accountName): array
    {
        $url = 'https://mybusinessbusinessinformation.googleapis.com/v1/' . $accountName
             . '/locations?readMask=name,title,storefrontAddress';
        return self::httpGet($url, self::getAccessToken());
    }

    /**
     * Récupère tous les avis d'un location.
     * Paginé : suit nextPageToken jusqu'à épuisement.
     *
     * @param string $location Format "accounts/123/locations/456"
     * @return array Liste agrégée d'avis bruts (payload Google).
     */
    public static function fetchAllReviews(string $location): array
    {
        $all = [];
        $pageToken = null;
        $maxLoop = 50; // garde-fou si l'API renvoie en boucle
        while ($maxLoop-- > 0) {
            $url = sprintf(self::REVIEWS_URL, $location);
            $url .= '?pageSize=50';
            if ($pageToken) $url .= '&pageToken=' . urlencode($pageToken);
            $data = self::httpGet($url, self::getAccessToken());
            foreach ($data['reviews'] ?? [] as $r) $all[] = $r;
            $pageToken = $data['nextPageToken'] ?? null;
            if (!$pageToken) break;
        }
        return $all;
    }

    /**
     * Poste une réponse à un avis.
     * @param string $reviewName Format "accounts/123/locations/456/reviews/789"
     */
    public static function replyToReview(string $reviewName, string $replyText): array
    {
        $url = 'https://mybusiness.googleapis.com/v4/' . $reviewName . '/reply';
        return self::httpPut($url, ['comment' => $replyText], self::getAccessToken());
    }

    /**
     * Supprime la réponse à un avis.
     */
    public static function deleteReply(string $reviewName): array
    {
        $url = 'https://mybusiness.googleapis.com/v4/' . $reviewName . '/reply';
        return self::httpDelete($url, self::getAccessToken());
    }

    /* ════════════════ Stockage tokens (fichier serveur) ════════════════ */

    private static function loadTokens(): array
    {
        $f = ROOT . self::TOKEN_FILE;
        if (!is_file($f)) return [];
        $raw = @file_get_contents($f);
        if ($raw === false) return [];
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private static function saveTokens(array $tokens): void
    {
        $dir = ROOT . '/app/cache';
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $f = ROOT . self::TOKEN_FILE;
        @file_put_contents($f, json_encode($tokens, JSON_PRETTY_PRINT), LOCK_EX);
        @chmod($f, 0600);
    }

    /* ════════════════ HTTP helpers (cURL) ════════════════ */

    private static function httpGet(string $url, string $bearer): array
    {
        return self::httpRequest('GET', $url, null, $bearer);
    }

    private static function httpPost(string $url, array $form, ?string $bearer = null): array
    {
        return self::httpRequest('POST', $url, $form, $bearer, 'form');
    }

    private static function httpPut(string $url, array $jsonBody, string $bearer): array
    {
        return self::httpRequest('PUT', $url, $jsonBody, $bearer, 'json');
    }

    private static function httpDelete(string $url, string $bearer): array
    {
        return self::httpRequest('DELETE', $url, null, $bearer);
    }

    private static function httpRequest(string $method, string $url, ?array $body, ?string $bearer, string $bodyKind = 'json'): array
    {
        $ch = curl_init($url);
        $headers = ['Accept: application/json'];
        if ($bearer) $headers[] = 'Authorization: Bearer ' . $bearer;

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_USERAGENT      => 'VillaPlaisance-Admin/1.0',
        ]);

        if ($body !== null) {
            if ($bodyKind === 'form') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                $headers[] = 'Content-Type: application/json';
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new \RuntimeException("cURL error: $err");
        }
        $decoded = json_decode((string)$raw, true);
        if ($code >= 400) {
            $msg = $decoded['error']['message'] ?? $decoded['error_description'] ?? "HTTP $code";
            throw new \RuntimeException("Google API error ($code): $msg");
        }
        return is_array($decoded) ? $decoded : [];
    }
}
