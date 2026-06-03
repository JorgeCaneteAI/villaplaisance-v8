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
    /** Liste des symbols disponibles dans le sprite (préfixe `icon-` retiré). */
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
     * Retourne le markup SVG pointant sur un symbol du sprite.
     * Le `aria-hidden` est forcé : l'icône est toujours décorative, le sens
     * est porté par le texte voisin (label, sr-only, etc.).
     */
    public static function svg(string $name, int $size = 20, string $cls = ''): string
    {
        $name = ltrim($name, 'icon-');
        $classAttr = $cls !== '' ? ' class="' . htmlspecialchars($cls, ENT_QUOTES) . '"' : '';
        return sprintf(
            '<svg width="%1$d" height="%1$d" aria-hidden="true"%2$s><use xlink:href="/assets/img/icons.svg#icon-%3$s"/></svg>',
            $size,
            $classAttr,
            htmlspecialchars($name, ENT_QUOTES)
        );
    }

    /**
     * Détecte si un libellé de pill correspond à un équipement connu et
     * retourne le nom d'icône à utiliser. Insensible à la casse + accents.
     * Retourne null si pas de match.
     *
     * Couvre l'équipement-CSV admin actuel : "WiFi gratuit", "Climatisation",
     * "Lit double 160×200", "TV smart 4K", "Vue jardin", "Salle de bain
     * privative", etc.
     */
    public static function pillIcon(string $label): ?string
    {
        // Normalisation : lowercase + retire accents simples.
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
        ];

        // Padding pour " tv " (match exact, pas inclus dans "television")
        $padded = ' ' . $n . ' ';
        foreach ($map as $needle => $iconName) {
            if (str_contains($padded, $needle)) {
                return $iconName;
            }
        }
        return null;
    }
}
