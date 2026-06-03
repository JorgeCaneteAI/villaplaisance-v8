<?php
declare(strict_types=1);

/**
 * IconService — renvoie un <svg> qui pointe sur le sprite SVG centralisé
 * `/public/assets/img/icons.svg` (55 symbols, stroke 1.5, currentColor).
 *
 * Pattern HTML :
 *   <svg width="20" height="20" aria-hidden="true">
 *     <use xlink:href="/assets/img/icons.svg#icon-wifi"/>
 *   </svg>
 *
 * Couleur : héritée de currentColor (donc CSS du parent).
 * Pour usage admin, utiliser la même fonction.
 *
 * Helper bonus : pillIcon($label) qui détecte un mot-clé dans le libellé
 * d'une pill (équipements chambres) et retourne le nom d'icône matching,
 * ou null si pas de match. Permet de brancher les pills sans modifier
 * l'admin (mapping fait côté serveur uniquement).
 */
class IconService
{
    /** Sprite custom Villa Plaisance (préfixe `icon-` retiré). */
    public const AVAILABLE = [
        // équipements chambres
        'lit', 'climatisation', 'tv', 'wifi', 'bibliotheque',
        'vue-jardin', 'jardin', 'clic-clac', 'salle-de-bain', 'douche',
        'vintage', 'cuisine', 'parking',
        // stats / chiffres clés
        'maison', 'personnes', 'piscine', 'vignoble', 'transport',
        'etoile', 'etoile-pleine',
        // territoire
        'vin', 'monument', 'theatre', 'antiquites', 'aqueduc',
        'village', 'archeologie', 'montagne',
        // plateformes avis
        'airbnb', 'booking', 'google', 'superhost',
        // contact / social
        'email', 'telephone', 'localisation', 'instagram', 'facebook',
        // nav / UI
        'fleche-droite', 'fleche-gauche', 'chevron-bas', 'horloge',
        'calendrier', 'check', 'plus', 'fermer', 'lien-externe',
        // infos pratiques
        'petit-dejeuner', 'serviette', 'cle', 'soleil', 'transat',
        'velo', 'barbecue', 'linge', 'animaux',
    ];

    /**
     * Catalogue Lucide curated (ISC license, https://lucide.dev/license),
     * ~75 icônes tourisme/hôtellerie organisées par catégorie. Préfixe `lc-`
     * dans le sprite : <use xlink:href="/assets/img/icons-lucide.svg#icon-lc-bed-double"/>.
     *
     * À regénérer via `php bin/build_lucide_sprite.php` quand on étend la liste.
     */
    public const LUCIDE_CATALOG = [
        'hebergement' => [
            'label' => 'Hébergement',
            'icons' => ['bed-double', 'bed-single', 'bed', 'hotel', 'key', 'key-round', 'accessibility', 'baby'],
        ],
        'confort' => [
            'label' => 'Confort',
            'icons' => ['wifi', 'tv', 'monitor', 'air-vent', 'fan', 'thermometer-sun', 'thermometer-snowflake', 'plug-zap', 'lamp', 'lightbulb'],
        ],
        'salle_de_bain' => [
            'label' => 'Salle de bain',
            'icons' => ['shower-head', 'bath', 'droplet', 'droplets'],
        ],
        'cuisine' => [
            'label' => 'Cuisine & repas',
            'icons' => ['coffee', 'utensils', 'utensils-crossed', 'wine', 'beer', 'croissant', 'cake', 'soup', 'refrigerator', 'chef-hat',
                        'salad', 'milk', 'egg', 'candy', 'cookie'],
        ],
        'terroir' => [
            'label' => 'Terroir & vignoble',
            'icons' => ['grape', 'wheat', 'wine-off', 'vault'],
        ],
        'marches' => [
            'label' => 'Marchés & artisanat',
            'icons' => ['shopping-bag', 'shopping-basket', 'store', 'gift', 'scissors'],
        ],
        'patrimoine' => [
            'label' => 'Patrimoine & lieux',
            'icons' => ['landmark', 'church', 'castle', 'building-2', 'archive'],
        ],
        'bien_etre' => [
            'label' => 'Bien-être & spa',
            'icons' => ['sparkles', 'sun-medium', 'moon', 'flame'],
        ],
        'nature' => [
            'label' => 'Nature & jardin',
            'icons' => ['tree-deciduous', 'tree-pine', 'trees', 'leaf', 'flower', 'flower-2', 'sprout', 'sun', 'mountain', 'palmtree',
                        'cloud', 'wind', 'rainbow', 'bird', 'bug'],
        ],
        'activites' => [
            'label' => 'Activités',
            'icons' => ['waves', 'bike', 'footprints', 'music', 'book', 'camera', 'palette'],
        ],
        'transport' => [
            'label' => 'Transport',
            'icons' => ['car', 'plane', 'train', 'bus', 'parking-circle'],
        ],
        'animaux' => [
            'label' => 'Animaux',
            'icons' => ['dog', 'cat', 'paw-print'],
        ],
        'famille' => [
            'label' => 'Famille',
            'icons' => ['users', 'users-round', 'gamepad-2', 'puzzle', 'party-popper'],
        ],
        'avis' => [
            'label' => 'Avis & réactions',
            'icons' => ['star', 'heart', 'thumbs-up', 'quote'],
        ],
        'contact' => [
            'label' => 'Contact & lieu',
            'icons' => ['mail', 'phone', 'message-circle', 'calendar', 'clock', 'map-pin', 'map', 'navigation'],
        ],
        'saison' => [
            'label' => 'Saison & climat',
            'icons' => ['sunrise', 'sunset', 'snowflake', 'umbrella', 'cloud-sun'],
        ],
    ];

    /** Aplatit le catalogue Lucide en simple liste de noms. */
    public static function availableLucide(): array
    {
        $out = [];
        foreach (self::LUCIDE_CATALOG as $cat) {
            foreach ($cat['icons'] as $name) {
                $out[] = 'lc-' . $name;
            }
        }
        return $out;
    }

    /**
     * Retourne le markup SVG pointant sur un symbol du sprite.
     *
     * Convention :
     *   - `lc-bed-double` → sprite Lucide (icons-lucide.svg)
     *   - `wifi` ou `icon-wifi` → sprite custom (icons.svg)
     *
     * Le `aria-hidden` est forcé : l'icône est toujours décorative, le sens
     * est porté par le texte voisin (label, sr-only, etc.).
     */
    public static function svg(string $name, int $size = 20, string $cls = ''): string
    {
        $name = ltrim($name, 'icon-');
        $isLucide = str_starts_with($name, 'lc-');
        $sprite = $isLucide ? '/assets/img/icons-lucide.svg' : '/assets/img/icons.svg';
        $symbolId = 'icon-' . $name;

        $classAttr = $cls !== '' ? ' class="' . htmlspecialchars($cls, ENT_QUOTES) . '"' : '';
        return sprintf(
            '<svg width="%1$d" height="%1$d" aria-hidden="true"%2$s><use xlink:href="%3$s#%4$s"/></svg>',
            $size,
            $classAttr,
            $sprite,
            htmlspecialchars($symbolId, ENT_QUOTES)
        );
    }

    /** Cache statique du mapping vp_icon_mapping (label → icon_name). */
    private static ?array $dbMapping = null;

    /**
     * Charge tous les mappings depuis la table vp_icon_mapping une seule fois
     * par requête. Si la table n'existe pas (avant migration 024), tableau
     * vide retourné silencieusement.
     */
    private static function getDbMapping(): array
    {
        if (self::$dbMapping !== null) return self::$dbMapping;
        self::$dbMapping = [];
        try {
            $rows = \Database::fetchAll("SELECT label, icon_name FROM vp_icon_mapping");
            foreach ($rows as $r) {
                $key = mb_strtolower((string)($r['label'] ?? ''), 'UTF-8');
                if ($key === '') continue;
                self::$dbMapping[$key] = (string)($r['icon_name'] ?? '');
            }
        } catch (\Throwable) {
            // Table absente ou inaccessible → silencieux, on bascule sur regex.
        }
        return self::$dbMapping;
    }

    /**
     * Détecte si un libellé de pill correspond à un équipement connu et
     * retourne le nom d'icône à utiliser. Insensible à la casse + accents.
     * Retourne null si pas de match.
     *
     * Ordre de résolution :
     *   1. Table vp_icon_mapping (libellé exact, lowercase) — override admin.
     *      Si icon_name est vide en BDD → null (pas d'icône, volontaire).
     *   2. Fallback regex maison (mots-clés FR courants).
     */
    public static function pillIcon(string $label): ?string
    {
        // 1. Lookup BDD par libellé exact lowercase.
        $exact = mb_strtolower($label, 'UTF-8');
        $db = self::getDbMapping();
        if (array_key_exists($exact, $db)) {
            return $db[$exact] !== '' ? $db[$exact] : null;
        }

        // 2. Fallback regex (mapping legacy maintenu par mots-clés).
        return self::pillIconRegexOnly($label);
    }

    /**
     * Calcule UNIQUEMENT le match auto par mots-clés, en bypass total de
     * la table vp_icon_mapping. Utile pour la page admin /admin/icons-lab
     * qui montre côte à côte « ce que ferait le regex » et « override BDD ».
     */
    public static function pillIconRegexOnly(string $label): ?string
    {
        $n = mb_strtolower($label, 'UTF-8');
        $n = strtr($n, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i',
            'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
        ]);

        // Ordre = priorité : les patterns spécifiques avant les généraux.
        $map = [
            'salle de bain'     => 'salle-de-bain',
            'sdb'               => 'salle-de-bain',
            'vue jardin'        => 'vue-jardin',
            'vue sur jardin'    => 'vue-jardin',
            'petit-dej'         => 'petit-dejeuner',
            'petit dej'         => 'petit-dejeuner',
            'breakfast'         => 'petit-dejeuner',
            'clic-clac'         => 'clic-clac',
            'clic clac'         => 'clic-clac',
            'wifi'              => 'wifi',
            'internet'          => 'wifi',
            'climatisation'     => 'climatisation',
            'clim '             => 'climatisation',
            'climatise'         => 'climatisation',
            'television'        => 'tv',
            ' tv '              => 'tv',
            'smart tv'          => 'tv',
            'bibliotheque'      => 'bibliotheque',
            'livres'            => 'bibliotheque',
            'jardin'            => 'jardin',
            'douche'            => 'douche',
            'cuisine'           => 'cuisine',
            'kitchenette'       => 'cuisine',
            'parking'           => 'parking',
            'piscine'           => 'piscine',
            'lit double'        => 'lit',
            'lit simple'        => 'lit',
            'lit king'          => 'lit',
            'lit queen'         => 'lit',
            'lits jumeaux'      => 'lit',
            'lit'               => 'lit',
            'serviette'         => 'serviette',
            'serviettes'        => 'serviette',
            'soleil'            => 'soleil',
            'sun'               => 'soleil',
            'transat'           => 'transat',
            'velo'              => 'velo',
            'bicyclette'        => 'velo',
            'barbecue'          => 'barbecue',
            'bbq'               => 'barbecue',
            'animaux'           => 'animaux',
            'animal'            => 'animaux',
            'lessive'           => 'linge',
            'linge'             => 'linge',
            'cle'               => 'cle',
            'check-in'          => 'cle',
            'instagram'         => 'instagram',
            'facebook'          => 'facebook',
            'airbnb'            => 'airbnb',
            'booking'           => 'booking',
            'google'            => 'google',
            'superhost'         => 'superhost',
            'courriel'          => 'email',
            'email'             => 'email',
            'e-mail'            => 'email',
            'mail'              => 'email',
            'sur place'         => 'localisation',
            'adresse'           => 'localisation',
            'localisation'      => 'localisation',
            'delai'             => 'horloge',
            'horloge'           => 'horloge',
        ];

        $padded = ' ' . $n . ' ';
        foreach ($map as $needle => $iconName) {
            if (str_contains($padded, $needle)) {
                return $iconName;
            }
        }
        return null;
    }
}
