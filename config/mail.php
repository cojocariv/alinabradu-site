<?php
declare(strict_types=1);

if (!defined('CONTACT_FORM_FROM_EMAIL')) {
    $v = getenv('CONTACT_FORM_FROM_EMAIL');
    define('CONTACT_FORM_FROM_EMAIL', is_string($v) && $v !== '' ? $v : 'contact@alinabradu.com');
}

if (!defined('CONTACT_FORM_TO_EMAIL')) {
    $v = getenv('CONTACT_FORM_TO_EMAIL');
    define('CONTACT_FORM_TO_EMAIL', is_string($v) && $v !== '' ? $v : 'admin@alinabradu.com');
}

if (!defined('SMTP_HOST')) {
    $v = getenv('SMTP_HOST');
    define('SMTP_HOST', is_string($v) && $v !== '' ? $v : 'alinabradu.com');
}

if (!defined('SMTP_PORT')) {
    $v = getenv('SMTP_PORT');
    define('SMTP_PORT', $v !== false && $v !== '' ? (int) $v : 465);
}

if (!defined('SMTP_USERNAME')) {
    $v = getenv('SMTP_USERNAME');
    define('SMTP_USERNAME', is_string($v) && $v !== '' ? $v : 'contact@alinabradu.com');
}

if (!defined('SMTP_PASSWORD')) {
    $v = getenv('SMTP_PASSWORD');
    define('SMTP_PASSWORD', is_string($v) ? $v : '');
}

if (!defined('SMTP_ENCRYPTION')) {
    $v = getenv('SMTP_ENCRYPTION');
    $enc = is_string($v) && $v !== '' ? strtolower($v) : 'tls';
    if (!in_array($enc, ['tls', 'ssl', 'none'], true)) {
        $enc = 'tls';
    }
    define('SMTP_ENCRYPTION', $enc);
}

if (!defined('SMTP_TIMEOUT')) {
    $v = getenv('SMTP_TIMEOUT');
    define('SMTP_TIMEOUT', $v !== false && $v !== '' ? (int) $v : 12);
}

/** Încarcă opțional parola / override-uri din mail.local.php (nu se comite în Git). */
$mailLocalFile = __DIR__ . '/mail.local.php';
if (is_readable($mailLocalFile)) {
    require $mailLocalFile;
}

/** @return array{use_smtp:bool,host:string,port:int,user:string,pass:string,encryption:string,timeout:int,try_localhost:bool} */
function getMailConfig(): array
{
    $host = SMTP_HOST;
    $user = SMTP_USERNAME;
    $pass = SMTP_PASSWORD;

    return [
        'use_smtp' => $host !== '' && $user !== '' && $pass !== '',
        'host' => $host,
        'port' => SMTP_PORT > 0 ? SMTP_PORT : 465,
        'user' => $user,
        'pass' => $pass,
        'encryption' => SMTP_ENCRYPTION,
        'timeout' => SMTP_TIMEOUT > 0 ? SMTP_TIMEOUT : 12,
        'try_localhost' => filter_var(getenv('MAIL_TRY_LOCALHOST') ?: '1', FILTER_VALIDATE_BOOLEAN),
    ];
}
