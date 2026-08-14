<?php
declare(strict_types=1);

require_once __DIR__ . '/mail.php';

/** Email afișat pe site (footer, contact, homepage). */
if (!defined('SITE_EMAIL')) {
    $envPublic = getenv('SITE_EMAIL');
    define('SITE_EMAIL', is_string($envPublic) && $envPublic !== '' ? $envPublic : 'admin@alinabradu.com');
}

if (!defined('SITE_PHONE_DISPLAY')) {
    define('SITE_PHONE_DISPLAY', '068 693 056');
}

/** Prefix E.164 pentru link-uri tel: (+373 pentru MD). */
if (!defined('SITE_PHONE_TEL')) {
    define('SITE_PHONE_TEL', '+37368693056');
}

/** Operator de date (titular site) — GDPR / Legea 195/2024. */
if (!defined('DATA_CONTROLLER_NAME')) {
    define('DATA_CONTROLLER_NAME', 'Alina Bradu');
}

if (!defined('DATA_CONTROLLER_ADDRESS')) {
    define('DATA_CONTROLLER_ADDRESS', 'str. Ștefan cel Mare și Sfânt 126, Chișinău, Republica Moldova');
}

/** Contact pentru exercitarea drepturilor privind datele personale. */
if (!defined('PRIVACY_EMAIL')) {
    $envPrivacy = getenv('PRIVACY_EMAIL');
    define('PRIVACY_EMAIL', is_string($envPrivacy) && $envPrivacy !== '' ? $envPrivacy : 'admin@alinabradu.com');
}

if (!defined('CNPDP_NAME')) {
    define('CNPDP_NAME', 'Centrul Național pentru Protecția Datelor cu Caracter Personal');
}

if (!defined('CNPDP_URL')) {
    define('CNPDP_URL', 'https://datepersonale.md');
}
