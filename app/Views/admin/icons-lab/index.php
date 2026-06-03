<?php
/**
 * @var string $csrf
 * @var array  $labels         array de [label, source, count]
 * @var array  $dbMapping      map label-lowercase => icon_name (chaîne, '' = volontairement vide)
 * @var array  $autoMatches    map label => match auto regex (string | null)
 * @var array  $availableIcons liste triée des noms d'icônes du sprite custom
 * @var int    $totalLabels
 * @var int    $mappedCount
 * @var int    $autoOnlyCount
 */

$sourceLabels = [
    'pill_chambre' => 'Pill équipement',
    'pill_solid'   => 'Pill solide',
    'sous_titre'   => 'Sous-titre',
    'contact'      => 'Contact',
    'reviews'      => 'Avis',
];

$lucideCatalog = \IconService::LUCIDE_CATALOG;
$availableLucide = \IconService::availableLucide(); // tous préfixés 'lc-'

function vp_icon_preview(?string $name, int $size = 18): string {
    if ($name === null || $name === '') {
        return '<span style="color:#999;font-size:11px;">—</span>';
    }
    return \IconService::svg($name, $size, 'icons-lab-preview');
}
?>
<style>
  .icons-lab-stats { display: flex; gap: 16px; margin-bottom: 24px; }
  .icons-lab-stat { background: #fff; border: 1px solid #DCD0B7; padding: 14px 20px; border-radius: 4px; flex: 1; }
  .icons-lab-stat .num { font-size: 28px; font-weight: 500; color: #1F1C16; line-height: 1; }
  .icons-lab-stat .lbl { font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; color: #888; margin-top: 6px; }

  .icons-lab-table { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #DCD0B7; }
  .icons-lab-table thead th { background: #f6f1e5; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; color: #1F1C16; border-bottom: 1px solid #DCD0B7; }
  .icons-lab-table tbody td { padding: 10px 12px; border-bottom: 1px solid #f0e8d8; vertical-align: middle; font-size: 14px; }
  .icons-lab-table tbody tr:hover { background: #fafafa; }
  .icons-lab-table .preview-cell { width: 56px; text-align: center; color: #A55C33; }
  .icons-lab-table .source-cell { width: 130px; }
  .icons-lab-table .source-tag { display: inline-block; font-size: 10px; padding: 2px 8px; border: 1px solid #DCD0B7; border-radius: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: #888; }
  .icons-lab-table select { width: 100%; padding: 6px 8px; border: 1px solid #DCD0B7; border-radius: 3px; font-size: 13px; }
  .icons-lab-table .label-cell { font-weight: 500; }
  .icons-lab-table .count-cell { width: 40px; text-align: center; color: #888; font-size: 12px; }

  .icons-lab-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; margin-top: 16px; }
  .icons-lab-grid .icon-card { background: #fff; border: 1px solid #DCD0B7; padding: 14px; border-radius: 4px; text-align: center; transition: border-color .15s, transform .15s; }
  .icons-lab-grid .icon-card:hover { border-color: #A55C33; transform: translateY(-1px); }
  .icons-lab-grid .icon-card svg { color: #A55C33; }
  .icons-lab-grid .icon-card .name { font-size: 11px; font-family: monospace; color: #1F1C16; margin-top: 8px; word-break: break-word; cursor: copy; user-select: all; }

  .icons-lab-actions { position: sticky; bottom: 0; background: #fff; padding: 16px; border: 1px solid #DCD0B7; border-radius: 4px; margin-top: 24px; display: flex; gap: 12px; align-items: center; box-shadow: 0 -4px 12px rgba(0,0,0,0.05); }
  .icons-lab-actions .btn-primary { background: #A55C33; color: #fff; padding: 10px 24px; border: none; border-radius: 3px; cursor: pointer; font-size: 14px; }
  .icons-lab-actions .hint { color: #888; font-size: 12px; }

  .icons-lab-section-title { margin: 40px 0 6px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.15em; color: #1F1C16; }
  .icons-lab-section-sub { color: #888; font-size: 13px; margin: 0 0 16px; }

  .icons-lab-search { width: 100%; max-width: 360px; padding: 10px 14px; border: 1px solid #DCD0B7; border-radius: 3px; font-size: 14px; margin: 0 0 16px; }
  .icons-lab-cat-title { margin: 24px 0 8px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.15em; color: #A55C33; font-weight: 500; }
</style>

<div class="page-header">
  <h1>Icônes — Lab</h1>
  <p style="color:#888;font-size:14px;margin:6px 0 0;">
    Audit + override de la correspondance libellé → icône.
    Le mapping enregistré ici prend toujours le pas sur le match auto par mots-clés.
    Tu peux choisir une icône custom (sprite Villa) ou Lucide (préfixe <code>lc-</code>).
  </p>
</div>

<div class="icons-lab-stats">
  <div class="icons-lab-stat">
    <div class="num"><?= (int) $totalLabels ?></div>
    <div class="lbl">Libellés détectés</div>
  </div>
  <div class="icons-lab-stat">
    <div class="num"><?= (int) $mappedCount ?></div>
    <div class="lbl">Override admin</div>
  </div>
  <div class="icons-lab-stat">
    <div class="num"><?= (int) $autoOnlyCount ?></div>
    <div class="lbl">Auto seulement</div>
  </div>
  <div class="icons-lab-stat">
    <div class="num"><?= count($availableIcons) + count($availableLucide) ?></div>
    <div class="lbl">Icônes dispo</div>
  </div>
</div>

<form method="POST" action="/admin/icons-lab/save">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

  <table class="icons-lab-table">
    <thead>
      <tr>
        <th>Libellé</th>
        <th class="source-cell">Source</th>
        <th class="count-cell" title="Nombre de fois trouvé">#</th>
        <th class="preview-cell">Auto</th>
        <th class="preview-cell">Final</th>
        <th style="width: 280px;">Choix admin</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($labels as $row):
        $label = $row['label'];
        $source = $row['source'];
        $count = $row['count'];
        $key = mb_strtolower($label, 'UTF-8');
        $autoMatch = $autoMatches[$label] ?? null;
        $hasOverride = array_key_exists($key, $dbMapping);
        $overrideValue = $hasOverride ? $dbMapping[$key] : null;
        $final = $hasOverride ? ($overrideValue !== '' ? $overrideValue : null) : $autoMatch;

        $selectVal = 'auto';
        if ($hasOverride) {
            $selectVal = $overrideValue === '' ? 'none' : $overrideValue;
        }
      ?>
      <tr>
        <td class="label-cell"><?= htmlspecialchars($label) ?></td>
        <td class="source-cell">
          <span class="source-tag"><?= htmlspecialchars($sourceLabels[$source] ?? $source) ?></span>
        </td>
        <td class="count-cell"><?= (int) $count ?></td>
        <td class="preview-cell" title="<?= htmlspecialchars($autoMatch ?? 'aucun match auto') ?>">
          <?= vp_icon_preview($autoMatch) ?>
        </td>
        <td class="preview-cell" title="<?= htmlspecialchars($final ?? 'aucune icône') ?>">
          <?= vp_icon_preview($final) ?>
        </td>
        <td>
          <select name="mapping[<?= htmlspecialchars($label) ?>]">
            <option value="auto" <?= $selectVal === 'auto' ? 'selected' : '' ?>>
              Auto<?= $autoMatch ? " → $autoMatch" : ' (rien)' ?>
            </option>
            <option value="none" <?= $selectVal === 'none' ? 'selected' : '' ?>>(forcer : aucune)</option>
            <optgroup label="Sprite Villa Plaisance">
              <?php foreach ($availableIcons as $icon): ?>
                <option value="<?= htmlspecialchars($icon) ?>" <?= $selectVal === $icon ? 'selected' : '' ?>>
                  <?= htmlspecialchars($icon) ?>
                </option>
              <?php endforeach; ?>
            </optgroup>
            <?php foreach ($lucideCatalog as $cat): ?>
            <optgroup label="Lucide · <?= htmlspecialchars($cat['label']) ?>">
              <?php foreach ($cat['icons'] as $icon):
                $value = 'lc-' . $icon;
              ?>
                <option value="<?= htmlspecialchars($value) ?>" <?= $selectVal === $value ? 'selected' : '' ?>>
                  <?= htmlspecialchars($icon) ?>
                </option>
              <?php endforeach; ?>
            </optgroup>
            <?php endforeach; ?>
          </select>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="icons-lab-actions">
    <button type="submit" class="btn-primary">Enregistrer les overrides</button>
    <span class="hint">
      « Auto » revient au match par mots-clés. « (forcer : aucune) » masque l'icône même si le regex aurait matché.
    </span>
  </div>
</form>

<h2 class="icons-lab-section-title">Bibliothèque Lucide tourisme (<?= count($availableLucide) ?>)</h2>
<p class="icons-lab-section-sub">
  Set MIT/ISC dérivé de <a href="https://lucide.dev" target="_blank">lucide.dev</a>,
  curé pour Villa Plaisance. Pour utiliser dans un libellé : copie le nom avec le préfixe
  <code>lc-</code> (ex. <code>lc-bed-double</code>) et colle-le dans le champ « Choix admin » ci-dessus,
  ou choisis directement depuis le dropdown.
</p>

<input type="text" id="lucide-search" class="icons-lab-search" placeholder="Filtrer (ex : lit, wifi, sun…)" autocomplete="off">

<div id="lucide-categories">
  <?php foreach ($lucideCatalog as $catKey => $cat): ?>
  <div class="icons-lab-cat-block" data-cat="<?= htmlspecialchars($catKey) ?>">
    <div class="icons-lab-cat-title"><?= htmlspecialchars($cat['label']) ?> (<?= count($cat['icons']) ?>)</div>
    <div class="icons-lab-grid">
      <?php foreach ($cat['icons'] as $icon):
        $fullName = 'lc-' . $icon;
      ?>
      <div class="icon-card" data-name="<?= htmlspecialchars($icon) ?>">
        <?= \IconService::svg($fullName, 32) ?>
        <div class="name" title="Clique pour copier"><?= htmlspecialchars($fullName) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<h2 class="icons-lab-section-title">Sprite Villa Plaisance (<?= count($availableIcons) ?>)</h2>
<p class="icons-lab-section-sub">
  Icônes thématiques spécifiques (vignoble, aqueduc, plateformes avis, etc.).
  Préfixe d'usage : nom seul, sans <code>lc-</code>.
</p>
<div class="icons-lab-grid">
  <?php foreach ($availableIcons as $icon): ?>
  <div class="icon-card" data-name="<?= htmlspecialchars($icon) ?>">
    <?= \IconService::svg($icon, 32) ?>
    <div class="name" title="Clique pour copier"><?= htmlspecialchars($icon) ?></div>
  </div>
  <?php endforeach; ?>
</div>

<script>
(function () {
    // Recherche live sur la bibliothèque Lucide (filtrage par nom).
    var input = document.getElementById('lucide-search');
    var blocks = document.querySelectorAll('#lucide-categories .icons-lab-cat-block');
    if (!input || !blocks.length) return;

    input.addEventListener('input', function () {
        var q = input.value.trim().toLowerCase();
        blocks.forEach(function (block) {
            var cards = block.querySelectorAll('.icon-card');
            var visibleCount = 0;
            cards.forEach(function (card) {
                var name = (card.getAttribute('data-name') || '').toLowerCase();
                var visible = q === '' || name.indexOf(q) !== -1;
                card.style.display = visible ? '' : 'none';
                if (visible) visibleCount++;
            });
            block.style.display = visibleCount === 0 ? 'none' : '';
        });
    });

    // Click sur un nom d'icône = sélection + copie auto.
    document.querySelectorAll('.icons-lab-grid .icon-card .name').forEach(function (el) {
        el.addEventListener('click', function () {
            var range = document.createRange();
            range.selectNodeContents(el);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            try {
                document.execCommand('copy');
                var oldTitle = el.title;
                el.title = 'Copié !';
                setTimeout(function () { el.title = oldTitle; }, 1200);
            } catch (e) {}
        });
    });
})();
</script>
