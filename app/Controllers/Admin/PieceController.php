<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

class PieceController extends AdminBaseController
{
    public function index(): void
    {
        $offerFilter = $_GET['offer'] ?? '';

        // Toutes les langues : on les regroupe par (offer, position) pour
        // afficher FR/EN/ES côte à côte et éditer en place.
        $sql = "SELECT * FROM vp_pieces";
        $params = [];
        if ($offerFilter !== '' && in_array($offerFilter, ['bb', 'villa', 'both'], true)) {
            $sql .= " WHERE offer = ?";
            $params[] = $offerFilter;
        }
        $sql .= " ORDER BY offer ASC, position ASC, lang ASC";
        $rows = \Database::fetchAll($sql, $params);

        $groups = [];
        foreach ($rows as $r) {
            $key = $r['offer'] . ':' . $r['position'];
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'offer'    => $r['offer'],
                    'position' => (int)$r['position'],
                    'type'     => $r['type'],
                    'ref'      => $r,   // ligne de référence (FR si dispo)
                    'langs'    => [],
                ];
            }
            $groups[$key]['langs'][$r['lang']] = $r;
            if ($r['lang'] === 'fr') {
                $groups[$key]['ref']  = $r;
                $groups[$key]['type'] = $r['type'];
            }
        }

        $csrf = $this->csrf();
        $langLabels = ['fr' => "\u{1F1EB}\u{1F1F7} Français", 'en' => "\u{1F1EC}\u{1F1E7} English", 'es' => "\u{1F1EA}\u{1F1F8} Español"];

        $this->render('admin/pieces/index_v8', compact('groups', 'offerFilter', 'langLabels', 'csrf'));
    }

    /**
     * Sauvegarde en place des 3 langues d'une pièce d'un coup.
     * Champs traduits par langue (name, sous_titre, description, equip, note,
     * meta label_a/label_b) ; champs partagés propagés aux 3 lignes (offer
     * dérivé des cases B&B/Villa, type, meta layout). Les images ne sont PAS
     * touchées ici (gérées via la page d'édition dédiée).
     */
    public function saveGroup(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide');
            $this->redirect('/admin/pieces');
            return;
        }

        $ids = is_array($_POST['ids'] ?? null) ? $_POST['ids'] : [];

        // Offre dérivée des cases ; si aucune cochée, on garde l'offre actuelle.
        $bb    = isset($_POST['offer_bb']);
        $villa = isset($_POST['offer_villa']);
        if ($bb && $villa)      $offer = 'both';
        elseif ($villa)         $offer = 'villa';
        elseif ($bb)            $offer = 'bb';
        else {
            $cur   = \Database::fetchOne("SELECT offer FROM vp_pieces WHERE id = ?", [$id]);
            $offer = $cur['offer'] ?? 'bb';
        }

        $type   = in_array($_POST['type'] ?? '', ['chambre', 'espace'], true) ? $_POST['type'] : 'chambre';
        $layout = in_array($_POST['meta_layout'] ?? '', ['normal', 'alt'], true) ? $_POST['meta_layout'] : '';

        // Lecture sûre d'un champ par langue (les inputs sont des tableaux name[lang]).
        $field = static function (string $key, string $lang): string {
            $v = $_POST[$key] ?? null;
            return is_array($v) ? trim((string)($v[$lang] ?? '')) : '';
        };

        foreach (SUPPORTED_LANGS as $lang) {
            $rowId = (int)($ids[$lang] ?? 0);
            if ($rowId <= 0) continue;

            $meta = array_filter([
                'label_a' => $field('meta_label_a', $lang),
                'label_b' => $field('meta_label_b', $lang),
                'layout'  => $layout,
            ], static fn($v) => $v !== '');

            \Database::update('vp_pieces', [
                'name'        => $field('name', $lang),
                'sous_titre'  => $field('sous_titre', $lang),
                'description' => $field('description', $lang),
                'equip'       => $field('equip', $lang),
                'note'        => $field('note', $lang),
                'offer'       => $offer,
                'type'        => $type,
                'meta'        => !empty($meta) ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            ], 'id = ?', [$rowId]);
        }

        $this->flash('success', 'Pièce mise à jour (FR/EN/ES).');
        $this->redirect('/admin/pieces');
    }

    public function edit(int $id): void
    {
        $piece = \Database::fetchOne("SELECT * FROM vp_pieces WHERE id = ?", [$id]);
        if (!$piece) {
            $this->flash('error', 'Pièce introuvable');
            $this->redirect('/admin/pieces');
            return;
        }
        $csrf = $this->csrf();
        $this->render('admin/pieces/edit', compact('piece', 'csrf'));
    }

    public function save(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide');
            $this->redirect('/admin/pieces');
            return;
        }

        // Images : repeater POST array de filenames (peut avoir des trous, on re-séquence)
        $imagesRaw = $_POST['images'] ?? [];
        if (is_string($imagesRaw)) {
            // Saisie textarea JSON brut (fallback)
            $decoded = json_decode(trim($imagesRaw), true);
            $images = is_array($decoded) ? $decoded : [];
        } else {
            $images = is_array($imagesRaw) ? $imagesRaw : [];
        }
        $images = array_values(array_filter(array_map('trim', $images), static fn($s) => $s !== ''));

        // Meta : recompose depuis les champs séparés
        $meta = [
            'label_a' => trim($_POST['meta_label_a'] ?? ''),
            'label_b' => trim($_POST['meta_label_b'] ?? ''),
            'layout' => $_POST['meta_layout'] ?? '',
        ];
        // Ne stocker que les clés non vides
        $meta = array_filter($meta, static fn($v) => $v !== '');

        $fields = [
            'name' => trim($_POST['name'] ?? ''),
            'sous_titre' => trim($_POST['sous_titre'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'equip' => trim($_POST['equip'] ?? ''),
            'note' => trim($_POST['note'] ?? ''),
            'offer' => $_POST['offer'] ?? 'bb',
            'type' => $_POST['type'] ?? 'chambre',
            'image' => trim($_POST['image'] ?? ''),
            'images' => !empty($images) ? json_encode($images, JSON_UNESCAPED_UNICODE) : null,
            'meta' => !empty($meta) ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
        ];

        \Database::update('vp_pieces', $fields, 'id = ?', [$id]);
        $this->flash('success', 'Chambre/espace mis à jour');

        // Redirect back to referrer if from sections page
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        if (str_contains($ref, '/admin/sections/page/')) {
            $this->redirect(parse_url($ref, PHP_URL_PATH));
        } else {
            $this->redirect('/admin/pieces');
        }
    }

    public function create(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide');
            $this->redirect('/admin/pieces');
            return;
        }

        $offer = $_POST['offer'] ?? 'bb';
        $type = $_POST['type'] ?? 'chambre';
        $maxPos = \Database::fetchOne("SELECT MAX(position) as m FROM vp_pieces WHERE lang = 'fr' AND offer = ?", [$offer]);
        $pos = ($maxPos['m'] ?? 0) + 1;

        foreach (SUPPORTED_LANGS as $lang) {
            \Database::insert('vp_pieces', [
                'name' => 'Nouvelle chambre',
                'offer' => $offer,
                'type' => $type,
                'position' => $pos,
                'lang' => $lang,
            ]);
        }

        $this->flash('success', 'Chambre/espace ajouté (FR/EN/ES)');
        $this->redirect('/admin/pieces');
    }

    public function delete(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide');
            $this->redirect('/admin/pieces');
            return;
        }

        $piece = \Database::fetchOne("SELECT * FROM vp_pieces WHERE id = ?", [$id]);
        if ($piece) {
            \Database::query(
                "DELETE FROM vp_pieces WHERE offer = ? AND position = ?",
                [$piece['offer'], $piece['position']]
            );
        }
        $this->flash('success', 'Supprimé (toutes langues)');
        $this->redirect('/admin/pieces');
    }
}
