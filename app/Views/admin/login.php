<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="fr" data-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion, Villa Plaisance Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <style>
        body.login-page {
            font-family: var(--font-sans);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }
        .login-shell {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }
        .login-brand {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
            text-align: center;
        }
        .login-brand .kicker {
            font-family: var(--font-mono);
            font-size: 10.5px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--text-3);
            margin: 0;
        }
        .login-brand .title {
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.01em;
            color: var(--text);
            margin: 0;
        }
        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--admin-radius);
            padding: clamp(24px, 4vw, 36px);
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .login-card .form-group { margin: 0; }
        .password-wrapper {
            position: relative;
            display: block;
        }
        .password-wrapper input {
            width: 100%;
            padding-right: 44px;
        }
        .toggle-password {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: 0;
            cursor: pointer;
            padding: 8px;
            color: var(--text-2);
            display: grid;
            place-items: center;
            border-radius: 4px;
            transition: color .15s, background .15s;
        }
        .toggle-password:hover { color: var(--text); background: var(--surface-2); }
        .toggle-password:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        .login-card .btn-primary {
            width: 100%;
            margin-top: 4px;
            justify-content: center;
        }
        .forgot-link {
            text-align: center;
            margin: 4px 0 0;
            font-size: 13px;
        }
        .forgot-link a {
            color: var(--text-2);
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: color .15s, border-color .15s;
        }
        .forgot-link a:hover {
            color: var(--text);
            border-bottom-color: var(--text-2);
        }
        .login-back {
            text-align: center;
            margin: 0;
            font-family: var(--font-mono);
            font-size: 10.5px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }
        .login-back a {
            color: var(--text-3);
            text-decoration: none;
            transition: color .15s;
        }
        .login-back a:hover { color: var(--text-2); }
    </style>
</head>
<body class="login-page">
    <main class="login-shell">

        <div class="login-brand">
            <p class="kicker">Espace administration</p>
            <h1 class="title">Villa Plaisance</h1>
        </div>

        <div class="login-card">
            <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-error" role="alert"><?= htmlspecialchars($flash['error']) ?></div>
            <?php endif; ?>
            <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success" role="status"><?= htmlspecialchars($flash['success']) ?></div>
            <?php endif; ?>

            <form method="POST" action="/admin/login" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autocomplete="email" autofocus
                           placeholder="contact@villaplaisance.fr">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••">
                        <button type="button" class="toggle-password" aria-label="Afficher le mot de passe" aria-pressed="false">
                            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="display:none" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Se connecter</button>

                <p class="forgot-link"><a href="/admin/forgot-password">Mot de passe oublié&nbsp;?</a></p>
            </form>
        </div>

        <p class="login-back"><a href="/">&larr; Retour au site</a></p>

    </main>

    <script>
    (function () {
        var btn = document.querySelector('.toggle-password');
        if (!btn) return;
        var input = document.getElementById('password');
        var open = btn.querySelector('.eye-open');
        var closed = btn.querySelector('.eye-closed');
        btn.addEventListener('click', function () {
            var shown = input.type === 'text';
            input.type = shown ? 'password' : 'text';
            open.style.display = shown ? 'block' : 'none';
            closed.style.display = shown ? 'none' : 'block';
            btn.setAttribute('aria-pressed', shown ? 'false' : 'true');
            btn.setAttribute('aria-label', shown ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
        });
    })();
    </script>
</body>
</html>
