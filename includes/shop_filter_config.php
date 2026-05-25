<?php
declare(strict_types=1);

/**
 * Configurare filtre magazin (subcategorii Rochie, mărimi).
 * Fișierul din config/ poate suprascrie valorile dacă există pe server.
 *
 * @return array{rochie_slug:string,subcategories:list<array{label:string,value:string,slug:string}>,sizes:list<string>}
 */
function shopFilterConfig(): array
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $overridePath = dirname(__DIR__) . '/config/shop_filters.php';
    if (is_readable($overridePath)) {
        $data = require $overridePath;
        if (is_array($data)) {
            $cached = $data;
            return $cached;
        }
    }

    $cached = [
        'rochie_slug' => 'rochii',
        'subcategories' => [
            ['label' => 'Colecția DOR', 'value' => 'Colecția Dor', 'slug' => 'colectia-dor'],
            ['label' => 'Colecția Mireasa', 'value' => 'Colecția Mireasă', 'slug' => 'colectia-mireasa'],
            ['label' => 'Colecția MISTERY', 'value' => 'Colecția Mistery', 'slug' => 'colectia-mistery'],
            ['label' => 'Colecția Soare', 'value' => 'Colecția Soare', 'slug' => 'colectia-soare'],
            ['label' => 'Colecția SPICUL', 'value' => 'Colecția Spicul', 'slug' => 'colectia-spicul'],
        ],
        'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
    ];

    return $cached;
}
