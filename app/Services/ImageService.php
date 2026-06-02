<?php
declare(strict_types=1);

class ImageService
{
    private static array $mediaCache = [];

    /**
     * Resolve image URL: checks uploads/ first, then assets/img/, then placeholder
     */
    public static function url(string $path): string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // Check uploads directory first
        if (file_exists(ROOT . '/public/uploads/' . ltrim($path, '/'))) {
            return '/uploads/' . ltrim($path, '/');
        }

        // Fallback to assets/img
        return '/assets/img/' . ltrim($path, '/');
    }

    /**
     * Generate SVG placeholder
     */
    public static function placeholder(int $width, int $height, string $text = ''): string
    {
        $label = $text ?: "{$width}×{$height}";
        return "data:image/svg+xml," . rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
            . '<rect fill="#EBF2FA" width="' . $width . '" height="' . $height . '"/>'
            . '<text fill="#5282AA" font-family="Inter,sans-serif" font-size="18" text-anchor="middle" x="' . ($width / 2) . '" y="' . ($height / 2 + 6) . '">' . htmlspecialchars($label) . '</text>'
            . '</svg>'
        );
    }

    /**
     * Render an optimized <img> tag
     * - Resolves file in uploads/ then assets/img/ then placeholder
     * - Auto-fetches alt text from vp_media if not provided
     * - Adds width/height, loading, decoding attributes
     */
    public static function img(string $src, string $alt, int $width, int $height, bool $lazy = true, string $class = ''): string
    {
        // Resolve actual source
        $actualSrc = self::resolve($src, $width, $height);

        // Auto-fetch alt from media library if alt is empty or generic
        if ($alt === '' || $alt === basename($src, '.webp')) {
            $mediaAlt = self::getMediaAlt($src);
            if ($mediaAlt !== null) {
                $alt = $mediaAlt;
            }
        }

        $loading = $lazy ? 'loading="lazy" decoding="async"' : 'loading="eager" fetchpriority="high" decoding="sync"';
        $classAttr = $class ? ' class="' . htmlspecialchars($class) . '"' : '';

        return sprintf(
            '<img src="%s" alt="%s" width="%d" height="%d" %s%s>',
            htmlspecialchars($actualSrc),
            htmlspecialchars($alt),
            $width,
            $height,
            $loading,
            $classAttr
        );
    }

    /**
     * Resolve a source path to an actual URL
     */
    private static function resolve(string $src, int $width, int $height): string
    {
        $cleanSrc = ltrim($src, '/');

        // Check uploads/
        if (file_exists(ROOT . '/public/uploads/' . $cleanSrc)) {
            return '/uploads/' . $cleanSrc;
        }

        // Check assets/img/
        if (file_exists(ROOT . '/public/assets/img/' . $cleanSrc)) {
            return '/assets/img/' . $cleanSrc;
        }

        // Placeholder
        return self::placeholder($width, $height, basename($src, '.webp'));
    }

    /**
     * Fetch alt text from vp_media for a given filename (with cache)
     */
    public static function getMediaAlt(string $filename, string $lang = ''): ?string
    {
        $cleanName = basename($filename);

        if (!isset(self::$mediaCache[$cleanName])) {
            try {
                $row = Database::fetchOne(
                    "SELECT alt_fr, alt_en, alt_es FROM vp_media WHERE filename = ?",
                    [$cleanName]
                );
                self::$mediaCache[$cleanName] = $row;
            } catch (\Throwable) {
                self::$mediaCache[$cleanName] = null;
            }
        }

        $row = self::$mediaCache[$cleanName];
        if (!$row) return null;

        if ($lang === '') {
            $lang = defined('CURRENT_LANG') ? CURRENT_LANG : (LangService::get() ?? 'fr');
        }

        $field = "alt_{$lang}";
        return !empty($row[$field]) ? $row[$field] : ($row['alt_fr'] ?: null);
    }

    /**
     * Render an SVG icon from the sprite
     */
    public static function icon(string $id, int $size = 24, string $class = ''): string
    {
        $classAttr = $class ? ' class="' . htmlspecialchars($class) . '"' : '';
        return '<svg width="' . $size . '" height="' . $size . '"' . $classAttr . ' aria-hidden="true"><use href="#' . htmlspecialchars($id) . '"></use></svg>';
    }

    /**
     * Get full media record for a filename
     */
    public static function getMedia(string $filename): ?array
    {
        $cleanName = basename($filename);
        try {
            return Database::fetchOne("SELECT * FROM vp_media WHERE filename = ?", [$cleanName]);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Get full media record by ID (used by V8 blocks that reference vp_media.id).
     * Cached per-request.
     */
    private static array $mediaCacheById = [];
    public static function getById(int $id): ?array
    {
        if (isset(self::$mediaCacheById[$id])) {
            return self::$mediaCacheById[$id];
        }
        try {
            $row = Database::fetchOne("SELECT * FROM vp_media WHERE id = ?", [$id]);
            self::$mediaCacheById[$id] = $row ?: null;
            return self::$mediaCacheById[$id];
        } catch (\Throwable) {
            self::$mediaCacheById[$id] = null;
            return null;
        }
    }

    /**
     * Resolve a vp_media ID to its public URL (/uploads/filename.webp).
     * Returns null if the ID doesn't exist or the file is missing on disk.
     */
    public static function urlById(int $id): ?string
    {
        $row = self::getById($id);
        if (!$row || empty($row['filename'])) return null;
        $path = '/uploads/' . ltrim($row['filename'], '/');
        if (!file_exists(ROOT . '/public' . $path)) return null;
        return $path;
    }

    /**
     * Get alt text in current/given language for a vp_media ID.
     * Falls back to alt_fr if the language-specific alt is empty.
     */
    public static function altById(int $id, string $lang = ''): string
    {
        $row = self::getById($id);
        if (!$row) return '';
        if ($lang === '') {
            $lang = defined('CURRENT_LANG') ? CURRENT_LANG : (LangService::get() ?? 'fr');
        }
        $field = "alt_{$lang}";
        return (string)(!empty($row[$field]) ? $row[$field] : ($row['alt_fr'] ?? ''));
    }
}
