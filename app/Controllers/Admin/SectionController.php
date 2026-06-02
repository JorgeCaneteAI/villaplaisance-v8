<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

class SectionController extends AdminBaseController
{
    public function index(string $page_slug = ''): void
    {
        $pages = [];
        try {
            $pages = \Database::fetchAll("SELECT DISTINCT slug FROM vp_pages WHERE lang = 'fr' ORDER BY slug ASC");
        } catch (\Throwable) {}

        $langs = SUPPORTED_LANGS;
        $currentLang = $_GET['lang'] ?? 'fr';
        if (!in_array($currentLang, $langs, true)) $currentLang = 'fr';

        // Load sections for all languages (utile pour les badges de présence/absence par langue)
        $sectionsByLang = [];
        if ($page_slug !== '') {
            foreach ($langs as $l) {
                $sectionsByLang[$l] = \BlockService::getAllSections($page_slug, $l);
            }
        }
        // Sections affichées dans la liste = celles de la langue courante
        $sections = $sectionsByLang[$currentLang] ?? [];

        $blockTypes = \BlockService::getBlockTypes();
        $csrf = $this->csrf();

        $body_class = '';
        $preview_url = '';

        // Load pieces for "cartes" blocks inline editing
        $pieces = [];
        try {
            $pieces = \Database::fetchAll("SELECT * FROM vp_pieces WHERE lang = 'fr' ORDER BY offer ASC, position ASC");
        } catch (\Throwable) {}

        // Load FAQ items grouped by page_slug and lang
        $faqBySlugLang = [];
        try {
            $allFaq = \Database::fetchAll("SELECT * FROM vp_faq ORDER BY page_slug, lang, position");
            foreach ($allFaq as $f) {
                $faqBySlugLang[$f['page_slug']][$f['lang']][] = $f;
            }
        } catch (\Throwable) {}

        // Load proximites grouped by lang
        $proxByLang = [];
        try {
            $allProx = \Database::fetchAll("SELECT * FROM vp_proximites ORDER BY lang, position");
            foreach ($allProx as $px) {
                $proxByLang[$px['lang']][] = $px;
            }
        } catch (\Throwable) {}

        // Load stats grouped by lang
        $statsByLang = [];
        try {
            $allStats = \Database::fetchAll("SELECT * FROM vp_stats ORDER BY lang, position");
            foreach ($allStats as $s) {
                $statsByLang[$s['lang']][] = $s;
            }
        } catch (\Throwable) {}

        // V8 — on utilise la nouvelle vue simplifiée list_v8.php quand un page_slug est fourni.
        // L'ancienne vue index.php (massive, mélange édition inline + FAQ + pieces + stats)
        // est conservée pour référence mais n'est plus appelée.
        if ($page_slug !== '') {
            $this->render('admin/sections/list_v8', compact('pages', 'sections', 'page_slug', 'blockTypes', 'csrf', 'currentLang', 'langs', 'sectionsByLang'));
        } else {
            $this->render('admin/sections/list_v8', compact('pages', 'sections', 'page_slug', 'blockTypes', 'csrf', 'currentLang', 'langs', 'sectionsByLang'));
        }
    }

    public function edit(int $id): void
    {
        $section = \BlockService::getSection($id);
        if (!$section) {
            $this->flash('error', 'Section introuvable.');
            $this->redirect('/admin/sections');
            return;
        }

        $blockTypes = \BlockService::getBlockTypes();
        $csrf = $this->csrf();

        $this->render('admin/sections/edit', compact('section', 'blockTypes', 'csrf'));
    }

    public function save(int $id): void
    {
        $section = \BlockService::getSection($id);
        $pageSlug = $section['page_slug'] ?? '';

        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/sections/page/' . $pageSlug);
            return;
        }

        // Build content JSON from typed fields or raw content.
        // Toute la logique de cast (repeater récursif, int, bool, media_id...)
        // est centralisée dans BlockFieldsService::castValue.
        if (isset($_POST['fields']) && is_array($_POST['fields'])) {
            $rawFields = $_POST['fields'];
            $blockType = $section['block_type'] ?? ($_POST['block_type'] ?? 'prose');
            $defs = \BlockFieldsService::fieldsFor($blockType);
            $defsByName = [];
            foreach ($defs as $d) { $defsByName[$d['name'] ?? ''] = $d; }

            $fields = [];
            foreach ($rawFields as $key => $val) {
                if (!isset($defsByName[$key])) {
                    // Champ inconnu (ex. champ legacy resté dans le form) : on conserve brut
                    $fields[$key] = $val;
                    continue;
                }
                $fields[$key] = \BlockFieldsService::castValue($val, $defsByName[$key]);
            }
            // Nettoyer les champs vides (sauf 0/false explicites) pour ne pas polluer le JSON
            $fields = array_filter($fields, static function ($v) {
                return !($v === '' || $v === null || (is_array($v) && empty($v)));
            });
            $contentJson = json_encode($fields, JSON_UNESCAPED_UNICODE);
        } else {
            $contentJson = $_POST['content'] ?? '{}';
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'block_type' => $_POST['block_type'] ?? $section['block_type'] ?? 'prose',
            'content' => $contentJson,
            'active' => isset($_POST['active']) ? 1 : 0,
        ];

        \BlockService::saveSection($id, $data);

        // Propagate shared fields to other language sections
        $sharedKeys = ['image', 'image_alt', 'compact', 'lead', 'dark', 'offer', 'images',
                       'limit', 'offer_filter', 'type', 'page_slug', 'style', 'button_url'];
        $savedFields = json_decode($contentJson, true) ?: [];
        $sharedData = [];
        foreach ($sharedKeys as $sk) {
            if (array_key_exists($sk, $savedFields)) {
                $sharedData[$sk] = $savedFields[$sk];
            }
        }
        if (!empty($sharedData) && !empty($section['position'])) {
            $otherSections = \Database::fetchAll(
                "SELECT id, content FROM vp_sections WHERE page_slug = ? AND position = ? AND id != ?",
                [$pageSlug, $section['position'], $id]
            );
            foreach ($otherSections as $other) {
                $otherContent = json_decode($other['content'], true) ?: [];
                foreach ($sharedData as $sk => $sv) {
                    $otherContent[$sk] = $sv;
                }
                \Database::update('vp_sections', [
                    'content' => json_encode($otherContent, JSON_UNESCAPED_UNICODE),
                ], 'id = ?', [$other['id']]);
            }
        }

        $this->flash('success', 'Section « ' . $data['title'] . ' » mise à jour.');
        $this->redirect('/admin/sections/page/' . $pageSlug);
    }

    public function reorder(): void
    {
        // Endpoint AJAX : reçoit un JSON { page_slug, lang, order: [id1, id2, ...] }
        // et met à jour la colonne `position` de chaque section selon l'ordre fourni.
        header('Content-Type: application/json');

        // CSRF via header X-CSRF-Token (envoyé par le JS) ou body
        $tokenHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if ($tokenHeader === '' || !hash_equals((string)$sessionToken, $tokenHeader)) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF']);
            return;
        }

        $input = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
        $order = $input['order'] ?? [];
        if (!is_array($order) || empty($order)) {
            http_response_code(400);
            echo json_encode(['error' => 'order missing']);
            return;
        }

        $updated = 0;
        foreach ($order as $i => $rawId) {
            $id = (int)$rawId;
            if ($id <= 0) continue;
            \Database::update('vp_sections', ['position' => $i + 1], 'id = ?', [$id]);
            $updated++;
        }

        echo json_encode(['ok' => true, 'updated' => $updated]);
    }

    public function create(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/sections');
            return;
        }

        $pageSlug = $_POST['page_slug'] ?? '';
        // Get max position
        $max = \Database::fetchOne(
            "SELECT MAX(position) as mx FROM vp_sections WHERE page_slug = ? AND lang = 'fr'",
            [$pageSlug]
        );

        $position = ($max['mx'] ?? 0) + 1;
        $blockType = $_POST['block_type'] ?? 'prose';
        $title = trim($_POST['title'] ?? 'Nouvelle section');

        // Create section for all supported languages
        foreach (SUPPORTED_LANGS as $lang) {
            $data = [
                'page_slug' => $pageSlug,
                'lang' => $lang,
                'block_type' => $blockType,
                'title' => $title,
                'content' => '{}',
                'position' => $position,
                'active' => 1,
            ];
            \BlockService::createSection($data);
        }
        $this->flash('success', 'Section créée (FR/EN/ES).');
        $this->redirect('/admin/sections/page/' . $pageSlug);
    }

    public function toggle(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/sections');
            return;
        }

        $section = \BlockService::getSection($id);
        \BlockService::toggleSection($id);
        $this->flash('success', 'Visibilité modifiée.');
        $this->redirect('/admin/sections/page/' . ($section['page_slug'] ?? ''));
    }

    public function move(int $id, string $direction): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/sections');
            return;
        }

        $section = \BlockService::getSection($id);
        \BlockService::moveSection($id, $direction);
        $this->redirect('/admin/sections/page/' . ($section['page_slug'] ?? ''));
    }

    public function duplicate(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/sections');
            return;
        }
        $section = \BlockService::getSection($id);
        if (!$section) {
            $this->flash('error', 'Bloc introuvable.');
            $this->redirect('/admin/sections');
            return;
        }

        // Calcule la nouvelle position : juste après le bloc dupliqué.
        // Décale tous les blocs suivants de +1 pour ne pas créer de collision.
        $pageSlug = $section['page_slug'];
        $lang     = $section['lang'];
        $oldPos   = (int)$section['position'];
        $newPos   = $oldPos + 1;

        \Database::query(
            "UPDATE vp_sections SET position = position + 1
             WHERE page_slug = ? AND lang = ? AND position >= ?",
            [$pageSlug, $lang, $newPos]
        );

        \BlockService::createSection([
            'page_slug'  => $pageSlug,
            'lang'       => $lang,
            'block_type' => $section['block_type'],
            'title'      => trim((string)($section['title'] ?? '')) . ' (copie)',
            'content'    => $section['content'],
            'position'   => $newPos,
            'active'     => 1,
        ]);

        $this->flash('success', "Bloc dupliqué en position $newPos.");
        $this->redirect('/admin/sections/page/' . $pageSlug . '?lang=' . $lang);
    }

    public function delete(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/sections');
            return;
        }

        $section = \BlockService::getSection($id);
        \BlockService::deleteSection($id);
        $this->flash('success', 'Section supprimée.');
        $this->redirect('/admin/sections/page/' . ($section['page_slug'] ?? ''));
    }
}
