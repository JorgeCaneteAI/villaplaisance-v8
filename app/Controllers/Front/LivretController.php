<?php
declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class LivretController extends BaseController
{
    public function index(): void
    {
        $lang = \LangService::get();
        $type = $_GET['type'] ?? 'villa';
        if (!in_array($type, ['bb', 'villa'], true)) {
            $type = 'villa';
        }

        // Le livret n'est plus protégé par mot de passe (accès direct, pour les QR).
        // Seul POST restant : le formulaire de message en bas de page.
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
            if (!$this->verifyCsrf()) {
                $this->flash('error', 'Token CSRF invalide.');
                $this->redirect(\LangService::url('livret') . '?type=' . $type);
                return;
            }
            $this->handleMessage($lang, $type);
            return;
        }

        $seo = [
            'title' => t('livret.title') . ', Villa Plaisance',
            'description' => '',
            'canonical' => '',
            'robots' => 'noindex, nofollow',
            'og' => [],
            'hreflang' => [],
        ];
        $jsonLd = [];
        $flash = $this->getFlash();
        $csrf = $this->csrf();

        $sections = [];
        try {
            $sections = \Database::fetchAll(
                "SELECT * FROM vp_livret WHERE type = ? AND lang = ? AND active = 1 ORDER BY position ASC",
                [$type, $lang]
            );
        } catch (\Throwable) {}

        $this->render('front/livret/show', compact('seo', 'flash', 'csrf', 'jsonLd', 'lang', 'type', 'sections'), 'front-proto');
    }

    /**
     * Aperçu public du livret (sans mot de passe). Sert un livret en mode démo,
     * utile pour lien partageable / aperçu commercial. Type par défaut : bb.
     * Marqué noindex pour ne pas être indexé par Google.
     */
    public function preview(): void
    {
        $lang = \LangService::get();
        $type = $_GET['type'] ?? 'bb';
        if (!in_array($type, ['bb', 'villa'], true)) {
            $type = 'bb';
        }

        $sections = [];
        try {
            $sections = \Database::fetchAll(
                "SELECT * FROM vp_livret WHERE type = ? AND lang = ? AND active = 1 ORDER BY position ASC",
                [$type, $lang]
            );
        } catch (\Throwable) {}

        $seo = [
            'title' => t('livret.title') . ', Aperçu, Villa Plaisance',
            'description' => 'Aperçu du livret d\'accueil Villa Plaisance.',
            'canonical' => '',
            'robots' => 'noindex, nofollow',
            'og' => [],
            'hreflang' => [],
        ];
        $jsonLd = [];

        $this->render('front/livret/preview', compact('seo', 'jsonLd', 'lang', 'type', 'sections'), 'front-proto');
    }

    private function handleMessage(string $lang, string $type): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Honeypot
        if (!empty($_POST['website'])) {
            $this->redirect(\LangService::url('livret') . '?type=' . $type);
            return;
        }

        if ($name === '' || $email === '' || $message === '') {
            $this->flash('error', t('contact.form.error'));
            $this->redirect(\LangService::url('livret') . '?type=' . $type);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', t('contact.form.error'));
            $this->redirect(\LangService::url('livret') . '?type=' . $type);
            return;
        }

        try {
            \Database::insert('vp_messages', [
                'name' => $name,
                'email' => $email,
                'subject' => 'Message livret (' . $type . ')',
                'message' => $message,
                'lang' => $lang,
                'source' => 'livret',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                'created_at' => date('Y-m-d H:i:s'),
                'read_at' => null,
            ]);
            $this->flash('success', t('livret.message_success'));
        } catch (\Throwable) {
            $this->flash('error', t('contact.form.error'));
        }

        $this->redirect(\LangService::url('livret') . '?type=' . $type);
    }
}
