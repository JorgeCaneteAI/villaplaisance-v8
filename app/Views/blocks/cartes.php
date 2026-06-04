<?php
/**
 * Bloc V8 `cartes`, affiche les chambres lues depuis vp_pieces.
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
// MODE B&B, 1 section par chambre, alternance normal/alt selon meta.layout
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
        <?= ImageService::imgFromBg($img, $idx === 0 ? 'big' : 'sm') ?>
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
        <?= ImageService::imgFromBg($img, $idx === 0 ? 'big' : 'sm') ?>
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
// MODE VILLA, pattern éditorial iso BB : 1 grande + 2 vignettes par chambre,
// alternance gauche/droite section par section.
// ─────────────────────────────────────────────────────────────────────────────
if ($offer === 'villa' || $offer === 'both'):
    $villaRooms = array_values(array_filter($rooms, fn($r) => in_array($r['offer'], ['villa', 'both'], true)));
    if (!empty($villaRooms)):
        // Header titre/intro (section indépendante).
        if ($heading !== '' || $intro !== '' || $labelText !== ''):
?>
<section class="section">
  <div class="container-wide">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap: 32px; flex-wrap: wrap;">
      <div>
        <?php if ($labelNumeral !== '' || $labelText !== ''): ?>
        <div class="section-label">
          <span class="numeral"><?= htmlspecialchars($labelNumeral) ?><?= ($labelNumeral !== '' && $labelText !== '') ? ' / ' : '' ?><?= TextService::renderTitle($labelText) ?></span>
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
  </div>
</section>
<?php
        endif;
        // Chiffres romains pour la numérotation des chambres.
        $roman = ['I','II','III','IV','V','VI','VII','VIII'];
        foreach ($villaRooms as $vidx => $room):
            $images = is_string($room['images'] ?? null) ? (json_decode($room['images'], true) ?: []) : [];
            if (empty($images) && !empty($room['image'])) $images = [$room['image']];
            $imgBig = $images[0] ?? '';
            $imgSm1 = $images[1] ?? '';
            $imgSm2 = $images[2] ?? '';
            $pills = $splitPills($room['equip'] ?? null);
            $solidPill = trim((string)($room['note'] ?? ''));
            $isAlt = $vidx % 2 === 1; // alternance gauche/droite
            $bgInline = $isAlt ? ' style="background: var(--linen-100);"' : '';
            $numLabel = $roman[$vidx] ?? (string)($vidx + 1);
?>
<section class="section"<?= $bgInline ?>>
  <div class="container-wide">
    <div class="ch-room<?= $isAlt ? ' alt' : '' ?>">
      <?php if ($isAlt && $imgBig !== ''): ?>
      <div class="ch-room-images">
        <?= ImageService::imgFromBg($imgBig, 'big') ?>
        <?php if ($imgSm1 !== ''): ?><?= ImageService::imgFromBg($imgSm1, 'sm') ?><?php endif; ?>
        <?php if ($imgSm2 !== ''): ?><?= ImageService::imgFromBg($imgSm2, 'sm') ?><?php endif; ?>
      </div>
      <?php endif; ?>
      <div class="ch-room-text">
        <div class="ch-room-num">
          <span><?= $numLabel ?> · <span><?= htmlspecialchars((string)($room['name'] ?? '')) ?></span></span>
          <?php if (!empty($solidPill)): ?>
          <span><?= htmlspecialchars($solidPill) ?></span>
          <?php endif; ?>
        </div>
        <?php if (!empty($room['sous_titre'])): ?>
        <p class="ch-room-tagline"><?= htmlspecialchars(mb_strtoupper($room['sous_titre'], 'UTF-8')) ?></p>
        <?php endif; ?>
        <h2 class="h-xl" style="margin: 0 0 24px;"><em><?= htmlspecialchars((string)($room['name'] ?? '')) ?></em></h2>
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
      <?php if (!$isAlt && $imgBig !== ''): ?>
      <div class="ch-room-images">
        <?= ImageService::imgFromBg($imgBig, 'big') ?>
        <?php if ($imgSm1 !== ''): ?><?= ImageService::imgFromBg($imgSm1, 'sm') ?><?php endif; ?>
        <?php if ($imgSm2 !== ''): ?><?= ImageService::imgFromBg($imgSm2, 'sm') ?><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php
        endforeach;
    endif;
endif;
?>
