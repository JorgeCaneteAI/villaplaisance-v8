<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

class PieceController extends AdminBaseController
{
    public function index(): void
    {
        $langFilter = $_GET['lang'] ?? 'fr';
        $offerFilter = $_GET['offer'] ?? '';
        if (!in_array($langFilter, SUPPORTED_LANGS, true)) $langFilter = 'fr';

        // Load filtered pieces
        $sql = "SELECT * FROM vp_pieces WHERE lang = ?";
        $params = [$langFilter];
        if ($offerFilter !== '' && in_array($offerFilter, ['bb', 'villa', 'both'], true)) {
            $sql .= " AND offer = ?";
            $params[] = $offerFilter;
        }
        $sql .= " ORDER BY offer ASC, position ASC";
        $pieces = \Database::fetchAll($sql, $params);

        $csrf = $this->csrf();
        $langLabels = ['fr' => "\u{1F1EB}\u{1F1F7} Français", 'en' => "\u{1F1EC}\u{1F1E7} English", 'es' => "\u{1F1EA}\u{1F1F8} Español"];

        $this->render('admin/pieces/index_v8', compact('pieces', 'langFilter', 'offerFilter', 'langLabels', 'csrf'));
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
