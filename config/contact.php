<?php
declare(strict_types=1);

/** Email afișat pe site (footer, contact, homepage). */
if (!defined('SITE_EMAIL')) {
    $envPublic = getenv('SITE_EMAIL');
    define('SITE_EMAIL', is_string($envPublic) && $envPublic !== '' ? $envPublic : 'admin@alinabradu.com');
}

/** Destinație pentru mesajele din formularul de contact. */
if (!defined('CONTACT_FORM_TO')) {
    $envTo = getenv('CONTACT_FORM_TO');
    define('CONTACT_FORM_TO', is_string($envTo) && $envTo !== '' ? $envTo : 'admin@alinabradu.com');
}

/** Antet From la trimitere (dacă serverul permite); Reply-To rămâne emailul vizitatorului. */
if (!defined('CONTACT_MAIL_FROM')) {
    $envFrom = getenv('CONTACT_MAIL_FROM');
    define('CONTACT_MAIL_FROM', is_string($envFrom) && $envFrom !== '' ? $envFrom : 'admin@alinabradu.com');
}

if (!defined('SITE_PHONE_DISPLAY')) {
    define('SITE_PHONE_DISPLAY', '068 693 056');
}

/** Prefix E.164 pentru link-uri tel: (+373 pentru MD). */
if (!defined('SITE_PHONE_TEL')) {
    define('SITE_PHONE_TEL', '+37368693056');
}
