<?php
/** @var array $message @var array $ipInfo @var string $csrf */

$initials = (function (string $name): string {
    $name = trim($name);
    if ($name === '') return '?';
    $parts = preg_split('/\s+/', $name);
    $a = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1));
    $b = isset($parts[1]) ? mb_strtoupper(mb_substr($parts[1], 0, 1)) : '';
    return $a . $b;
})($message['name'] ?? '');

$ts = strtotime($message['created_at']);
$ago = (function () use ($ts): string {
    if (!$ts) return '';
    $diff = time() - $ts;
    if ($diff < 60) return 'à l’instant';
    if ($diff < 3600) return 'il y a ' . floor($diff / 60) . ' min';
    if ($diff < 86400) return 'il y a ' . floor($diff / 3600) . ' h';
    if ($diff < 86400 * 7) return 'il y a ' . floor($diff / 86400) . ' j';
    return date('d/m/Y', $ts);
})();

$dayLabels = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
$monthLabels = [1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
$datePretty = $ts ? sprintf('%s %d %s %d à %s', $dayLabels[(int) date('w', $ts)], (int) date('j', $ts), $monthLabels[(int) date('n', $ts)], (int) date('Y', $ts), date('H\hi', $ts)) : '';
?>

<div class="message-shell">

    <header class="message-topbar">
        <a href="/admin/messages" class="message-back">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            <span>Tous les messages</span>
        </a>
        <div class="message-topbar-right">
            <span class="badge <?= empty($message['read_at']) ? 'badge-warning' : 'badge-success' ?>">
                <?= empty($message['read_at']) ? 'Non lu' : 'Lu' ?>
            </span>
        </div>
    </header>

    <div class="message-layout">

        <!-- ═══ Zone principale ═══ -->
        <article class="message-main">
            <div class="message-card">
                <div class="message-card-head">
                    <div class="message-avatar"><?= htmlspecialchars($initials) ?></div>
                    <div class="message-from">
                        <div class="message-from-name"><?= htmlspecialchars($message['name'] ?? '(anonyme)') ?></div>
                        <a class="message-from-email" href="mailto:<?= htmlspecialchars($message['email'] ?? '') ?>"><?= htmlspecialchars($message['email'] ?? '') ?></a>
                    </div>
                    <div class="message-date" title="<?= htmlspecialchars(date('Y-m-d H:i:s', $ts)) ?>"><?= htmlspecialchars($ago) ?></div>
                </div>

                <h1 class="message-subject"><?= htmlspecialchars($message['subject'] ?: '(sans sujet)') ?></h1>

                <div class="message-body"><?= nl2br(htmlspecialchars($message['message'] ?? '')) ?></div>

                <footer class="message-actions">
                    <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=<?= rawurlencode('Re: ' . ($message['subject'] ?? 'Villa Plaisance')) ?>" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 11 22 2 13 21 11 13 3 11"/></svg>
                        Répondre par email
                    </a>
                    <form method="POST" action="/admin/messages/<?= (int) $message['id'] ?>/delete" onsubmit="return confirm('Supprimer définitivement ce message ?')" class="message-action-inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <button type="submit" class="btn btn-danger">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            Supprimer
                        </button>
                    </form>
                </footer>
            </div>
        </article>

        <!-- ═══ Sidebar info ═══ -->
        <aside class="message-sidebar">

            <!-- Bloc Réception -->
            <section class="info-card">
                <h3 class="info-card-title">Réception</h3>
                <dl class="info-list">
                    <div class="info-row">
                        <dt>Date</dt>
                        <dd><?= htmlspecialchars($datePretty) ?></dd>
                    </div>
                    <div class="info-row">
                        <dt>Langue</dt>
                        <dd><span class="badge badge-meta"><?= htmlspecialchars(strtoupper($message['lang'] ?? 'fr')) ?></span></dd>
                    </div>
                    <?php if (!empty($message['source'])): ?>
                    <div class="info-row">
                        <dt>Source</dt>
                        <dd><span class="badge badge-info"><?= htmlspecialchars($message['source']) ?></span></dd>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <dt>Statut</dt>
                        <dd>
                            <?php if (empty($message['read_at'])): ?>
                                <span class="badge badge-warning">Non lu</span>
                            <?php else: ?>
                                <span class="badge badge-success">Lu</span>
                                <span class="text-muted text-sm">le <?= date('d/m/Y H\hi', strtotime($message['read_at'])) ?></span>
                            <?php endif; ?>
                        </dd>
                    </div>
                </dl>
            </section>

            <!-- Bloc Origine (IP enrichie) -->
            <section class="info-card">
                <h3 class="info-card-title">
                    Origine
                    <?php if (!empty($ipInfo['country_flag'])): ?>
                        <span class="info-card-flag"><?= $ipInfo['country_flag'] ?></span>
                    <?php endif; ?>
                </h3>

                <?php if (!empty($ipInfo['available'])): ?>
                <dl class="info-list">
                    <?php if (!empty($ipInfo['ip'])): ?>
                    <div class="info-row">
                        <dt>Adresse IP</dt>
                        <dd><code class="info-mono"><?= htmlspecialchars($ipInfo['ip']) ?></code></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($ipInfo['country_name'])): ?>
                    <div class="info-row">
                        <dt>Pays</dt>
                        <dd><?= htmlspecialchars($ipInfo['country_name']) ?> <span class="text-muted">(<?= htmlspecialchars($ipInfo['country_code']) ?>)</span></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($ipInfo['region']) || !empty($ipInfo['city'])): ?>
                    <div class="info-row">
                        <dt>Lieu</dt>
                        <dd>
                            <?= htmlspecialchars(trim(($ipInfo['city'] ?? '') . (!empty($ipInfo['city']) && !empty($ipInfo['region']) ? ', ' : '') . ($ipInfo['region'] ?? ''))) ?>
                            <?php if (!empty($ipInfo['postal'])): ?> <span class="text-muted">(<?= htmlspecialchars($ipInfo['postal']) ?>)</span><?php endif; ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($ipInfo['timezone'])): ?>
                    <div class="info-row">
                        <dt>Fuseau</dt>
                        <dd><?= htmlspecialchars($ipInfo['timezone']) ?>
                            <?php if (!empty($ipInfo['utc_offset'])): ?> <span class="text-muted">(UTC <?= htmlspecialchars($ipInfo['utc_offset']) ?>)</span><?php endif; ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($ipInfo['org'])): ?>
                    <div class="info-row">
                        <dt>FAI</dt>
                        <dd><?= htmlspecialchars($ipInfo['org']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($ipInfo['asn'])): ?>
                    <div class="info-row">
                        <dt>Réseau</dt>
                        <dd><code class="info-mono"><?= htmlspecialchars($ipInfo['asn']) ?></code>
                            <?php if (!empty($ipInfo['network'])): ?><br><span class="text-muted text-sm"><?= htmlspecialchars($ipInfo['network']) ?></span><?php endif; ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($ipInfo['languages'])): ?>
                    <div class="info-row">
                        <dt>Langues locales</dt>
                        <dd class="text-muted text-sm"><?= htmlspecialchars($ipInfo['languages']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($ipInfo['currency'])): ?>
                    <div class="info-row">
                        <dt>Devise</dt>
                        <dd><?= htmlspecialchars($ipInfo['currency']) ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>

                <?php if (!empty($ipInfo['map_url'])): ?>
                <a href="<?= htmlspecialchars($ipInfo['map_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm info-card-cta">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Voir sur la carte
                </a>
                <?php endif; ?>

                <p class="info-card-note">Géolocalisation indicative (précision à la ville, peut différer du lieu réel — VPN, mobile en itinérance, etc.).</p>

                <?php else: ?>
                <p class="info-card-empty">
                    <?php if (!empty($message['ip'])): ?>
                        <code class="info-mono"><?= htmlspecialchars($message['ip']) ?></code><br>
                    <?php endif; ?>
                    <span class="text-muted text-sm"><?= htmlspecialchars($ipInfo['note'] ?? 'IP non renseignée.') ?></span>
                </p>
                <?php endif; ?>
            </section>

        </aside>
    </div>
</div>
