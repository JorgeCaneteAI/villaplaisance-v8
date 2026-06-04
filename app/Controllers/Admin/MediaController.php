<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

class MediaController extends AdminBaseController
{
    private const UPLOAD_DIR = ROOT . '/public/uploads/';
    private const THUMB_DIR = ROOT . '/public/uploads/thumb/';   // miniatures servies au MediaPicker
    private const THUMB_MAX = 400;                                // côté le plus long en pixels (2× retina sur 200px)
    private const THUMB_QUALITY = 70;                             // qualité WebP des miniatures
    private const MAX_SIZE = 5 * 1024 * 1024; // 5 Mo
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'];
    private const FOLDERS = ['general', 'chambres', 'villa', 'exterieurs', 'journal', 'surplace', 'hero', 'divers'];

    /**
     * Convention : pour une image `nom.ext`, la miniature est `thumb/nom.webp`
     * (toujours WebP, indépendamment du format source).
     */
    public static function thumbFilename(string $sourceFilename): string
    {
        return pathinfo($sourceFilename, PATHINFO_FILENAME) . '.webp';
    }

    public static function thumbUrl(string $sourceFilename): string
    {
        return '/uploads/thumb/' . self::thumbFilename($sourceFilename);
    }

    public static function thumbAbsolutePath(string $sourceFilename): string
    {
        return self::THUMB_DIR . self::thumbFilename($sourceFilename);
    }

    public function index(): void
    {
        $folder = $_GET['folder'] ?? '';
        $search = trim($_GET['q'] ?? '');

        $sql = "SELECT * FROM vp_media";
        $params = [];
        $where = [];

        if ($folder !== '') {
            $where[] = "folder = ?";
            $params[] = $folder;
        }
        if ($search !== '') {
            $where[] = "(filename LIKE ? OR alt_fr LIKE ? OR title LIKE ? OR tags LIKE ?)";
            $s = "%{$search}%";
            $params = array_merge($params, [$s, $s, $s, $s]);
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY created_at DESC";

        $media = [];
        try {
            $media = \Database::fetchAll($sql, $params);
        } catch (\Throwable) {}

        $folders = self::FOLDERS;
        $stats = $this->getStats();
        $csrf = $this->csrf();

        $this->render('admin/media/index', compact('media', 'folders', 'folder', 'search', 'stats', 'csrf'));
    }

    public function upload(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/media');
            return;
        }

        if (empty($_FILES['images'])) {
            $this->flash('error', 'Aucun fichier sélectionné.');
            $this->redirect('/admin/media');
            return;
        }

        $folder = $_POST['folder'] ?? 'general';
        if (!in_array($folder, self::FOLDERS, true)) {
            $folder = 'general';
        }
        $files = $_FILES['images'];
        $uploaded = 0;
        $errors = 0;

        // Normalize files array (multi-upload)
        $fileCount = is_array($files['name']) ? count($files['name']) : 1;

        for ($i = 0; $i < $fileCount; $i++) {
            $name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $tmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $size = is_array($files['size']) ? $files['size'][$i] : $files['size'];
            $error = is_array($files['error']) ? $files['error'][$i] : $files['error'];
            $type = is_array($files['type']) ? $files['type'][$i] : $files['type'];

            if ($error !== UPLOAD_ERR_OK || $size === 0) {
                $errors++;
                continue;
            }

            if ($size > self::MAX_SIZE) {
                $errors++;
                continue;
            }

            // Validate MIME from file content
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $realMime = $finfo->file($tmpName);
            if (!in_array($realMime, self::ALLOWED_MIME, true)) {
                $errors++;
                continue;
            }

            // Generate SEO-friendly filename
            $seoName = $this->seoFilename($name);
            $ext = $this->getExtension($realMime);
            $finalName = $seoName . '.' . $ext;

            // Avoid collisions
            $counter = 1;
            while (file_exists(self::UPLOAD_DIR . $finalName)) {
                $finalName = $seoName . '-' . $counter . '.' . $ext;
                $counter++;
            }

            // Convert to WebP if JPEG/PNG and GD available
            $converted = false;
            if (in_array($realMime, ['image/jpeg', 'image/png'], true) && function_exists('imagecreatefromjpeg')) {
                $webpName = $seoName . '.webp';
                $wcounter = 1;
                while (file_exists(self::UPLOAD_DIR . $webpName)) {
                    $webpName = $seoName . '-' . $wcounter . '.webp';
                    $wcounter++;
                }

                $converted = $this->convertToWebp($tmpName, self::UPLOAD_DIR . $webpName, $realMime);
                if ($converted) {
                    $finalName = $webpName;
                    $realMime = 'image/webp';
                }
            }

            // Move file if not already converted
            if (!$converted) {
                if (!move_uploaded_file($tmpName, self::UPLOAD_DIR . $finalName)) {
                    $errors++;
                    continue;
                }
            }

            // Get image dimensions
            $dims = @getimagesize(self::UPLOAD_DIR . $finalName);
            $width = $dims[0] ?? 0;
            $height = $dims[1] ?? 0;
            $finalSize = filesize(self::UPLOAD_DIR . $finalName);

            // Generate default alt from filename
            $defaultAlt = $this->filenameToAlt($seoName);

            // Insert into DB
            try {
                \Database::insert('vp_media', [
                    'filename' => $finalName,
                    'original_name' => $name,
                    'alt_fr' => $defaultAlt,
                    'title' => $defaultAlt,
                    'mime_type' => $realMime,
                    'file_size' => $finalSize ?: $size,
                    'width' => $width,
                    'height' => $height,
                    'folder' => $folder,
                    'seo_filename' => $seoName,
                ]);
                $uploaded++;
                // Génère la miniature (best-effort, n'échoue jamais l'upload si la thumb plante)
                self::generateThumb(self::UPLOAD_DIR . $finalName, self::thumbAbsolutePath($finalName));
            } catch (\Throwable $e) {
                @unlink(self::UPLOAD_DIR . $finalName);
                $errors++;
            }
        }

        if ($uploaded > 0) {
            $this->flash('success', "{$uploaded} fichier(s) importé(s)" . ($errors > 0 ? " ({$errors} erreur(s))" : '') . '.');
        } else {
            $this->flash('error', 'Aucun fichier importé.');
        }

        $this->redirect('/admin/media' . ($folder !== 'general' ? '?folder=' . $folder : ''));
    }

    /**
     * Recherche Unsplash (renvoie JSON pour la modale admin).
     * Route : GET /admin/media/unsplash/search?q=...&page=N
     */
    public function unsplashSearch(): void
    {
        $q    = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        try {
            $data = \UnsplashService::search($q, $page);
            $this->json(['ok' => true] + $data);
        } catch (\Throwable $e) {
            $this->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Import d'une photo Unsplash dans la médiathèque locale.
     * Route : POST /admin/media/unsplash/import
     * Form : photo_id, folder, alt_fr, credit_shown, + tout le payload utile
     *        envoyé par le JS (photographer, photographer_url, source_url, tags, …)
     */
    public function unsplashImport(): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['ok' => false, 'error' => 'CSRF invalide'], 400);
            return;
        }
        $photoId = trim($_POST['photo_id'] ?? '');
        if ($photoId === '') {
            $this->json(['ok' => false, 'error' => 'photo_id manquant'], 400);
            return;
        }

        $folder = $_POST['folder'] ?? 'general';
        if (!in_array($folder, self::FOLDERS, true)) $folder = 'general';

        $altFr        = trim($_POST['alt_fr'] ?? '');
        $altEn        = trim($_POST['alt_en'] ?? '');
        $photographer = trim($_POST['photographer'] ?? '');
        $photogUrl    = trim($_POST['photographer_url'] ?? '');
        $sourceUrl    = trim($_POST['source_url'] ?? '');
        $downloadUrl  = trim($_POST['download_url'] ?? '');
        $downloadTrack= trim($_POST['download_track'] ?? '');
        $tagsArr      = $_POST['tags'] ?? [];
        if (is_string($tagsArr)) $tagsArr = array_map('trim', explode(',', $tagsArr));
        $tagsArr      = array_filter(array_map('trim', $tagsArr));

        try {
            // Slug du sujet + suffix d'unicité (6 premiers chars du photo_id)
            $base = $altFr !== '' ? $altFr : ($altEn !== '' ? $altEn : ('unsplash ' . $photoId));
            $slug = $this->seoFilename($base);
            $slug = mb_substr($slug, 0, 60) ?: ('unsplash-' . $photoId);
            $filename = $slug . '-' . substr($photoId, 0, 6) . '.webp';
            $absPath  = self::UPLOAD_DIR . $filename;

            // Tracker (ping) puis téléchargement
            \UnsplashService::trackDownload($downloadTrack);
            \UnsplashService::downloadBinary($downloadUrl, $absPath);

            // Conversion WebP si pas déjà
            $mime = mime_content_type($absPath) ?: 'image/jpeg';
            if ($mime !== 'image/webp') {
                $tmpPath = $absPath . '.tmp.webp';
                if ($this->convertToWebp($absPath, $tmpPath, $mime)) {
                    @unlink($absPath);
                    @rename($tmpPath, $absPath);
                }
            }

            // Dimensions
            $size = filesize($absPath) ?: 0;
            [$w, $h] = @getimagesize($absPath) ?: [0, 0];

            $credit = $photographer
                ? "Photo : {$photographer} · Unsplash"
                : 'Unsplash';

            $id = \Database::insert('vp_media', [
                'filename'      => $filename,
                'original_name' => $sourceUrl ?: ('https://unsplash.com/photos/' . $photoId),
                'alt_fr'        => $altFr,
                'alt_en'        => $altEn,
                'alt_es'        => '',
                'title'         => '',
                'caption'       => '',
                'credit'        => $credit . ($photogUrl ? " ({$photogUrl})" : ''),
                'mime_type'     => 'image/webp',
                'file_size'     => (int)$size,
                'width'         => (int)$w,
                'height'        => (int)$h,
                'folder'        => $folder,
                'tags'          => implode(', ', array_slice($tagsArr, 0, 12)),
                'seo_filename'  => $slug,
            ]);

            // Génère la miniature
            self::generateThumb($absPath, self::thumbAbsolutePath($filename));

            $this->json(['ok' => true, 'id' => $id, 'filename' => $filename]);
        } catch (\Throwable $e) {
            $this->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/media');
            return;
        }

        $folder = $_POST['folder'] ?? 'general';
        if (!in_array($folder, self::FOLDERS, true)) {
            $folder = 'general';
        }

        $data = [
            'alt_fr' => trim($_POST['alt_fr'] ?? ''),
            'alt_en' => trim($_POST['alt_en'] ?? ''),
            'alt_es' => trim($_POST['alt_es'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'caption' => trim($_POST['caption'] ?? ''),
            'credit' => trim($_POST['credit'] ?? ''),
            'folder' => $folder,
            'tags' => trim($_POST['tags'] ?? ''),
        ];

        \Database::update('vp_media', $data, 'id = ?', [$id]);
        $this->flash('success', 'Média mis à jour.');
        $this->redirect('/admin/media');
    }

    public function delete(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Token CSRF invalide.');
            $this->redirect('/admin/media');
            return;
        }

        $media = \Database::fetchOne("SELECT * FROM vp_media WHERE id = ?", [$id]);
        if ($media) {
            $filePath = self::UPLOAD_DIR . $media['filename'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            \Database::delete('vp_media', 'id = ?', [$id]);
            $this->flash('success', 'Média supprimé.');
        }

        $this->redirect('/admin/media');
    }

    public function edit(int $id): void
    {
        $media = \Database::fetchOne("SELECT * FROM vp_media WHERE id = ?", [$id]);
        if (!$media) {
            $this->flash('error', 'Média introuvable.');
            $this->redirect('/admin/media');
            return;
        }

        $folders = self::FOLDERS;
        $csrf = $this->csrf();
        $this->render('admin/media/edit', compact('media', 'folders', 'csrf'));
    }

    // --- Helpers ---

    private function seoFilename(string $name): string
    {
        $name = pathinfo($name, PATHINFO_FILENAME);
        $name = mb_strtolower($name);
        // Transliterate accents
        $name = str_replace(
            ['à', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç', 'ñ'],
            ['a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c', 'n'],
            $name
        );
        // Replace non-alphanumeric with hyphens
        $name = preg_replace('/[^a-z0-9]+/', '-', $name);
        $name = trim($name, '-');
        // Prefix for SEO context
        return 'villa-plaisance-' . ($name ?: 'image');
    }

    private function filenameToAlt(string $seoName): string
    {
        // "villa-plaisance-chambre-deluxe" → "Villa Plaisance Chambre Deluxe"
        $words = explode('-', $seoName);
        return implode(' ', array_map('ucfirst', $words));
    }

    private function getExtension(string $mime): string
    {
        return match ($mime) {
            'image/webp' => 'webp',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/avif' => 'avif',
            default => 'webp',
        };
    }

    /**
     * Génère une miniature WebP (ratio préservé, côté le plus long ≤ $maxSize).
     *
     * - Sortie : toujours WebP, qualité $quality.
     * - Crée le dossier de destination si absent.
     * - Idempotent : si la thumb existe déjà, retourne true sans regénérer.
     * - Best-effort : retourne false silencieusement si GD manque ou source invalide.
     *
     * Public static pour pouvoir être appelée depuis le script bin/generate_thumbs.php.
     */
    public static function generateThumb(
        string $sourcePath,
        string $thumbPath,
        int $maxSize = self::THUMB_MAX,
        int $quality = self::THUMB_QUALITY
    ): bool {
        if (file_exists($thumbPath)) return true;            // idempotent
        if (!file_exists($sourcePath)) return false;
        if (!function_exists('imagewebp')) return false;     // GD sans WebP

        $info = @getimagesize($sourcePath);
        if (!$info) return false;
        [$srcW, $srcH, $type] = $info;
        if ($srcW <= 0 || $srcH <= 0) return false;

        $src = match ($type) {
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            IMAGETYPE_JPEG => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($sourcePath) : false,
            IMAGETYPE_PNG  => function_exists('imagecreatefrompng')  ? @imagecreatefrompng($sourcePath)  : false,
            IMAGETYPE_GIF  => function_exists('imagecreatefromgif')  ? @imagecreatefromgif($sourcePath)  : false,
            default => false,
        };
        if (!$src) return false;

        // Dimensions cibles, ratio préservé, max sur le côté le plus long
        if ($srcW >= $srcH) {
            $dstW = min($maxSize, $srcW);
            $dstH = max(1, (int) round($dstW * $srcH / $srcW));
        } else {
            $dstH = min($maxSize, $srcH);
            $dstW = max(1, (int) round($dstH * $srcW / $srcH));
        }

        // Dossier de destination
        $dir = dirname($thumbPath);
        if (!is_dir($dir)) @mkdir($dir, 0755, true);

        $dst = imagecreatetruecolor($dstW, $dstH);
        // Préserve la transparence pour PNG/WebP
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        $ok = imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH)
           && imagewebp($dst, $thumbPath, $quality);

        imagedestroy($src);
        imagedestroy($dst);

        return $ok && file_exists($thumbPath);
    }

    private function convertToWebp(string $source, string $dest, string $mime): bool
    {
        try {
            $image = match ($mime) {
                'image/jpeg' => imagecreatefromjpeg($source),
                'image/png' => imagecreatefrompng($source),
                default => false,
            };

            if (!$image) return false;

            // Quality 82 = good balance size/quality
            $result = imagewebp($image, $dest, 82);
            imagedestroy($image);

            return $result && file_exists($dest);
        } catch (\Throwable) {
            return false;
        }
    }

    private function getStats(): array
    {
        $stats = ['total' => 0, 'size' => 0, 'webp' => 0];
        try {
            $row = \Database::fetchOne("SELECT COUNT(*) as total, COALESCE(SUM(file_size), 0) as size FROM vp_media");
            $stats['total'] = (int)($row['total'] ?? 0);
            $stats['size'] = (int)($row['size'] ?? 0);
            $webp = \Database::fetchOne("SELECT COUNT(*) as c FROM vp_media WHERE mime_type = 'image/webp'");
            $stats['webp'] = (int)($webp['c'] ?? 0);
        } catch (\Throwable) {}
        return $stats;
    }

    public static function formatSize(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' o';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' Ko';
        return round($bytes / (1024 * 1024), 2) . ' Mo';
    }
}
