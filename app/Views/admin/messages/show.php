<?php /** @var array $message @var string $csrf */ ?>
<div class="page-header">
    <h1>Message reçu</h1>
    <a href="/admin/messages" class="btn">← Tous les messages</a>
</div>

<div class="mail-detail">
    <div class="mail-detail-header">
        <div class="mail-detail-from"><?= htmlspecialchars($message['name'] ?? '(anonyme)') ?></div>
        <div class="mail-detail-email"><?= htmlspecialchars($message['email'] ?? '') ?></div>

        <div class="mail-detail-meta">
            <div>
                <strong>Reçu le</strong>
                <span><?= date('d/m/Y à H:i', strtotime($message['created_at'])) ?></span>
            </div>
            <div>
                <strong>Langue</strong>
                <span><?= htmlspecialchars(strtoupper($message['lang'] ?? 'fr')) ?></span>
            </div>
            <?php if (!empty($message['ip'])): ?>
            <div>
                <strong>IP</strong>
                <span><?= htmlspecialchars($message['ip']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mail-detail-subject-line">
        Sujet
        <strong><?= htmlspecialchars($message['subject'] ?: '(sans sujet)') ?></strong>
    </div>

    <div class="mail-detail-body"><?= htmlspecialchars($message['message'] ?? '') ?></div>

    <div class="mail-detail-actions">
        <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=<?= rawurlencode('Re: ' . ($message['subject'] ?? 'Villa Plaisance')) ?>" class="btn btn-primary">
            ✉ Répondre par email
        </a>
        <form method="POST" action="/admin/messages/<?= (int) $message['id'] ?>/delete" onsubmit="return confirm('Supprimer définitivement ce message ?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
    </div>
</div>
