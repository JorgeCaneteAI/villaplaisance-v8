<?php
declare(strict_types=1);

/**
 * Scan (lecture seule) du contenu ES qui tutoie (vosotros/tú) dans
 * vp_sections + vp_pages, pour harmonisation en "usted". Affiche chaque
 * phrase avec son contexte. N'ÉCRIT RIEN.
 *
 * Usage serveur : php bin/scan_es_register.php
 */

$root = dirname(__DIR__);
require $root . '/config.php';

$markers = [
    'vuestr', 'os respond', 'os esper', 'deseáis', 'podéis', 'tenéis',
    'habéis', 'disfrutáis', 'estáis', 'vivís', 'elegís', 'sabéis',
    'Decidnos', 'Contadnos', 'Decidme', 'sigue siendo tu', ' tu casa',
    ' tu estancia', ' tu llegada', ' tus ',
];

function scanRows(string $table, string $idCol, array $cols, array $markers): void
{
    $rows = Database::fetchAll("SELECT * FROM {$table} WHERE lang = 'es'");
    foreach ($rows as $row) {
        foreach ($cols as $col) {
            $text = (string)($row[$col] ?? '');
            if ($text === '') continue;
            foreach ($markers as $m) {
                $pos = mb_stripos($text, $m);
                while ($pos !== false) {
                    $start = max(0, $pos - 45);
                    $ctx = mb_substr($text, $start, 110);
                    $ctx = preg_replace('/\s+/', ' ', $ctx);
                    echo sprintf(
                        "[%s #%s · %s] …%s…\n",
                        $table, $row[$idCol] ?? '?', $col, trim($ctx)
                    );
                    $pos = mb_stripos($text, $m, $pos + 1);
                }
            }
        }
    }
}

echo "===== vp_sections (content) =====\n";
scanRows('vp_sections', 'id', ['content'], $markers);
echo "\n===== vp_pages (meta_title, meta_desc) =====\n";
scanRows('vp_pages', 'id', ['meta_title', 'meta_desc'], $markers);
echo "\nFini.\n";
