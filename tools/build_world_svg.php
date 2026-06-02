<?php
declare(strict_types=1);

/**
 * Convertit Natural Earth 110m (GeoJSON CC0/public domain) en SVG
 * world map au trait, projection equirectangulaire pure.
 *
 * Usage :
 *   php tools/build_world_svg.php /chemin/vers/ne_110m_admin_0_countries.geojson
 *
 * Sortie : public/assets/img/world-equirectangular.svg
 *
 * Projection equirectangular pure :
 *   x = (lng + 180) / 360 * W
 *   y = (90 - lat) / 180 * H
 * avec W = 1000, H = 500 (ratio 2:1 conforme).
 */

$input = $argv[1] ?? null;
if (!$input || !is_file($input)) {
    fwrite(STDERR, "Usage: php tools/build_world_svg.php <geojson>\n");
    exit(1);
}

$W = 1000;
$H = 500;

$json = file_get_contents($input);
$data = json_decode($json, true);
if (!isset($data['features'])) {
    fwrite(STDERR, "GeoJSON invalide : pas de features\n");
    exit(1);
}

$project = static function (float $lng, float $lat) use ($W, $H): array {
    $x = ($lng + 180) / 360 * $W;
    $y = (90 - $lat) / 180 * $H;
    return [$x, $y];
};

$ringToPath = static function (array $ring) use ($project): string {
    $cmds = [];
    foreach ($ring as $i => $pt) {
        [$x, $y] = $project((float)$pt[0], (float)$pt[1]);
        $x = round($x, 2);
        $y = round($y, 2);
        $cmds[] = ($i === 0 ? 'M' : 'L') . $x . ' ' . $y;
    }
    return implode(' ', $cmds) . ' Z';
};

$paths = [];
foreach ($data['features'] as $feature) {
    $geom = $feature['geometry'] ?? null;
    if (!$geom) continue;

    $type = $geom['type'];
    $coords = $geom['coordinates'];
    $countryPaths = [];

    if ($type === 'Polygon') {
        foreach ($coords as $ring) {
            $countryPaths[] = $ringToPath($ring);
        }
    } elseif ($type === 'MultiPolygon') {
        foreach ($coords as $polygon) {
            foreach ($polygon as $ring) {
                $countryPaths[] = $ringToPath($ring);
            }
        }
    }

    if (!empty($countryPaths)) {
        $name = htmlspecialchars((string)($feature['properties']['NAME'] ?? ''), ENT_QUOTES);
        $iso  = htmlspecialchars((string)($feature['properties']['ISO_A2'] ?? ''), ENT_QUOTES);
        $paths[] = '<path data-name="' . $name . '" data-iso="' . $iso . '" d="' . implode(' ', $countryPaths) . '"/>';
    }
}

$svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . "\n";
$svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $W . ' ' . $H . '"';
$svg .= ' preserveAspectRatio="xMidYMid meet" aria-hidden="true">' . "\n";
$svg .= '  <g class="countries" fill="none" stroke="currentColor" stroke-width="0.5" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke">' . "\n";
foreach ($paths as $p) {
    $svg .= '    ' . $p . "\n";
}
$svg .= '  </g>' . "\n";
$svg .= '</svg>' . "\n";

$out = __DIR__ . '/../public/assets/img/world-equirectangular.svg';
if (!is_dir(dirname($out))) {
    mkdir(dirname($out), 0755, true);
}
file_put_contents($out, $svg);

echo "OK : " . strlen($svg) . " octets écrits dans " . $out . "\n";
echo "Pays : " . count($paths) . "\n";
echo "ViewBox : 0 0 $W $H (projection equirectangular pure)\n";
