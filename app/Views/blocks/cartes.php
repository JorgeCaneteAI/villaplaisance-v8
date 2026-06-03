<?php
/**
 * Bloc V8 `cartes` — affiche les chambres lues depuis vp_pieces.
 *
 * Dispatch sur le champ `offer` du bloc :
 *   - 'bb'    → markup .ch-room (1 section par chambre, alternance normal/alt)
 *               Utilise vp_pieces.meta.label_a, label_b, layout pour le rendu B&B.
 *   - 'villa' → markup .room-card-x (grille compacte, wrapper section-label + h2 + intro)
 *   - 'both'  → enchaîne les deux modes
 *
 * Variables disponibles (depuis BlockService::renderBlock via extract) :
 *   $heading, $intro, $offer, $label_numeral, $label_text, $surface
 */
$offer = $offer ?? 'bb';
$heading = $heading ?? '';
$intro = $intro ?? '';
$labelNumeral = $label_numeral ?? '';
$labelText = $label_text ?? '';
$lang = LangService::get();

// SQL : filtre `chambre` uniquement (les `espace` se font via un autre bloc)
$where = $offer === 'both'
    ? "offer IN ('bb', 'villa', 'both')"
    : "offer IN (?, 'both')";
$params = $offer === 'both' ? [$lang] : [$offer, $lang];

$rooms = [];
try {
    $rooms = Database::fetchAll(
        "SELECT * FROM vp_pieces WHERE $where AND lang = ? AND type = 'chambre' ORDER BY position ASC",
        $params
    );
} catch (\Throwable) {}
if (empty($rooms)) return;

// Helper : split equip CSV en array de pills propre
$splitPills = static function (?string $equip): array {
    if (!$equip) return [];
    return array_values(array_filter(array_map('trim', explode(',', $equip))));
};

// Helper : split "first | second" pour les pills 2-lignes (ex. "2 lits 90×200 | jumelables")
$pillLines = static function (string $pill): array {
    return array_map('trim', explode('|', $pill, 2));
};

// ─────────────────────────────────────────────────────────────────────────────
// MODE B&B — 1 section par chambre, alternance normal/alt selon meta.layout
// ─────────────────────────────────────────────────────────────────────────────
if ($offer === 'bb' || $offer === 'both'):
    $bbRooms = array_values(array_filter($rooms, fn($r) => in_array($r['offer'], ['bb', 'both'], true)));
    foreach ($bbRooms as $i => $room):
        $meta = is_string($room['meta'] ?? null) ? (json_decode($room['meta'], true) ?: []) : [];
        $images = is_string($room['images'] ?? null) ? (json_decode($room['images'], true) ?: []) : [];
        if (empty($images) && !empty($room['image'])) $images = [$room['image']];

        $pills = $splitPills($room['equip'] ?? null);
        $layout = $meta['layout'] ?? ($i % 2 === 1 ? 'alt' : 'normal');
        $labelA = (string)($meta['label_a'] ?? '');
        $labelB = (string)($meta['label_b'] ?? '');
        $bg = $layout === 'alt' ? 'var(--linen-100)' : 'transparent';
?>
<section class="section" style="background: <?= $bg ?>;">
  <div class="container-wide">
    <div class="ch-room<?= $layout === 'alt' ? ' alt' : '' ?>">
      <?php if ($layout === 'alt'): ?>
      <div class="ch-room-images">
        <?php foreach ($images as $idx => $img): ?>
        <div class="<?= $idx === 0 ? 'big' : 'sm' ?>" style="background-image: url('/uploads/<?= htmlspecialchars($img) ?>')"></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="ch-room-text">
        <?php if ($labelA !== '' || $labelB !== ''): ?>
        <div class="ch-room-num">
          <?php if ($labelA !== ''): ?><span><?= htmlspecialchars($labelA) ?></span><?php endif; ?>
          <?php if ($labelB !== ''): ?><span><?= htmlspecialchars($labelB) ?></span><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($room['sous_titre'])): ?>
        <p class="ch-room-tagline"><?= htmlspecialchars(mb_strtoupper($room['sous_titre'], 'UTF-8')) ?></p>
        <?php endif; ?>
        <h2 class="h-xl" style="margin: 0 0 24px;"><em><?= htmlspecialchars($room['name'] ?? '') ?></em></h2>
        <?php if (!empty($room['description'])): ?>
        <p class="body-lg" style="margin: 0 0 16px;"><?= htmlspecialchars($room['description']) ?></p>
        <?php endif; ?>
        <?php if (!empty($pills)): ?>
        <div class="ch-pills">
          <?php foreach ($pills as $pill): $lines = $pillLines($pill); ?>
          <?php $pillIcon = IconService::pillIcon($lines[0]); ?>
          <span class="pill"><?php if ($pillIcon !== null): ?><?= IconService::svg($pillIcon, 14, 'pill-icon') ?><?php endif; ?><?= htmlspecialchars($lines[0]) ?><?php if (isset($lines[1])): ?> <span style="color: var(--stone-500); margin-left: 4px;"><?= htmlspecialchars($lines[1]) ?></span><?php endif; ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($layout !== 'alt'): ?>
      <div class="ch-room-images">
        <?php foreach ($images as $idx => $img): ?>
        <div class="<?= $idx === 0 ? 'big' : 'sm' ?>" style="background-image: url('/uploads/<?= htmlspecialchars($img) ?>')"></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php
    endforeach;
endif;

// ─────────────────────────────────────────────────────────────────────────────
// MODE VILLA — grille compacte, wrapper section-label + h2 + intro au dessus
// ─────────────────────────────────────────────────────────────────────────────
if ($offer === 'villa' || $offer === 'both'):
    $villaRooms = array_values(array_filter($rooms, fn($r) => in_array($r['offer'], ['villa', 'both'], true)));
    if (!empty($villaRooms)):
?>
<section class="section">
  <div class="container-wide">
    <?php if ($heading !== '' || $intro !== '' || $labelText !== ''): ?>
    <div class="prose-grid" style="align-items: end; gap: 32px; margin: 0 0 28px;">
      <div>
        <?php if ($labelNumeral !== '' || $labelText !== ''): ?>
        <div class="section-label">
          <span class="numeral">— <?= htmlspecialchars($labelNumeral) ?><?= ($labelNumeral !== '' && $labelText !== '') ? ' / ' : '' ?><?= TextService::renderTitle($labelText) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($heading !== ''): ?>
        <h2 class="h-xl" style="margin: 0; max-width: 18ch;"><?= TextService::renderTitle($heading) ?></h2>
        <?php endif; ?>
      </div>
      <?php if ($intro !== ''): ?>
      <p class="body-lg" style="max-width: 38ch; margin: 0;"><?= htmlspecialchars($intro) ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="room-cards">
      <?php foreach ($villaRooms as $room):
        $images = is_string($room['images'] ?? null) ? (json_decode($room['images'], true) ?: []) : [];
        if (empty($images) && !empty($room['image'])) $images = [$room['image']];
        $img = $images[0] ?? '';
        $pills = $splitPills($room['equip'] ?? null);
        $solidPill = trim((string)($room['note'] ?? ''));
      ?>
      <article class="room-card-x">
        <?php if ($img !== ''): ?>
        <div class="img" style="background-image: url('/uploads/<?= htmlspecialchars($img) ?>')"></div>
        <?php endif; ?>
        <div class="head">
          <div class="name"><em><?= htmlspecialchars($room['name'] ?? '') ?></em></div>
          <?php if (!empty($room['sous_titre'])): ?>
          <div class="tagline"><?= htmlspecialchars($room['sous_titre']) ?></div>
          <?php endif; ?>
        </div>
        <?php if (!empty($room['description'])): ?>
        <p class="desc"><?= htmlspecialchars($room['description']) ?></p>
        <?php endif; ?>
        <?php if (!empty($pills) || $solidPill !== ''): ?>
        <div class="pills">
          <?php foreach ($pills as $pill): $lines = $pillLines($pill); ?>
          <?php $pillIcon = IconService::pillIcon($lines[0]); ?>
          <span class="pill"><?php if ($pillIcon !== null): ?><?= IconService::svg($pillIcon, 14, 'pill-icon') ?><?php endif; ?><?= htmlspecialchars($lines[0]) ?><?php if (isset($lines[1])): ?> <span style="color: var(--stone-500); margin-left: 4px;"><?= htmlspecialchars($lines[1]) ?></span><?php endif; ?></span>
          <?php endforeach; ?>
          <?php if ($solidPill !== ''): ?>
          <span class="pill solid"><?= htmlspecialchars($solidPill) ?></span>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php
    endif;
endif;
?>
