<?php
/** @var array $messages @var string $csrf */
$unreadCount = 0;
foreach ($messages as $m) {
    if (empty($m['read_at'])) $unreadCount++;
}
$initialOf = static function(string $name): string {
    $name = trim($name);
    if ($name === '') return '?';
    $parts = preg_split('/\s+/', $name);
    $first = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1));
    $last  = isset($parts[1]) ? mb_strtoupper(mb_substr($parts[1], 0, 1)) : '';
    return $first . $last;
};
$shortDate = static function(string $iso): string {
    $ts = strtotime($iso);
    if (!$ts) return '';
    $today = strtotime('today');
    $yesterday = strtotime('yesterday');
    if ($ts >= $today) return date('H:i', $ts);
    if ($ts >= $yesterday) return 'Hier';
    if ($ts >= strtotime('-6 days')) {
        // strftime() est deprecated PHP 8.1 — mapping FR maison.
        static $days = [0 => 'dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.'];
        return $days[(int) date('w', $ts)];
    }
    if (date('Y', $ts) === date('Y')) return date('d/m', $ts);
    return date('d/m/Y', $ts);
};
$preview = static function(string $body): string {
    $clean = preg_replace('/\s+/', ' ', $body);
    return mb_strimwidth($clean ?? '', 0, 180, '…');
};
?>
<div class="page-header">
    <h1>Messages</h1>
</div>

<?php if (empty($messages)): ?>
<div class="mail-empty">
    <div class="mail-empty-icon">✉︎</div>
    Aucun message reçu pour le moment.
</div>
<?php else: ?>
<div class="mail-toolbar">
    <div class="mail-count">
        <strong><?= count($messages) ?></strong> message<?= count($messages) > 1 ? 's' : '' ?>
        <?php if ($unreadCount > 0): ?>
            · <strong style="color: var(--terra-500)"><?= $unreadCount ?></strong> non lu<?= $unreadCount > 1 ? 's' : '' ?>
        <?php endif; ?>
    </div>
</div>

<div class="mail-list">
    <?php foreach ($messages as $msg): ?>
        <?php $unread = empty($msg['read_at']); ?>
        <a href="/admin/messages/<?= (int) $msg['id'] ?>" class="mail-card <?= $unread ? 'is-unread' : '' ?>">
            <div class="mail-avatar"><?= htmlspecialchars($initialOf($msg['name'] ?? '')) ?></div>
            <div class="mail-body">
                <div class="mail-head">
                    <span class="mail-from"><?= htmlspecialchars($msg['name'] ?? '(anonyme)') ?></span>
                    <span class="mail-email"><?= htmlspecialchars($msg['email'] ?? '') ?></span>
                </div>
                <div class="mail-subject"><?= htmlspecialchars($msg['subject'] ?: '(sans sujet)') ?></div>
                <div class="mail-preview"><?= htmlspecialchars($preview($msg['message'] ?? '')) ?></div>
            </div>
            <div class="mail-meta">
                <span class="mail-date" title="<?= htmlspecialchars(date('d/m/Y H:i', strtotime($msg['created_at']))) ?>">
                    <?= htmlspecialchars($shortDate($msg['created_at'])) ?>
                </span>
                <form method="POST" action="/admin/messages/<?= (int) $msg['id'] ?>/delete" class="mail-delete-form" onclick="event.stopPropagation();" onsubmit="return confirm('Supprimer ce message ?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <button type="submit" class="mail-delete" title="Supprimer">Suppr.</button>
                </form>
            </div>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
