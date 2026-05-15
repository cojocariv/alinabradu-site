<?php
declare(strict_types=1);

/**
 * Cheie Browser pentru Google Maps JavaScript API + Embed API.
 * Lasă gol pentru modul embed (fără cheie) pe pagina Contact.
 * https://console.cloud.google.com/google/maps-apis
 */
if (!defined('GOOGLE_MAPS_API_KEY')) {
    $envKey = getenv('GOOGLE_MAPS_API_KEY');
    define('GOOGLE_MAPS_API_KEY', is_string($envKey) && $envKey !== '' ? $envKey : '');
}
