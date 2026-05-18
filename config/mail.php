<?php
declare(strict_types=1);

/** Expeditor și destinatar formular contact */
define('CONTACT_FORM_FROM_EMAIL', 'admin@alinabradu.com');
define('CONTACT_FORM_TO_EMAIL', 'admin@alinabradu.com');

/** SMTP Plesk — user = același mailbox ca expeditorul */
define('SMTP_HOST', 'mail.alinabradu.com');
define('SMTP_PORT', 465);
define('SMTP_USERNAME', 'admin@alinabradu.com');
define('SMTP_ENCRYPTION', 'ssl');
define('SMTP_TIMEOUT', 15);

$mailLocal = __DIR__ . '/mail.local.php';
if (is_readable($mailLocal)) {
    require $mailLocal;
}

if (!defined('SMTP_PASSWORD')) {
    $pass = getenv('SMTP_PASSWORD');
    define('SMTP_PASSWORD', is_string($pass) && $pass !== '' ? $pass : '');
}
