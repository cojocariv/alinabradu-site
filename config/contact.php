<?php
declare(strict_types=1);

/** Email pentru mesaje de pe site și antet From (dacă serverul permite). */
if (!defined('SITE_EMAIL')) {
    define('SITE_EMAIL', 'info@alinabradu.md');
}

if (!defined('SITE_PHONE_DISPLAY')) {
    define('SITE_PHONE_DISPLAY', '068 693 056');
}

/** Prefix E.164 pentru link-uri tel: (+373 pentru MD). */
if (!defined('SITE_PHONE_TEL')) {
    define('SITE_PHONE_TEL', '+37368693056');
}
