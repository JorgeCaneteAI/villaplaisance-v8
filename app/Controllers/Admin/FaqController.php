<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

class FaqController extends AdminBaseController
{
    public function index(): void
    {
        $langFilter = $_GET['lang'] ?? 'fr';
        $slugFilter = $_GET['page_slug'] ?? '';
        if (!in_array($langFilter, SUPPORTED_LANGS, true)) $langFilter = 'fr';

        // Liste des page_slugs distinctes pour le filtre
        $slugs = [];
        try {
            $rows = \Database::fetchAll("SELECT DISTINCT page_slug FROM vp_faq ORDER BY page_slug");
            foreach ($rows as $r) { $slugs[] = $r['page_slug']; }
        } catch (\Throwable) {}

        // Récupération filtrée
        $sql = "SELECT * FROM vp_faq WHERE lang = ?";
        $params = [$langFilter];
        if ($slugFilter !== '') {
            $sql .= " AND page_slug = ?";
            $params[] = $slugFilter;
        }
        $sql .= " ORDER BY page_slug ASC, position ASC";
        $faqs = \Database::fetchAll($sql, $params);

        $csrf = $this->csrf();
        $langLabels = ['fr' => "\u{1F1EB}\u{1F1F7} Français", 'en' => "\u{1F1EC}\u{1F1E7} English", 'es' => "\u{1F1EA}\u{1F1F8} Español"];

        $this->render('admin/faq/index', compact('faqs', 'slugs', 'langFilter', 'slugFilter', 'langLabels', 'csrf'));
    }

    public function edit(int $id): void
    {
        $faq = \Database::fetchOne("SELECT * FROM vp_faq WHERE id = ?", [$id]);
        if (!$faq) {
            $this->flash('error', 'FAQ introuvable.');
            $this->redirect('/admin/faq');
            return;
        }
        $csrf = $this->csrf();
        $this->render('admin/faq/edit', compact('faq', 'csrf'));
    }

    public function save(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->back();
            return;
        }

        $fields = [
            'question' => trim($_POST['question'] ?? ''),
            'answer' => trim($_POST['answer'] ?? ''),
            'position' => (int)($_POST['position'] ?? 0),
            'active' => isset($_POST['active']) ? 1 : 0,
        ];

        \Database::update('vp_faq', $fields, 'id = ?', [$id]);
        $this->flash('success', 'FAQ mise à jour.');
        $this->back();
    }

    public function toggle(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->back();
            return;
        }
        $row = \Database::fetchOne("SELECT active FROM vp_faq WHERE id = ?", [$id]);
        if ($row) {
            \Database::update('vp_faq', ['active' => $row['active'] ? 0 : 1], 'id = ?', [$id]);
            $this->flash('success', 'Visibilité modifiée.');
        }
        $this->back();
    }

    public function create(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->back();
            return;
        }

        $pageSlug = $_POST['page_slug'] ?? '';
        $max = \Database::fetchOne(
            "SELECT MAX(position) as mx FROM vp_faq WHERE page_slug = ? AND lang = 'fr'",
            [$pageSlug]
        );
        $position = ($max['mx'] ?? 0) + 1;

        // Create for all supported languages
        foreach (SUPPORTED_LANGS as $lang) {
            \Database::query(
                "INSERT INTO vp_faq (page_slug, lang, question, answer, position, active) VALUES (?, ?, ?, ?, ?, 1)",
                [$pageSlug, $lang, '', '', $position]
            );
        }

        $this->flash('success', 'FAQ ajoutée (FR/EN/ES).');
        $this->back();
    }

    public function delete(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->back();
            return;
        }

        // Get the FAQ to find page_slug + position, then delete all languages
        $faq = \Database::fetchOne("SELECT * FROM vp_faq WHERE id = ?", [$id]);
        if ($faq) {
            \Database::query(
                "DELETE FROM vp_faq WHERE page_slug = ? AND position = ?",
                [$faq['page_slug'], $faq['position']]
            );
        }

        $this->flash('success', 'FAQ supprimée (toutes langues).');
        $this->back();
    }

    private function back(): void
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '/admin/faq';
        $path = parse_url($ref, PHP_URL_PATH) ?: '/admin/faq';
        $this->redirect($path);
    }
}
