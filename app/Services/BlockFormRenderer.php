<?php
declare(strict_types=1);

/**
 * BlockFormRenderer — rend le formulaire d'édition d'un bloc V8 dans l'admin.
 *
 * Méthodes statiques récursives qui consomment les définitions de
 * BlockFieldsService et produisent les inputs HTML adaptés à chaque type.
 *
 * Types de champ supportés :
 *   text, textarea, select, bool, int, media_id, repeater
 *
 * Pour les repeaters, deux sous-formats :
 *   - 'item_type' => 'string' | 'int'  → liste plate de scalaires
 *   - 'item_fields' => [...]            → liste d'objets, chaque item rend ses
 *                                         propres sub-fields (récursivement —
 *                                         supporte les sub-repeaters de profondeur 2)
 *
 * Naming pattern HTML :
 *   - Champ simple top-level : fields[image_id]
 *   - Item de repeater (objet) : fields[items][0][title]
 *   - Item de repeater (scalaire) : fields[tags][0]
 *   - Sub-repeater (cta dans formula) : fields[formulas][0][cta][0][label]
 *
 * Côté SectionController::save(), PHP désérialise tout ça en arrays nested.
 * Les indices peuvent contenir des trous (après suppression d'un item côté
 * JS sans re-numérotation) — le save() côté serveur re-séquence et caste
 * récursivement selon les item_fields.
 */
class BlockFormRenderer
{
    /**
     * Rend un champ unique (label + input adapté au type) + son help text.
     *
     * @param array  $field       Définition du champ (cf. BlockFieldsService)
     * @param array  $content     Le tableau contenant la valeur (e.g. tout le JSON du bloc)
     * @param string $namePrefix  Préfixe HTML pour le name (e.g. "fields" ou "fields[items][0]")
     * @param string $idPrefix    Préfixe pour l'id HTML (e.g. "f" ou "f-items-0")
     */
    public static function renderField(array $field, array $content, string $namePrefix = 'fields', string $idPrefix = 'f'): void
    {
        $name = (string)($field['name'] ?? '');
        $type = (string)($field['type'] ?? 'text');
        $label = (string)($field['label'] ?? $name);
        $help = (string)($field['help'] ?? '');
        $value = $content[$name] ?? $field['default'] ?? '';
        $inputName = $namePrefix . '[' . $name . ']';
        $inputId = $idPrefix . '-' . self::slug($name);

        echo '<div class="form-group" data-field="' . htmlspecialchars($name) . '" data-type="' . htmlspecialchars($type) . '">';
        echo '<label for="' . htmlspecialchars($inputId) . '">' . htmlspecialchars($label) . '</label>';

        switch ($type) {
            case 'text':
                self::renderText($inputName, $inputId, $value);
                break;
            case 'textarea':
                self::renderTextarea($inputName, $inputId, $value);
                break;
            case 'select':
                self::renderSelect($inputName, $inputId, $value, $field['options'] ?? []);
                break;
            case 'bool':
                self::renderBool($inputName, $inputId, (bool)$value);
                break;
            case 'int':
                self::renderInt($inputName, $inputId, $value);
                break;
            case 'media_id':
                self::renderMediaId($inputName, $inputId, $value);
                break;
            case 'repeater':
                self::renderRepeater($field, is_array($value) ? $value : [], $inputName, $inputId);
                break;
            default:
                self::renderText($inputName, $inputId, $value);
        }

        if ($help !== '') {
            echo '<p class="text-sm text-muted" style="margin-top: 4px;">' . htmlspecialchars($help) . '</p>';
        }
        echo '</div>';
    }

    // ───────────────────────────────────────────────────────────
    // Champs simples
    // ───────────────────────────────────────────────────────────

    private static function renderText(string $name, string $id, $value): void
    {
        printf(
            '<input type="text" id="%s" name="%s" value="%s">',
            htmlspecialchars($id),
            htmlspecialchars($name),
            htmlspecialchars(is_scalar($value) ? (string)$value : '')
        );
    }

    private static function renderTextarea(string $name, string $id, $value, int $rows = 4): void
    {
        printf(
            '<textarea id="%s" name="%s" rows="%d">%s</textarea>',
            htmlspecialchars($id),
            htmlspecialchars($name),
            $rows,
            htmlspecialchars(is_scalar($value) ? (string)$value : '')
        );
    }

    private static function renderSelect(string $name, string $id, $value, array $options): void
    {
        echo '<select id="' . htmlspecialchars($id) . '" name="' . htmlspecialchars($name) . '">';
        $cur = (string)(is_scalar($value) ? $value : '');
        foreach ($options as $key => $optLabel) {
            $k = (string)$key;
            echo '<option value="' . htmlspecialchars($k) . '"' . ($k === $cur ? ' selected' : '') . '>' . htmlspecialchars($optLabel) . '</option>';
        }
        echo '</select>';
    }

    private static function renderBool(string $name, string $id, bool $value): void
    {
        echo '<label style="display:flex; align-items:center; gap:8px; cursor:pointer;">';
        echo '<input type="hidden" name="' . htmlspecialchars($name) . '" value="0">';
        echo '<input type="checkbox" id="' . htmlspecialchars($id) . '" name="' . htmlspecialchars($name) . '" value="1"' . ($value ? ' checked' : '') . '>';
        echo '<span class="text-sm text-muted">Activer</span>';
        echo '</label>';
    }

    private static function renderInt(string $name, string $id, $value): void
    {
        printf(
            '<input type="number" id="%s" name="%s" value="%s" style="max-width: 120px;">',
            htmlspecialchars($id),
            htmlspecialchars($name),
            htmlspecialchars(is_scalar($value) ? (string)$value : '')
        );
    }

    private static function renderMediaId(string $name, string $id, $value): void
    {
        $idVal = is_scalar($value) ? (int)$value : 0;
        $previewUrl = $idVal > 0 ? ImageService::urlById($idVal) : null;
        ?>
        <div class="vp-mp-row">
            <input type="number" id="<?= htmlspecialchars($id) ?>" name="<?= htmlspecialchars($name) ?>" value="<?= $idVal ?: '' ?>" placeholder="ID" style="max-width: 100px;">
            <button type="button" class="vp-mp-trigger" data-target="<?= htmlspecialchars($id) ?>">Choisir une image…</button>
            <img class="vp-mp-preview" data-for="<?= htmlspecialchars($id) ?>" src="<?= htmlspecialchars($previewUrl ?? '') ?>" alt="" <?= $previewUrl ? '' : 'hidden' ?>>
            <span class="vp-mp-preview-label" data-for="<?= htmlspecialchars($id) ?>"><?= $idVal > 0 ? 'id=' . $idVal : '' ?></span>
            <?php if ($idVal > 0 && !$previewUrl): ?>
                <span class="text-sm" style="color: var(--admin-error);">image introuvable</span>
            <?php endif; ?>
        </div>
        <?php
    }

    // ───────────────────────────────────────────────────────────
    // Repeater (récursif)
    // ───────────────────────────────────────────────────────────

    /**
     * Rend un repeater complet : liste d'items existants + template caché + bouton Ajouter.
     * Le JS (admin-section-edit.js) gère add/remove/move via délégation.
     */
    public static function renderRepeater(array $field, array $items, string $namePrefix, string $idPrefix): void
    {
        $itemType = $field['item_type'] ?? null;           // 'string' | 'int' | null
        $itemFields = $field['item_fields'] ?? null;       // array | null
        $max = isset($field['max']) ? (int)$field['max'] : 0;
        $isScalar = $itemType !== null;

        // Garde-fou : on supporte au moins l'un des deux
        if (!$isScalar && !is_array($itemFields)) {
            echo '<p class="text-sm" style="color: var(--admin-error);">⚠ Repeater mal défini (ni item_type ni item_fields).</p>';
            return;
        }

        // Re-séquence les indices (PHP peut avoir des trous si on a édité via JSON brut)
        $items = array_values($items);

        $dataAttrs = sprintf(
            'data-name-prefix="%s" data-id-prefix="%s" data-item-kind="%s"%s',
            htmlspecialchars($namePrefix),
            htmlspecialchars($idPrefix),
            $isScalar ? htmlspecialchars($itemType) : 'object',
            $max > 0 ? ' data-max="' . $max . '"' : ''
        );

        echo '<div class="vp-rp" ' . $dataAttrs . '>';
        echo '<div class="vp-rp-items">';
        foreach ($items as $i => $item) {
            self::renderRepeaterItem($field, $item, $namePrefix, $idPrefix, (int)$i);
        }
        echo '</div>';

        // Template d'item vierge — utilisé par le JS pour ajouter
        // L'index __INDEX__ est remplacé par le JS au moment du clone
        echo '<template class="vp-rp-tpl">';
        self::renderRepeaterItem($field, $isScalar ? '' : [], $namePrefix, $idPrefix, '__INDEX__');
        echo '</template>';

        $atMax = $max > 0 && count($items) >= $max;
        echo '<button type="button" class="vp-rp-add"' . ($atMax ? ' disabled' : '') . '>+ Ajouter</button>';
        if ($max > 0) {
            echo '<span class="vp-rp-max-note">Max ' . $max . ' item' . ($max > 1 ? 's' : '') . '</span>';
        }
        echo '</div>';
    }

    /**
     * Rend un item unique du repeater (utilisé pour les items existants ET pour le template).
     * $index peut être int (item réel) ou la string '__INDEX__' (template).
     */
    private static function renderRepeaterItem(array $field, $item, string $namePrefix, string $idPrefix, $index): void
    {
        $itemType = $field['item_type'] ?? null;
        $itemFields = $field['item_fields'] ?? null;
        $itemNamePrefix = $namePrefix . '[' . $index . ']';
        $itemIdPrefix = $idPrefix . '-' . $index;

        echo '<div class="vp-rp-item" data-index="' . htmlspecialchars((string)$index) . '">';
        echo '<div class="vp-rp-item-handle"><span class="vp-rp-item-num">' . (is_int($index) ? ($index + 1) : '#') . '</span></div>';
        echo '<div class="vp-rp-item-body">';

        if ($itemType === 'string') {
            printf(
                '<input type="text" name="%s" value="%s" placeholder="(texte)">',
                htmlspecialchars($itemNamePrefix),
                htmlspecialchars(is_scalar($item) ? (string)$item : '')
            );
        } elseif ($itemType === 'int') {
            printf(
                '<input type="number" name="%s" value="%s" placeholder="(nombre)">',
                htmlspecialchars($itemNamePrefix),
                htmlspecialchars(is_scalar($item) ? (string)$item : '')
            );
        } elseif (is_array($itemFields)) {
            $itemContent = is_array($item) ? $item : [];
            foreach ($itemFields as $subField) {
                self::renderField($subField, $itemContent, $itemNamePrefix, $itemIdPrefix);
            }
        }

        echo '</div>';
        echo '<div class="vp-rp-item-actions">';
        echo '<button type="button" class="vp-rp-up" title="Monter">↑</button>';
        echo '<button type="button" class="vp-rp-down" title="Descendre">↓</button>';
        echo '<button type="button" class="vp-rp-remove" title="Supprimer">×</button>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Slugify un nom pour usage en id HTML (pas de crochets ni d'underscores en sortie).
     */
    private static function slug(string $name): string
    {
        $s = preg_replace('/[^a-zA-Z0-9-]/', '-', $name);
        return trim($s ?? $name, '-');
    }
}
