<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Endpoints d'administration pour la sync des avis Google Business Profile.
 *
 * Routes :
 *   GET  /admin/google/connect      → redirige vers le consent Google
 *   GET  /admin/google/callback     → reçoit le code, échange contre refresh_token
 *   POST /admin/google/sync         → tire les derniers avis (manuel uniquement)
 *   POST /admin/google/reply/{id}   → poste une réponse à l'avis local id
 *   POST /admin/google/disconnect   → supprime les tokens
 */
class GoogleReviewsController extends AdminBaseController
{
    public function connect(): void
    {
        if (!\GoogleBusinessProfileService::isConfigured()) {
            $this->flash('error', 'Google OAuth non configuré dans .env (CLIENT_ID, CLIENT_SECRET, REDIRECT_URI).');
            $this->redirect('/admin/avis');
            return;
        }
        $url = \GoogleBusinessProfileService::buildAuthUrl();
        header('Location: ' . $url);
        exit;
    }

    public function callback(): void
    {
        $code  = $_GET['code']  ?? '';
        $state = $_GET['state'] ?? '';
        if (!$code) {
            $err = $_GET['error_description'] ?? $_GET['error'] ?? 'Code manquant';
            $this->flash('error', 'Connexion Google annulée ou échouée : ' . $err);
            $this->redirect('/admin/avis');
            return;
        }
        try {
            \GoogleBusinessProfileService::exchangeCodeForTokens($code, $state);
            $this->flash('success', 'Connexion Google établie. Tu peux maintenant lancer une synchronisation.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Échec de l’échange OAuth : ' . $e->getMessage());
        }
        $this->redirect('/admin/avis');
    }

    public function disconnect(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/avis');
            return;
        }
        \GoogleBusinessProfileService::disconnect();
        $this->flash('success', 'Déconnexion Google effectuée.');
        $this->redirect('/admin/avis');
    }

    public function sync(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/avis');
            return;
        }
        try {
            $location = $_ENV['GOOGLE_BUSINESS_LOCATION'] ?? '';
            if (!$location) {
                // Auto-détection : 1er compte → 1er location
                $accounts  = \GoogleBusinessProfileService::listAccounts()['accounts'] ?? [];
                $accountId = $accounts[0]['name'] ?? null;
                if (!$accountId) throw new \RuntimeException('Aucun compte Business Profile lié à ce token.');
                $locs = \GoogleBusinessProfileService::listLocations($accountId)['locations'] ?? [];
                $location = $locs[0]['name'] ?? null;
                if (!$location) throw new \RuntimeException('Aucun établissement trouvé dans ce compte Business Profile.');
                $location = $accountId . '/' . $location; // format complet "accounts/x/locations/y"
            }

            $raw = \GoogleBusinessProfileService::fetchAllReviews($location);
            $stats = $this->upsertReviews($raw);
            $this->flash('success', sprintf(
                'Sync OK, %d insérés, %d mis à jour, %d inchangés.',
                $stats['inserted'], $stats['updated'], $stats['unchanged']
            ));
        } catch (\Throwable $e) {
            $this->flash('error', 'Sync Google échouée : ' . $e->getMessage());
        }
        $this->redirect('/admin/avis');
    }

    public function reply(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/avis');
            return;
        }
        $review = \Database::fetchOne("SELECT * FROM vp_reviews WHERE id = ?", [$id]);
        if (!$review || $review['platform'] !== 'google' || empty($review['external_id'])) {
            $this->flash('error', 'Avis non Google ou external_id manquant, réponse impossible.');
            $this->redirect('/admin/avis');
            return;
        }
        $replyText = trim($_POST['reply_text'] ?? '');
        if ($replyText === '') {
            $this->flash('error', 'La réponse ne peut pas être vide.');
            $this->redirect('/admin/avis');
            return;
        }
        try {
            \GoogleBusinessProfileService::replyToReview($review['external_id'], $replyText);
            \Database::update('vp_reviews', [
                'reply_text' => $replyText,
                'reply_date' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$id]);
            $this->flash('success', 'Réponse postée à Google.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Échec de la réponse Google : ' . $e->getMessage());
        }
        $this->redirect('/admin/avis');
    }

    /**
     * UPSERT des avis Google dans vp_reviews via l'index UNIQUE (platform, external_id).
     */
    private function upsertReviews(array $rawReviews): array
    {
        $inserted = $updated = $unchanged = 0;
        foreach ($rawReviews as $r) {
            $externalId = $r['name'] ?? null; // accounts/x/locations/y/reviews/z
            if (!$externalId) continue;

            // Star rating : Google renvoie "FIVE", "FOUR", … → on mappe
            $starMap = ['ONE' => 1, 'TWO' => 2, 'THREE' => 3, 'FOUR' => 4, 'FIVE' => 5];
            $rating  = $starMap[$r['starRating'] ?? ''] ?? 5;

            $author     = $r['reviewer']['displayName'] ?? '(anonyme)';
            $content    = $r['comment'] ?? '';
            $createTime = $r['createTime'] ?? null;
            $updateTime = $r['updateTime'] ?? null;
            $reviewDate = $createTime ? substr($createTime, 0, 10) : null;
            $reply      = $r['reviewReply']['comment'] ?? null;
            $replyDate  = $r['reviewReply']['updateTime'] ?? null;
            $lang       = $this->detectLang($content);

            // Existe déjà ?
            $existing = \Database::fetchOne(
                "SELECT id, content, rating, reply_text FROM vp_reviews WHERE platform = 'google' AND external_id = ?",
                [$externalId]
            );

            $data = [
                'platform'    => 'google',
                'external_id' => $externalId,
                'author'      => mb_substr($author, 0, 100),
                'origin'      => 'Google',
                'content'     => $content,
                'rating'      => $rating,
                'review_date' => $reviewDate,
                'language'    => $lang,
                'reply_text'  => $reply,
                'reply_date'  => $replyDate ? str_replace(['T', 'Z'], [' ', ''], substr($replyDate, 0, 19)) : null,
                'synced_at'   => date('Y-m-d H:i:s'),
                'status'      => 'published',
            ];

            if ($existing) {
                if ($existing['content'] === $content
                    && (int)$existing['rating'] === $rating
                    && ($existing['reply_text'] ?? null) === ($reply ?? null)
                ) {
                    \Database::update('vp_reviews', ['synced_at' => date('Y-m-d H:i:s')], 'id = ?', [$existing['id']]);
                    $unchanged++;
                } else {
                    \Database::update('vp_reviews', $data, 'id = ?', [$existing['id']]);
                    $updated++;
                }
            } else {
                $data['offer'] = 'both'; // défaut → Jorge ajustera à la main
                \Database::insert('vp_reviews', $data);
                $inserted++;
            }
        }
        return compact('inserted', 'updated', 'unchanged');
    }

    /** Détection langue rudimentaire (3 langues suffisent pour Villa Plaisance). */
    private function detectLang(string $text): ?string
    {
        if ($text === '') return null;
        $t = mb_strtolower($text);
        $en = preg_match_all('/\b(the|and|with|stayed|breakfast|amazing|wonderful)\b/u', $t);
        $es = preg_match_all('/\b(que|para|muy|encantador|estancia|desayuno)\b/u', $t);
        $fr = preg_match_all('/\b(le|la|les|nous|très|séjour|petit-déjeuner|merveilleux)\b/u', $t);
        $max = max($en, $es, $fr);
        if ($max === 0) return null;
        return $max === $en ? 'en' : ($max === $es ? 'es' : 'fr');
    }
}
