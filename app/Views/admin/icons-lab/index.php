<?php
/**
 * @var string $csrf
 * @var array  $labels         array de [label, source, count]
 * @var array  $dbMapping      map label-lowercase => icon_name (chaîne, '' = volontairement vide)
 * @var array  $autoMatches    map label => match auto regex (string | null)
 * @var array  $availableIcons liste triée des noms d'icônes dispo dans le sprite
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
  .icons-lab-grid .icon-card { background: #fff; border: 1px solid #DCD0B7; padding: 14px; border-radius: 4px; text-align: center; }
  .icons-lab-grid .icon-card svg { color: #A55C33; }
  .icons-lab-grid .icon-card .name { font-size: 11px; font-family: monospace; color: #1F1C16; margin-top: 8px; word-break: break-word; }

  .icons-lab-actions { position: sticky; bottom: 0; background: #fff; padding: 16px; border: 1px solid #DCD0B7; border-radius: 4px; margin-top: 24px; display: flex; gap: 12px; align-items: center; box-shadow: 0 -4px 12px rgba(0,0,0,0.05); }
  .icons-lab-actions .btn-primary { background: #A55C33; color: #fff; padding: 10px 24px; border: none; border-radius: 3px; cursor: pointer; font-size: 14px; }
  .icons-lab-actions .hint { color: #888; font-size: 12px; }

  .icons-lab-section-title { margin: 32px 0 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.15em; color: #888; }
</style>

<div class="page-header">
  <h1>Icônes — Lab</h1>
  <p style="color:#888;font-size:14px;margin:6px 0 0;">
    Audit + override de la correspondance libellé → icône.
    Le mapping enregistré ici prend toujours le pas sur le match auto par mots-clés.
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
    <div class="num"><?= count($availableIcons) ?></div>
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
        <th style="width: 240px;">Choix admin</th>
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
        $overrideValue = $hasOverride ? $dbMapping[$key] : null; // string|''|null

        // Valeur effective : override si présent (string ou '' = none), sinon auto regex.
        $final = $hasOverride ? ($overrideValue !== '' ? $overrideValue : null) : $autoMatch;

        // Valeur du select : 'auto' (par défaut) | 'none' (override vide) | nom d'icône
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
              Auto (regex)<?= $autoMatch ? " → $autoMatch" : ' → aucune' ?>
            </option>
            <option value="none" <?= $selectVal === 'none' ? 'selected' : '' ?>>
              (forcer : aucune icône)
            </option>
            <optgroup label="Icônes disponibles">
              <?php foreach ($availableIcons as $icon): ?>
                <option value="<?= htmlspecialchars($icon) ?>" <?= $selectVal === $icon ? 'selected' : '' ?>>
                  <?= htmlspecialchars($icon) ?>
                </option>
              <?php endforeach; ?>
            </optgroup>
          </select>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="icons-lab-actions">
    <button type="submit" class="btn-primary">Enregistrer les overrides</button>
    <span class="hint">
      « Auto » revient au match par mots-clés. « (forcer : aucune icône) » masque l'icône même si le regex aurait matché.
    </span>
  </div>
</form>

<h2 class="icons-lab-section-title">Toutes les icônes du sprite (<?= count($availableIcons) ?>)</h2>
<div class="icons-lab-grid">
  <?php foreach ($availableIcons as $icon): ?>
  <div class="icon-card">
    <?= \IconService::svg($icon, 32) ?>
    <div class="name"><?= htmlspecialchars($icon) ?></div>
  </div>
  <?php endforeach; ?>
</div>
