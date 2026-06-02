<?php
declare(strict_types=1);

// Mapping des slugs de page par langue.
//
// La clé est TOUJOURS le slug FR canonique (celui utilisé dans le Router
// comme route principale). La valeur est le slug effectivement servi dans
// la langue donnée.
//
// LangService::url('chambres-d-hotes', 'en') → '/en/bed-and-breakfast'
// Le Router résout une URL entrante en inversant ce map.
return [
    'fr' => [
        'accueil' => '/',
        'chambres-d-hotes' => 'chambres-d-hotes',
        'location-villa-provence' => 'location-villa-provence',
        'espaces-exterieurs' => 'espaces-exterieurs',
        'journal' => 'journal',
        'itineraire' => 'itineraire',
        'contact' => 'contact',
        'avis' => 'avis',
        'votre-hote' => 'votre-hote',
        'disponibilites' => 'disponibilites',
        'mentions-legales' => 'mentions-legales',
        'politique-confidentialite' => 'politique-confidentialite',
        'plan-du-site' => 'plan-du-site',
        'livret' => 'livret',
    ],
    'en' => [
        'accueil' => '/',
        'chambres-d-hotes' => 'bed-and-breakfast',
        'location-villa-provence' => 'villa-rental-provence',
        'espaces-exterieurs' => 'outdoor-spaces',
        'journal' => 'journal',
        'itineraire' => 'things-to-do',
        'contact' => 'contact',
        'avis' => 'guest-reviews',
        'votre-hote' => 'your-host',
        'disponibilites' => 'availability',
        'mentions-legales' => 'legal-notice',
        'politique-confidentialite' => 'privacy-policy',
        'plan-du-site' => 'sitemap',
        'livret' => 'guest-booklet',
    ],
    'es' => [
        'accueil' => '/',
        'chambres-d-hotes' => 'habitaciones',
        'location-villa-provence' => 'alquiler-villa-provenza',
        'espaces-exterieurs' => 'espacios-exteriores',
        'journal' => 'diario',
        'itineraire' => 'que-hacer',
        'contact' => 'contacto',
        'avis' => 'opiniones',
        'votre-hote' => 'su-anfitrion',
        'disponibilites' => 'disponibilidad',
        'mentions-legales' => 'aviso-legal',
        'politique-confidentialite' => 'politica-privacidad',
        'plan-du-site' => 'mapa-del-sitio',
        'livret' => 'guia-de-acogida',
    ],
];
