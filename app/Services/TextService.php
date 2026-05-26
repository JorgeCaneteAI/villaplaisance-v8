<?php
declare(strict_types=1);

/**
 * Helpers texte pour les blocs V8.
 *
 * Mini-markdown maison autorisé dans les champs `title`/`heading` des
 * blocs `vp_sections` :
 *   - `*texte*`         → <em>texte</em>
 *   - retour à la ligne → <br>
 *
 * Aucun autre HTML n'est autorisé : on htmlspecialchars d'abord, puis on
 * réintroduit uniquement nos 2 patterns. Pas de risque XSS.
 *
 * Pour le SEO (titres bruts dans <title>, OG, JSON-LD…), utiliser
 * TextService::stripMarkdown() qui retire les marqueurs sans rendre
 * de HTML.
 */
class TextService
{
    /**
     * Convertit un titre en mini-markdown vers du HTML safe.
     *
     * Exemple : "Villa\n*Plaisance*" → "Villa<br /><em>Plaisance</em>"
     */
    public static function renderTitle(?string $raw): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }
        $t = htmlspecialchars($raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $t = preg_replace('/\*([^*\n]+)\*/u', '<em>$1</em>', $t);
        return nl2br($t, false);
    }

    /**
     * Retire le mini-markdown d'un titre pour usage SEO / méta.
     *
     * Exemple : "Villa\n*Plaisance*" → "Villa Plaisance"
     */
    public static function stripMarkdown(?string $raw): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }
        $t = preg_replace('/\*([^*\n]+)\*/u', '$1', $raw);
        $t = preg_replace('/\s+/', ' ', $t);
        return trim($t ?? '');
    }
}
