<?php
declare(strict_types=1);

require_once __DIR__ . '/mail.php';

/** Email afișat pe site (footer, contact, homepage). */
if (!defined('SITE_EMAIL')) {
    $envPublic = getenv('SITE_EMAIL');
    define('SITE_EMAIL', is_string($envPublic) && $envPublic !== '' ? $envPublic : CONTACT_FORM_TO_EMAIL);
}

if (!defined('SITE_PHONE_DISPLAY')) {
    define('SITE_PHONE_DISPLAY', '068 693 056');
}

/** Prefix E.164 pentru link-uri tel: (+373 pentru MD). */
if (!defined('SITE_PHONE_TEL')) {
    define('SITE_PHONE_TEL', '+37368693056');
}
