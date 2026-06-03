<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * IconLabController — page admin /admin/icons-lab.
 *
 * Liste TOUS les libellés "iconisables" rencontrés dans la base (pills
 * équipements chambres, solid pills, sous-titres) + sources fixes (rows
 * contact, plateformes avis) + leur mapping actuel dans vp_icon_mapping
 * et le match auto que ferait IconService::pillIcon() (regex fallback).
 *
 * Le formulaire POST permet d'override chaque libellé par une icône du
 * sprite (ou "(aucune)" pour signifier "volontairement sans icône").
 *
 * Requiert la table vp_icon_mapping (seed v8/024_icon_mapping.php).
 */
class IconLabController extends AdminBaseController
{
    /** Libellés fixes côté front (contact rows, plateformes avis, etc.). */
    private const FIXED_LABELS = [
        'contact'  => ['Par courriel', 'Sur place', 'Délai de réponse', 'Et pour suivre'],
        'reviews'  => ['Airbnb', 'Booking', 'Google', 'Superhost'],
    ];

    public function index(): void
    {
        $csrf = $this->csrf();
        $labels = $this->collectLabels();

        // Mapping actuel en BDD : label → icon_name (peut être '' = sans icône).
        $dbMapping = [];
        try {
            $rows = \Database::fetchAll("SELECT label, icon_name FROM vp_icon_mapping");
            foreach ($rows as $r) {
                $dbMapping[mb_strtolower((string)$r['label'], 'UTF-8')] = (string)($r['icon_name'] ?? '');
            }
        } catch (\Throwable) {}

        // Match auto que ferait IconService::pillIcon() (fallback regex).
        // On le calcule en bypass DB pour montrer ce que ferait le fallback.
        $autoMatches = [];
        foreach ($labels as $row) {
            $autoMatches[$row['label']] = $this->regexFallbackMatch($row['label']);
        }

        // Stats
        $totalLabels  = count($labels);
        $mappedCount  = 0;
        $autoOnlyCount = 0;
        foreach ($labels as $row) {
            $key = mb_strtolower($row['label'], 'UTF-8');
            if (array_key_exists($key, $dbMapping)) {
                $mappedCount++;
            } elseif ($autoMatches[$row['label']] !== null) {
                $autoOnlyCount++;
            }
        }

        $availableIcons = \IconService::AVAILABLE;
        sort($availableIcons);

        $this->render('admin/icons-lab/index', compact(
            'csrf', 'labels', 'dbMapping', 'autoMatches',
            'availableIcons', 'totalLabels', 'mappedCount', 'autoOnlyCount'
        ));
    }

    public function save(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/icons-lab');
            return;
        }

        $mapping = $_POST['mapping'] ?? [];
        if (!is_array($mapping)) {
            $this->flash('error', 'Données invalides.');
            $this->redirect('/admin/icons-lab');
            return;
        }

        $available = \IconService::AVAILABLE;
        $saved = 0;
        $skipped = 0;

        foreach ($mapping as $labelRaw => $iconRaw) {
            $label = trim((string) $labelRaw);
            if ($label === '' || mb_strlen($label, 'UTF-8') > 160) {
                $skipped++;
                continue;
            }

            $icon = trim((string) $iconRaw);
            // Valeur 'auto' = on n'override pas, on retombe sur le regex
            // → on supprime l'éventuelle entrée DB pour ce label.
            if ($icon === 'auto') {
                try {
                    \Database::query("DELETE FROM vp_icon_mapping WHERE label = ?", [$label]);
                    $saved++;
                } catch (\Throwable) {
                    $skipped++;
                }
                continue;
            }

            // Valeur '' (vide) = on force "pas d'icône" (override négatif).
            if ($icon === '' || $icon === 'none') {
                $icon = '';
            } elseif (!in_array($icon, $available, true)) {
                $skipped++;
                continue;
            }

            try {
                \Database::query(
                    "INSERT INTO vp_icon_mapping (label, icon_name) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE icon_name = VALUES(icon_name)",
                    [$label, $icon === '' ? null : $icon]
                );
                $saved++;
            } catch (\Throwable) {
                $skipped++;
            }
        }

        $msg = "$saved mapping(s) enregistré(s)" . ($skipped > 0 ? ", $skipped ignoré(s)" : '') . '.';
        $this->flash('success', $msg);
        $this->redirect('/admin/icons-lab');
    }

    /**
     * Collecte tous les libellés iconisables de la base + sources fixes.
     * Retourne un array de [label, source, count] dédupliqué (par label).
     */
    private function collectLabels(): array
    {
        $bag = [];

        // 1. Pills équipements (vp_pieces.equip) — CSV séparé par virgules,
        // sous-séparé par | pour les variantes 2-lignes.
        try {
            $rows = \Database::fetchAll(
                "SELECT equip FROM vp_pieces WHERE lang = 'fr' AND equip IS NOT NULL AND equip != ''"
            );
            foreach ($rows as $r) {
                $equip = (string) $r['equip'];
                foreach (explode(',', $equip) as $pill) {
                    $first = trim(explode('|', $pill, 2)[0] ?? '');
                    if ($first !== '') $this->addLabel($bag, $first, 'pill_chambre');
                }
            }
        } catch (\Throwable) {}

        // 2. Solid pills (vp_pieces.note).
        try {
            $rows = \Database::fetchAll(
                "SELECT note FROM vp_pieces WHERE lang = 'fr' AND note IS NOT NULL AND note != ''"
            );
            foreach ($rows as $r) {
                $note = trim((string) $r['note']);
                if ($note !== '') $this->addLabel($bag, $note, 'pill_solid');
            }
        } catch (\Throwable) {}

        // 3. Sous-titres chambres (vp_pieces.sous_titre).
        try {
            $rows = \Database::fetchAll(
                "SELECT sous_titre FROM vp_pieces WHERE lang = 'fr' AND sous_titre IS NOT NULL AND sous_titre != ''"
            );
            foreach ($rows as $r) {
                $sub = trim((string) $r['sous_titre']);
                if ($sub !== '') $this->addLabel($bag, $sub, 'sous_titre');
            }
        } catch (\Throwable) {}

        // 4. Libellés fixes (contact rows + plateformes avis).
        foreach (self::FIXED_LABELS as $source => $list) {
            foreach ($list as $label) {
                $this->addLabel($bag, $label, $source);
            }
        }

        // Tri alphabétique pour rendu stable.
        ksort($bag, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($bag);
    }

    private function addLabel(array &$bag, string $label, string $source): void
    {
        $key = mb_strtolower($label, 'UTF-8');
        if (isset($bag[$key])) {
            $bag[$key]['count']++;
            // Si on rencontre le même label dans plusieurs sources, on garde
            // la plus prioritaire (pill_chambre > pill_solid > sous_titre > fixe).
            $priority = ['pill_chambre' => 4, 'pill_solid' => 3, 'sous_titre' => 2];
            $oldPrio = $priority[$bag[$key]['source']] ?? 1;
            $newPrio = $priority[$source] ?? 1;
            if ($newPrio > $oldPrio) {
                $bag[$key]['source'] = $source;
            }
            return;
        }
        $bag[$key] = ['label' => $label, 'source' => $source, 'count' => 1];
    }

    /**
     * Calcule ce que retournerait IconService::pillIcon() en bypass total
     * de la table vp_icon_mapping (pour montrer le match auto regex).
     * Implémentation : on consulte IconService directement, mais on vide
     * temporairement son cache DB pour forcer le fallback regex.
     */
    private function regexFallbackMatch(string $label): ?string
    {
        // Hack léger : on appelle pillIcon() qui consultera la DB d'abord.
        // Si la DB a une entrée pour ce label, on ne sait pas distinguer
        // "le regex aurait matché aussi" de "seul l'override DB compte".
        // Solution propre : exposer une méthode regexOnly() dans IconService.
        return \IconService::pillIconRegexOnly($label);
    }
}
