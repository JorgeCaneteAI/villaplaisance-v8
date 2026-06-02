<?php
declare(strict_types=1);

/**
 * V8 — Lot 1 : hero éditable sur Contact, Disponibilités, Journal.
 *
 * Stratégie : juste le hero en pos 1 pour chaque page. Le reste (formulaire,
 * widget calendrier, grid d'articles) reste en HTML dur côté vue front, sauf
 * si Jorge ajoute d'autres blocs via « + Nouveau bloc » dans l'admin.
 *
 * Pas d'images de fond (page-hero standard sans `has-image`).
 * Idempotent : DELETE pos 1 WHERE page_slug+lang avant INSERT.
 *
 * FR + EN + ES en un seul fichier.
 */

require __DIR__ . '/../../config.php';

echo "=== Seed V8 — Lot 1 (Contact, Disponibilités, Journal) ===\n\n";

$pages = [
    'contact' => [
        'fr' => [
            'title' => "Une lettre,\nun *appel*.",
            'lede'  => "Pas de moteur de réservation, pas de confirmation automatique. Dites-nous ce que vous souhaitez, on vérifie le calendrier, on vous répond — à la main, dans la journée.",
            'tags'  => ['06 · Contact', 'Réponse dans la journée'],
        ],
        'en' => [
            'title' => "A letter,\na *call*.",
            'lede'  => "No booking engine, no automatic confirmation. Tell us what you'd like, we check the calendar, we write back — by hand, within the day.",
            'tags'  => ['06 · Contact', 'Reply within the day'],
        ],
        'es' => [
            'title' => "Una carta,\nuna *llamada*.",
            'lede'  => "Sin motor de reservas, sin confirmación automática. Decidnos lo que deseáis, revisamos el calendario, os respondemos — a mano, en el día.",
            'tags'  => ['06 · Contacto', 'Respuesta en el día'],
        ],
    ],
    'disponibilites' => [
        'fr' => [
            'title' => "Douze *mois*,\nd'un seul tenant.",
            'lede'  => "Toutes nos disponibilités sur l'année à venir — synchronisées avec Airbnb et Booking, mises à jour toutes les trente minutes. Pour réserver, il suffit de nous écrire.",
            'tags'  => ['Disponibilités', 'Sync. Airbnb & Booking'],
        ],
        'en' => [
            'title' => "Twelve *months*,\nin a single view.",
            'lede'  => "All our availability for the year ahead — synchronised with Airbnb and Booking, updated every thirty minutes. To book, simply write to us.",
            'tags'  => ['Availability', 'Synced with Airbnb & Booking'],
        ],
        'es' => [
            'title' => "Doce *meses*,\nde un solo vistazo.",
            'lede'  => "Toda nuestra disponibilidad para el próximo año — sincronizada con Airbnb y Booking, actualizada cada treinta minutos. Para reservar, basta con escribirnos.",
            'tags'  => ['Disponibilidad', 'Sync. Airbnb y Booking'],
        ],
    ],
    'journal' => [
        'fr' => [
            'title' => "Voyager *autrement*\nen Provence.",
            'lede'  => "Cinq façons de regarder la région — ce qui bouge dans la Provence contemporaine, ceux qui la font vivre, et comment la traverser sans cocher de cases.",
            'tags'  => ['Journal · 04 / Tourisme', 'N° 01 · 2026'],
        ],
        'en' => [
            'title' => "Travelling *differently*\nin Provence.",
            'lede'  => "Five ways of looking at the region — what's changing in contemporary Provence, who keeps it alive, and how to travel through it without ticking boxes.",
            'tags'  => ['Journal · 04 / Tourism', 'N° 01 · 2026'],
        ],
        'es' => [
            'title' => "Viajar *de otro modo*\nen Provenza.",
            'lede'  => "Cinco maneras de mirar la región — lo que cambia en la Provenza contemporánea, quiénes la hacen vivir, y cómo recorrerla sin marcar casillas.",
            'tags'  => ['Diario · 04 / Turismo', 'N° 01 · 2026'],
        ],
    ],
];

foreach ($pages as $slug => $langs) {
    echo "─── $slug ───\n";
    foreach ($langs as $lang => $data) {
        // Reset uniquement le hero pos 1 (pas les autres blocs si Jorge en a ajouté)
        Database::query(
            "DELETE FROM vp_sections WHERE page_slug = ? AND lang = ? AND position = 1",
            [$slug, $lang]
        );
        Database::insert('vp_sections', [
            'page_slug'  => $slug,
            'lang'       => $lang,
            'block_type' => 'hero',
            'title'      => 'Hero ' . $slug,
            'content'    => json_encode([
                'title' => $data['title'],
                'lede'  => $data['lede'],
                'tags'  => $data['tags'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'position'   => 1,
            'active'     => 1,
        ]);
        echo "  ✓ $slug/$lang hero\n";
    }
    echo "\n";
}

echo "Done.\n";
echo "💡 Édition via /admin/sections/page/{contact,disponibilites,journal}\n";
echo "💡 Pour ajouter d'autres blocs (intro, CTA…) : « + Nouveau bloc » dans l'admin.\n";
