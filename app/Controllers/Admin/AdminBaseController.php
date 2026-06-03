<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

class AdminBaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $flash = $this->getFlash();

        ob_start();
        require ROOT . '/app/Views/' . $view . '.php';
        $content = ob_get_clean();

        require ROOT . '/app/Views/layouts/admin.php';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function csrf(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function getFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Rate limiting par IP + action stocké en SESSION.
     * Dupliqué depuis BaseController car AdminBaseController est volontairement
     * indépendant (deux scopes distincts admin/front).
     */
    protected function checkRateLimit(string $action, int $maxAttempts = 5, int $windowSeconds = 3600): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = 'rate_' . $action . '_' . md5($ip);

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'reset_at' => time() + $windowSeconds];
        }

        if (time() > $_SESSION[$key]['reset_at']) {
            $_SESSION[$key] = ['count' => 0, 'reset_at' => time() + $windowSeconds];
        }

        $_SESSION[$key]['count']++;
        return $_SESSION[$key]['count'] <= $maxAttempts;
    }
}
